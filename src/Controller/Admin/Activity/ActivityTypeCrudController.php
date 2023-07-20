<?php

namespace App\Controller\Admin\Activity;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Activity\ActivityType;

class ActivityTypeCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActivityType::class;
    }
}
