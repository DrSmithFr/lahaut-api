<?php

namespace App\Entity\Activity\Place;

use App\Entity\_Interfaces\Serializable;
use App\Repository\Activity\Place\TakeOffRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: TakeOffRepository::class)]
class TakeOffPoint extends PlacePoint implements Serializable
{
}
