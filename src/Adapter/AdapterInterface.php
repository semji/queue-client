<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;

interface AdapterInterface
{
    /**
     * @param string $queueName
     * @param mixed  $message
     * @param Priority $priority
     * @param int $delaySeconds
     *
     * @return AdapterInterface
     */
    public function addMessage($queueName, $message, Priority $priority = null, $delaySeconds = 0);

    /**
     * @param string $queueName
     * @param int    $nbMsg
     * @param Priority $priority
     *
     * @return array
     */
    public function getMessages($queueName, $nbMsg = 1, Priority $priority = null);

    /**
     * @param string $queueName
     * @param array  $message
     *
     * @return AdapterInterface
     */
    public function deleteMessage($queueName, $message);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return bool
     */
    public function isEmpty($queueName, Priority $priority = null);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return int
     */
    public function getNumberMessages($queueName, Priority $priority = null);

    /**
     * @param string $queueName
     *
     * @return AdapterInterface
     */
    public function deleteQueue($queueName);

    /**
     * @param string $queueName
     *
     * @return AdapterInterface
     */
    public function createQueue($queueName);

    /**
     * @param string $sourceQueueName
     * @param string $targetQueueName
     *
     * @return AdapterInterface
     */
    public function renameQueue($sourceQueueName, $targetQueueName);

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return AdapterInterface
     */
    public function purgeQueue($queueName, Priority $priority = null);

    /**
     * @param string $prefix
     *
     * @return array
     */
    public function listQueues($prefix = '');

    /**
     * @return PriorityHandlerInterface
     */
    public function getPriorityHandler();
}
