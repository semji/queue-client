<?php

namespace ReputationVIP\QueueClient\Utils;

use Symfony\Component\Filesystem\LockHandler;

class LockHandlerFactory implements LockHandlerFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function getLockHandler($file, $lockPath = null)
    {
        return new LockHandler($file, $lockPath = null);
    }
}
