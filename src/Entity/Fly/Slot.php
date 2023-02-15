<?php

namespace App\Entity\Fly;

use App\Entity\Fly\Place\LandingPoint;
use App\Entity\Fly\Place\MeetingPoint;
use App\Entity\Fly\Place\TakeOffPoint;
use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Repository\Fly\SlotRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot implements Serializable
{
    use IdTrait;

    #[ORM\JoinColumn(name: 'monitor_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'slots')]
    private User $monitor;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'meeting_point_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: MeetingPoint::class)]
    #[OA\Property(
        description: 'Meeting point Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private MeetingPoint $meetingPoint;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'take_off_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: TakeOffPoint::class)]
    #[OA\Property(
        description: 'Take off Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private TakeOffPoint $takeOffPoint;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'landing_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: LandingPoint::class)]
    #[OA\Property(
        description: 'Landing Uuid',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private LandingPoint $landingPoint;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    #[OA\Property(
        description: 'End at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $endAt;

    #[JMS\Expose]
    #[JMS\Type("DateInterval<'PT%iM'>")]
    #[ORM\Column(type: Types::DATEINTERVAL)]
    #[OA\Property(
        description: 'Average fly duration',
        type: 'string',
        example: 'PT1M'
    )]
    private DateInterval $averageFlyDuration;

    #[JMS\Expose]
    #[JMS\Type(FlyTypeEnum::class)]
    #[ORM\Column(type: Types::STRING, enumType: FlyTypeEnum::class)]
    #[OA\Property(
        description: 'Fly type',
        type: 'string',
        example: 'discovery|freestyle|kid'
    )]
    private FlyTypeEnum $type;

    #[ORM\OneToMany(
        mappedBy: 'slot',
        targetEntity: SlotLock::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $locks;

    #[ORM\OneToOne(mappedBy: 'slot', targetEntity: Booking::class)]
    private Booking $booking;

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("monitor")]
    #[OA\Property(
        description: 'Monitor Uuid',
        type: 'string',
        example: '1ed82229-3199-6552-afb9-5752dd505444'
    )]
    public function getMonitorUuid(): ?string
    {
        return $this->getMonitor()?->getUuid();
    }

    public function getMonitor(): User
    {
        return $this->monitor;
    }

    public function setMonitor(User $monitor): self
    {
        $this->monitor = $monitor;
        return $this;
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

    public function getLocks(): Collection
    {
        return $this->locks;
    }

    public function setLocks(Collection $locks): self
    {
        $this->locks = $locks;
        return $this;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking): self
    {
        $this->booking = $booking;
        return $this;
    }
}
