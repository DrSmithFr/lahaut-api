<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use JMS\Serializer\Annotation as JMS;

trait SoftDeletableTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[JMS\Exclude]
    protected ?DateTime $deletedAt = null;

    /**
     * Sets deletedAt.
     * @return $this
     */
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function setDeleted(): self
    {
        $this->setDeletedAt(new DateTime());
        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
