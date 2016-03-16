<?php

namespace ReputationVIP\QueueClient\PriorityHandler\Priority;

use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;

class Priority
{
    /** @var string $name */
    private $name;

    /** @var integer $level */
    private $level;

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    public function __construct($name, $level, PriorityHandlerInterface $priorityHandler = null)
    {
        $this->name = $name;
        $this->level = $level;
        $this->priorityHandler = $priorityHandler;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * @return PriorityHandlerInterface
     */
    public function getPriorityHandler() {
        return $this->priorityHandler;
    }

    /**
     * @param PriorityHandlerInterface $priorityHandler
     * @return $this
     */
    public function setPriorityHandler(PriorityHandlerInterface $priorityHandler) {
        $this->priorityHandler = $priorityHandler;

        return $this;
    }

    /**
     * @return Priority
     */
    public function next()
    {
        if (null === $this->priorityHandler) {
            return $this;
        }

        return $this->priorityHandler->getAfter($this);
    }

    /**
     * @return Priority
     */
    public function prev()
    {
        if (null === $this->priorityHandler) {
            return $this;
        }

        return $this->priorityHandler->getBefore($this);
    }
}