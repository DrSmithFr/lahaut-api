<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;

trait BlamableTrait
{
    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'create')]
    #[JMS\Groups(['blameable'])]
    private ?string $createdBy = null;

    #[ORM\Column(nullable: true)]
    #[Gedmo\Blameable(on: 'update')]
    #[JMS\Groups(['blameable'])]
    private ?string $updatedBy = null;

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
}
