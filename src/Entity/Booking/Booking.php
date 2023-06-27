<?php

namespace App\Entity\Booking;

use App\Entity\Slot\Slot;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Enum\BookingStatusEnum;
use App\Repository\Slot\SlotRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Common\Model\Entity\VideoSettings
 *
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="booking_unique",
 *            columns={"monitor", "slot_id"})
 *    }
 * )
 */
#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Booking
{
    use IdTrait;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'customer_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookings')]
    private User $customer;

    #[JMS\Groups(groups: ['slot'])]
    #[ORM\JoinColumn(name: 'slot_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\OneToOne(inversedBy: 'booking', targetEntity: Slot::class)]
    private Slot $slot;

    #[JMS\Expose]
    #[ORM\Column(type: Types::STRING, nullable: false, enumType: BookingStatusEnum::class)]
    private BookingStatusEnum $status;

    public function __construct()
    {
        $this->setStatus(BookingStatusEnum::DRAFT);
    }

    #[JMS\VirtualProperty]
    #[JMS\SerializedName('id')]
    #[JMS\Expose]
    public function serializedId(): ?int
    {
        return $this->getId();
    }

    public function getCustomer(): User
    {
        return $this->customer;
    }

    public function setCustomer(User $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    public function getSlot(): Slot
    {
        return $this->slot;
    }

    public function setSlot(Slot $slot): self
    {
        $this->slot = $slot;
        return $this;
    }

    public function getStatus(): BookingStatusEnum
    {
        return $this->status;
    }

    public function setStatus(BookingStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }
}
