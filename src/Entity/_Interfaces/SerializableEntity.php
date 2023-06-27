<?php

namespace App\Entity\_Interfaces;

/**
 * Explicitly mark entities that can be serialized.
 */
interface SerializableEntity extends Serializable
{
    public function getIdentifier(): string;
}
