<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\Exception\DomainException;
use ReputationVIP\QueueClient\Exception\InvalidArgumentException
use ReputationVIP\QueueClient\Exception\LogicException;
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
     */
    public function addMessage($queueName, $message, $priority = null, $delaySeconds = 0)
    {
        if (null === $priority) {
            $priority = $this->priorityHandler->getDefault();
        }

        if (empty($queueName)) {
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (empty($message)) {
            throw new InvalidArgumentException('Message empty or not defined.');
        }
        if (isset($this->queues[$queueName][$priority])) {
            $new_message = [
                'id' => uniqid($queueName . $priority, true),
                'time-in-flight' => null,
                'delayed-until' => time() + $delaySeconds,
                'Body' => serialize($message),
            ];
            /** @var SplQueue $splQueue */
            $splQueue = $this->queues[$queueName][$priority];
            $splQueue->enqueue($new_message);
        } else {
            throw new DomainException('Priority ' . $priority . ' unknown.');
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteMessage($queueName, $message)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidArgumentException('Message empty or not defined.');
        }
        if (!is_array($message)) {
            throw new InvalidArgumentException('Message must be an array.');
        }
        if (!isset($message['id'])) {
            throw new InvalidArgumentException('Message id not found in message.');
        }
        if (!isset($message['priority'])) {
            throw new InvalidArgumentException('Message priority not found in message.');
        }

        if (isset($this->queues[$queueName][$message['priority']])) {
            foreach ($this->queues[$queueName][$message['priority']] as $key => $messageIterator) {
                if ($messageIterator['id'] === $message['id']) {
                    unset($this->queues[$queueName][$message['priority']][$key]);
                    break;
                }
            }
        } else {
            throw new DomainException('priority ' . $message['priority'] . ' unknown.');
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null)
    {
        $messages = [];
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
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        if (isset($this->queues[$queueName][$priority])) {
            foreach ($this->queues[$queueName][$priority] as $key => $message) {
                $timeDiff = time() - $message['time-in-flight'];
                if ((null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT)
                    && $message['delayed-until'] <= time()
                ) {
                    $splQueueContent = $this->queues[$queueName][$priority][$key];
                    $splQueueContent['time-in-flight'] = time();
                    $this->queues[$queueName][$priority][$key] = $splQueueContent;
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
        } else {
            throw new DomainException('Unknown priority: ' . $priority);
        }

        return $messages;
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
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority])) {
            throw new DomainException('Unknown priority: ' . $priority);
        }

        /** @var SplQueue $splQueue */
        $splQueue = $this->queues[$queueName][$priority];
        return $splQueue->isEmpty();
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
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority])) {
            throw new DomainException('Unknown priority: ' . $priority);
        }

        foreach ($this->queues[$queueName][$priority] as $key => $message) {
            $timeDiff = time() - $message['time-in-flight'];
            if (null === $message['time-in-flight'] || $timeDiff > self::MAX_TIME_IN_FLIGHT) {
                ++$nbrMsg;
            }
        }

        return $nbrMsg;
    }

    /**
     * @inheritdoc
     */
    public function deleteQueue($queueName, $nb_try = 0)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }

        unset($this->queues[$queueName]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createQueue($queueName)
    {
        if (empty($queueName)) {
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (isset($this->queues[$queueName])) {
            throw new LogicException('A queue named ' . $queueName . ' already exist.');
        }
        if (strpos($queueName, ' ') !== false) {
            throw new InvalidArgumentException('Queue name must not contain white spaces.');
        }

        $priorities = $this->priorityHandler->getAll();
        foreach ($priorities as $priority) {
            $this->queues[$queueName][$priority] = new SplQueue();
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        if (empty($sourceQueueName)) {
            throw new InvalidArgumentException('Source queue name empty or not defined.');
        }

        if (!isset($this->queues[$sourceQueueName])) {
            throw new LogicException("Queue " . $sourceQueueName . " doesn't exist, please create it before using it.");
        }
        if (empty($targetQueueName)) {
            throw new InvalidArgumentException('Target queue name empty or not defined.');
        }

        if (isset($this->queues[$targetQueueName])) {
            throw new LogicException("Queue " . $targetQueueName . ' already exist.');
        }

        $this->createQueue($targetQueueName);
        $this->queues[$targetQueueName] = $this->queues[$sourceQueueName];
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
            throw new InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!isset($this->queues[$queueName])) {
            throw new LogicException("Queue " . $queueName . " doesn't exist, please create it before using it.");
        }
        if (!isset($this->queues[$queueName][$priority])) {
            throw new DomainException('Unknown priority: ' . $priority);
        }

        $this->queues[$queueName][$priority] = new SplQueue();

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
