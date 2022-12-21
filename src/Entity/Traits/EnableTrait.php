<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

trait EnableTrait
{
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[JMS\Exclude]
    protected ?bool $enable = false;

    public function getEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(?bool $enable): self
    {
        $this->enable = $enable;
        return $this;
    }
}
