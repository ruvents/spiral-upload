# Spiral Upload

Upload management package with misc helpers.

## Installation

```sh
composer require ruvents/spiral-upload
```

Then add `UploadBootloader` to your `App.php`:

```php
use Ruvents\SpiralUpload\UploadBootloader;

class App extends Kernel {
    protected const LOAD = [
        ...
        UploadBootloader::class,
    ]
}
```


## Configuration

Put the following code into file `app/config/upload.php`:

```php
<?php

declare(strict_types=1);

return [
    'uploadClass' => Upload::class, // Custom UploadInterface implementation class.
    'urlPrefix' => 'https://foo.bar/uploads/', // Public URL to uploads. Example:
    // https://foo.bar/uploads/f8/some-upload.png -- full URL to upload
    // https://foo.bar/uploads -- URL prefix
    // f8/some-upload.png -- relative path to upload
];
```


## Use

Use `UploadManager` for upload-relative tasks:

```php
public function manageUploads(UploadManager $manager) {
    // Create upload from file path.
    $upload = $manager->create('/path/to/file.txt', 'file.txt');

    // Create upload from resource.
    $upload = $manager->create($handle = fopen('/path/to/file.txt'), 'file.txt');
    fclose($handle);

    // Or from UploadedFileInterface.
    $stream = clone $uploadedFile->getStream();
    $upload = $manager->create($stream->detach(), 'file.txt');

    // Get full URL of upload.
    $url = $manager->url($upload);

    // Delete stored file associated with upload.
    $manager->delete($upload);
}
```
