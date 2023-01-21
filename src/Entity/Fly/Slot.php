<?php

namespace App\Entity\Fly;

use App\Entity\Fly\Place\Landing;
use App\Entity\Fly\Place\MeetingPoint;
use App\Entity\Fly\Place\TakeOff;
use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Enum\FlyTypeEnum;
use App\Repository\Fly\SlotRepository;
use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot implements Serializable
{
    use IdTrait;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'monitor_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'slots')]
    private User $monitor;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'meeting_point_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: MeetingPoint::class)]
    private MeetingPoint $meetingPoint;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'take_off_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: TakeOff::class)]
    private TakeOff $takeOff;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'landing_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: Landing::class)]
    private Landing $landing;

    #[JMS\Expose]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $endAt;

    #[JMS\Expose]
    #[ORM\Column(type: Types::DATEINTERVAL)]
    private DateInterval $averageFlyDuration;

    #[JMS\Expose]
    #[ORM\Column]
    private ?string $type;

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

    public function getTakeOff(): TakeOff
    {
        return $this->takeOff;
    }

    public function setTakeOff(TakeOff $takeOff): self
    {
        $this->takeOff = $takeOff;
        return $this;
    }

    public function getLanding(): Landing
    {
        return $this->landing;
    }

    public function setLanding(Landing $landing): self
    {
        $this->landing = $landing;
        return $this;
    }

    public function getStartAt(): ?DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;
        return $this;
    }

    public function getEndAt(): ?DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?DateTimeImmutable $endAt): self
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        if (!FlyTypeEnum::tryFrom($type)) {
            throw new InvalidArgumentException('Invalid fly type');
        }

        $this->type = $type;
        return $this;
    }
}
