<?php

namespace ReputationVIP\QueueClient\Adapter\Exception;

use ReputationVIP\QueueClient\Exception\QueueClientException;

class InvalidMessageException extends \InvalidArgumentException implements QueueClientException {}
