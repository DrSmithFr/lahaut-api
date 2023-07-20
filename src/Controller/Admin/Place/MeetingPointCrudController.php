<?php

namespace App\Controller\Admin\Place;

use App\Entity\Activity\Place\MeetingPoint;

class MeetingPointCrudController extends PlacePointCrudController
{
    public static function getEntityFqcn(): string
    {
        return MeetingPoint::class;
    }
}
