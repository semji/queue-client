<?php

namespace ReputationVIP\QueueClient\tests\units\Adapter;

use ArrayIterator;
use mageekguy\atoum;
use ReputationVIP\QueueClient\PriorityHandler\ThreeLevelPriorityHandler;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Lock\Factory;

class MockIOExceptionInterface extends \Exception implements IOExceptionInterface
{
    public function getPath()
    {
        return '';
    }
}

class FileAdapter extends atoum\test
{
    public function testFileAdapterClass()
    {
        $this->testedClass->implements('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapter__construct()
    {
        $this->object(new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/'));
    }

    public function testFileAdapter__constructWithFilesystemError(Filesystem $fs, Finder $finder)
    {
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->exception(function () use ($fs, $finder, $mockLockHandlerFactory) {
            $this->newTestedInstance('', null, $fs, $finder, $mockLockHandlerFactory);
        });

        $this->calling($fs)->mkdir->throw = new MockIOExceptionInterface;
        $this->calling($fs)->exists = false;

        $this->exception(function () use ($fs, $finder, $mockLockHandlerFactory) {
            $this->newTestedInstance('/tmp/test/', null, $fs, $finder, $mockLockHandlerFactory);
        });
    }

    public function testFileAdapterDeleteQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->calling($mockFs)->exists = true;
        $this->calling($mockLockHandlerFactory)->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->class($fileAdapter->deleteQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterDeleteQueueWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->calling($mockFs)->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteQueue('');
        });
    }

    public function testFileAdapterDeleteQueueWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->calling($mockFs)->exists = false;
        $this->calling($mockLockHandlerFactory)->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteQueue('testQueue');
        });
    }

    public function testFileAdapterDeleteQueueWithLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->calling($mockFs)->exists = true;
        $this->calling($mockLockHandlerFactory)->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteQueue('testQueue');
        });
    }

    public function testFileAdapterCreateQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $mockFs->getMockController()->exists = false;
        $this->given($fileAdapter)
            ->class($fileAdapter->createQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterCreateQueueWithFsException()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFs->getMockController()->exists = false;
        $mockFs->getMockController()->dumpFile = function ($repository) {
            throw new \Exception('test exception');
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->createQueue('testQueue');
        });
    }

    public function testFileAdapterCreateQueueWithLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $mockFs->getMockController()->exists = false;
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->createQueue('testQueue');
        });
    }

    public function testFileAdapterCreateQueueWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $this->calling($mockFs)->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->createQueue('');
        });
    }

    public function testFileAdapterCreateQueueWithExistingQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->createQueue('testQueue');
        });
    }

    public function testFileAdapterCreateQueueWithSpaceIngQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->createQueue('test Queue');
        });
    }

    public function testFileAdapterPurgeQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"queue":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->class($fileAdapter->purgeQueue('testQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterPurgeQueueWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $FileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($FileAdapter) {
            $FileAdapter->purgeQueue('testQueue');
        });
    }

    public function testFileAdapterPurgeQueueWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->purgeQueue('');
        });
    }

    public function testFileAdapterPurgeQueueWithLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->purgeQueue('testQueue');
        });
    }

    public function testFileAdapterPurgeQueueWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->purgeQueue('testQueue');
        });
    }

    public function testFileAdapterPurgeQueueWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->purgeQueue('testQueue');
        });
    }

    public function testFileAdapterIsEmptyWithEmptyQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"queue":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this
            ->given($fileAdapter)
            ->boolean($fileAdapter->isEmpty('testQueue'))
            ->isTrue();
    }

    public function testFileAdapterIsEmptyWithNoEmptyQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[{"id":"testQueue-HIGH559f77704e87c5.40358915","time-in-flight":null, "delayed-until":null,"time-in-flight":null, "delayed-until":null,"Body":"s:12:\"Test message\";"}]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this
            ->given($fileAdapter)
            ->boolean($fileAdapter->isEmpty('testQueue'))
            ->isFalse();
    }

    public function testFileAdapterIsEmptyWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->isEmpty('');
        });
    }

    public function testFileAdapterIsEmptyWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->isEmpty('testQueue');
        });
    }

    public function testFileAdapterIsEmptyWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->isEmpty('testQueue');
        });
    }

    public function testFileAdapterIsEmptyWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->isEmpty('testQueue');
        });
    }

    public function testFileAdapterListQueues()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testOneQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'prefixTestTwoQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'testTwoQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'testThreeQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this
            ->given($fileAdapter)
            ->array($fileAdapter->listQueues())
            ->containsValues(['testOneQueue', 'testTwoQueue', 'testThreeQueue']);
    }

    public function testFileAdapterListQueuesWithPrefix()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testOneQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'prefixTestTwoQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'testTwoQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                $files[] = 'prefixTestOneQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this
            ->given($fileAdapter)
            ->array($fileAdapter->listQueues('prefix'))
            ->containsValues(['prefixTestOneQueue', 'prefixTestTwoQueue']);
    }

    public function testFileAdapterListQueuesWithEmptyQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFinder->getMockController()->getIterator = function () {
            return new ArrayIterator([]);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this
            ->given($fileAdapter)
            ->array($fileAdapter->listQueues())
            ->isEmpty();
    }

    public function testFileAdapterAddMessage()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"queue":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $FileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($FileAdapter)
            ->class($FileAdapter->addMessage('testQueue', 'test Message one'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterAddMessageWithDelay()
    {
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue'.\ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR.$priority->getName().'.'.\ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = $fileAdapter->addMessage('testQueue', 'test Message one', null, 1);
        sleep(1);
        $this->given($fileAdapter)
            ->class($fileAdapter)->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterAddMessageWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('', '');
        });
    }

    public function testFileAdapterAddMessageWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('testQueue', 'test Message one');
        });
    }

    public function testFileAdapterAddMessageWithEmptyMessage()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('testQueue', '');
        });
    }

    public function testFileAdapterAddMessageLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('testQueue', 'test message');
        });
    }

    public function testFileAdapterAddMessageWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('testQueue', 'test message');
        });
    }

    public function testFileAdapterAddMessageWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->addMessage('testQueue', 'test message');
        });
    }

    public function testFileAdapterGetNumberMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getNumberMessages('');
        });
    }

    public function testFileAdapterGetNumberMessagesWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getNumberMessages('testQueue');
        });
    }

    public function testFileAdapterGetNumberMessagesLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getNumberMessages('testQueue');
        });
    }

    public function testFileAdapterGetNumberMessagesWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getNumberMessages('testQueue');
        });
    }

    public function testFileAdapterGetNumberMessagesWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getNumberMessages('testQueue');
        });
    }

    public function testFileAdapterGetNumberMessages()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[{"id":"testQueue-HIGH559f77704e87c5.40358915","time-in-flight":null, "delayed-until":null,"delayed-until":null,"Body":"s:12:\"Test message\";"},{"id":"testQueue-HIGH559f9a97733a01.98514574","time-in-flight":null, "delayed-until":null,"Body":"s:16:\"test message two\";"}]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->integer($fileAdapter->getNumberMessages('testQueue'))->isEqualTo(6);
    }

    public function testFileAdapterGetMessagesWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('', 1);
        });
    }

    public function testFileAdapterGetMessagesWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue', 1);
        });
    }

    public function testFileAdapterAddMessagesWithNoNumericNbrMsg()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue', 'toto');
        });
    }

    public function testFileAdapterGetMessagesWithNotValidNumericNbrMsg()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue', -5);
        });
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue', (\ReputationVIP\QueueClient\Adapter\FileAdapter::MAX_NB_MESSAGES + 1));
        });
    }

    public function testFileAdapterGetMessagesLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue');
        });
    }

    public function testFileAdapterGetMessagesWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue');
        });
    }

    public function testFileAdapterGetMessagesWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->getMessages('testQueue');
        });
    }

    public function testFileAdapterGetMessages()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[{"id":"testQueue-HIGH559f77704e87c5.40358915","time-in-flight":null, "delayed-until":null,"Body":"s:12:\"Test message\";"},{"id":"testQueue-HIGH559f9a97733a01.98514574","time-in-flight":null, "delayed-until":null,"Body":"s:16:\"test message two\";"}]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->array($fileAdapter->GetMessages('testQueue', 6));
        $this->given($fileAdapter)
            ->array($fileAdapter->GetMessages('testQueue', 8));
    }

    public function testFileAdapterDeleteMessageWithEmptyQueueName()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteMessage('', []);
        });
    }

    public function testFileAdapterDeleteMessageWithNoQueueFile()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter, $priorityHandler) {
            $fileAdapter->deleteMessage('testQueue', ['id' => 'testQueue-HIGH559f77704e87c5.40358915', 'priority' => $priorityHandler->getHighest()->getLevel()]);
        });
    }

    public function testFileAdapterDeleteMessageWithNoMessage()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = false;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteMessage('testQueue', []);
        });
    }

    public function testFileAdapterDeleteMessageWithNoIdField()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $FileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($FileAdapter, $priorityHandler) {
            $FileAdapter->deleteMessage('testQueue', ['priority' => $priorityHandler->getHighest()->getLevel()]);
        });
    }

    public function testFileAdapterDeleteMessageWithNotPriorityField()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteMessage('testQueue', ['id' => 'testQueue-HIGH559f77704e87c5.40358915']);
        });
    }

    public function testFileAdapterDeleteMessageWithBadMessageType()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $mockFs->getMockController()->exists = true;
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->deleteMessage('testQueue', 'message');
        });
    }

    public function testFileAdapterDeleteMessageLockFailed()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = false;
            return $mockLockHandler;
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter, $priorityHandler) {
            $fileAdapter->deleteMessage('testQueue', ['id' => 'testQueue-HIGH559f77704e87c5.40358915', 'priority' => $priorityHandler->getHighest()->getLevel()]);
        });
    }

    public function testFileAdapterDeleteMessageWithEmptyQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter, $priorityHandler) {
            $fileAdapter->deleteMessage('testQueue', ['id' => 'testQueue-HIGH559f77704e87c5.40358915', 'priority' => $priorityHandler->getHighest()->getLevel()]);
        });
    }

    public function testFileAdapterDeleteMessageWithBadQueueContent()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () {
                    return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
                };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use ($file) {
                    return $file;
                };
                $mockSplFileInfo->getMockController()->getPathname = function () use ($file) {
                    return '/tmp/test/' . $file;
                };
                $mockSplFileInfo->getMockController()->getContents = function () use ($file) {
                    return '{"bad":[]}';
                };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter, $priorityHandler) {
            $fileAdapter->deleteMessage('testQueue', ['id' => 'testQueue-HIGH559f77704e87c5.40358915', 'priority' => $priorityHandler->getHighest()->getLevel()]);
        });
    }

    public function testFileAdapterDeleteMessage()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = true;
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[{"id":"testQueue-HIGH559f77704e87c5.40358915","time-in-flight":null, "delayed-until":null,"Body":"s:12:\"Test message\";"},{"id":"testQueue-HIGH559f9a97733a01.98514574","time-in-flight":null, "delayed-until":null,"Body":"s:16:\"test message two\";"}]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->class($fileAdapter->deleteMessage('testQueue', array('id' => 'testQueue-HIGH559f77704e87c5.40358915', 'priority' => $priorityHandler->getHighest()->getLevel())))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterRenameQueueWithEmptyParameter()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->renameQueue('', 'newTestQueue');
        });
        $this->exception(function () use ($fileAdapter) {
            $fileAdapter->renameQueue('testQueue', '');
        });
    }

    public function testFileAdapterRenameQueue()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $priorityHandler = new ThreeLevelPriorityHandler();
        $mockFs->getMockController()->exists = function ($queue) {
            if (strstr($queue, 'new')) {
                return false;
            }
            return true;
        };
        $mockLockHandlerFactory->getMockController()->createLock = function ($repository) {
            $mockLockHandler = new \mock\Symfony\Component\Lock\LockInterface;
            $mockLockHandler->getMockController()->acquire = true;
            return $mockLockHandler;
        };
        $mockFinder->getMockController()->getIterator = function () use ($priorityHandler) {
            $files = [];
            $priorities = $priorityHandler->getAll();
            foreach ($priorities as $priority) {
                $files[] = 'testQueue' . \ReputationVIP\QueueClient\Adapter\FileAdapter::PRIORITY_SEPARATOR . $priority->getName() . '.' . \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION;
            }
            $mocksSplFileInfo = [];
            foreach ($files as $file) {
                $mockSplFileInfo = new \mock\Symfony\Component\Finder\SplFileInfo('', '', '');

                $mockSplFileInfo->getMockController()->getExtension = function () { return \ReputationVIP\QueueClient\Adapter\FileAdapter::QUEUE_FILE_EXTENSION; };
                $mockSplFileInfo->getMockController()->getRelativePathname = function () use($file) { return $file; };
                $mockSplFileInfo->getMockController()->getPathname = function () use($file) { return '/tmp/test/' . $file; };
                $mockSplFileInfo->getMockController()->getContents = function () use($file) { return '{"queue":[{"id":"testQueue-HIGH559f77704e87c5.40358915","time-in-flight":null, "delayed-until":null,"Body":"s:12:\"Test message\";"},{"id":"testQueue-HIGH559f9a97733a01.98514574","time-in-flight":null, "delayed-until":null,"Body":"s:16:\"test message two\";"}]}'; };
                $mocksSplFileInfo[] = $mockSplFileInfo;
            }
            return new ArrayIterator($mocksSplFileInfo);
        };
        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', $priorityHandler, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->class($fileAdapter->renameQueue('testQueue', 'newTestQueue'))->hasInterface('\ReputationVIP\QueueClient\Adapter\AdapterInterface');
    }

    public function testFileAdapterGetPriorityHandler()
    {
        $this->mockGenerator->shuntParentClassCalls();
        $mockFs = new \mock\Symfony\Component\Filesystem\Filesystem;
        $this->mockGenerator->unshuntParentClassCalls();
        $mockFinder = new \mock\Symfony\Component\Finder\Finder;
        $this->mockGenerator->orphanize('__construct');
        $mockLockHandlerFactory = new \mock\Symfony\Component\Lock\Factory;

        $fileAdapter = new \ReputationVIP\QueueClient\Adapter\FileAdapter('/tmp/test/', null, $mockFs, $mockFinder, $mockLockHandlerFactory);
        $this->given($fileAdapter)
            ->class($fileAdapter->getPriorityHandler())->hasInterface('\ReputationVIP\QueueClient\PriorityHandler\PriorityHandlerInterface');
    }
}
