<?php

namespace App\Model\Chat;

use App\Entity\User;
use OpenApi\Attributes as OA;

class NewConversationModel
{
    /**
     * @var User[]
     */
    #[OA\Property(
        description: 'List of user UuiD',
        type: 'string[]',
        example: '["1ed82229-3199-6552-afb9-5752dd505444"]'
    )]
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
