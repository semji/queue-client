<?php

namespace ReputationVIP\QueueClient\Adapter;

use Aws\Sqs\Exception\SqsException;
use Aws\Sqs\SqsClient;
use ReputationVIP\QueueClient\Adapter\Exception\MalformedMessageException;
use ReputationVIP\QueueClient\PriorityHandler\Priority\Priority;
use ReputationVIP\QueueClient\Adapter\Exception\InvalidMessageException;
use ReputationVIP\QueueClient\Adapter\Exception\QueueAccessException;
use ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface;
use ReputationVIP\QueueClient\PriorityHandler\StandardPriorityHandler;

class SQSAdapter extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var SqsClient
     */
    private $sqsClient;

    /** @var PriorityHandlerInterface $priorityHandler */
    private $priorityHandler;

    const MAX_NB_MESSAGES = 10;
    const SENT_MESSAGES_BATCH_SIZE = 10;
    const PRIORITY_SEPARATOR = '-';

    /**
     * @param string $queueName
     * @param Priority $priority
     *
     * @return string
     */
    private function getQueueNameWithPrioritySuffix($queueName, Priority $priority) {
        $prioritySuffix = '';
        if ('' !== $priority->getName()) {
            $prioritySuffix = static::PRIORITY_SEPARATOR . $priority->getName();
        }

        return $queueName . $prioritySuffix;
    }

    /**
     * @param SqsClient $sqsClient
     * @param PriorityHandlerInterface $priorityHandler
     */
    public function __construct(SqsClient $sqsClient, PriorityHandlerInterface $priorityHandler = null)
    {
        $this->sqsClient = $sqsClient;

        if (null === $priorityHandler) {
            $priorityHandler = new StandardPriorityHandler();
        }

        $this->priorityHandler = $priorityHandler;
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws QueueAccessException
     */
    public function addMessages($queueName, $messages, Priority $priority = null)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (null === $priority) {
            $priority = $this->priorityHandler->getDefault();
        }

        $i = 0;
        $batch = [];
        $totalMessagesCount = count($messages);

        foreach ($messages as $index => $message) {
            if (empty($message)) {
                throw new InvalidMessageException($message, 'Message empty or not defined.');
            }

            $messageData = [
                'Id' => (string)$index,
                'MessageBody' => serialize($message)
            ];

            $batch[] = $messageData;

            if (++$i < $totalMessagesCount && count($batch) < self::SENT_MESSAGES_BATCH_SIZE) {
                continue;
            }

            try {
                $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
                $this->sqsClient->sendMessageBatch([
                    'QueueUrl' => $queueUrl,
                    'Entries' => $batch,
                ]);
            } catch (SqsException $e) {
                throw new QueueAccessException('Cannot add messages in queue.', 0, $e);
            }

            $batch = [];
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function addMessage($queueName, $message, Priority $priority = null, $delaySeconds = 0)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidMessageException($message, 'Message empty or not defined.');
        }

        if (null === $priority) {
            $priority = $this->priorityHandler->getDefault();
        }

        $message = serialize($message);
        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $this->sqsClient->sendMessage([
                'QueueUrl' => $queueUrl,
                'MessageBody' => $message,
                'delaySeconds' => $delaySeconds,
            ]);
        } catch (SqsException $e) {
            throw new QueueAccessException('Cannot add message in queue.', 0, $e);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws MalformedMessageException
     * @throws QueueAccessException
     */
    public function getMessages($queueName, $nbMsg = 1, Priority $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            $messages = [];
            foreach ($priorities as $priority) {
                $messagesPriority = $this->getMessages($queueName, $nbMsg, $priority);
                $nbMsg -= count($messagesPriority);
                $messages = array_merge($messages, $messagesPriority);
                if ($nbMsg <= 0) {
                    return $messages;
                }
            }
            return $messages;
        }

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (!is_numeric($nbMsg)) {
            throw new \InvalidArgumentException('Number of messages must be numeric.');
        }
        if ($nbMsg <= 0 || $nbMsg > self::MAX_NB_MESSAGES) {
            throw new \InvalidArgumentException('Number of messages not valid.');
        }

        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $results = $this->sqsClient->receiveMessage([
                'QueueUrl' => $queueUrl,
                'MaxNumberOfMessages' => $nbMsg,
            ]);
            $messages = $results->get('Messages');
        } catch (SqsException $e) {
            throw new QueueAccessException('Cannot get messages from queue.', 0, $e);
        }

        if (is_null($messages)) {
            return [];
        }
        foreach ($messages as $messageId => $message) {
            try {
                $messages[$messageId]['Body'] = @unserialize($message['Body']);

                // for php 7 compatibility
                if (false === $messages[$messageId]['Body']) {
                    $message['priority'] = $priority->getLevel();

                    throw new MalformedMessageException($message, 'Message seems to be malformed.');
                }
            } catch (\Exception $e) {
                $message['priority'] = $priority->getLevel();

                throw new MalformedMessageException($message, 'Message seems to be malformed.');
            }
            $messages[$messageId]['priority'] = $priority->getLevel();
        }

        return $messages;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws InvalidMessageException
     * @throws QueueAccessException
     */
    public function deleteMessage($queueName, $message)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        if (empty($message)) {
            throw new InvalidMessageException($message, 'Message empty or not defined.');
        }
        if (!is_array($message)) {
            throw new InvalidMessageException($message, 'Message must be an array.');
        }
        if (!isset($message['ReceiptHandle'])) {
            throw new InvalidMessageException($message, 'ReceiptHandle not found in message.');
        }
        if (!isset($message['priority'])) {
            throw new InvalidMessageException($message, 'Priority not found in message.');
        }

        $priority = $this->priorityHandler->getPriorityByLevel($message['priority']);

        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $this->sqsClient->deleteMessage([
                'QueueUrl' => $queueUrl,
                'ReceiptHandle' => $message['ReceiptHandle'],
            ]);
        } catch (SqsException $e) {
            throw new QueueAccessException('Cannot delete message from queue.', 0, $e);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function isEmpty($queueName, Priority $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority)
                if (!($this->isEmpty($queueName, $priority))) {
                    return false;
                }
            return true;
        }

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $result = $this->sqsClient->getQueueAttributes([
                'QueueUrl' => $queueUrl,
                'AttributeNames' => ['ApproximateNumberOfMessages'],
            ]);
        } catch (SqsException $e) {
            throw new QueueAccessException('Unable to determine whether queue is empty.', 0, $e);
        }

        $result = $result->get('Attributes');
        if (!empty($result['ApproximateNumberOfMessages']) && $result['ApproximateNumberOfMessages'] > 0) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function getNumberMessages($queueName, Priority $priority = null)
    {
        $nbrMsg = 0;

        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $nbrMsg += $this->getNumberMessages($queueName, $priority);
            }

            return $nbrMsg;
        }

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $result = $this->sqsClient->getQueueAttributes([
                'QueueUrl' => $queueUrl,
                'AttributeNames' => ['ApproximateNumberOfMessages'],
            ]);
        } catch (SqsException $e) {
            throw new QueueAccessException('Unable to get number of messages.', 0, $e);
        }

        $result = $result->get('Attributes');
        if (!empty($result['ApproximateNumberOfMessages']) && $result['ApproximateNumberOfMessages'] > 0) {
            return $result['ApproximateNumberOfMessages'];
        }

        return 0;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function deleteQueue($queueName)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();

        foreach ($priorities as $priority) {
            try {
                $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
                $this->sqsClient->deleteQueue([
                    'QueueUrl' => $queueUrl,
                ]);
            } catch (SqsException $e) {
                throw new QueueAccessException('Cannot delete queue.', 0, $e);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function createQueue($queueName)
    {
        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        $priorities = $this->priorityHandler->getAll();

        foreach ($priorities as $priority) {
            try {
                $this->sqsClient->createQueue([
                    'QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority),
                    'Attributes' => [],
                ]);
            } catch (SqsException $e) {
                throw new QueueAccessException('Cannot create queue', 0, $e);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function renameQueue($sourceQueueName, $targetQueueName)
    {
        if (empty($sourceQueueName)) {
            throw new \InvalidArgumentException('Source queue name empty or not defined.');
        }
        if (empty($targetQueueName)) {
            throw new \InvalidArgumentException('Target queue name empty or not defined.');
        }
        $this->createQueue($targetQueueName);

        $priorities = $this->priorityHandler->getAll();

        foreach ($priorities as $priority) {
            while (count($messages = $this->getMessages($sourceQueueName, 1, $priority)) > 0) {
                $this->deleteMessage($sourceQueueName, $messages[0]);
                array_walk($messages, function (&$item) {
                    $item = $item['Body'];
                });
                $this->addMessage($targetQueueName, $messages[0], $priority);
            }
        }

        $this->deleteQueue($sourceQueueName);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException
     * @throws QueueAccessException
     */
    public function purgeQueue($queueName, Priority $priority = null)
    {
        if (null === $priority) {
            $priorities = $this->priorityHandler->getAll();

            foreach ($priorities as $priority) {
                $this->purgeQueue($queueName, $priority);
            }

            return $this;
        }

        if (empty($queueName)) {
            throw new \InvalidArgumentException('Queue name empty or not defined.');
        }

        try {
            $queueUrl = $this->sqsClient->getQueueUrl(['QueueName' => $this->getQueueNameWithPrioritySuffix($queueName, $priority)])->get('QueueUrl');
            $this->sqsClient->purgeQueue([
                'QueueUrl' => $queueUrl,
            ]);
        } catch (SqsException $e) {
            throw new QueueAccessException('Cannot purge queue', 0, $e);
        }

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws QueueAccessException
     */
    public function listQueues($prefix = '')
    {
        $listQueues = [];

        try {
            if (empty($prefix)) {
                $results = $this->sqsClient->listQueues();
            } else {
                $results = $this->sqsClient->listQueues([
                    'QueueNamePrefix' => $prefix,
                ]);
            }
        } catch (SqsException $e) {
            throw new QueueAccessException('Cannot list queues', 0, $e);
        }

        $results = $results->get('QueueUrls');

        foreach ($results as $result) {
            $result = explode('/', $result);
            $result = array_pop($result);
            $priorities = $this->priorityHandler->getAll();
            foreach ($priorities as $priority) {
                if (!empty($priority)) {
                    $result = str_replace(static::PRIORITY_SEPARATOR . $priority->getName(), '', $result);
                }
            }
            $listQueues[] = $result;
        }
        $listQueues = array_unique($listQueues);

        return $listQueues;
    }
    
    /**
     * @inheritdoc
     */
    public function getPriorityHandler()
    {
        return $this->priorityHandler;
    }
}
