<?php

namespace App\Model\Fly;

use App\Entity\Fly\Slot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
class RemoveSlotsModel
{
    /**
     * @var Collection<Slot>
     */
    #[JMS\Expose]
    #[JMS\Type('ArrayCollection<App\Entity\Fly\Slot>')]
    private Collection $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
    }

    /**
     * @return Collection<Slot>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    /**
     * @param Collection<Slot> $slots
     * @return self
     */
    public function setSlots(Collection $slots): self
    {
        $this->slots = $slots;
        return $this;
    }

    public function addSlot(Slot $slot): self
    {
        $this->slots->add($slot);
        return $this;
    }

    public function removeSlot(Slot $slot): self
    {
        $this->slots->removeElement($slot);
        return $this;
    }
}
