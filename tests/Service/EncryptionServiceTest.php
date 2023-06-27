<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\_Utils\EncryptionService;
use App\Tests\ApiTestCase;

class EncryptionServiceTest extends ApiTestCase
{
    private ?EncryptionService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(EncryptionService::class);
    }

    public function testBasicEncryption(): void
    {
        $message = 'secret message';
        $encrypted = $this->service->encryptData($message);
        $this->assertNotEquals($message, $encrypted, 'cannot perform encryption');
        $decrypted = $this->service->decryptData($encrypted);
        $this->assertEquals($message, $decrypted, 'message has been corrupted');
    }
}
