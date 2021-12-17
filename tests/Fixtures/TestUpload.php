<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Tests\Fixtures;

use Ruvents\SpiralUpload\UploadInterface;

final class TestUpload implements UploadInterface
{
    private string $path;

    private string $title;

    public function __construct(string $path, string $title)
    {
        $this->path = $path;
        $this->title = $title;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public static function create(string $path, string $title): self
    {
        return new self($path, $title);
    }
}
