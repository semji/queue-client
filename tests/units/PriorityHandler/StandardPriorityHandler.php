<?php

namespace ReputationVIP\QueueClient\tests\units\PriorityHandler;

use mageekguy\atoum;

class StandardPriorityHandler extends atoum\test
{
    public function testStandardPriorityHandlerAddWithAddValuesAlreadyExists()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->add('testPriority');
        });
    }

    public function testStandardPriorityHandlerAdd()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->add('testPriority'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['', 'testPriority']);
    }

    public function testStandardPriorityHandlerRemoveWithNoPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->remove('testPriority');
        });
    }

    public function testStandardPriorityHandlerRemoveWithDefaultValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->setDefault('testPriority');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->remove('testPriority'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerRemove()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->remove('testPriority'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerAddBeforeWithAddValuesAlreadyExists()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->addBefore('testPriority', '');
        });
    }

    public function testStandardPriorityHandlerAddBeforeWithNoBeforeValues()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->addBefore('testPriority', 'wrongPriority');
        });
    }

    public function testStandardPriorityHandlerAddBefore()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->addBefore('testPriority', ''))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->addBefore('testPriorityTwo', ''))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['testPriority', 'testPriorityTwo', '']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerRemoveBeforeWithNoBeforeValues()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->removeBefore('testPriority');
        });
    }

    public function testStandardPriorityHandlerRemoveBeforeWithDefaultValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriority', '');
        $standardPriorityHandler->setDefault('testPriority');
        $standardPriorityHandler->addBefore('testPriorityTwo', '');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->removeBefore('testPriorityTwo'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['testPriorityTwo', '']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('testPriorityTwo');
    }

    public function testStandardPriorityHandlerRemoveBefore()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriority', '');
        $standardPriorityHandler->addBefore('testPriorityTwo', '');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->removeBefore('testPriorityTwo'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['testPriorityTwo', '']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerAddAfterWithAddValuesAlreadyExists()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->addAfter('testPriority', '');
        });
    }

    public function testStandardPriorityHandlerAddAfterWithNoBeforeValues()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->addAfter('testPriority', 'wrongPriority');
        });
    }

    public function testStandardPriorityHandlerAddAfter()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->addAfter('testPriority', ''))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->addAfter('testPriorityTwo', ''))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['', 'testPriorityTwo', 'testPriority']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerRemoveAfterWithNoBeforeValues()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->removeAfter('testPriority');
        });
    }

    public function testStandardPriorityHandlerRemoveAfterWithDefaultValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $standardPriorityHandler->setDefault('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->removeAfter('testPriority'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['', 'testPriority']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerRemoveAfter()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->removeAfter(''))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['', 'testPriorityTwo']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerClear()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->clear())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerHas()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has('testPriority'))->isTrue();
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has($standardPriorityHandler->getDefault()))->isTrue();
        $this->given($standardPriorityHandler)
            ->boolean($standardPriorityHandler->has('wrongPriority'))->isFalse();
    }

    public function testStandardPriorityHandlerGetNameWithWrongIndex()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getName(-1);
        });
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getName(42);
        });
    }

    public function testStandardPriorityHandlerGetName()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getName(0))->isIdenticalTo('');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getName(2))->isIdenticalTo('testPriorityTwo');
    }

    public function testStandardPriorityHandlerGetDefaultWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove('');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerSetDefaultWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove('');
        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->setDefault('testPriority');
        });
    }

    public function testStandardPriorityHandlerSetDefaultWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->setDefault('wrongPriority');
        });
    }

    public function testStandardPriorityHandlerSetDefault()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->setDefault('testPriority');
        $this->given($standardPriorityHandler)
            ->class($standardPriorityHandler->setDefault('testPriority'))->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getDefault())->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetHighestWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove('');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getHighest())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetHighest()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriorityHigh', '');
        $standardPriorityHandler->add('testPriorityLow');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['testPriorityHigh', '', 'testPriorityLow']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getHighest())->isIdenticalTo('testPriorityHigh');
    }

    public function testStandardPriorityHandlerGetLowestWithEmptyPriority()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove('');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getLowest())->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetLowest()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriorityHigh', '');
        $standardPriorityHandler->add('testPriorityLow');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['testPriorityHigh', '', 'testPriorityLow']);
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getLowest())->isIdenticalTo('testPriorityLow');
    }

    public function testStandardPriorityHandlerGetBeforeWithFirstValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriority', '');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore('testPriority'))->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetBeforeWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getBefore('wrongPriority');
        });
    }

    public function testStandardPriorityHandlerGetBefore()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getBefore('testPriority'))->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetAfterWithFirstValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter('testPriority'))->isIdenticalTo('testPriority');
    }

    public function testStandardPriorityHandlerGetAfterWithUndefinedValue()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $this->exception(function() use($standardPriorityHandler) {
            $standardPriorityHandler->getAfter('wrongPriority');
        });
    }

    public function testStandardPriorityHandlerGetAfter()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->addBefore('testPriority', '');
        $this->given($standardPriorityHandler)
            ->string($standardPriorityHandler->getAfter('testPriority'))->isIdenticalTo('');
    }

    public function testStandardPriorityHandlerGetAll()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->array($standardPriorityHandler->getAll())->isIdenticalTo(['', 'testPriority', 'testPriorityTwo']);
    }

    public function testStandardPriorityHandlerCount()
    {
        $standardPriorityHandler = new \ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler();

        $standardPriorityHandler->remove('');
        $this->given($standardPriorityHandler)
            ->integer($standardPriorityHandler->count())->isEqualTo(0);
        $standardPriorityHandler->add('testPriority');
        $standardPriorityHandler->add('testPriorityTwo');
        $this->given($standardPriorityHandler)
            ->integer($standardPriorityHandler->count())->isEqualTo(2);
    }
}
