<?php

namespace App\Model\Fly;

use App\Entity\Fly\FlyType;
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
        description: 'Fly Type Identifier',
        type: 'string',
        example: 'chamonix-discovery'
    )]
    private FlyType $flyType;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'H:i'>")]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'H:i'>")]
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
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("flyType")]
    public function getFlyLocationIdentifier(): string
    {
        return $this->getFlyType()->getIdentifier();
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

    public function getFlyType(): FlyType
    {
        return $this->flyType;
    }

    public function setFlyType(FlyType $flyType): self
    {
        $this->flyType = $flyType;
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
}
