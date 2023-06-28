<?php

namespace App\Model\Slot;

use App\Entity\Activity\ActivityType;
use DateInterval;
use DateTimeImmutable;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
class SlotModel
{
    #[JMS\Expose]
    #[OA\Property(
        description: 'activity price',
        type: 'string',
        example: '130.00'
    )]
    private float $price;

    #[OA\Property(
        description: 'Activity Type Identifier',
        type: 'string',
        example: 'chamonix-discovery'
    )]
    private ActivityType $activityType;

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
        description: 'Average activity duration',
        type: 'string',
        example: 'PT1M'
    )]
    private DateInterval $averageActivityDuration;

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("activityType")]
    public function getActivityLocationIdentifier(): string
    {
        return $this->getActivityType()->getIdentifier();
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

    public function getActivityType(): ActivityType
    {
        return $this->activityType;
    }

    public function setActivityType(ActivityType $activityType): self
    {
        $this->activityType = $activityType;
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

    public function getAverageActivityDuration(): DateInterval
    {
        return $this->averageActivityDuration;
    }

    public function setAverageActivityDuration(DateInterval $averageActivityDuration): self
    {
        $this->averageActivityDuration = $averageActivityDuration;
        return $this;
    }
}
