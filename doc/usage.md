# Using Queue Client

- [Installation](#installation)
- [Core Concepts](#core-concepts)
- [Configuring Queue Client](#configuring-queue-client)

## Installation

Queue Client is available on Packagist ([reputation-vip/queue-client](http://packagist.org/packages/reputation-vip/queue-client))
and is also installable via [Composer](http://getcomposer.org/).

```bash
composer require reputation-vip/queue-client
```

## Core Concepts

Every `QueueClient` instance has a queue adapter.
Each adapter works the same.
If you want to change your queue system just switch adapter.

For example you could use a file storage adapter in development environment and a in-memory storage adapter in production.

## Configuring queue client

Here is a basic setup to use file storage with three priority level:

```php
<?php

use ReputationVIP\QueueClient\QueueClient;
use ReputationVIP\QueueClient\Adapter\FileAdapter;
use ReputationVIP\QueueClient\PriorityHandler\ThreeLevelPriorityHandler;

// Create the queue client
$priorityHandler = new ThreeLevelPriorityHandler();
$adapter = new FileAdapter('/tmp', $priorityHandler);
$queueClient = new QueueClient($adapter);

// You can now use your queue client
$queueClient->createQueue('testQueue');
$queueClient->addMessage('testQueue', 'testMessage', $priorityHandler->getLowest());

$messages = $queueClient->getMessages('testQueue');
$message = $messages[0];
$queueClient->deleteMessage($message);
echo $message['Body'];
```

[Adapter, Priority and Aliases](adapter-priority-aliases.md) &rarr;
