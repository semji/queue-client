<?php

namespace ReputationVIP\QueueClient\Utils;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\LockHandler;

interface LockHandlerFactoryInterface
{
    /**
     * @param string $file
     * @param  string|null $lockPath
     * @throws IOException
     * @return LockHandler
     */
    public function getLockHandler($file, $lockPath = null);
}
