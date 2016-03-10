<?php

namespace ReputationVIP\QueueClient\PriorityHandler\Exception;

use ReputationVIP\QueueClient\Common\Exception\QueueClientException;

class LevelException extends \RuntimeException implements QueueClientException {}
