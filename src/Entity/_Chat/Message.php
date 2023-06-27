<?php

namespace App\Entity\_Chat;

use App\Entity\_Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Repository\Chat\MessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

/**
 * @ORM\Table(indexes={@Index(name="created_at_index", columns={"created_at"})})
 */
#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message implements Serializable
{
    use IdTrait;
    use TimestampableEntity;

    #[JMS\Expose]
    #[JMS\Type('string')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    private User $user;

    #[ORM\JoinColumn(name: 'conversation_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    private Conversation $conversation;

    #[JMS\Expose]
    #[JMS\Type("DateTimeImmutable<'Y-m-d H:i'>")]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[OA\Property(
        description: 'Start at',
        type: 'string',
        example: '2021-01-01 00:00'
    )]
    private DateTimeImmutable $sentAt;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }

    public function getSentAt(): DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTimeImmutable $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }
}
