<?php

namespace App\Model\Fly;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;

class AddSlotsModel
{
    /**
     * @var Collection<SlotModel>
     */
    #[OA\Property(description: 'List of slot', type: 'Collection<SlotModel>')]
    private Collection $slots;

    public function __construct()
    {
        $this->slots = new ArrayCollection();
    }

    /**
     * @return Collection<SlotModel>
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    /**
     * @param Collection<SlotModel> $slots
     * @return self
     */
    public function setSlots(Collection $slots): self
    {
        $this->slots = $slots;
        return $this;
    }

    public function addSlot(mixed $slot): self
    {
        $this->slots->add($slot);
        return $this;
    }

    public function removeSlot(mixed $slot): self
    {
        $this->slots->removeElement($slot);
        return $this;
    }
}
