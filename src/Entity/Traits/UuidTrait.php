<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

trait UuidTrait
{
    #[ORM\Id]
    #[JMS\Expose]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[OA\Property(
        type: 'string',
        example: '1ed82229-3199-6552-afb9-5752dd505444'
    )]
    private UuidInterface|string $uuid;

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface|string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }
}
