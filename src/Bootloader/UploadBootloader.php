<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Bootloader;

use Ruvents\SpiralUpload\Config\UploadConfig;
use Ruvents\SpiralUpload\Upload\UploadManager;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Storage\BucketInterface;

class UploadBootloader extends Bootloader
{
    public const SINGLETONS = [
        UploadManager::class => [self::class, 'manager'],
    ];

    public function manager(UploadConfig $config, BucketInterface $bucket): UploadManager
    {
        return new UploadManager(
            $bucket,
            $config->getUploadClass(),
            $config->getUrlPrefix()
        );
    }
}
