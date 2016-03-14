<?php

namespace ReputationVIP\QueueClient\PriorityHandler\Exception;

use ReputationVIP\QueueClient\Exception\QueueClientException;

class PriorityLevelException extends \RuntimeException implements QueueClientException {}
