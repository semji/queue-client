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
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessage('', '');
        });
    }

    public function testSQSAdapterAddMessageWithEmptyMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessage('testQueue', '');
        });
    }

    public function testSQSAdapterAddMessageWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->sendMessage = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessage('testQueue', 'test message');
        });
    }

    public function testSQSAdapterAddMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->sendMessage = function () {
        };
        $this->given($sqsAdapter)
            ->class($sqsAdapter->addMessage('testQueue', 'test message'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterAddMessages()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $mockSqsClient->getMockController()->sendMessageBatch = function () {};

        $this
            ->if($mockSqsClient)
            ->and($sqsAdapter->addMessages('testQueue', array_fill(0, 11, 'test message')))
            ->mock($mockSqsClient)
                ->call('sendMessageBatch')->twice();
    }

    public function testSQSAdapterAddMessagesWithEmptyMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessages('testQueue', ['test message', '']);
        });
    }

    public function testSQSAdapterAddMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessages('', ['']);
        });
    }

    public function testSQSAdapterAddMessagesWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->sendMessageBatch = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->addMessages('testQueue', array_fill(0, 11, 'test message'));
        });
    }

    public function testSQSAdapterGetMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('');
        });
    }

    public function testSQSAdapterGetMessagesWithBadMessageNumber()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('testQueue', 'BadNumber');
        });

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('testQueue', 0);
        });

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('testQueue', \ReputationVIP\QueueClient\Adapter\SQSAdapter::MAX_NB_MESSAGES + 1);
        });
    }

    public function testSQSAdapterGetMessagesWithNoMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $this->given($sqsAdapter)
            ->array($sqsAdapter->getMessages('testQueue', 5))->isEmpty();
    }

    public function testSQSAdapterGetMessagesWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('testQueue');
        });
    }

    public function testSQSAdapterGetMessages()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['Body' => serialize('test message one')], ['Body' => serialize('test message two')]];
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $this->given($sqsAdapter)
            ->array($sqsAdapter->getMessages('testQueue', 6))->hasSize(6);
    }

    public function testSQSAdapterDeleteMessageWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteMessage('', []);
        });
    }

    public function testSQSAdapterDeleteMessageWithEmptyMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteMessage('testQueue', []);
        });
    }

    public function testSQSAdapterDeleteMessageWithBadMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteMessage('testQueue', 'Bad message');
        });
    }

    public function testSQSAdapterDeleteMessageWithNoMessageReceiptHandle()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $this->exception(function() use($sqsAdapter, $priorityHandler) {
            $sqsAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest()]);
        });
    }

    public function testSQSAdapterDeleteMessageWithNoMessagePriority()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteMessage('testQueue', ['ReceiptHandle' => 'testReceiptHandle']);
        });
    }

    public function testSQSAdapterDeleteMessageWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->deleteMessage = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter, $priorityHandler) {
            $sqsAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest()->getLevel(), 'ReceiptHandle' => 'testReceiptHandle']);
        });
    }

    public function testSQSAdapterDeleteMessage()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->deleteMessage = function () use($mockQueueUrlModel) {
            return null;
        };
        $this->given($sqsAdapter)
            ->class($sqsAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest()->getLevel(), 'ReceiptHandle' => 'testReceiptHandle']))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterIsEmptyWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->isEmpty('');
        });
    }

    public function testSQSAdapterIsEmptyWithEmptyQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['name' => 'ApproximateNumberOfMessages', 'value' => 0]];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->boolean($sqsAdapter->isEmpty('testQueue'))->IsTrue();
    }

    public function testSQSAdapterIsEmptyWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->isEmpty('testQueue');
        });
    }


    public function testSQSAdapterIsEmpty()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['ApproximateNumberOfMessages' => 6];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->boolean($sqsAdapter->isEmpty('testQueue'))->IsFalse();
    }

    public function testSQSAdapterGetNumberMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getNumberMessages('');
        });
    }

    public function testSQSAdapterGetNumberMessagesWithEmptyQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['name' => 'ApproximateNumberOfMessages', 'value' => 0]];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->integer($sqsAdapter->getNumberMessages('testQueue'))->IsEqualTo(0);
    }

    public function testSQSAdapterGetNumberMessagesWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getNumberMessages('testQueue');
        });
    }

    public function testSQSAdapterGetNumberMessages()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler =new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['ApproximateNumberOfMessages' => 6];
        };
        $mockSqsClient->getMockController()->getQueueAttributes = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->integer($sqsAdapter->getNumberMessages('testQueue'))->IsEqualTo(18);
    }

    public function testSQSAdapterDeleteQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteQueue('');
        });
    }

    public function testSQSAdapterDeleteQueueWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->deleteQueue = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->deleteQueue('testQueue');
        });
    }

    public function testSQSAdapterDeleteQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->deleteQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->class($sqsAdapter->deleteQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterCreateQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->createQueue('');
        });
    }

    public function testSQSAdapterCreateQueueWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->createQueue = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->createQueue('testQueue');
        });
    }

    public function testSQSAdapterCreateQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->createQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->class($sqsAdapter->createQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterPurgeQueueWithEmptyQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->purgeQueue('');
        });
    }
    public function testSQSAdapterPurgeQueueWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->purgeQueue = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->purgeQueue('testQueue');
        });
    }

    public function testSQSAdapterPurgeQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->purgeQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->class($sqsAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterListQueuesWithPrefix()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['prefixTestQueueOne-HIGH', 'prefixTestQueueOne-MID', 'prefixTestQueueOne-LOW', 'prefixTestQueueTwo-HIGH', 'prefixTestQueueTwo-MID', 'prefixTestQueueTwo-LOW'];
        };
        $mockSqsClient->getMockController()->listQueues = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->array($sqsAdapter->listQueues('prefix'))->containsValues(['prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testSQSAdapterListQueuesWithSqsException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $sqsException = new \mock\Aws\Sqs\Exception\SqsException;

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockSqsClient->getMockController()->listQueues = function () use ($sqsException) {
            throw $sqsException;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->listQueues();
        });
    }

    public function testSQSAdapterListQueues()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return ['testQueueOne-HIGH', 'testQueueOne-MID', 'testQueueOne-LOW', 'prefixTestQueueOne-HIGH', 'prefixTestQueueOne-MID', 'prefixTestQueueOne-LOW', 'prefixTestQueueTwo-HIGH', 'prefixTestQueueTwo-MID', 'prefixTestQueueTwo-LOW'];
        };
        $mockSqsClient->getMockController()->listQueues = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->array($sqsAdapter->listQueues())->containsValues(['testQueueOne', 'prefixTestQueueOne', 'prefixTestQueueTwo']);
    }

    public function testSQSAdapterRenameQueueWithEmptySourceQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->renameQueue('', 'targetQueue');
        });
    }

    public function testSQSAdapterRenameQueueWithEmptyTargetQueueName()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->renameQueue('sourceQueue', '');
        });
    }

    public function testSQSAdapterRenameQueue()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

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
        $this->given($sqsAdapter)
            ->class($sqsAdapter->renameQueue('sourceQueue', 'targetQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testSQSAdapterGetPrioritiesHandler()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return null;
        };
        $mockSqsClient->getMockController()->purgeQueue = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };

        $this->given($sqsAdapter)
            ->class($sqsAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }

    public function testMalformedMessageException()
    {
        $this->mockGenerator->orphanize('__construct');
        $this->mockGenerator->shuntParentClassCalls();
        $mockSqsClient = new \mock\Aws\Sqs\SqsClient;
        $mockQueueUrlModel = new \mock\Guzzle\Service\Resource\Model;
        $priorityHandler = new ThreeLevelPriorityHandler();
        $sqsAdapter = new \ReputationVIP\QueueClient\Adapter\SQSAdapter($mockSqsClient, $priorityHandler);

        $mockSqsClient->getMockController()->getQueueUrl = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $mockQueueUrlModel->getMockController()->get = function () use($mockQueueUrlModel) {
            return [['Body' => 'test message one']];
        };
        $mockSqsClient->getMockController()->receiveMessage = function () use($mockQueueUrlModel) {
            return $mockQueueUrlModel;
        };
        $this->exception(function() use($sqsAdapter) {
            $sqsAdapter->getMessages('testQueue', 1);
        })->isInstanceOf('\ReputationVIP\QueueClient\Adapter\Exception\MalformedMessageException');
    }

}
