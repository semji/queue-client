<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\QueueClientInterface;

class AbstractAdapter
{

    /**
     * @param string $queueName
     * @param array  $messages
     * @param Priority $priority
     *
     * @return QueueClientInterface
     */
    public function addMessages($queueName, $messages, Priority $priority = null)
    {
        foreach ($messages as $message) {
            $this->addMessage($queueName, $message, $priority);
        }

        return $this;
    }

    /**
     * @param string $queueName
     * @param mixed  $message
     * @param Priority $priority
     * @param int $delaySeconds
     *
     * @return AdapterInterface
     */
    public function addMessage($queueName, $message, Priority $priority = null, $delaySeconds = 0)
    {
        return $this;
    }

}
