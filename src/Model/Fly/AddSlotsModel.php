<?php

namespace App\Model\Fly;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
class AddSlotsModel
{
    /**
     * @var Collection<SlotModel>
     */
    #[JMS\Expose]
    #[JMS\Type('ArrayCollection<App\Model\Fly\SlotModel>')]
    private Collection $slots;

    #[JMS\Expose]
    private bool $overwrite = false;

    #[JMS\Expose]
    private bool $wipe = false;

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

    public function isOverwrite(): bool
    {
        return $this->overwrite;
    }

    public function setOverwrite(bool $overwrite): self
    {
        $this->overwrite = $overwrite;
        return $this;
    }

    public function isWipe(): bool
    {
        return $this->wipe;
    }

    public function setWipe(bool $wipe): self
    {
        $this->wipe = $wipe;
        return $this;
    }
}
