<?php

namespace ReputationVIP\QueueClient\tests\units\PriorityHandler;

use mageekguy\atoum;
use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;

class StandardPriorityHandler extends atoum\test
{
    public function testStandardPriorityHandlerAddWithAddPriorityWithLevelAlreadyExists()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->add(new Priority('testPriorityTwo', 100));
        });
    }

    public function testStandardPriorityHandlerAddWithAddPriorityWithNameAlreadyExists()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->add(new Priority('testPriority', 200));
        });
    }

    public function testStandardPriorityHandlerAdd()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->add(new Priority('testPriority', 100)))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler), 100 => new Priority('testPriority', 100, $standardPriorityHandler)]);
    }

    public function testStandardPriorityHandlerRemoveWithNoPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->remove(new Priority('testPriority', 100));
        });
    }

    public function testStandardPriorityHandlerRemoveWithDefaultValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->setDefault(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->remove(new Priority('testPriority', 100)))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler)]);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault()->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerRemove()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->remove(new Priority('testPriority', 100)))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler)]);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault()->getName())->isEqualTo('');
    }

    public function testStandardPriorityHandlerClear()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->clear())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler)]);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault()->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerHas()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has('testPriority'))->isTrue();
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has($standardPriorityHandler->getDefault()->getName()))->isTrue();
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has('wrongPriority'))->isFalse();
    }

    public function testStandardPriorityHandlerGetPriorityByLevelWithWrongLevel()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getPriorityByLevel(100);
        });
    }

    public function testStandardPriorityHandlerGetPriorityByLevel()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getPriorityByLevel(200)->getName())->isIdenticalTo('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getPriorityByLevel(100)->getName())->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetPriorityByNameWithWrongLevel()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getPriorityByName('WrongPriorityName');
        });
    }

    public function testStandardPriorityHandlerGetPriorityByName()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getPriorityByName('testPriorityTwo')->getName())->isIdenticalTo('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getPriorityByName('testPriority')->getName())->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetDefaultWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault()->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerSetDefaultWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->setDefault(new Priority('testPriority', 100));
        });
    }

    public function testStandardPriorityHandlerSetDefaultWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->setDefault(new Priority('wrongPriority', 500));
        });
    }

    public function testStandardPriorityHandlerSetDefault()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->setDefault(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->setDefault(new Priority('testPriority', 100)))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault()->getName())->isEqualTo('testPriority');
    }

    public function testStandardPriorityHandlerGetHighestWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getHighest()->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetHighest()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $standardPriorityHandler->add(new Priority('testPriorityLow', 500));
        $standardPriorityHandler->add(new Priority('testPriorityHigh', 100));
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([100 => new Priority('testPriorityHigh', 100, $standardPriorityHandler), 500 => new Priority('testPriorityLow', 500, $standardPriorityHandler)]);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getHighest()->getName())->isIdenticalTo('testPriorityHigh');
    }

    public function testStandardPriorityHandlerGetLowestWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getLowest()->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetLowest()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriorityHigh', 100));
        $standardPriorityHandler->add(new Priority('testPriorityLow', 200));
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler), 100 => new Priority('testPriorityHigh', 100, $standardPriorityHandler), 200 => new Priority('testPriorityLow', 200, $standardPriorityHandler)]);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getLowest()->getName())->isIdenticalTo('testPriorityLow');
    }

    public function testStandardPriorityHandlerGetBeforeWithFirstValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriorityLow', 300));
        $standardPriorityHandler->add(new Priority('testPriorityHigh', 100));
        $standardPriorityHandler->add(new Priority('testPriorityMid', 200));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore(new Priority('testPriorityMid', 200))->getName())->isIdenticalTo('testPriorityHigh');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore(new Priority('testPriorityHigh', 100))->getName())->isIdenticalTo('');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore(new Priority('testPriorityLow', 300))->getName())->isIdenticalTo('testPriorityMid');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore(new Priority('', 0))->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetBeforeWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getBefore(new Priority('wrongPriority', 100));
        });
    }

    public function testStandardPriorityHandlerGetBefore()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore(new Priority('testPriority', 100))->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetAfterWithFirstValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter(new Priority('', 0))->getName())->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetAfterWithOneValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter(new Priority('', 0))->getName())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetAfterWithLastValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter(new Priority('testPriority', 100))->getName())->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetAfterWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getAfter(new Priority('wrongPriority', 500));
        });
    }

    public function testStandardPriorityHandlerGetAfter()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter(new Priority('testPriority', 100))->getName())->isIdenticalTo('testPriorityTwo');
    }

    public function testStandardPriorityHandlerGetAll()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isEqualTo([0 => new Priority('', 0, $standardPriorityHandler), 100 => new Priority('testPriority', 100, $standardPriorityHandler), 200 => new Priority('testPriorityTwo', 200, $standardPriorityHandler)]);
    }

    public function testStandardPriorityHandlerCount()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove(new Priority('', 0));
        $this->given($standardPriorityHandler)
            ->integer($standardPriorityHandler->count())->isEqualTo(0);
        $standardPriorityHandler->add(new Priority('testPriority', 100));
        $standardPriorityHandler->add(new Priority('testPriorityTwo', 200));
        $this->given($standardPriorityHandler)
            ->integer($standardPriorityHandler->count())->isEqualTo(2);
    }
}