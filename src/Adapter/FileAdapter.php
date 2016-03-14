<?php

namespace ReputationVIP\QueueClient\Adapter;

use InvalidArgumentException;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;
use ReputationVIP\QueueClient\Utils\LockHandlerFactory;
use ReputationVIP\QueueClient\Utils\LockHandlerFactoryInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileAdapter extends AbstractAdapter implements AdapterInterface
{
    const QUEUE_FILE_EXTENSION = 'queue';
    const MAX_NB_MESSAGES = 10;
    const MAX_TIME_IN_FLIGHT = 30;
    const MAX_LOCK_TRIES = 30;
    const PRIORITY_SEPARATOR = '-';

    /** @var Finder $finder */
    private $finder;

    /** @var string $repository */
    private $repository;

    /** @var Filesystem $fs */
    private $fs;

    /** @var LockHandlerFactoryInterface $fs */
    private $lockHandlerFactory;

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    /**
     * @param string $repository
     * @param PriorityHandlerInterface $priorityHandler
     * @param Filesystem $fs
     * @param Finder $finder
     * @param LockHandlerFactoryInterface $lockHandlerFactory
     */
    public function __construct($repository, PriorityHandlerInterface $priorityHandler = null, Filesystem $fs = null, Finder $finder = null, LockHandlerFactoryInterface $lockHandlerFactory = null)
    {
        if (empty($repository)) {
            throw new InvalidArgumentException('Argument repository empty or not defined.');
        }

        if (null === $fs) {
            $fs = new Filesystem();
        }

        if (null === $finder) {
            $finder = new Finder();
        }

        if (null === $lockHandlerFactory) {
            $lockHandlerFactory = new LockHandlerFactory();
        }

        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        $this->fs = $fs;
        if (!$this->fs->exists($repository)) {
            try {
                $this->fs->mkdir($repository);
            } catch (IOExceptionInterface $e) {
                throw new InvalidArgumentException('An error occurred while creating your directory at ' . $e->getPath());
            }
        }

        $this->priorityHandler = $priorityHandler;
        $this->repository = $repository;
        $this->finder = $finder;
        $this->finder->files()->in($repository);
        $this->lockHandlerFactory = $lockHandlerFactory;

        return $this;
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    private function startsWith($haystack, $needle)
    {
        return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /**
     * @param string $queueName
     * @param string $priority
     *
     * @return string
     */
    private function getQueuePath($queueName, $priority)
    {
        $prioritySuffix = '';
        if ('' !== $priority) {
            $prioritySuffix = static::PRIORITY_SEPARATOR . $priority;
        }
        return (rtrim($this->repository, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $queueName . $prioritySuffix . '.' . static::QUEUE_FILE_EXTENSION);
    }

    /**
     * @param string $queueName
     * @param string $priority
     * @param int $nbTries
     *
     * @return array
     *
     * @throws \Exception
     */
    private function readQueueFromFile($queueName, $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(10);

            return $this->readQueueFromFile($queueName, $priority, ($nbTries + 1));
        }
        try {
            $content = '';
            /* @var  SplFileInfo $file */
            foreach ($this->finder as $file) {
                if ($queueFilePath === $file->getPathname()) {
                    $content = $file->getContents();
                }
            }
            if (empty($content)) {
                throw new \Exception('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
        } catch (\Exception $e) {
            $lockHandler->release();
            throw $e;
        }
        $lockHandler->release();

        return $queue;
    }

    /**
     * @param string $queueName
     * @param string $priority
     * @param array $queue
     * @param int $nbTries
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function writeQueueInFile($queueName, $priority, $queue, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(100);
            return $this->writeQueueInFile($queueName, $priority, $queue, ($nbTries + 1));
        }
        try {
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lockHandler->release();
            throw $e;
        }
        $lockHandler->release();
        return $this;
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param string $priority
     * @param int $nbTries
     * @param int $delaySeconds
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function addMessageLock($queueName, $message, $priority, $nbTries = 0, $delaySeconds = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(10);

            return $this->addMessageLock($queueName, $message, $priority, ($nbTries + 1), $delaySeconds);
        }
        try {
            $content = '';
            /* @var  SplFileInfo $file */
            foreach ($this->finder as $file) {
                if ($queueFilePath === $file->getPathname()) {
                    $content = $file->getContents();
                }
            }
            if (empty($content)) {
                throw new \Exception('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!(isset($queue['queue']))) {
                throw new \Exception('Queue content bad format.');
            }
            $new_message = [
                'id' => uniqid($queueName . $priority, true),
                'time-in-flight' => null,
                'delayed-until' => time() + $delaySeconds,
                'Body' => serialize($message),
            ];
            array_push($queue['queue'], $new_message);
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lockHandler->release();
            throw $e;
        }
        $lockHandler->release();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addMessage($queueName, $message, $priority = null, $delaySeconds = 0)
    {
        if (null === $priority) {
            $priority = $this->priorityHandler->getDefault();
        }
        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }
        if (empty($message)) {
            throw new InvalidArgumentException('Parameter message empty or not defined.');
        }

        $this->addMessageLock($queueName, $message, $priority);

        return $this;
    }

    /**
     * @param string $queueName
     * @param string $priority
     * @param int $nbMsg
     * @param int $nbTries
     *
     * @return array
     *
     * @throws \Exception
     */
    private function getMessagesLock($queueName, $nbMsg, $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(10);

            return $this->getMessagesLock($queueName, $nbMsg, $priority, ($nbTries + 1));
        }
        $messages = [];
        try {
            $content = '';
            /* @var  SplFileInfo $file */
            foreach ($this->finder as $file) {
                if ($queueFilePath === $file->getPathname()) {
                    $content = $file->getContents();
                }
            }
            if (empty($content)) {
                throw new \Exception('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!isset($queue['queue'])) {
                throw new \Exception('Queue content bad format.');
            }
            foreach ($queue['queue'] as $key => $message) {
                $timeDiff = time() - $message['time-in-flight'];
                if ((null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT)
                    && $message['delayed-until'] <= time()
                ) {
                    $queue['queue'][$key]['time-in-flight'] = time();
                    $message['time-in-flight'] = time();
                    $message['Body'] = unserialize($message['Body']);
                    $message['priority'] = $priority;
                    $messages[] = $message;
                    --$nbMsg;
                    if (0 === $nbMsg) {
                        break;
                    }
                }
            }
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lockHandler->release();
            throw $e;
        }
        $lockHandler->release();

        return $messages;
    }

    /**
     * @inheritdoc
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            $messages = [];
            foreach ($priorities as $priority) {
                $messagesPriority = $this->getMessages($queueName, $nbMsg, $priority);
                $nbMsg -= count($messagesPriority);
                $messages = array_merge($messages, $messagesPriority);
                if ($nbMsg <= 0) {
                    return $messages;
                }
            }
            return $messages;
        }

        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        if (!is_numeric($nbMsg)) {
            throw new InvalidArgumentException('Parameter number is not a number.');
        }
        if ($nbMsg <= 0 || $nbMsg > static::MAX_NB_MESSAGES) {
            throw new InvalidArgumentException('Parameter number is not valid.');
        }
        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }
        return $this->getMessagesLock($queueName, $nbMsg, $priority);
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param string $priority
     * @param int $nbTries
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function deleteMessageLock($queueName, $message, $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(10);
            return $this->deleteMessageLock($queueName, $message, $priority, ($nbTries + 1));
        }
        try {
            $content = '';
            /* @var  SplFileInfo $file */
            foreach ($this->finder as $file) {
                if ($queueFilePath === $file->getPathname()) {
                    $content = $file->getContents();
                }
            }
            if (empty($content)) {
                throw new \Exception('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!isset($queue['queue'])) {
                throw new \Exception('Queue content bad format.');
            }
            foreach ($queue['queue'] as $key => $messageIterator) {
                if ($messageIterator['id'] === $message['id']) {
                    unset($queue['queue'][$key]);
                    break;
                }
            }
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lockHandler->release();
            throw $e;
        }
        $lockHandler->release();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteMessage($queueName, $message)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidArgumentException('Parameter message empty or not defined.');
        }
        if (!is_array($message)) {
            throw new InvalidArgumentException('message must be an array.');
        }
        if (!isset($message['id'])) {
            throw new InvalidArgumentException('Message id not found in message.');
        }
        if (!isset($message['priority'])) {
            throw new InvalidArgumentException('Message priority not found in message.');
        }
        if (!$this->fs->exists($this->getQueuePath($queueName, $message['priority']))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }

        $this->deleteMessageLock($queueName, $message, $message['priority']);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty($queueName, $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority)
                if (!($this->isEmpty($queueName, $priority))) {
                    return false;
            }
            return true;
        }

        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!(isset($queue['queue']))) {
            throw new \Exception('Queue content bad format.');
        }

        return count($queue['queue']) > 0 ? false : true;
    }

    /**
     * @inheritdoc
     */
    public function getNumberMessages($queueName, $priority = null)
    {
        $nbrMsg = 0;

        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $nbrMsg += $this->getNumberMessages($queueName, $priority);
            }

            return $nbrMsg;
        }

        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!(isset($queue['queue']))) {
            throw new \Exception('Queue content bad format.');
        }
        foreach ($queue['queue'] as $key => $message) {
            $timeDiff = time() - $message['time-in-flight'];
            if (null === $message['time-in-flight'] || $timeDiff > static::MAX_TIME_IN_FLIGHT) {
                ++$nbrMsg;
            }
        }

        return $nbrMsg;
    }

    /**
     * @param string $queueName
     * @param string $priority
     * @param int $nbTries
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function deleteQueueLock($queueName, $priority, $nbTries = 0)
    {
        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }

        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lockHandler = $this->lockHandlerFactory->getLockHandler($queueFilePath);
        if (!$lockHandler->lock()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new \Exception('Lock timeout for file ' . $queueFilePath);
            }
            usleep(10);
            return $this->deleteQueueLock($queueName, $priority, ($nbTries + 1));
        }
        $this->fs->remove($queueFilePath);
        $lockHandler->release();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteQueue($queueName)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->deleteQueueLock($queueName, $priority);
        }

        return $this;
    }

    /**
     * @param string $queueName
     * @param string $priority
     *
     * @throws \Exception
     */
    private function createQueueLock($queueName, $priority)
    {
        if ($this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new \Exception('Queue with name ' . $queueName . ' already exist.');
        }
        if (strpos($queueName, ' ') !== false) {
            throw new \Exception('QueueName must not contain any space.');
        }

        $queue = [
            'queue' => [],
        ];
        $this->writeQueueInFile($queueName, $priority, $queue);
    }

    /**
     * @inheritdoc
     */
    public function createQueue($queueName)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->createQueueLock($queueName, $priority);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        if (empty($sourceQueueName)) {
            throw new InvalidArgumentException('Parameter sourceQueueName empty or not defined.');
        }

        if (empty($targetQueueName)) {
            throw new InvalidArgumentException('Parameter targetQueueName empty or not defined.');
        }

        $this->createQueue($targetQueueName);

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $queue = $this->readQueueFromFile($sourceQueueName, $priority);
            $this->writeQueueInFile($targetQueueName, $priority, $queue);
        }

        $this->deleteQueue($sourceQueueName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function purgeQueue($queueName, $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $this->purgeQueue($queueName, $priority);
            }

            return $this;
        }

        if (empty($queueName)) {
            throw new InvalidArgumentException('Parameter queueName empty or not defined.');
        }


        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new InvalidArgumentException('Queue ' . $queueName . " doesn't exist, please create it before use it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!isset($queue['queue'])) {
            throw new \Exception('Queue content bad format.');
        }
        $queue['queue'] = [];
        $this->writeQueueInFile($queueName, $priority, $queue);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function listQueues($prefix = '')
    {
        $result = [];
        /* @var  SplFileInfo $file */
        foreach ($this->finder as $file) {
            if (!empty($prefix) && !$this->startsWith($file->getRelativePathname(), $prefix)) {
                continue;
            }
            if ($file->getExtension() === static::QUEUE_FILE_EXTENSION) {
                $explode = explode('.', $file->getRelativePathname());
                array_pop($explode);
                $implode = implode('.', $explode);
                $priorities = $this->priorityHandler->getAll();
                foreach ($priorities as $priority) {
                    if (!empty($priority)) {
                        $implode = str_replace(static::PRIORITY_SEPARATOR . $priority, '', $implode);
                    }
                }
                $result[] = $implode;
            }
        }
        $result = array_unique($result);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPriorityHandler()
    {
        return $this->priorityHandler;
    }
}
