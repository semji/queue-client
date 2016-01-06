<?php

namespace ReputationVIP\QueueClient;

use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;

interface QueueClientInterface
{
    /**
     * @param string $queueName
     * @param mixed  $message
     * @param string $priority
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function addMessage($queueName, $message, $priority = null);

    /**
     * @param string $queueName
     * @param array  $messages
     * @param string $priority
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function addMessages($queueName, $messages, $priority = null);

    /**
     * @param string $queueName
     * @param string $priority
     * @param int    $nbMsg
     *
     * @return array
     * @throw InvalidArgumentException
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null);

    /**
     * @param string $queueName
     * @param array  $message
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function deleteMessage($queueName, $message);

    /**
     * @param string $queueName
     * @param array  $messages
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function deleteMessages($queueName, $messages);

    /**
     * @param string $queueName
     * @param string $priority
     *
     * @return bool
     * @throw InvalidArgumentException
     */
    public function isEmpty($queueName, $priority = null);

    /**
     * @param string $queueName
     * @param string $priority
     *
     * @return int
     * @throw InvalidArgumentException
     */
    public function getNumberMessages($queueName, $priority = null);

    /**
     * @param string $queueName
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function deleteQueue($queueName);

    /**
     * @param string $queueName
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function createQueue($queueName);

    /**
     * @param string $sourceQueueName
     * @param string $targetQueueName
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function renameQueue($sourceQueueName, $targetQueueName);

    /**
     * @param string $queueName
     * @param string $priority
     * @throw InvalidArgumentException
     *
     * @return QueueClientInterface
     */
    public function purgeQueue($queueName, $priority = null);

    /**
     * @param string $regex
     *
     * @return array
     * @throw InvalidArgumentException
     */
    public function listQueues($regex = null);

    /**
     * @param string $queueName
     * @param string $alias
     *
     * @return QueueClientInterface
     * @throw InvalidArgumentException
     */
    public function addAlias($queueName, $alias);

    /**
     * @param string $alias
     *
     * @return QueueClientInterface
     * @throw InvalidArgumentException
     */
    public function removeAlias($alias);

    /**
     * @return []
     */
    public function getAliases();

    /**
     * @return PriorityHandlerInterface
     */
    public function getPriorityHandler();
}
