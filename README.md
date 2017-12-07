# Queue Client

[![Join the chat at https://gitter.im/ReputationVIP/queue-client](https://badges.gitter.im/ReputationVIP/queue-client.svg)](https://gitter.im/ReputationVIP/queue-client?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Build Status](https://travis-ci.org/ReputationVIP/queue-client.svg?branch=master)](https://travis-ci.org/ReputationVIP/queue-client) [![Coverage Status](https://coveralls.io/repos/ReputationVIP/queue-client/badge.svg?branch=master&service=github)](https://coveralls.io/github/ReputationVIP/queue-client?branch=master)

Queue Client is a PHP library that provides a queue abstraction layer (SQS, File, Memory ...).

## Use case

Queue Client can be used to manage a lot of various queue systems. For example, you could have AWS SQS in production
environment, but in-memory queues on the development environment.

## Installation

Development version:

```bash
php composer.phar require reputation-vip/queue-client:*@dev
```

Stable version:

```bash
php composer.phar require reputation-vip/queue-client:0.1.*
```

## Basic Usage

### Setup your queue client

For example, let's set up the Queue Client with a file adapter. To setup other adapters, take a look at the [Adapter](doc/adapter-priority-aliases.md#adapters) section.

```php
<?php

use ReputationVIP\QueueClient\QueueClient;
use ReputationVIP\QueueClient\Adapter\FileAdapter;

$adapter = new FileAdapter('/tmp');
$queueClient = new QueueClient($adapter);
```

### Use the queue client

```php
<?php

// ... setup your queue client

$queueClient->createQueue('testQueue');
$queueClient->addMessage('testQueue', 'testMessage');

$messages = $queueClient->getMessages('testQueue');
$message = $messages[0];
$queueClient->deleteMessage($message);
echo $message['Body'];
```

## Unit test

Unit tests are provided by [Atoum ![Atoum](doc/images/atoum.png)](https://github.com/atoum/atoum).

To launch unit tests, run the following command:

```php vendor/atoum/atoum/bin/atoum -c coverage.php -d tests/units/```

OR

```make test``` (docker and docker-compose are required)

**php xdebug extension must be installed for code coverage report to be generated**

## Documentation

- [Usage Instructions](doc/usage.md)
- [Adapter, Priority and Aliases](doc/adapter-priority-aliases.md)
- [Extending Queue Client](doc/extending.md)

## About

### Requirements

- PHP 7.1.3 or above.

### Submitting bugs and feature requests

Bugs and feature requests are tracked on [GitHub](https://github.com/ReputationVIP/queue-client/issues)

### Framework Integrations

- [Symfony](http://symfony.com) with its own [Queue Client Bundle](https://github.com/ReputationVIP/queue-client-bundle).

### Author

Nicolas Couet - <tejerka@gmail.com> - <https://twitter.com/tejerka> - <https://github.com/tejerka><br />
See also the list of [contributors](https://github.com/ReputationVIP/queue-client/contributors) who participated to this project.
