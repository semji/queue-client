<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use mageekguy\atoum;

class NullAdapter extends atoum\test
{

    public function testNullAdapterAddMessage()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->addMessage('testQueue', 'test Message one'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterAddMessages()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->addMessages('testQueue', ['test Message one']))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterGetMessages()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->array($nullAdapter->getMessages('testQueue'))->isEmpty();
    }

    public function testNullAdapterDeleteMessage()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->deleteMessage('testQueue', []))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterIsEmpty()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->boolean($nullAdapter->isEmpty('testQueue'))->isTrue();
    }

    public function testNullAdapterGetNumberMessages()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->integer($nullAdapter->getNumberMessages('testQueue'))->isEqualTo(0);
    }

    public function testNullAdapterDeleteQueue()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->deleteQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterCreateQueue()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->createQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterRenameQueue()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->renameQueue('testQueue', 'newTestQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterPurgeQueue()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterListQueues()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->array($nullAdapter->listQueues())->isEmpty();
    }

    public function testNullAdapterGetPriorityHandler()
    {
        $nullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($nullAdapter)->class($nullAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
