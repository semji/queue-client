<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;

class NullAdapter extends AbstractAdapter implements AdapterInterface
{

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    /**
     * @param PriorityHandlerInterface|null $priorityHandler
     */
    public function __construct(PriorityHandlerInterface $priorityHandler = null)
    {
        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        $this->priorityHandler = $priorityHandler;
    }

    /**
     * @inheritdoc
     */
    public function addMessage($queueName, $message, $priority = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessages($queueName, $nbMsg = 1, $priority = null)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function deleteMessage($queueName, $message, $priority = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty($queueName, $priority = null)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getNumberMessages($queueName, $priority = null)
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function deleteQueue($queueName)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createQueue($queueName)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function purgeQueue($queueName, $priority = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function listQueues($prefix = '')
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getPriorityHandler()
    {
        return $this->priorityHandler;
    }
}
