<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\ApiVersionModel;

class ApiVersionService
{
    private string $version;

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function getVersion(): ApiVersionModel
    {
        return new ApiVersionModel($this->version);
    }
}
