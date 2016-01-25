<?php

namespace ReputationVIP\QueueClient\Adapter;

use ReputationVIP\QueueClient\QueueClientInterface;

class AbstractAdapter
{

    /**
     * @param string $queueName
     * @param array  $messages
     * @param string $priority
     *
     * @return QueueClientInterface
     */
    public function addMessages($queueName, $messages, $priority = null)
    {
        foreach ($messages as $message) {
            $this->addMessage($queueName, $message, $priority);
        }

        return $this;
    }

    /**
     * @param string $queueName
     * @param mixed  $message
     * @param string $priority
     *
     * @return AdapterInterface
     */
    public function addMessage($queueName, $message, $priority = null)
    {
        return $this;
    }

}
