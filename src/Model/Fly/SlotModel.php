<?php

namespace App\Model\Fly;

use App\Entity\Fly\FlyLocation;
use App\Enum\FlyTypeEnum;
use DateInterval;
use DateTimeImmutable;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
class SlotModel
{
    #[JMS\Expose]
    #[OA\Property(
        description: 'fly price',
        type: 'string',
        example: '130.00'
    )]
    private float $price;

    #[OA\Property(
        description: 'Fly Location Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private FlyLocation $flyLocation;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[OA\Property(
        description: 'End at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $endAt;

    #[JMS\Expose]
    #[JMS\Type("DateInterval<'PT%iM'>")]
    #[OA\Property(
        description: 'Average fly duration',
        type: 'string',
        example: 'PT1M'
    )]
    private DateInterval $averageFlyDuration;

    #[JMS\Expose]
    #[JMS\Type(FlyTypeEnum::class)]
    #[OA\Property(
        description: 'Fly type',
        type: 'string',
        example: 'discovery|freestyle|kid'
    )]
    private FlyTypeEnum $type;

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("flyLocation")]
    public function getFlyLocationUuid(): string
    {
        return $this->getFlyLocation()->getUuid();
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getFlyLocation(): FlyLocation
    {
        return $this->flyLocation;
    }

    public function setFlyLocation(FlyLocation $flyLocation): self
    {
        $this->flyLocation = $flyLocation;
        return $this;
    }

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;
        return $this;
    }

    public function getAverageFlyDuration(): DateInterval
    {
        return $this->averageFlyDuration;
    }

    public function setAverageFlyDuration(DateInterval $averageFlyDuration): self
    {
        $this->averageFlyDuration = $averageFlyDuration;
        return $this;
    }

    public function getType(): FlyTypeEnum
    {
        return $this->type;
    }

    public function setType(FlyTypeEnum $type): self
    {
        $this->type = $type;
        return $this;
    }
}
