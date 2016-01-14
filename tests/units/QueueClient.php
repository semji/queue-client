<?php

namespace ReputationVIP\QueueClient\tests\units;

use mageekguy\atoum;
use ReputationVIP\QueueClient\Adapter\MemoryAdapter;

class QueueClient extends atoum\test
{
    public function testQueueClient__construct()
    {
        $this->testedClass->implements('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientAddMessageWithAlias(MemoryAdapter $adapter)
    {
        $this
            ->given($this->newTestedInstance($adapter))
            ->when(
                $this->testedInstance->createQueue('testQueueOne'),
                $this->testedInstance->createQueue('testQueueTwo'),
                $this->testedInstance->addAlias('testQueueOne', 'queueAlias')
            )
            ->then
                ->object($this->testedInstance->addMessage('queueAlias', 'testMessage'))->isTestedInstance()
            ->when($this->testedInstance->addAlias('testQueueTwo', 'queueAlias'))
            ->then
                ->object($this->testedInstance->addMessage('queueAlias', 'testMessage'))->isTestedInstance()
        ;
    }

    public function testQueueClientAddMessage()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->addMessage('testQueue', 'testMessage'))->isTestedInstance();
    }

    public function testQueueClientAddMessages()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->addMessages('testQueue', ['testMessageOne', 'testMessageTwo', 'testMessageThree']))->isTestedInstance();
    }

    public function testQueueClientGetMessagesWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->exception(function() use($queueClient) {
            $queueClient->getMessages('queueAlias');
        });
    }

    public function testQueueClientGetMessages()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->given($queueClient)
            ->array($queueClient->getMessages('testQueue'));
    }

    public function testQueueClientDeleteMessageWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->exception(function() use($queueClient) {
            $queueClient->deleteMessage('queueAlias', ['testMessage']);
        });
    }

    public function testQueueClientDeleteMessage()
    {
        $queueClient = $this->newTestedInstance();

        $queueClient->createQueue('testQueue');
        $this->object($queueClient->deleteMessage('testQueue', 'testMessage'))->isTestedInstance();
    }

    public function testQueueClientDeleteMessages()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->deleteMessages('testQueue', ['testMessageOne', 'testMessageTwo', 'testMessageThree']))->isTestedInstance();
    }

    public function testQueueClientIsEmptyWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->exception(function() use($queueClient) {
            $queueClient->isEmpty('queueAlias');
        });
    }

    public function testQueueClientIsEmpty()
    {
        $queueClient = $this->newTestedInstance();

        $this->given($queueClient->createQueue('testQueue'))
            ->boolean($queueClient->isEmpty('testQueue'));
    }

    public function testQueueClientGetNumberMessageWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->exception(function() use($queueClient) {
            $queueClient->getNumberMessages('queueAlias');
        });
    }

    public function testQueueClientNumberMessage()
    {
        $queueClient = $this->newTestedInstance();

        $this->given($queueClient->createQueue('testQueue'))
            ->integer($queueClient->getNumberMessages('testQueue'))->isZero();
    }

    public function testQueueClientDeleteQueueWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $queueClient->addAlias('testQueue', 'queueAliasOne');
        $queueClient->addAlias('testQueue', 'queueAliasTwo');
        $this->object($queueClient->deleteQueue('testQueue'))->isTestedInstance();
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isEmpty();
    }

    public function testQueueClientDeleteQueue()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->deleteQueue('testQueue'))->isTestedInstance();
    }

    public function testQueueClientCreateQueue()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->createQueue('testQueue'))->isTestedInstance();
    }

    public function testQueueClientRenameQueueWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $queueClient->addAlias('testQueue', 'queueAliasOne');
        $queueClient->addAlias('testQueue', 'queueAliasTwo');
        $this->object($queueClient->renameQueue('testQueue', 'testRenameQueue'))->isTestedInstance();
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAliasOne' => ['testRenameQueue'], 'queueAliasTwo' => ['testRenameQueue']]);
    }

    public function testQueueClientRenameQueue()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->renameQueue('testQueue', 'testRenameQueue'))->isTestedInstance();
    }

    public function testQueueClientPurgeQueueWithAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->exception(function() use($queueClient) {
            $queueClient->purgeQueue('queueAlias');
        });
    }

    public function testQueueClientPurgeQueue()
    {
        $queueClient = $this->newTestedInstance();

        $queueClient->createQueue('testQueue');
        $this->object($queueClient->purgeQueue('testQueue'))->isTestedInstance();
    }

    public function testQueueClientListQueue()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $queueClient->createQueue('testRegexQueue');
        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testRegexQueueTwo');
        $queueClient->createQueue('testQueueTwo');
        $this->given($queueClient)
            ->array($queueClient->listQueues())->isIdenticalTo(['testQueue', 'testRegexQueue', 'testQueueOne', 'testRegexQueueTwo', 'testQueueTwo']);
        $this->given($queueClient)
            ->array($queueClient->listQueues('/.*Regex.*/'))->isIdenticalTo(['testRegexQueue', 'testRegexQueueTwo']);
    }

    public function testQueueClientAddAliasWithEmptyAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('testQueue', '');
        });
    }

    public function testQueueClientAddAliasWithEmptyQueueName()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('', 'queueAlias');
        });
    }

    public function testQueueClientAddAliasOnUndefinedQueue()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('testQueue', 'queueAlias');
        });
    }

    public function testQueueClientAddAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $this->object($queueClient->addAlias('testQueueOne', 'queueAlias'))->isTestedInstance();
        $this->object($queueClient->addAlias('testQueueTwo', 'queueAlias'))->isTestedInstance();
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAlias' => ['testQueueOne', 'testQueueTwo']]);
    }

    public function testQueueClientRemoveAliasWithUndefinedAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $this->exception(function() use($queueClient) {
            $queueClient->RemoveAlias('queueAlias');
        });
    }

    public function testQueueClientRemoveAlias()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAliasOne');
        $queueClient->addAlias('testQueueTwo', 'queueAliasTwo');
        $this->object($queueClient->removeAlias('queueAliasOne'))->isTestedInstance();
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAliasTwo' => ['testQueueTwo']]);
    }

    public function testQueueClientGetAliases()
    {
        $queueClient = $this->newTestedInstance(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAliasOne');
        $queueClient->addAlias('testQueueTwo', 'queueAliasOne');
        $queueClient->addAlias('testQueueTwo', 'queueAliasTwo');
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAliasOne' => ['testQueueOne', 'testQueueTwo'], 'queueAliasTwo' => ['testQueueTwo']]);
    }

    public function testQueueClientGetPriorityHandler()
    {
        $queueClient = $this->newTestedInstance();

        $this->object($queueClient->getPriorityHandler())->isInstanceOf('ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
