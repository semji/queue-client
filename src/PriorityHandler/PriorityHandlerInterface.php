<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;

interface PriorityHandlerInterface
{
    /**
     * @param Priority $priority
     * @return PriorityHandlerInterface
     */
    public function add(Priority $priority);

    /**
     * @param Priority $priority
     * @return PriorityHandlerInterface
     */
    public function remove(Priority $priority);

    /**
     * @return PriorityHandlerInterface
     */
    public function clear();

    /**
     * @param string $name
     * @return boolean
     */
    public function has($name);

    /**
     * @return Priority
     */
    public function getDefault();

    /**
     * @param Priority $priority
     * @return PriorityHandlerInterface
     */
    public function setDefault(Priority $priority);

    /**
     * @param string $name
     * @return Priority
     */
    public function getPriorityByName($name);

    /**
     * @param integer $level
     * @return Priority
     */
    public function getPriorityByLevel($level);

    /**
     * @return Priority
     */
    public function getHighest();

    /**
     * @return Priority
     */
    public function getLowest();

    /**
     * @param Priority $priority
     * @return Priority
     */
    public function getBefore(Priority $priority);

    /**
     * @param Priority $priority
     * @return Priority
     */
    public function getAfter(Priority $priority);

    /**
     * @return Priority[]
     */
    public function getAll();

    /**
     * @return int
     */
    public function count();
}
