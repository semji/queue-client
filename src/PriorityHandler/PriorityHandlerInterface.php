<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

interface PriorityHandlerInterface
{
    /**
     * @param string $name
     * @return PriorityHandlerInterface
     */
    public function add($name);

    /**
     * @param string $name
     * @return PriorityHandlerInterface
     */
    public function remove($name);

    /**
     * @param string $addName
     * @param string $beforeName
     * @return PriorityHandlerInterface
     */
    public function addBefore($addName, $beforeName);

    /**
     * @param string $beforeName
     * @return PriorityHandlerInterface
     */
    public function removeBefore($beforeName);

    /**
     * @param string $addName
     * @param string $afterName
     * @return PriorityHandlerInterface
     */
    public function addAfter($addName, $afterName);

    /**
     * @param string $afterName
     * @return PriorityHandlerInterface
     */
    public function removeAfter($afterName);

    /**
     * @return PriorityHandlerInterface
     */
    public function clear();

    /**
     * @param $name
     * @return boolean
     */
    public function has($name);

    /**
     * @param int $index
     * @return string
     */
    public function getName($index);

    /**
     * @return string
     */
    public function getDefault();

    /**
     * @param string $newDefault
     * @return PriorityHandlerInterface
     */
    public function setDefault($newDefault);

    /**
     * @return string
     */
    public function getHighest();

    /**
     * @return string
     */
    public function getLowest();

    /**
     * @param string $beforeName
     * @return string
     */
    public function getBefore($beforeName);

    /**
     * @param string $afterName
     * @return string
     */
    public function getAfter($afterName);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @return int
     */
    public function count();
}
