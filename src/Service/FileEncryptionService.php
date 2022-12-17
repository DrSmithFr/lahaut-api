<?php

declare(strict_types = 1);

namespace App\Service;

use Generator;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\File;

class FileEncryptionService
{
    /**
     * Define the number of blocks that should be read from the source file for each chunk.
     * For 'AES-128-CBC' each block consist of 16 bytes.
     * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
     * to read/write shorter or longer chunks.
     */
    final public const FILE_CHUNK_SIZE = 10000;

    public function encryptFile(File $file, string $key, string $destination): File
    {
        $key = substr(sha1($key, true), 0, 16);
        $iv  = openssl_random_pseudo_bytes(16);

        if ($iv === false) {
            throw new RuntimeException('cannot create initialisation vector');
        }

        if (!$encrypted = fopen($destination, 'wb')) {
            throw new RuntimeException('cannot create destination file');
        }

        // Put the initialization vector to the beginning of the file
        fwrite($encrypted, $iv);

        if (!$uploaded = fopen($file->getRealPath(), 'rb')) {
            throw new RuntimeException('cannot open uploaded file');
        }

        while (!feof($uploaded)) {
            $plaintext = fread($uploaded, 16 * self::FILE_CHUNK_SIZE);
            $cipher    = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

            // Use the first 16 bytes of the cipher as the next initialization vector
            $iv = substr($cipher, 0, 16);
            fwrite($encrypted, $cipher);
        }

        fclose($uploaded);
        fclose($encrypted);

        return new File($destination, true);
    }

    public function decryptFile(File $source, string $key): Generator
    {
        $key = substr(sha1($key, true), 0, 16);

        if (!$fpIn = fopen($source->getRealPath(), 'rb')) {
            throw new RuntimeException('cannot open source file');
        }

        // Get the initialization vector from the beginning of the file
        $iv = fread($fpIn, 16);

        while (!feof($fpIn)) {
            // we have to read one block more for decrypting than for encrypting
            $cipher = fread($fpIn, 16 * (self::FILE_CHUNK_SIZE + 1));
            $plaintext  = openssl_decrypt($cipher, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

            // Use the first 16 bytes of the cipher as the next initialization vector
            $iv = substr($cipher, 0, 16);

            yield $plaintext;
        }

        fclose($fpIn);
    }
}
