<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use mageekguy\atoum;

class NullAdapter extends atoum\test
{

    public function testNullAdapterAddMessage()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->addMessage('testQueue', 'test Message one'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterAddMessages()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->addMessages('testQueue', ['test Message one']))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterGetMessages()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->array($NullAdapter->getMessages('testQueue'))->isEmpty();
    }

    public function testNullAdapterDeleteMessage()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->deleteMessage('testQueue', []))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterIsEmpty()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->boolean($NullAdapter->isEmpty('testQueue'))->isTrue();
    }

    public function testNullAdapterGetNumberMessages()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->integer($NullAdapter->getNumberMessages('testQueue'))->isEqualTo(0);
    }

    public function testNullAdapterDeleteQueue()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->deleteQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterCreateQueue()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->createQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterRenameQueue()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->renameQueue('testQueue', 'newTestQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterPurgeQueue()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testNullAdapterListQueues()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->array($NullAdapter->listQueues())->isEmpty();
    }

    public function testNullAdapterGetPriorityHandler()
    {
        $NullAdapter = new \ReputationVIP\QueueClient\Adapter\NullAdapter();
        $this->given($NullAdapter)->class($NullAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
