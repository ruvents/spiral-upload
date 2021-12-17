<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload;

use Ruvents\SpiralUpload\Exception\ConfigException;
use Ruvents\SpiralUpload\UploadInterface;
use Spiral\Core\InjectableConfig;

final class UploadConfig extends InjectableConfig
{
    public const CONFIG = 'upload';

    /** @var class-string */
    protected string $uploadClass;

    protected string $urlPrefix;

    public function __construct(array $config = [])
    {
        if (empty($config['uploadClass'])) {
            throw new ConfigException(
                '"uploadClass" option must be specified'
            );
        }

        $uploadClass = $config['uploadClass'];

        if (false === \is_string($uploadClass)) {
            throw new ConfigException(sprintf(
                '"uploadClass" option must be a string, "%s" given',
                \gettype($config['uploadClass'])
            ));
        }

        if (false === is_subclass_of($uploadClass, UploadInterface::class)) {
            throw new ConfigException(sprintf(
                'Class "%s" must implement %s',
                $uploadClass,
                UploadInterface::class
            ));
        }

        $this->uploadClass = $uploadClass;

        if (empty($config['urlPrefix'])) {
            throw new ConfigException(
                '"urlPrefix" option must be specified'
            );
        }

        $this->urlPrefix = $config['urlPrefix'];
    }

    /**
     * @return class-string
     */
    public function getUploadClass(): string
    {
        return $this->uploadClass;
    }

    public function getUrlPrefix(): string
    {
        return $this->urlPrefix;
    }
}
