<?php

namespace App\Entity\Activity\Place;

use App\Entity\_Interfaces\Serializable;
use App\Repository\Activity\Place\MeetingPointRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: MeetingPointRepository::class)]
class MeetingPoint extends PlacePoint implements Serializable
{
}
