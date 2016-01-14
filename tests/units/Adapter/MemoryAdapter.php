<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use mageekguy\atoum;
use ReputationVIP\QueueClient\PriorityHandler\ThreeLevelPriorityHandler;

class MemoryAdapter extends atoum\test
{
    public function testMemoryAdapterCreateQueue()
    {
        $this->given($this->newTestedInstance)
            ->object($this->testedInstance->createQueue('testQueue'))->isTestedInstance()
        ;
    }

    public function testMemoryAdapterCreateQueueWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->createQueue('');
        });
    }

    public function testMemoryAdapterCreateQueueWithQueueNameSpace()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->createQueue('test Queue');
        });
    }

    public function testMemoryAdapterCreateQueueWithQueueExists()
    {
        $this
            ->given($this->newTestedInstance)
            ->when($this->testedInstance->createQueue('testQueue'))
            ->then
                ->exception(function() {
                    $this->testedInstance->createQueue('testQueue');
                })
        ;
    }

    public function testMemoryAdapterDeleteQueue()
    {
        $this
            ->given($this->newTestedInstance)
            ->when($this->testedInstance->createQueue('testQueue'))
            ->then
                ->object($this->testedInstance->deleteQueue('testQueue'))->isTestedInstance()
        ;
    }

    public function testMemoryAdapterDeleteQueueWithEmptyQueueName()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function() {
                    $this->testedInstance->deleteQueue('');
                })
        ;
    }

    public function testMemoryAdapterDeleteQueueWithQueueDoesNotExists()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->exception(function() {
                    $this->testedInstance->deleteQueue('testQueue');
                })
        ;
    }

    public function testMemoryAdapterRenameQueue()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->given($MemoryAdapter)
            ->class($MemoryAdapter->renameQueue('testQueue', 'newTestQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterRenameQueueWithEmptyTargetQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->renameQueue('testQueue', '');
        });
    }

    public function testMemoryAdapterRenameQueueWithTargetQueueExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->createQueue('newTestQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->renameQueue('testQueue', 'newTestQueue');
        });
    }

    public function testMemoryAdapterRenameQueueWithEmptySourceQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->renameQueue('', 'newTestQueue');
        });
    }

    public function testMemoryAdapterRenameQueueWithSourceQueueDoesNotExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->renameQueue('testQueue', 'newTestQueue');
        });
    }

    public function testMemoryAdapterPurgeQueue()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->given($MemoryAdapter)
            ->class($MemoryAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterPurgeQueueWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->purgeQueue('');
        });
    }

    public function testMemoryAdapterPurgeQueueWithQueueDoesNotExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->purgeQueue('testQueue');
        });
    }

    public function testMemoryAdapterPurgeQueueWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->purgeQueue('testQueue', 'BAD_PRIORITY');
        });
    }

    public function testMemoryAdapterAddMessage()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->given($MemoryAdapter)
            ->class($MemoryAdapter->AddMessage('testQueue', 'test message'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterAddMessageWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->AddMessage('', 'test message');
        });
    }

    public function testMemoryAdapterAddMessageWithNoQueue()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->AddMessage('testQueue', 'test message');
        });
    }

    public function testMemoryAdapterAddMessageWithEmptyMessage()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->AddMessage('testQueue', '');
        });
    }

    public function testMemoryAdapterAddMessageWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->AddMessage('testQueue', 'test message', 'BAD_PRIORITY');
        });
    }

    public function testMemoryAdapterIsEmpty()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->given($MemoryAdapter)
            ->boolean($MemoryAdapter->isEmpty('testQueue'))->isTrue();
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $this->given($MemoryAdapter)
            ->boolean($MemoryAdapter->isEmpty('testQueue'))->isFalse();
    }

    public function testMemoryAdapterIsEmptyWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->isEmpty('');
        });
    }

    public function testMemoryAdapterIsEmptyWithQueueDoesNotExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->isEmpty('testQueue');
        });
    }

    public function testMemoryAdapterIsEmptyWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->isEmpty('testQueue', 'BAD_PRIORITY');
        });
    }

    /**
     * @throws \Exception
     */
    public function testMemoryAdapterListQueues()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueueOne');
        $MemoryAdapter->createQueue('testQueueTwo');
        $MemoryAdapter->createQueue('testQueueThree');
        $this->given($MemoryAdapter)
            ->array($MemoryAdapter->listQueues())->containsValues(['testQueueOne', 'testQueueTwo', 'testQueueThree']);
    }

    public function testMemoryAdapterListQueuesEmpty()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->given($MemoryAdapter)
            ->array($MemoryAdapter->listQueues())->isEmpty();
    }

    /**
     * @throws \Exception
     */
    public function testMemoryAdapterListQueuesWithPrefix()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueueOne');
        $MemoryAdapter->createQueue('prefixTestQueueOne');
        $MemoryAdapter->createQueue('testQueueTwo');
        $MemoryAdapter->createQueue('prefixTestQueueTwo');
        $MemoryAdapter->createQueue('testQueueThree');
        $this->given($MemoryAdapter)
            ->array($MemoryAdapter->listQueues('prefix'))->containsValues(['prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testMemoryAdapterGetNumberMessagesWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getNumberMessages('');
        });
    }

    public function testMemoryAdapterGetNumberMessagesWithQueueDoesNotExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getNumberMessages('testQueue');
        });
    }

    public function testMemoryAdapterGetNumberMessagesWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getNumberMessages('testQueue', 'BAD_PRIORITY');
        });
    }

    public function testMemoryAdapterGetNumberMessages()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $MemoryAdapter->createQueue('testQueue');
        $this->given($MemoryAdapter)
            ->integer($MemoryAdapter->getNumberMessages('testQueue'))->isEqualTo(0);
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $MemoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($MemoryAdapter)
            ->integer($MemoryAdapter->getNumberMessages('testQueue'))->isEqualTo(4);
    }

    public function testMemoryAdapterDeleteMessageWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->deleteMessage('', []);
        });
    }

    public function testMemoryAdapterDeleteMessageWithEmptyMessage()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->deleteMessage('testQueue', []);
        });
    }

    public function testMemoryAdapterDeleteMessageWithNoMessageId()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $message = $MemoryAdapter->getMessages('testQueue');
        $message = $message[0];
        unset($message['id']);
        $this->exception(function() use($MemoryAdapter, $message) {
            $MemoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithNoMessagePriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $message = $MemoryAdapter->getMessages('testQueue');
        $message = $message[0];
        unset($message['priority']);
        $this->exception(function() use($MemoryAdapter, $message) {
            $MemoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithBadMessage()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $message = 'test message';
        $this->exception(function() use($MemoryAdapter, $message) {
            $MemoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $message = $MemoryAdapter->getMessages('testQueue');
        $message = $message[0];
        $message['priority'] = 'BAD_PRIORITY';
        $this->exception(function() use($MemoryAdapter, $message) {
            $MemoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessage()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $message = $MemoryAdapter->getMessages('testQueue');
        $message = $message[0];
        $this->given($MemoryAdapter)
            ->class($MemoryAdapter->deleteMessage('testQueue', $message))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterGetMessagesWithEmptyQueueName()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getMessages('');
        });
    }

    public function testMemoryAdapterGetMessagesWithQueueDoesNotExists()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getMessages('testQueue');
        });
    }

    public function testMemoryAdapterGetMessagesWithBadPriority()
    {
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $this->exception(function() use($MemoryAdapter) {
            $MemoryAdapter->getMessages('testQueue', 1, 'BAD_PRIORITY');
        });
    }

    public function testMemoryAdapterGetMessages()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $MemoryAdapter->createQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $MemoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($MemoryAdapter)
            ->array($MemoryAdapter->getMessages('testQueue', 4))->hasSize(4);
        $MemoryAdapter->purgeQueue('testQueue');
        $MemoryAdapter->addMessage('testQueue', 'test message');
        $MemoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $MemoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($MemoryAdapter)
            ->array($MemoryAdapter->getMessages('testQueue', 6))->hasSize(4);
    }

    public function testMemoryAdapterGetPriorityHandler()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $MemoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $this->given($MemoryAdapter)
            ->class($MemoryAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
