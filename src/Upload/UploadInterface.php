<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Upload;

interface UploadInterface
{
    public function getTitle(): string;

    public function getPath(): string;

    public static function create(string $path, string $title): self;
}
