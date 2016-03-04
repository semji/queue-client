<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use mageekguy\atoum;
use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;
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
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->createQueue('');
        });
    }

    public function testMemoryAdapterCreateQueueWithQueueNameSpace()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->createQueue('test Queue');
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
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->given($memoryAdapter)
            ->class($memoryAdapter->renameQueue('testQueue', 'newTestQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterRenameQueueWithEmptyTargetQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->renameQueue('testQueue', '');
        });
    }

    public function testMemoryAdapterRenameQueueWithTargetQueueExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->createQueue('newTestQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->renameQueue('testQueue', 'newTestQueue');
        });
    }

    public function testMemoryAdapterRenameQueueWithEmptySourceQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->renameQueue('', 'newTestQueue');
        });
    }

    public function testMemoryAdapterRenameQueueWithSourceQueueDoesNotExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->renameQueue('testQueue', 'newTestQueue');
        });
    }

    public function testMemoryAdapterPurgeQueue()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->given($memoryAdapter)
            ->class($memoryAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterPurgeQueueWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->purgeQueue('');
        });
    }

    public function testMemoryAdapterPurgeQueueWithQueueDoesNotExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->purgeQueue('testQueue');
        });
    }

    public function testMemoryAdapterPurgeQueueWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->purgeQueue('testQueue', new Priority('BAD_PRIORITY', 100));
        });
    }

    public function testMemoryAdapterAddMessage()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->given($memoryAdapter)
            ->class($memoryAdapter->addMessage('testQueue', 'test message'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterAddMessageWithDelay()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter = $memoryAdapter->addMessage('testQueue', 'test message', null, 1);
        sleep(1);
        $this->given($memoryAdapter)
            ->class($memoryAdapter)->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterAddMessageWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->addMessage('', 'test message');
        });
    }

    public function testMemoryAdapterAddMessageWithNoQueue()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->addMessage('testQueue', 'test message');
        });
    }

    public function testMemoryAdapterAddMessageWithEmptyMessage()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->addMessage('testQueue', '');
        });
    }

    public function testMemoryAdapterAddMessageWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->AddMessage('testQueue', 'test message', new Priority('BAD_PRIORITY', 100));
        });
    }

    public function testMemoryAdapterIsEmpty()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->given($memoryAdapter)
            ->boolean($memoryAdapter->isEmpty('testQueue'))->isTrue();
        $memoryAdapter->addMessage('testQueue', 'test message');
        $this->given($memoryAdapter)
            ->boolean($memoryAdapter->isEmpty('testQueue'))->isFalse();
    }

    public function testMemoryAdapterIsEmptyWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->isEmpty('');
        });
    }

    public function testMemoryAdapterIsEmptyWithQueueDoesNotExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->isEmpty('testQueue');
        });
    }

    public function testMemoryAdapterIsEmptyWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->isEmpty('testQueue', new Priority('BAD_PRIORITY', 100));
        });
    }

    /**
     * @throws \Exception
     */
    public function testMemoryAdapterListQueues()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueueOne');
        $memoryAdapter->createQueue('testQueueTwo');
        $memoryAdapter->createQueue('testQueueThree');
        $this->given($memoryAdapter)
            ->array($memoryAdapter->listQueues())->containsValues(['testQueueOne', 'testQueueTwo', 'testQueueThree']);
    }

    public function testMemoryAdapterListQueuesEmpty()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->given($memoryAdapter)
            ->array($memoryAdapter->listQueues())->isEmpty();
    }

    /**
     * @throws \Exception
     */
    public function testMemoryAdapterListQueuesWithPrefix()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueueOne');
        $memoryAdapter->createQueue('prefixTestQueueOne');
        $memoryAdapter->createQueue('testQueueTwo');
        $memoryAdapter->createQueue('prefixTestQueueTwo');
        $memoryAdapter->createQueue('testQueueThree');
        $this->given($memoryAdapter)
            ->array($memoryAdapter->listQueues('prefix'))->containsValues(['prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testMemoryAdapterGetNumberMessagesWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getNumberMessages('');
        });
    }

    public function testMemoryAdapterGetNumberMessagesWithQueueDoesNotExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getNumberMessages('testQueue');
        });
    }

    public function testMemoryAdapterGetNumberMessagesWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getNumberMessages('testQueue', new Priority('BAD_PRIORITY', 100));
        });
    }

    public function testMemoryAdapterGetNumberMessages()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $memoryAdapter->createQueue('testQueue');
        $this->given($memoryAdapter)
            ->integer($memoryAdapter->getNumberMessages('testQueue'))->isEqualTo(0);
        $memoryAdapter->addMessage('testQueue', 'test message');
        $memoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($memoryAdapter)
            ->integer($memoryAdapter->getNumberMessages('testQueue'))->isEqualTo(4);
    }

    public function testMemoryAdapterDeleteMessageWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->deleteMessage('', []);
        });
    }

    public function testMemoryAdapterDeleteMessageWithEmptyMessage()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->deleteMessage('testQueue', []);
        });
    }

    public function testMemoryAdapterDeleteMessageWithNoMessageId()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $message = $memoryAdapter->getMessages('testQueue');
        $message = $message[0];
        unset($message['id']);
        $this->exception(function() use($memoryAdapter, $message) {
            $memoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithNoMessagePriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $message = $memoryAdapter->getMessages('testQueue');
        $message = $message[0];
        unset($message['priority']);
        $this->exception(function() use($memoryAdapter, $message) {
            $memoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithBadMessage()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $message = 'test message';
        $this->exception(function() use($memoryAdapter, $message) {
            $memoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessageWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $message = $memoryAdapter->getMessages('testQueue');
        $message = $message[0];
        $message['priority'] = 'BAD_PRIORITY';
        $this->exception(function() use($memoryAdapter, $message) {
            $memoryAdapter->deleteMessage('testQueue', $message);
        });
    }

    public function testMemoryAdapterDeleteMessage()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $message = $memoryAdapter->getMessages('testQueue');
        $message = $message[0];
        $this->given($memoryAdapter)
            ->class($memoryAdapter->deleteMessage('testQueue', $message))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testMemoryAdapterGetMessagesWithEmptyQueueName()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getMessages('');
        });
    }

    public function testMemoryAdapterGetMessagesWithQueueDoesNotExists()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getMessages('testQueue');
        });
    }

    public function testMemoryAdapterGetMessagesWithBadPriority()
    {
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter();

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $this->exception(function() use($memoryAdapter) {
            $memoryAdapter->getMessages('testQueue', 1, new Priority('BAD_PRIORITY', 100));
        });
    }

    public function testMemoryAdapterGetMessages()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $memoryAdapter->createQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $memoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($memoryAdapter)
            ->array($memoryAdapter->getMessages('testQueue', 4))->hasSize(4);
        $memoryAdapter->purgeQueue('testQueue');
        $memoryAdapter->addMessage('testQueue', 'test message');
        $memoryAdapter->addMessage('testQueue', 'test message high one', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message high two', $priorityHandler->getHighest());
        $memoryAdapter->addMessage('testQueue', 'test message low', $priorityHandler->getLowest());
        $this->given($memoryAdapter)
            ->array($memoryAdapter->getMessages('testQueue', 6))->hasSize(4);
    }

    public function testMemoryAdapterGetPriorityHandler()
    {
        $priorityHandler = new ThreeLevelPriorityHandler();
        $memoryAdapter = new \ReputationVIP\QueueClient\Adapter\MemoryAdapter($priorityHandler);

        $this->given($memoryAdapter)
            ->class($memoryAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
