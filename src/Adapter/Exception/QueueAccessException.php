<?php

namespace ReputationVIP\QueueClient\Adapter\Exception;

use ReputationVIP\QueueClient\Exception\QueueClientException;

class QueueAccessException extends \RuntimeException implements QueueClientException {}
