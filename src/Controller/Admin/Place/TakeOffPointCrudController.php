<?php

namespace App\Controller\Admin\Place;

use App\Entity\Activity\Place\TakeOffPoint;

class TakeOffPointCrudController extends PlacePointCrudController
{
    public static function getEntityFqcn(): string
    {
        return TakeOffPoint::class;
    }
}
