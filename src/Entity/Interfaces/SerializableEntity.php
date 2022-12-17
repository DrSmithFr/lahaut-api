<?php

namespace App\Entity\Interfaces;

interface SerializableEntity
{
    public function getIdentifier(): string;
}
