<?php

namespace ReputationVIP\QueueClient;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;

interface QueueClientInterface
{
    /**
     * @param string $queueName
     * @param mixed  $message
     * @param Priority $priority
     * @param int $delaySeconds
     *
     * @return QueueClientInterface
     */
    public function addMessage($queueName, $message, $priority = null, $delaySeconds = 0);

    /**
     * @param string $queueName
     * @param array  $messages
     * @param Priority $priority
     *
     * @return QueueClientInterface
     */
    public function addMessages($queueName, $messages, $priority = null);

    /**
     * @param string $queueName
     * @param Priority $priority
     * @param int $nbMsg
     *
     * @return array
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null);

    /**
     * @param string $queueName
     * @param array  $message
     *
     * @return QueueClientInterface
     */
    public function deleteMessage($queueName, $message);

    /**
     * @param string $queueName
     * @param array  $messages
     *
     * @return QueueClientInterface
     */
    public function deleteMessages($queueName, $messages);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return bool
     */
    public function isEmpty($queueName, $priority = null);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return int
     */
    public function getNumberMessages($queueName, $priority = null);

    /**
     * @param string $queueName
     *
     * @return QueueClientInterface
     */
    public function deleteQueue($queueName);

    /**
     * @param string $queueName
     *
     * @return QueueClientInterface
     */
    public function createQueue($queueName);

    /**
     * @param string $sourceQueueName
     * @param string $targetQueueName
     *
     * @return QueueClientInterface
     */
    public function renameQueue($sourceQueueName, $targetQueueName);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return QueueClientInterface
     */
    public function purgeQueue($queueName, $priority = null);

    /**
     * @param string $regex
     *
     * @return array
     */
    public function listQueues($regex = null);

    /**
     * @param string $queueName
     * @param string $alias
     *
     * @return QueueClientInterface
     */
    public function addAlias($queueName, $alias);

    /**
     * @param string $alias
     *
     * @return QueueClientInterface
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
