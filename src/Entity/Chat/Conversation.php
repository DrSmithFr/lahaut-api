<?php

namespace App\Entity\Chat;

use App\Entity\_Interfaces\Serializable;
use App\Entity\Traits\UuidTrait;
use App\Repository\Chat\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ReadableCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Attributes as OA;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation implements Serializable
{
    use UuidTrait;

    #[JMS\Expose]
    #[ORM\OneToOne(targetEntity: Message::class)]
    #[ORM\JoinColumn(name: 'last_message_id', referencedColumnName: 'id')]
    private ?Message $lastMessage;

    #[ORM\OneToMany(
        mappedBy: 'conversation',
        targetEntity: Participant::class,
        cascade: ['persist', 'remove'],
        fetch: 'EAGER'
    )]
    private Collection $participants;

    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    #[ORM\OneToMany(
        mappedBy: 'conversation',
        targetEntity: Message::class,
        cascade: ['remove'],
        fetch: 'EXTRA_LAZY'
    )]
    private Collection $messages;

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('string')]
    #[JMS\SerializedName("id")]
    #[OA\Property(
        description: 'Conversation Uuid',
        type: 'string',
        example: '1ed82229-3199-6552-afb9-5752dd505444'
    )]
    public function getConversationUuid(): ?string
    {
        return $this->getUuid();
    }

    #[JMS\Expose]
    #[JMS\VirtualProperty]
    #[JMS\Type('ArrayCollection<App\Entity\User>')]
    #[JMS\SerializedName("participants")]
    public function getParticipantUuids(): ReadableCollection
    {
        return $this
            ->getParticipants()
            ->map(fn(Participant $participant) => $participant->getUser());
    }

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    /**
     * @return Collection<Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setConversation($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
            // set the owning side to null (unless already changed)
            if ($participant->getConversation() === $this) {
                $participant->setConversation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): void
    {
        $this->lastMessage = $lastMessage;
    }
}
