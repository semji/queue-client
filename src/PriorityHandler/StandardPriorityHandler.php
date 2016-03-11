<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\Exception\PriorityLevelException;

class StandardPriorityHandler implements PriorityHandlerInterface
{
    /**
     * @var int
     */
    protected $defaultIndex = 0;

    /**
     * @var Priority[]
     */
    protected $priorities = [];


    public function __construct()
    {
        $this->add(new Priority('', 0));
    }

    /**
     * @inheritdoc
     *
     * @throws PriorityLevelException
     */
    public function add(Priority $priority)
    {
        $newName = $priority->getName();
        $alreadyAdded = false;

        foreach ($this->priorities as $checkPriority) {
            if ($checkPriority->getName() === $newName) {
                $alreadyAdded = true;
                break;
            }
        }

        if ($alreadyAdded) {
            throw new PriorityLevelException('Level name ' . $priority->getName() . ' already exist.');
        }
        if (isset($this->priorities[$priority->getLevel()])) {
            throw new PriorityLevelException('Level ' . $priority->getLevel() . ' already exist.');
        }

        $priority->setPriorityHandler($this);
        $this->priorities[$priority->getLevel()] = $priority;
        ksort($this->priorities);
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws PriorityLevelException
     */
    public function remove(Priority $priority)
    {
        if (!isset($this->priorities[$priority->getLevel()])) {
            throw new PriorityLevelException('Level ' . $priority->getLevel() . ' doesn\'t exist.');
        }
        $default = $this->getDefault();
        unset($this->priorities[$priority->getLevel()]);
        if ($priority->getLevel() === $default->getLevel()) {
            $this->defaultIndex = 0;
        } else {
            $this->setDefault($default);
        }
        ksort($this->priorities);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->priorities = [];
        $priority = new Priority('', 0);
        $this->add($priority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        $has = false;

        foreach ($this->priorities as $priority) {
            if ($priority->getName() === $name) {
                $has = true;
                break;
            }
        }

        return $has;
    }

    /**
     * @inheritdoc
     */
    public function getDefault()
    {
        if (empty($this->priorities)) {
            return new Priority('', 0);
        }
        return $this->priorities[$this->defaultIndex];
    }

    /**
     * @inheritdoc
     *
     * @throws PriorityLevelException
     */
    public function setDefault(Priority $priority)
    {
        if (!isset($this->priorities[$priority->getLevel()])) {
            throw new PriorityLevelException('Level ' . $priority->getLevel() . ' doesn\'t exist.');
        }

        $this->defaultIndex = $priority->getLevel();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriorityByName($name)
    {
        foreach ($this->priorities as $priority) {
            if ($priority->getName() === $name) {
                return $priority;
            }
        }

        throw new \InvalidArgumentException('Level name' . $name . ' doesn\'t exist.');
    }

    /**
     * @inheritdoc
     */
    public function getPriorityByLevel($level)
    {
        if (isset($this->priorities[$level])) {
            return $this->priorities[$level];
        }

        throw new \InvalidArgumentException('Level ' . $level . ' doesn\'t exist.');
    }

    /**
     * @inheritdoc
     */
    public function getHighest()
    {
        foreach ($this->priorities as $priority) {
            return $priority;
        }
        return new Priority('', 0);
    }

    /**
     * @inheritdoc
     */
    public function getLowest()
    {
        if (empty($this->priorities)) {
            return new Priority('', 0);
        }

        $priorityNames = array_values($this->priorities);
        return $priorityNames[count($priorityNames) - 1];
    }

    /**
     * @inheritdoc
     *
     * @throws PriorityLevelException
     */
    public function getBefore(Priority $priority)
    {
        /** @var Priority $searchPriority*/
        $searchPriority = reset($this->priorities);

        if ($searchPriority->getLevel() === $priority->getLevel()) {
            return $searchPriority;
        }
        while ($searchPriority = next($this->priorities)) {
            if ($searchPriority->getLevel() === $priority->getLevel()) {
                $prevPriority = prev($this->priorities);
                return $prevPriority;
            }
        }

        throw new PriorityLevelException('Level ' . $priority->getLevel() . ' doesn\'t exist.');
    }

    /**
     * @inheritdoc
     *
     * @throws PriorityLevelException
     */
    public function getAfter(Priority $priority)
    {
        /** @var Priority $searchPriority*/
        $searchPriority = reset($this->priorities);

        if ($searchPriority->getLevel() === $priority->getLevel()) {
            $nextPriority = next($this->priorities);
            if (false === $nextPriority) {
                return $searchPriority;
            }
            return $nextPriority;
        }
        while ($searchPriority = next($this->priorities)) {
            if ($searchPriority->getLevel() === $priority->getLevel()) {
                $nextPriority = next($this->priorities);
                if (false === $nextPriority) {
                    return $searchPriority;
                }
                return $nextPriority;
            }
        }

        throw new PriorityLevelException('Level ' . $priority->getLevel() . ' doesn\'t exist.');
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return $this->priorities;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->priorities);
    }
}
