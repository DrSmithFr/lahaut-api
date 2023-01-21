<?php

namespace App\Entity;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'medias')]
#[ORM\Entity]
#[JMS\ExclusionPolicy('all')]
class Media implements Serializable
{
    use UuidTrait;

    use TimestampableEntity;
    use BlameableEntity;
    use SoftDeleteableEntity;

    #[JMS\Expose]
    #[ORM\Column(name: 'content_type', length: 255, nullable: true)]
    private ?string $contentType = null;

    #[JMS\Expose]
    #[ORM\Column(name: 'size', nullable: true)]
    private ?int $size = null;

    #[JMS\Expose]
    #[JMS\Type('string')]
    #[ORM\Column(length: 255)]
    private ?string $extension = null;

    #[JMS\Expose]
    #[JMS\Type('string')]
    #[ORM\Column(length: 1024)]
    private ?string $key = null;

    /**
     * This is just a temporary file holder, for file uploads through a form.
     * @var UploadedFile|File|SplFileInfo|null
     */
    #[Assert\File(maxSize: 10_000_000)]
    private ?SplFileInfo $file = null;

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function setFile(?SplFileInfo $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }
}
