<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Tests;

use PHPUnit\Framework\TestCase;
use Ruvents\SpiralUpload\Exception\ConfigException;
use Ruvents\SpiralUpload\Tests\Fixtures\TestUpload;
use Ruvents\SpiralUpload\UploadConfig;

/**
 * @internal
 * @covers \Ruvents\SpiralUpload\UploadConfig
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
