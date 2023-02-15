<?php

namespace App\Entity\Fly;

use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Repository\Fly\SlotLockRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: SlotLockRepository::class)]
class SlotLock
{
    use IdTrait;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'customer_uuid', referencedColumnName: 'uuid', nullable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $customer;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'slot_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Slot::class, inversedBy: 'locks')]
    private Slot $slot;

    #[JMS\Expose]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $until;

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

    public function getUntil(): DateTimeImmutable
    {
        return $this->until;
    }

    public function setUntil(DateTimeImmutable $until): self
    {
        $this->until = $until;
        return $this;
    }
}
