<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use mageekguy\atoum;
use ReputationVIP\QueueClient\PriorityHandler\ThreeLevelPriorityHandler;

class SQSAdapter extends atoum\test
{
    public function testSQSAdapterAddMessageWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->addMessage('', '');
        });
    }

    public function testSQSAdapterAddMessageWithEmptyMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->addMessage('testQueue', '');
        });
    }

    public function testSQSAdapterAddMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->sendMessage = function () {
        };
        $this->given($SQSAdapter)
            ->class($SQSAdapter->addMessage('testQueue', 'test message'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterGetMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->getMessages('');
        });
    }

    public function testSQSAdapterGetMessagesWithBadMessageNumber()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->getMessages('testQueue', 'BadNumber');
        });

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->getMessages('testQueue', 0);
        });

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->getMessages('testQueue', \ReputationVIP\QueueClient\Adapter\SQSAdapter::MAX_NB_MESSAGES + 1);
        });
    }

    public function testSQSAdapterGetMessagesWithNoMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $this->given($SQSAdapter)
            ->array($SQSAdapter->getMessages('testQueue', 5))->isEmpty();
    }

    public function testSQSAdapterGetMessages()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['Body' => serialize('test message one')], ['Body' => serialize('test message two')]];
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $this->given($SQSAdapter)
            ->array($SQSAdapter->getMessages('testQueue', 6))->hasSize(6);
    }

    public function testSQSAdapterDeleteMessageWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->deleteMessage('', []);
        });
    }

    public function testSQSAdapterDeleteMessageWithEmptyMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->deleteMessage('testQueue', []);
        });
    }

    public function testSQSAdapterDeleteMessageWithBadMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->deleteMessage('testQueue', 'Bad message');
        });
    }

    public function testSQSAdapterDeleteMessageWithNoMessageReceiptHandle()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $this->exception(function() use($SQSAdapter, $priorityHandler) {
            $SQSAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest()]);
        });
    }

    public function testSQSAdapterDeleteMessageWithNoMessagePriority()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->deleteMessage('testQueue', ['ReceiptHandle' => 'testReceiptHandle']);
        });
    }

    public function testSQSAdapterDeleteMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->deleteMessage = function () use($mockQueueUrlModel) {
            return null;
        };
        $this->given($SQSAdapter)
            ->class($SQSAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest(), 'ReceiptHandle' => 'testReceiptHandle']))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterIsEmptyWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->isEmpty('');
        });
    }

    public function testSQSAdapterIsEmptyWithEmptyQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['name' => 'ApproximateNumberOfMessages', 'value' => 0]];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->boolean($SQSAdapter->isEmpty('testQueue'))->IsTrue();
    }

    public function testSQSAdapterIsEmpty()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['ApproximateNumberOfMessages' => 6];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->boolean($SQSAdapter->isEmpty('testQueue'))->IsFalse();
    }

    public function testSQSAdapterGetNumberMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->getNumberMessages('');
        });
    }

    public function testSQSAdapterGetNumberMessagesWithEmptyQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['name' => 'ApproximateNumberOfMessages', 'value' => 0]];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->integer($SQSAdapter->getNumberMessages('testQueue'))->IsEqualTo(0);
    }

    public function testSQSAdapterGetNumberMessages()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler =new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['ApproximateNumberOfMessages' => 6];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->integer($SQSAdapter->getNumberMessages('testQueue'))->IsEqualTo(18);
    }

    public function testSQSAdapterDeleteQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->deleteQueue('');
        });
    }

    public function testSQSAdapterDeleteQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->deleteQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->class($SQSAdapter->deleteQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterCreateQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->createQueue('');
        });
    }

    public function testSQSAdapterCreateQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->createQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->class($SQSAdapter->createQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterPurgeQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->purgeQueue('');
        });
    }

    public function testSQSAdapterPurgeQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->purgeQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->class($SQSAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterListQueuesWithPrefix()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['prefixTestQueueOne-HIGH', 'prefixTestQueueOne-MID', 'prefixTestQueueOne-LOW', 'prefixTestQueueTwo-HIGH', 'prefixTestQueueTwo-MID', 'prefixTestQueueTwo-LOW'];
        };
        $mockSqsClient->getMockController()->listQueues = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->array($SQSAdapter->listQueues('prefix'))->containsValues(['prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testSQSAdapterListQueues()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['testQueueOne-HIGH', 'testQueueOne-MID', 'testQueueOne-LOW', 'prefixTestQueueOne-HIGH', 'prefixTestQueueOne-MID', 'prefixTestQueueOne-LOW', 'prefixTestQueueTwo-HIGH', 'prefixTestQueueTwo-MID', 'prefixTestQueueTwo-LOW'];
        };
        $mockSqsClient->getMockController()->listQueues = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->array($SQSAdapter->listQueues())->containsValues(['testQueueOne', 'prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testSQSAdapterRenameQueueWithEmptySourceQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->renameQueue('', 'targetQueue');
        });
    }

    public function testSQSAdapterRenameQueueWithEmptyTargetQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($SQSAdapter) {
            $SQSAdapter->renameQueue('sourceQueue', '');
        });
    }

    public function testSQSAdapterRenameQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->listQueues = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->sendMessage = function () use($mockQueueUrlModel) {
        };
        $mockQueueUrlModel->getMockController()->get = function ($attr) use($mockQueueUrlModel, $priorityHandler) {

            if ($attr === 'Messages')
            {
                static $i = true;

                if ($i) {
                    $i = false;
                    return [['priority' => $priorityHandler->getHighest(), 'ReceiptHandle' => 'testReceiptHandle', 'Body' => serialize('test message one')], ['priority' => $priorityHandler->getHighest(), 'ReceiptHandle' => 'testReceiptHandle', 'Body' => serialize('test message two')]];
                } else {
                    $i = true;
                    return [];
                }
            }
            return null;
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->deleteMessage = function () use($mockQueueUrlModel) {
        };
        $mockSqsClient->getMockController()->createQueue = function () use($mockQueueUrlModel) {
        };
        $mockSqsClient->getMockController()->deleteQueue = function () use($mockQueueUrlModel) {
        };
        $this->given($SQSAdapter)
            ->class($SQSAdapter->renameQueue('sourceQueue', 'targetQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterGetPrioritiesHandler()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $SQSAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->purgeQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($SQSAdapter)
            ->class($SQSAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
