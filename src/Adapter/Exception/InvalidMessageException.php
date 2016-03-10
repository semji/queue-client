<?php

namespace ReputationVIP\QueueClient\Adapter\Exception;

use ReputationVIP\QueueClient\Common\Exception\QueueClientException;

class InvalidMessageException extends \InvalidArgumentException implements QueueClientException {}
