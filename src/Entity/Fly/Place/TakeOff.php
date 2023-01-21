<?php

namespace App\Entity\Fly\Place;

use App\Entity\Interfaces\Serializable;
use App\Repository\Fly\Place\TakeOffRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy('all')]
#[ORM\Entity(repositoryClass: TakeOffRepository::class)]
class TakeOff extends Place implements Serializable
{
}
