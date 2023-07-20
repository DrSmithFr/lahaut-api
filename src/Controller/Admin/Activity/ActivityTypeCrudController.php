<?php

namespace App\Controller\Admin\Activity;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Activity\ActivityType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ActivityTypeCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActivityType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('identifier');
        yield TextField::new('name');

        yield AssociationField::new('activityLocation')
            ->setCrudController(ActivityLocationCrudController::class)
            ->autocomplete()
            ->hideOnIndex();
    }
}
