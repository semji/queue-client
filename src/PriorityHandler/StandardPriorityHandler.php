<?php

namespace ReputationVIP\QueueClient\PriorityHandler;

use ReputationVIP\QueueClient\Exception\RangeException;
use ReputationVIP\QueueClient\PriorityHandler\Exception\LevelException;

class StandardPriorityHandler implements PriorityHandlerInterface
{
    /**
     * @var int
     */
    protected $defaultIndex = 0;

    /**
     * @var []
     */
    protected $priorities = [''];

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function add($name)
    {
        if (in_array($name, $this->priorities)) {
            throw new LevelException('Level ' . $name . ' already exists.');
        }
        $this->priorities[] = $name;
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function remove($name)
    {
        $key = array_search($name, $this->priorities);
        if (false === $key) {
            throw new LevelException("Level " . $name . " doesn't exist.");
        }
        $default = $this->getDefault();
        unset($this->priorities[$key]);
        $this->priorities = array_values($this->priorities);
        if ($name === $default) {
            $this->defaultIndex = 0;
        } else {
            $this->setDefault($default);
        }
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function addBefore($addName, $beforeName)
    {
        $key = array_search($beforeName, $this->priorities);
        if (false !== $key) {
            if (in_array($addName, $this->priorities)) {
                throw new LevelException('Level ' . $addName . ' already exists.');
            }
            $default = $this->getDefault();
            if (0 === $key) {
                array_unshift($this->priorities, $addName);
            } else {
                $oldPriorities = $this->priorities;
                $this->priorities = array_slice($oldPriorities, 0, $key, true);
                $this->priorities[] = $addName;
                $this->priorities = array_merge($this->priorities, array_slice($oldPriorities, $key, count($oldPriorities) - 1, true));
            }
            $this->setDefault($default);
        } else {
            throw new LevelException("Level " . $beforeName . " doesn't exist.");
        }
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function removeBefore($beforeName)
    {
        $key = array_search($beforeName, $this->priorities);
        if (false !== $key) {
            if (0 !== $key) {
                $default = $this->getDefault();
                $name = $this->priorities[$key - 1];
                unset($this->priorities[$key - 1]);
                $this->priorities = array_values($this->priorities);
                if ($name === $default) {
                    $this->defaultIndex = 0;
                } else {
                    $this->setDefault($default);
                }
            }
        } else {
            throw new LevelException("Level " . $beforeName . " doesn't exist.");
        }
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function addAfter($addName, $afterName)
    {
        $key = array_search($afterName, $this->priorities);
        if (false !== $key) {
            if (in_array($addName, $this->priorities)) {
                throw new LevelException('Level ' . $addName . ' already exists.');
            }
            $default = $this->getDefault();
            if ($key === (count($this->priorities) - 1)) {
                $this->priorities[] = $addName;
            } else {
                $oldPriorities = $this->priorities;
                $this->priorities = array_slice($oldPriorities, 0, $key + 1, true);
                $this->priorities[] = $addName;
                $this->priorities = array_merge($this->priorities, array_slice($oldPriorities, $key + 1, count($oldPriorities) - 1, true));
            }
            $this->setDefault($default);
        } else {
            throw new LevelException("Level " . $afterName . " doesn't exist.");
        }
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function removeAfter($afterName)
    {
        $key = array_search($afterName, $this->priorities);
        if (false !== $key) {
            if ($key !== (count($this->priorities) - 1)) {
                $default = $this->getDefault();
                $name = $this->priorities[$key + 1];
                unset($this->priorities[$key + 1]);
                $this->priorities = array_values($this->priorities);
                if ($name === $default) {
                    $this->defaultIndex = 0;
                } else {
                    $this->setDefault($default);
                }
            }
        } else {
            throw new LevelException("Level " . $afterName . " doesn't exist.");
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->priorities = [''];
        $this->defaultIndex = 0;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function has($name)
    {
        $key = array_search($name, $this->priorities);
        if (false === $key) {
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws RangeException
     */
    public function getName($index)
    {
        if ($index < 0 || $index >= count($this->priorities)) {
            throw new RangeException('Level index out of range.');
        }
        return $this->priorities[$index];
    }

    /**
     * @inheritdoc
     */
    public function getDefault()
    {
        if (empty($this->priorities)) {
            return '';
        }
        return $this->priorities[$this->defaultIndex];
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function setDefault($newDefault)
    {
        if (empty($this->priorities)) {
            $this->defaultIndex = 0;
        }
        $key = array_search($newDefault, $this->priorities);
        if (false !== $key) {
            $this->defaultIndex = $key;
        } else {
            throw new LevelException("Level " . $newDefault . " doesn't exist.");
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHighest()
    {
        if (empty($this->priorities)) {
            return '';
        }
        return $this->priorities[0];
    }

    /**
     * @inheritdoc
     */
    public function getLowest()
    {
        if (empty($this->priorities)) {
            return '';
        }
        return $this->priorities[count($this->priorities) - 1];
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function getBefore($beforeName)
    {
        $key = array_search($beforeName, $this->priorities);

        if (false === $key) {
            throw new LevelException("Level " . $beforeName . " doesn't exist.");
        }

        if (0 === $key) {
            return $this->priorities[0];
        }

        return $this->priorities[$key - 1];
    }

    /**
     * @inheritdoc
     *
     * @throws LevelException
     */
    public function getAfter($afterName)
    {
        $key = array_search($afterName, $this->priorities);

        if (false === $key) {
            throw new LevelException("Level " . $afterName . " doesn't exist.");
        }

        if (count($this->priorities) - 1 === $key) {
            return $this->priorities[count($this->priorities) - 1];
        }

        return $this->priorities[$key + 1];
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
