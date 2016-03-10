<?php

namespace ReputationVIP\QueueClient\PriorityHandler\Exception;

use ReputationVIP\QueueClient\Exception\QueueClientException;

class LevelException extends \RuntimeException implements QueueClientException {}
