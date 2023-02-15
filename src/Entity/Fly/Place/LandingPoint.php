<?php

namespace App\Entity\Fly\Place;

use App\Entity\Interfaces\Serializable;
use App\Repository\Fly\Place\LandingRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: LandingRepository::class)]
class LandingPoint extends PlacePoint implements Serializable
{
}
