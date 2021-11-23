<?php

declare(strict_types=1);

namespace Ruvents\SpiralUpload\Upload;

use Ruvents\SpiralUpload\Exception\FileNotReadableException;
use League\MimeTypeDetection\GeneratedExtensionToMimeTypeMap;
use Spiral\Storage\BucketInterface;
use Webmozart\Assert\Assert;

final class UploadManager
{
    private BucketInterface $bucket;

    private string $uploadClass;

    private string $urlPrefix;

    private array $mimeTypesMap;

    /**
     * @param class-string $uploadClass
     *
     * @throws \DomainException if $uploadClass points to class that does not implement UploadInterface
     */
    public function __construct(
        BucketInterface $bucket,
        string $uploadClass,
        string $urlPrefix
    ) {
        $this->bucket = $bucket;

        if (false === is_subclass_of($uploadClass, UploadInterface::class)) {
            throw new \DomainException(sprintf(
                '"%s" must implement %s',
                $uploadClass,
                UploadInterface::class
            ));
        }

        $this->uploadClass = $uploadClass;
        $this->urlPrefix = rtrim($urlPrefix, '/');
        $this->mimeTypesMap = array_flip(GeneratedExtensionToMimeTypeMap::MIME_TYPES_FOR_EXTENSIONS);
    }

    /**
     * @throws \InvalidArgumentException if $source is neither string, nor resource
     * @throws FileNotReadableException  if $source points to not existing or not readable file
     */
    public function create(mixed $source, string $name): UploadInterface
    {
        $shouldCloseResource = false;

        if (\is_resource($source)) {
            $resource = $source;
        } elseif (\is_string($source)) {
            $resource = fopen($source, 'r');
            $shouldCloseResource = true;

            if (false === $resource) {
                throw new FileNotReadableException(sprintf(
                    'Source file "%s" is not readable',
                    $source
                ));
            }
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    '$source must be either string or resource. %s given',
                    \gettype($source)
                )
            );
        }

        $uploadPath = $this->generatePath($this->getExtension($resource));

        $this->bucket->file($uploadPath)
            ->create()
            ->write($resource)
        ;

        if ($shouldCloseResource) {
            fclose($resource);
        }

        /** @var UploadInterface */
        $upload = ($this->uploadClass)::create($uploadPath, $name);

        return $upload;
    }

    public function url(string|UploadInterface $upload): string
    {
        $path = $upload instanceof UploadInterface ? $upload->getPath() : $upload;

        return sprintf(
            '%s/%s',
            $this->urlPrefix,
            ltrim($path, '/')
        );
    }

    public function delete(UploadInterface $upload): bool
    {
        $file = $this->bucket->file($upload->getPath());

        if (false === $file->exists()) {
            return false;
        }

        $file->delete(true);

        return true;
    }

    private function getExtension($resource): string
    {
        Assert::resource($resource);

        return $this->mimeTypesMap[mime_content_type($resource)] ?? '';
    }

    private function generatePath(?string $extension = null): string
    {
        $random = bin2hex(random_bytes(16));

        return substr($random, 0, 2).'/'.substr($random, 2).($extension ? '.'.$extension : '');
    }
}
