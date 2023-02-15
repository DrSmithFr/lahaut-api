<?php

namespace App\Model\Fly;

use App\Entity\Fly\Place\LandingPoint;
use App\Entity\Fly\Place\MeetingPoint;
use App\Entity\Fly\Place\TakeOffPoint;
use App\Enum\FlyTypeEnum;
use DateInterval;
use DateTimeImmutable;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
class SlotModel
{
    #[OA\Property(
        description: 'Meeting point Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private MeetingPoint $meetingPoint;

    #[OA\Property(
        description: 'Take off Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private TakeOffPoint $takeOffPoint;

    #[OA\Property(
        description: 'Landing Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private LandingPoint $landingPoint;

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
    #[JMS\SerializedName("meetingPoint")]
    public function getMeetingPointUuid(): string
    {
        return $this->getMeetingPoint()->getUuid();
    }

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("takeOffPoint")]
    public function getTakeOffPointUuid(): string
    {
        return $this->getTakeOffPoint()->getUuid();
    }

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("landingPoint")]
    public function getLandingPointUuid(): string
    {
        return $this->getLandingPoint()->getUuid();
    }

    public function getMeetingPoint(): MeetingPoint
    {
        return $this->meetingPoint;
    }

    public function setMeetingPoint(MeetingPoint $meetingPoint): self
    {
        $this->meetingPoint = $meetingPoint;
        return $this;
    }

    public function getTakeOffPoint(): TakeOffPoint
    {
        return $this->takeOffPoint;
    }

    public function setTakeOffPoint(TakeOffPoint $takeOffPoint): self
    {
        $this->takeOffPoint = $takeOffPoint;
        return $this;
    }

    public function getLandingPoint(): LandingPoint
    {
        return $this->landingPoint;
    }

    public function setLandingPoint(LandingPoint $landingPoint): self
    {
        $this->landingPoint = $landingPoint;
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
