<?php

declare(strict_types = 1);

namespace App\Tests\Service;

use App\Tests\ApiTestCase;
use App\Service\EncryptionService;

class EncryptionServiceTest extends ApiTestCase
{
    private ?EncryptionService $service;

    protected function setUp(): void
    {
        self::bootKernel();
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
