<?php

declare(strict_types=1);

namespace App\Model;

use JMS\Serializer\Annotation\Expose;
use OpenApi\Attributes as OA;

class FormErrorDetailModel
{
    #[Expose]
    #[OA\Property(description: 'Form error', type: 'string[]', example: '{"field-name": "error message"}')]
    private array $errors = [];

    #[Expose]
    #[OA\Property(description: 'Form Child error', type: 'string[]', example: '{"field-name": "error message"}')]
    private array $children = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}
