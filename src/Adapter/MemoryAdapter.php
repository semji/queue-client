<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\Adapter\Exception\InvalidMessageException;
use ReputationVIP\QueueClient\Adapter\Exception\QueueAccessException;
use ReputationVIP\QueueClient\PriorityHandler\Exception\InvalidPriorityException;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;
use SplQueue;

class MemoryAdapter extends AbstractAdapter implements AdapterInterface
{
    const MAX_TIME_IN_FLIGHT = 30;

    /** @var array */
    private $queues;

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    public function __construct(PriorityHandlerInterface $priorityHandler = null)
    {
        $this->queues = [];

        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        $this->priorityHandler = $priorityHandler;
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
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws QueueAccessException
     * @throws InvalidPriorityException
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

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        if (isset($this->queues[$queueName][$priority->getName()])) {
            $new_message = [
                'id' => uniqid($queueName . $priority->getName(), true),
                'time-in-flight' => null,
                'delayed-until' => time() + $delaySeconds,
                'Body' => serialize($message),
            ];
            /** @var SplQueue $splQueue */
            $splQueue = $this->queues[$queueName][$priority->getName()];
            $splQueue->enqueue($new_message);
        } else {
            throw new InvalidPriorityException('Unknown priority: ' . $priority->getName());
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws InvalidPriorityException
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

        if (isset($this->queues[$queueName][$message['priority']])) {
            foreach ($this->queues[$queueName][$message['priority']] as $key => $messageIterator) {
                if ($messageIterator['id'] === $message['id']) {
                    unset($this->queues[$queueName][$message['priority']][$key]);
                    break;
                }
            }
        } else {
            throw new InvalidPriorityException('Unknown priority: ' . $message['priority']);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws InvalidPriorityException
     */
    public function getMessages($queueName, $nbMsg = 1, Priority $priority = null)
    {
        $messages = [];

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
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

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        if (isset($this->queues[$queueName][$priority->getName()])) {
            foreach ($this->queues[$queueName][$priority->getName()] as $key => $message) {
                $timeDiff = time() - $message['time-in-flight'];
                if ((null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT)
                    && $message['delayed-until'] <= time()
                ) {
                    $splQueueContent = $this->queues[$queueName][$priority->getName()][$key];
                    $splQueueContent['time-in-flight'] = time();
                    $this->queues[$queueName][$priority->getName()][$key] = $splQueueContent;
                    $message['time-in-flight'] = time();
                    $message['Body'] = unserialize($message['Body']);
                    $message['priority'] = $priority->getName();
                    $messages[] = $message;
                    --$nbMsg;
                    if (0 === $nbMsg) {
                        break;
                    }
                }
            }
        } else {
            throw new InvalidPriorityException('Unknown priority: ' . $priority->getName());
        }

        return $messages;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws InvalidPriorityException
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

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority->getName()])) {
            throw new InvalidPriorityException('Unknown priority: ' . $priority->getName());
        }

        /** @var SplQueue $splQueue */
        $splQueue = $this->queues[$queueName][$priority->getName()];
        return $splQueue->isEmpty();
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws InvalidPriorityException
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

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority->getName()])) {
            throw new InvalidPriorityException('Unknown priority: ' . $priority->getName());
        }

        foreach ($this->queues[$queueName][$priority->getName()] as $key => $message) {
            $timeDiff = time() - $message['time-in-flight'];
            if (null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT) {
                ++$nbrMsg;
            }
        }

        return $nbrMsg;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function deleteQueue($queueName, $nb_try = 0)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        unset($this->queues[$queueName]);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function createQueue($queueName)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (strpos($queueName, ' ') !== false) {
            throw new \InvalidArgumentException('Queue name must not contain white spaces.');
        }

        if (isset($this->queues[$queueName])) {
            throw new QueueAccessException('A queue named ' . $queueName . ' already exist.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->queues[$queueName][$priority->getName()] = new SplQueue();
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        if (empty($sourceQueueName)) {
            throw new \InvalidArgumentException('Source queue name empty or not defined.');
        }

        if (empty($targetQueueName)) {
            throw new \InvalidArgumentException('Target queue name empty or not defined.');
        }

        if (!isset($this->queues[$sourceQueueName])) {
            throw new QueueAccessException("Queue " . $sourceQueueName . " doesn't exist, please create it before using it.");
        }

        if (isset($this->queues[$targetQueueName])) {
            throw new QueueAccessException("Queue " . $targetQueueName . ' already exist.');
        }

        $this->createQueue($targetQueueName);
        $this->queues[$targetQueueName] = $this->queues[$sourceQueueName];
        $this->deleteQueue($sourceQueueName);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     * @throws InvalidPriorityException
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

        if (!isset($this->queues[$queueName])) {
            throw new QueueAccessException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority->getName()])) {
            throw new InvalidPriorityException('Unknown priority: ' . $priority->getName());
        }

        $this->queues[$queueName][$priority->getName()] = new SplQueue();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function listQueues($prefix = '')
    {
        $result = [];
        foreach ($this->queues as $queueName => $queue) {
            if (!empty($prefix) && !$this->startsWith($queueName, $prefix)) {
                continue;
            }
            $result[] = $queueName;
        }

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
