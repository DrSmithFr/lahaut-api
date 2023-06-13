<?php

namespace App\Entity\Fly;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
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
#[ORM\UniqueConstraint(
    name: 'slot_unique_idx',
    columns: ['monitor_uuid', 'fly_type', 'start_at', 'end_at']
)]
class Slot implements Serializable
{
    use IdTrait;

    #[JMS\Expose]
    #[JMS\Groups(groups: ['monitor'])]
    #[ORM\JoinColumn(name: 'monitor_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'slots')]
    private User $monitor;

    #[JMS\Expose]
    #[ORM\Column(type: Types::FLOAT)]
    #[OA\Property(
        description: 'fly price',
        type: 'string',
        example: '130.00'
    )]
    private float $price;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'fly_type', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: FlyType::class)]
    #[OA\Property(
        description: 'Fly Type Identifier',
        type: 'string',
        example: '123e4567-e89b-12d3-a456-426614174000'
    )]
    private FlyType $flyType;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $startAt;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
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

    #[JMS\Exclude]
    #[ORM\OneToMany(
        mappedBy: 'slot',
        targetEntity: SlotLock::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $locks;

    #[JMS\Expose]
    #[JMS\Groups(['booking'])]
    #[ORM\OneToOne(mappedBy: 'slot', targetEntity: Booking::class)]
    private Booking|null $booking;

    #[JMS\VirtualProperty]
    #[JMS\SerializedName('id')]
    #[JMS\Expose]
    public function serializedId(): ?int
    {
        return $this->getId();
    }

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\SerializedName('flyLocation')]
    public function getFlyLocation(): ?FlyLocation
    {
        return $this->getFlyType()->getFlyLocation();
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

    public function getLocks(): Collection
    {
        return $this->locks;
    }

    public function setLocks(Collection $locks): self
    {
        $this->locks = $locks;
        return $this;
    }

    public function getBooking(): Booking|null
    {
        return $this->booking;
    }

    public function setBooking(Booking|null $booking): self
    {
        $this->booking = $booking;
        return $this;
    }
}
