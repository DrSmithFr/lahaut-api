<?php

namespace App\Entity\Chat;

use App\Entity\Interfaces\Serializable;
use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Repository\Chat\MessageRepository;
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

    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messages')]
    private User $user;

    #[ORM\JoinColumn(name: 'conversation_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    private Conversation $conversation;

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("user")]
    #[OA\Property(
        description: 'User Uuid',
        type: 'string',
        example: '1ed82229-3199-6552-afb9-5752dd505444'
    )]
    public function getUserUuid(): ?string
    {
        return $this->getUser()?->getUuid();
    }

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
}
