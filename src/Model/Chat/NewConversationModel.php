<?php

namespace App\Model\Chat;

use App\Entity\User;
use OpenApi\Attributes as OA;

class NewConversationModel
{
    /**
     * @var User[]
     */
    #[OA\Property(description: 'List of user ID', type: 'string[]', example: '["1", "2"]')]
    private array $users = [];

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param User[] $users
     */
    public function setUsers(array $users): self
    {
        $this->users = $users;
        return $this;
    }
}
