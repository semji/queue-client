<?php

namespace ReputationVIP\QueueClient\PriorityHandler\Exception;

use ReputationVIP\QueueClient\Exception\QueueClientException;

class InvalidPriorityException extends \InvalidArgumentException implements QueueClientException {}
