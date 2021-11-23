<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Tests;

use Ruvents\SpiralUpload\Config\UploadConfig;
use Ruvents\SpiralUpload\Exception\ConfigException;
use Ruvents\SpiralUpload\Tests\Fixtures\TestUpload;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $config = new UploadConfig([
            'uploadClass' => $class = TestUpload::class,
            'urlPrefix' => $urlPrefix = '/',
        ]);

        $this->assertSame($class, $config->getUploadClass());
        $this->assertSame($urlPrefix, $config->getUrlPrefix());
    }

    public function testExceptionOnMissingUploadClass(): void
    {
        $this->expectException(ConfigException::class);

        new UploadConfig(['urlPrefix' => '/']);
    }

    public function testExceptionOnIncorrectUploadClass(): void
    {
        $this->expectException(ConfigException::class);

        new UploadConfig(['uploadClass' => self::class, 'urlPrefix' => '/']);
    }

    public function testExceptionOnIncorrectUploadClassType(): void
    {
        $this->expectException(ConfigException::class);

        new UploadConfig(['uploadClass' => ['wrong type'], 'urlPrefix' => '/']);
    }

    public function testExceptionOnMissingUploadPrefix(): void
    {
        $this->expectException(ConfigException::class);

        $config = new UploadConfig(['uploadClass' => TestUpload::class]);
    }
}
