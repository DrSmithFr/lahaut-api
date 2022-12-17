<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

trait TimestampableTrait
{
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    #[JMS\Groups(['timestampable'])]
    private ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    #[JMS\Groups(['timestampable'])]
    private ?DateTime $updatedAt = null;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
