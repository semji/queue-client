<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\Adapter\Exception\InvalidMessageException;
use ReputationVIP\QueueClient\Adapter\Exception\QueueAccessException;
use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

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

    /** @var Factory $lockHandlerFactory */
    private $lockHandlerFactory;

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    /**
     * @param string $repository
     * @param PriorityHandlerInterface $priorityHandler
     * @param Filesystem $fs
     * @param Finder $finder
     * @param Factory $lockHandlerFactory
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function __construct($repository, PriorityHandlerInterface $priorityHandler = null, Filesystem $fs = null, Finder $finder = null, Factory $lockHandlerFactory = null)
    {
        if (empty($repository)) {
            throw new \InvalidArgumentException('Argument repository empty or not defined.');
        }

        if (null === $fs) {
            $fs = new Filesystem();
        }

        if (null === $finder) {
            $finder = new Finder();
        }

        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        $this->fs = $fs;

        if (!$this->fs->exists($repository)) {
            try {
                $this->fs->mkdir($repository);
            } catch (IOExceptionInterface $e) {
                throw new QueueAccessException('An error occurred while creating your directory at ' . $e->getPath());
            }
        }

        if (null === $lockHandlerFactory) {
            $lockHandlerFactory = new Factory(new FlockStore($repository));
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
     * @param Priority $priority
     *
     * @return string
     */
    private function getQueuePath($queueName, Priority $priority)
    {
        $prioritySuffix = '';
        if ('' !== $priority->getName()) {
            $prioritySuffix = static::PRIORITY_SEPARATOR . $priority->getName();
        }
        return (rtrim($this->repository, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $queueName . $prioritySuffix . '.' . static::QUEUE_FILE_EXTENSION);
    }

    /**
     * @param string $queueName
     * @param Priority $priority
     * @param int $nbTries
     *
     * @return array
     *
     * @throws QueueAccessException
     * @throws \Exception
     */
    private function readQueueFromFile($queueName, Priority $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
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
                throw new QueueAccessException('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
        } catch (\Exception $e) {
            $lock->release();
            throw $e;
        }
        $lock->release();

        return $queue;
    }

    /**
     * @param string $queueName
     * @param Priority $priority
     * @param array $queue
     * @param int $nbTries
     *
     * @return AdapterInterface
     *
     * @throws QueueAccessException
     * @throws \Exception
     */
    private function writeQueueInFile($queueName, Priority $priority, $queue, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
            }
            usleep(100);
            return $this->writeQueueInFile($queueName, $priority, $queue, ($nbTries + 1));
        }
        try {
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lock->release();
            throw $e;
        }
        $lock->release();
        return $this;
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param Priority $priority
     * @param int $nbTries
     * @param int $delaySeconds
     *
     * @return AdapterInterface
     *
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     * @throws \Exception
     */
    private function addMessageLock($queueName, $message, Priority $priority, $nbTries = 0, $delaySeconds = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
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
                throw new QueueAccessException('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!(isset($queue['queue']))) {
                throw new \UnexpectedValueException('Queue content bad format.');
            }
            $new_message = [
                'id' => uniqid($queueName . $priority->getLevel(), true),
                'time-in-flight' => null,
                'delayed-until' => time() + $delaySeconds,
                'Body' => serialize($message),
            ];
            array_push($queue['queue'], $new_message);
            $queueJson = json_encode($queue);
            $this->fs->dumpFile($queueFilePath, $queueJson);
        } catch (\Exception $e) {
            $lock->release();
            throw $e;
        }
        $lock->release();
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws QueueAccessException
     */
    public function addMessage($queueName, $message, Priority $priority = null, $delaySeconds = 0)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidMessageException($message, 'Message empty or not defined.');
        }

        if (null === $priority) {
            $priority = $this->priorityHandler->getDefault();
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        $this->addMessageLock($queueName, $message, $priority);

        return $this;
    }

    /**
     * @param string $queueName
     * @param Priority $priority
     * @param int $nbMsg
     * @param int $nbTries
     *
     * @return array
     *
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     * @throws \Exception
     */
    private function getMessagesLock($queueName, $nbMsg, Priority $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
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
                throw new QueueAccessException('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!isset($queue['queue'])) {
                throw new \UnexpectedValueException('Queue content bad format.');
            }
            foreach ($queue['queue'] as $key => $message) {
                $timeDiff = time() - $message['time-in-flight'];
                if ((null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT)
                    && $message['delayed-until'] <= time()
                ) {
                    $queue['queue'][$key]['time-in-flight'] = time();
                    $message['time-in-flight'] = time();
                    $message['Body'] = unserialize($message['Body']);
                    $message['priority'] = $priority->getLevel();
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
            $lock->release();
            throw $e;
        }
        $lock->release();

        return $messages;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function getMessages($queueName, $nbMsg = 1, Priority $priority = null)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!is_numeric($nbMsg)) {
            throw new \InvalidArgumentException('Number of messages must be numeric.');
        }

        if ($nbMsg <= 0 || $nbMsg > static::MAX_NB_MESSAGES) {
            throw new \InvalidArgumentException('Number of messages is not valid.');
        }

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

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        return $this->getMessagesLock($queueName, $nbMsg, $priority);
    }

    /**
     * @param string $queueName
     * @param string $message
     * @param Priority $priority
     * @param int $nbTries
     *
     * @return AdapterInterface
     *
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     * @throws \Exception
     */
    private function deleteMessageLock($queueName, $message, Priority $priority, $nbTries = 0)
    {
        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
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
                throw new QueueAccessException('Fail to get content from file ' . $queueFilePath);
            }
            $queue = json_decode($content, true);
            if (!isset($queue['queue'])) {
                throw new \UnexpectedValueException('Queue content bad format.');
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
            $lock->release();
            throw $e;
        }
        $lock->release();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws QueueAccessException
     */
    public function deleteMessage($queueName, $message)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidMessageException($message, 'Message empty or not defined.');
        }

        if (!is_array($message)) {
            throw new InvalidMessageException($message, 'Message must be an array.');
        }

        if (!isset($message['id'])) {
            throw new InvalidMessageException($message, 'Message id not found in message.');
        }

        if (!isset($message['priority'])) {
            throw new InvalidMessageException($message, 'Message priority not found in message.');
        }

        $priority = $this->priorityHandler->getPriorityByLevel($message['priority']);

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before use it.");
        }

        $this->deleteMessageLock($queueName, $message, $priority);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     */
    public function isEmpty($queueName, Priority $priority = null)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority)
                if (!($this->isEmpty($queueName, $priority))) {
                    return false;
            }
            return true;
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!(isset($queue['queue']))) {
            throw new \UnexpectedValueException('Queue content bad format.');
        }

        return count($queue['queue']) > 0 ? false : true;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     */
    public function getNumberMessages($queueName, Priority $priority = null)
    {
        $nbrMsg = 0;

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $nbrMsg += $this->getNumberMessages($queueName, $priority);
            }

            return $nbrMsg;
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!(isset($queue['queue']))) {
            throw new \UnexpectedValueException('Queue content bad format.');
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
     * @param Priority $priority
     * @param int $nbTries
     *
     * @return AdapterInterface
     * @throws QueueAccessException
     */
    private function deleteQueueLock($queueName, Priority $priority, $nbTries = 0)
    {
        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        $queueFilePath = $this->getQueuePath($queueName, $priority);
        $lock = $this->lockHandlerFactory->createLock($queueFilePath);
        if (!$lock->acquire()) {
            if ($nbTries >= static::MAX_LOCK_TRIES) {
                throw new QueueAccessException('Reach max retry for locking queue file ' . $queueFilePath);
            }
            usleep(10);
            return $this->deleteQueueLock($queueName, $priority, ($nbTries + 1));
        }
        $this->fs->remove($queueFilePath);
        $lock->release();
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    public function deleteQueue($queueName)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->deleteQueueLock($queueName, $priority);
        }

        return $this;
    }

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    private function createQueueLock($queueName, Priority $priority)
    {
        if (strpos($queueName, ' ') !== false) {
            throw new \InvalidArgumentException('Queue name must not contain white spaces.');
        }

        if ($this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException('A queue named ' . $queueName . ' already exist.');
        }

        $queue = [
            'queue' => [],
        ];
        $this->writeQueueInFile($queueName, $priority, $queue);
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    public function createQueue($queueName)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->createQueueLock($queueName, $priority);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        if (empty($sourceQueueName)) {
            throw new \InvalidArgumentException('Source queue name empty or not defined.');
        }

        if (empty($targetQueueName)) {
            throw new \InvalidArgumentException('Target queue name empty or not defined.');
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
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws \UnexpectedValueException
     */
    public function purgeQueue($queueName, Priority $priority = null)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $this->purgeQueue($queueName, $priority);
            }

            return $this;
        }

        if (!$this->fs->exists($this->getQueuePath($queueName, $priority))) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        $queue = $this->readQueueFromFile($queueName, $priority);
        if (!isset($queue['queue'])) {
            throw new \UnexpectedValueException('Queue content bad format.');
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
                        $implode = str_replace(static::PRIORITY_SEPARATOR . $priority->getName(), '', $implode);
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
