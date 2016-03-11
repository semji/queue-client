<?php

namespace ReputationVIP\QueueClient;

use ReputationVIP\QueueClient\Adapter\AdapterInterface;
use ReputationVIP\QueueClient\Adapter\Exception\QueueAccessException;
use ReputationVIP\QueueClient\Adapter\NullAdapter;
use ReputationVIP\QueueClient\Exception\QueueAliasException;

class QueueClient implements QueueClientInterface
{
    /** @var AdapterInterface */
    private $adapter;

    /** @var [] */
    private $aliases = [];

    /**
     * @param string $queueName
     * @return string|array
     */
    protected function resolveAliasQueueName($queueName)
    {
        if (array_key_exists($queueName, $this->aliases)) {
            $queues = $this->aliases[$queueName];
            if (count($queues) === 1) {
                return $queues[0];
            }
            return $queues;
        }
        return $queueName;
    }

    /**
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $adapter = new NullAdapter();

        }

        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addMessage($queueName, $message, $priority = null, $delaySeconds = 0)
    {
        $queues = (array) $this->resolveAliasQueueName($queueName);

        foreach ($queues as $queue) {
            $this->adapter->addMessage($queue, $message, $priority, $delaySeconds);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addMessages($queueName, $messages, $priority = null)
    {
        $queues = (array) $this->resolveAliasQueueName($queueName);

        foreach ($queues as $queue) {
            $this->adapter->addMessages($queue, $messages, $priority);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null)
    {
        $queues = $this->resolveAliasQueueName($queueName);
        if (is_array($queues)) {
            throw new QueueAliasException('Alias ' . $queueName . ' corresponds to several queues: ' . implode(' , ', $queues));
        } else {
            return $this->adapter->getMessages($queues, $nbMsg, $priority);
        }
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function deleteMessage($queueName, $message)
    {
        $queues = $this->resolveAliasQueueName($queueName);
        if (is_array($queues)) {
            throw new QueueAliasException('Alias ' . $queueName . ' corresponds to several queues: ' . implode(' , ', $queues));
        } else {
            $this->adapter->deleteMessage($queues, $message);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function deleteMessages($queueName, $messages)
    {
        foreach ($messages as $message) {
            $this->deleteMessage($queueName, $message);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function isEmpty($queueName, $priority = null)
    {
        $queues = $this->resolveAliasQueueName($queueName);

        if (is_array($queues)) {
            throw new QueueAliasException('Alias ' . $queueName . ' corresponds to several queues: ' . implode(' , ', $queues));
        } else {
            return $this->adapter->isEmpty($queues, $priority);
        }
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function getNumberMessages($queueName, $priority = null)
    {
        $queues = $this->resolveAliasQueueName($queueName);

        if (is_array($queues)) {
            throw new QueueAliasException('Alias ' . $queueName . ' corresponds to several queues: ' . implode(' , ', $queues));
        } else {
            return $this->adapter->getNumberMessages($queues, $priority);
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteQueue($queueName)
    {
        $this->adapter->deleteQueue($queueName);

        foreach ($this->aliases as $keyAlias => $alias) {
            $keys = array_keys($alias, $queueName);
            foreach ($keys as $key) {
                unset($this->aliases[$keyAlias][$key]);
                if (empty($this->aliases[$keyAlias])) {
                    unset($this->aliases[$keyAlias]);
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createQueue($queueName)
    {
        $this->adapter->createQueue($queueName);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        $this->adapter->renameQueue($sourceQueueName, $targetQueueName);

        foreach ($this->aliases as $keyAlias => $alias) {
            $keys = array_keys($alias, $sourceQueueName);
            foreach ($keys as $key) {
                $this->aliases[$keyAlias][$key] = $targetQueueName;
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function purgeQueue($queueName, $priority = null)
    {
        $queues = $this->resolveAliasQueueName($queueName);

        if (is_array($queues)) {
            throw new QueueAliasException('Alias ' . $queueName . ' corresponds to several queues: ' . implode(' , ', $queues));
        } else {
            $this->adapter->purgeQueue($queues, $priority);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function listQueues($regex = null)
    {
        $listQueuesAdapter = $this->adapter->listQueues();

        $listQueuesRegex = [];

        foreach ($listQueuesAdapter as $queue) {
            if (null === $regex || preg_match($regex, $queue)) {
                $listQueuesRegex[] = $queue;
            }
        }

        return $listQueuesRegex;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAliasException
     * @throws QueueAccessException
     */
    public function addAlias($queueName, $alias)
    {
        $listQueues = $this->listQueues();

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name is empty.');
        }

        if (empty($alias)) {
            throw new QueueAliasException('Alias is empty.');
        }

        if (!in_array($queueName, $listQueues)) {
            throw new QueueAccessException('Attempting to create alias on unknown queue.');
        }

        if (empty($this->aliases[$alias])) {
            $this->aliases[$alias] = [];
        }

        $this->aliases[$alias][] = $queueName;
        $this->aliases[$alias] = array_unique($this->aliases[$alias]);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAliasException
     */
    public function removeAlias($alias)
    {

        if (array_key_exists($alias, $this->aliases)) {
            unset($this->aliases[$alias]);
            if (empty($this->aliases[$alias])) {
                unset($this->aliases[$alias]);
            }
        } else {
            throw new QueueAliasException('No alias found.');
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @inheritdoc
     */
    public function getPriorityHandler()
    {
        return $this->adapter->getPriorityHandler();
    }
}
