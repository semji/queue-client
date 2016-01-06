<?php

namespace ReputationVIP\QueueClient\tests\units;

require_once __DIR__ . '/../../vendor/autoload.php';

use mageekguy\atoum;
use ReputationVIP\QueueClient\Adapter\MemoryAdapter;

class QueueClient extends atoum\test
{
    public function testQueueClient__construct()
    {
        $this->class(new \ReputationVIP\QueueClient\QueueClient())->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientAddMessageWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAlias');
        $this->class($queueClient->addMessage('queueAlias', 'testMessage'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $queueClient->addAlias('testQueueTwo', 'queueAlias');
        $this->class($queueClient->addMessage('queueAlias', 'testMessage'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientAddMessage()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->addMessage('testQueue', 'testMessage'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientAddMessages()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->addMessages('testQueue', ['testMessageOne', 'testMessageTwo', 'testMessageThree']))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientGetMessagesWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->given($queueClient)
            ->array($queueClient->getMessages('testQueue'));
    }

    public function testQueueClientDeleteMessageWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $queueClient->createQueue('testQueue');
        $this->class($queueClient->deleteMessage('testQueue', 'testMessage'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientDeleteMessages()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->deleteMessages('testQueue', ['testMessageOne', 'testMessageTwo', 'testMessageThree']))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientIsEmptyWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $queueClient->createQueue('testQueue');
        $this->given($queueClient)
            ->boolean($queueClient->isEmpty('testQueue'));
    }

    public function testQueueClientGetNumberMessageWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $queueClient->createQueue('testQueue');
        $this->given($queueClient)
            ->integer($queueClient->getNumberMessages('testQueue'));
    }

    public function testQueueClientDeleteQueueWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $queueClient->addAlias('testQueue', 'queueAliasOne');
        $queueClient->addAlias('testQueue', 'queueAliasTwo');
        $this->class($queueClient->deleteQueue('testQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isEmpty();
    }

    public function testQueueClientDeleteQueue()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->deleteQueue('testQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientCreateQueue()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->createQueue('testQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientRenameQueueWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $queueClient->addAlias('testQueue', 'queueAliasOne');
        $queueClient->addAlias('testQueue', 'queueAliasTwo');
        $this->class($queueClient->renameQueue('testQueue', 'testRenameQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAliasOne' => ['testRenameQueue'], 'queueAliasTwo' => ['testRenameQueue']]);
    }

    public function testQueueClientRenameQueue()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->renameQueue('testQueue', 'testRenameQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientPurgeQueueWithAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $queueClient->createQueue('testQueue');
        $this->class($queueClient->purgeQueue('testQueue'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
    }

    public function testQueueClientListQueue()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('testQueue', '');
        });
    }

    public function testQueueClientAddAliasWithEmptyQueueName()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueue');
        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('', 'queueAlias');
        });
    }

    public function testQueueClientAddAliasOnUndefinedQueue()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $this->exception(function() use($queueClient) {
            $queueClient->addAlias('testQueue', 'queueAlias');
        });
    }

    public function testQueueClientAddAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $this->class($queueClient->addAlias('testQueueOne', 'queueAlias'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $this->class($queueClient->addAlias('testQueueTwo', 'queueAlias'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAlias' => ['testQueueOne', 'testQueueTwo']]);
    }

    public function testQueueClientRemoveAliasWithUndefinedAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $this->exception(function() use($queueClient) {
            $queueClient->RemoveAlias('queueAlias');
        });
    }

    public function testQueueClientRemoveAlias()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

        $queueClient->createQueue('testQueueOne');
        $queueClient->createQueue('testQueueTwo');
        $queueClient->addAlias('testQueueOne', 'queueAliasOne');
        $queueClient->addAlias('testQueueTwo', 'queueAliasTwo');
        $this->class($queueClient->removeAlias('queueAliasOne'))->hasInterface('ReputationVIP\QueueClient\QueueClientInterface');
        $this->given($queueClient)
            ->array($queueClient->getAliases())->isIdenticalTo(['queueAliasTwo' => ['testQueueTwo']]);
    }

    public function testQueueClientGetAliases()
    {
        $queueClient = new \ReputationVIP\QueueClient\QueueClient(new MemoryAdapter());

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
        $queueClient = new \ReputationVIP\QueueClient\QueueClient();

        $this->class($queueClient->getPriorityHandler())->hasInterface('ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
