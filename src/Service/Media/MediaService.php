<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Entity\Media\Media;
use App\Service\_Utils\EncryptionService;
use App\Service\_Utils\FileEncryptionService;
use Exception;
use Generator;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;

class MediaService
{
    public final const SECRET_KEY_LENGTH = 256;

    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly EncryptionService $encryptionService,
        private readonly FileEncryptionService $fileEncryption,
        private readonly string $mediaFolder
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * @throws Exception
     */
    public function upload(Media $media): ?string
    {
        $file = $media->getFile();

        if ($file === null) {
            return null;
        }

        $password = $this->generatePassword();
        $encryptedPassword = $this->encryptionService->encryptData($password);

        $media
            ->setContentType($file->getClientMimeType())
            ->setSize($file->getSize())
            ->setExtension($file->guessExtension())
            ->setKey(base64_encode($encryptedPassword));

        $path = $this->absolutePath($media);

        if ($this->filesystem->exists($path)) {
            // must never occur with Uuid as filename
            throw new RuntimeException('createUniqueFileName() doesnt work as expected');
        }

        // ensure upload directory exist
        if (!$this->filesystem->exists($this->mediaFolder)) {
            $this
                ->filesystem
                ->mkdir($this->mediaFolder);
        }

        // write document to filesystem
        $this->fileEncryption->encryptFile($file, $password, $path);

        // remove uploaded file
        $this
            ->filesystem
            ->remove($file->getRealPath());

        return realpath($path);
    }

    /**
     * @param Media $media
     *
     * @return Generator<string>
     * @throws FileNotFoundException
     */
    public function decrypt(Media $media): Generator
    {
        $path = $this->absolutePath($media);

        $key = $this
            ->encryptionService
            ->decryptData(base64_decode($media->getKey()));

        yield from $this
            ->fileEncryption
            ->decryptFile(new File($path, true), $key);
    }

    private function absolutePath(Media $media): ?string
    {
        return sprintf(
            '%s%s%s',
            $this->mediaFolder,
            DIRECTORY_SEPARATOR,
            $media->getUuid()
        );
    }

    private function generatePassword(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' .
            '0123456789-=~!@#$%^&*()_+,.<>?;:[]{}';

        $password = '';
        $max = strlen($chars) - 1;

        for ($index = 0; $index < self::SECRET_KEY_LENGTH; $index++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}
