<?php

namespace App\Controller\Admin\Place;

use App\Entity\Activity\Place\LandingPoint;

class LandingPointCrudController extends PlacePointCrudController
{
    public static function getEntityFqcn(): string
    {
        return LandingPoint::class;
    }
}
