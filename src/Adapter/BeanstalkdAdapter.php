<?php

namespace ReputationVIP\QueueClient\Adapter;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;

class BeanstalkdAdapter extends AbstractAdapter implements AdapterInterface
{

    const DEFAULT_HOST = '127.0.0.1';

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    /** @var PheanstalkInterface $pheanstalkInterface */
    private $pheanstalkInterface;

    /**
     * @param PheanstalkInterface $pheanstalkInterface
     * @param PriorityHandlerInterface|null $priorityHandler
     */
    public function __construct(PheanstalkInterface $pheanstalkInterface = null, PriorityHandlerInterface $priorityHandler = null)
    {
        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        if (null === $pheanstalkInterface) {
            $pheanstalkInterface = new Pheanstalk(static::DEFAULT_HOST);
        }

        $this->pheanstalkInterface = $pheanstalkInterface;
        $this->priorityHandler = $priorityHandler;
    }

    /**
     * @param string $queueName
     * @param mixed  $message
     * @param Priority $priority
     *
     * @return AdapterInterface
     */
    public function addMessage($queueName, $message, Priority $priority = null)
    {

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessages($queueName, $nbMsg = 1, Priority $priority = null)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function deleteMessage($queueName, $message, Priority $priority = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty($queueName, Priority $priority = null)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getNumberMessages($queueName, Priority $priority = null)
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
    public function purgeQueue($queueName, Priority $priority = null)
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