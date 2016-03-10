<?php

namespace ReputationVIP\QueueClient\Adapter\Exception;

use ReputationVIP\QueueClient\Common\Exception\QueueClientException;

class QueueAccessException extends \RuntimeException implements QueueClientException {}
