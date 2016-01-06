<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;

interface AdapterInterface
{
    /**
     * @param string $queueName
     * @param mixed  $message
     * @param string $priority
     * @throw InvalidArgumentException
     *
     * @return AdapterInterface
     */
    public function addMessage($queueName, $message, $priority = null);

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
     * @return AdapterInterface
     */
    public function deleteMessage($queueName, $message);

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
     * @return AdapterInterface
     */
    public function deleteQueue($queueName);

    /**
     * @param string $queueName
     * @throw InvalidArgumentException
     *
     * @return AdapterInterface
     */
    public function createQueue($queueName);

    /**
     * @param string $sourceQueueName
     * @param string $targetQueueName
     * @throw InvalidArgumentException
     *
     * @return AdapterInterface
     */
    public function renameQueue($sourceQueueName, $targetQueueName);

    /**
     * @param string $queueName
     * @param string $priority
     * @throw InvalidArgumentException
     *
     * @return AdapterInterface
     */
    public function purgeQueue($queueName, $priority = null);

    /**
     * @param string $prefix
     *
     * @return array
     * @throw InvalidArgumentException
     */
    public function listQueues($prefix = '');

    /**
     * @return PriorityHandlerInterface
     */
    public function getPriorityHandler();
}
