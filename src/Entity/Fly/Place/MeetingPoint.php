<?php

namespace App\Entity\Fly\Place;

use App\Entity\Interfaces\Serializable;
use App\Repository\Fly\Place\MeetingPointRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: MeetingPointRepository::class)]
class MeetingPoint extends PlacePoint implements Serializable
{
}
