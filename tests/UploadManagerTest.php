<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Ruvents\SpiralUpload\Tests\Fixtures\TestUpload;
use Ruvents\SpiralUpload\UploadManager;
use Spiral\Storage\Bucket;

/**
 * @internal
 * @covers \Ruvents\SpiralUpload\UploadManager
 */
final class UploadManagerTest extends TestCase
{
    public function testUploadCreationFromPath(): void
    {
        [$filesystem, $bucket] = $this->getFilesystemAndBucket();
        $manager = new UploadManager($bucket, TestUpload::class, '/');

        $tmpName = bin2hex(random_bytes(16));
        $tmpFile = sys_get_temp_dir().'/'.$tmpName;
        touch(sys_get_temp_dir().'/'.$tmpName);

        $upload = $manager->create($tmpFile, basename($tmpFile));

        $this->assertNotEmpty($upload->getPath());
        $this->assertTrue($filesystem->fileExists($upload->getPath()));
        $this->assertSame($tmpName, $upload->getTitle());

        unlink($tmpFile);
    }

    public function testUploadCreationFromResource(): void
    {
        [$filesystem, $bucket] = $this->getFilesystemAndBucket();
        $manager = new UploadManager($bucket, TestUpload::class, '/');

        $file = fopen('php://memory', 'rw');
        fwrite($file, $content = 'hello from memory!');
        $upload = $manager->create($file, 'memoryfile');
        fclose($file);

        $this->assertNotEmpty($upload->getPath());
        $this->assertTrue($filesystem->fileExists($upload->getPath()));
        $this->assertSame($filesystem->read($upload->getPath()), $content);
        $this->assertSame('txt', pathinfo($upload->getPath(), \PATHINFO_EXTENSION));
    }

    public function testCorrectExtensionForPngImage(): void
    {
        [$filesystem, $bucket] = $this->getFilesystemAndBucket();
        $manager = new UploadManager($bucket, TestUpload::class, '/');

        $image = imagecreatetruecolor(32, 32);
        imagefill($image, 0, 0, imagecolorallocate($image, 5, 55, 255));

        $tmpFile = tempnam(sys_get_temp_dir(), 'upload').'.png';
        imagepng($image, $tmpFile);

        $upload = $manager->create($tmpFile, basename($tmpFile));

        $this->assertNotEmpty($upload->getPath());
        $this->assertTrue($filesystem->fileExists($upload->getPath()));
        $this->assertSame('png', pathinfo($upload->getPath(), \PATHINFO_EXTENSION));

        unlink($tmpFile);
    }

    public function testUriGenerationFromUpload(): void
    {
        [$filesystem, $bucket] = $this->getFilesystemAndBucket();
        $manager = new UploadManager(
            $bucket,
            TestUpload::class,
            'http://example.site/'
        );

        $this->assertSame(
            'http://example.site/test/upload/path.php',
            $manager->url(TestUpload::create('/test/upload/path.php', 'Not important'))
        );
    }

    public function testUriGenerationFromString(): void
    {
        [$filesystem, $bucket] = $this->getFilesystemAndBucket();
        $manager = new UploadManager(
            $bucket,
            TestUpload::class,
            'http://example.site/'
        );

        $this->assertSame(
            'http://example.site/test/upload/path.php',
            $manager->url('/test/upload/path.php')
        );
    }

    private function getFilesystemAndBucket(): array
    {
        return [
            $filesystem = new Filesystem(new InMemoryFilesystemAdapter()),
            new Bucket($filesystem),
        ];
    }
}
