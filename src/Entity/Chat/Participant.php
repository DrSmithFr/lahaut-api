<?php

namespace App\Entity\Chat;

use App\Entity\Traits\IdTrait;
use App\Entity\User;
use App\Repository\Chat\ParticipantRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    use IdTrait;

    #[JMS\Expose]
    #[ORM\JoinColumn(name: 'user_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    private ?User $user;

    #[ORM\JoinColumn(name: 'conversation_uuid', referencedColumnName: 'uuid')]
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'participants')]
    private ?Conversation $conversation;

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

        if ($conversation !== null) {
            $this->conversation->addParticipant($this);
        }

        return $this;
    }
}
