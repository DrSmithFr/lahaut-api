<?php

namespace App\Controller\Admin\Activity;

use App\Controller\Admin\Place\LandingPointCrudController;
use App\Controller\Admin\Place\MeetingPointCrudController;
use App\Controller\Admin\Place\TakeOffPointCrudController;
use App\Entity\Activity\ActivityLocation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ActivityLocationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ActivityLocation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Location Details')
            ->setIcon('map-location');

        yield IdField::new('uuid')
            ->hideOnForm()
            ->hideOnIndex();

        yield TextField::new('identifier');
        yield TextField::new('name');

        yield FormField::addPanel('Places')
            ->setIcon('map-marker-alt');

        yield AssociationField::new('meetingPoint')
            ->setCrudController(MeetingPointCrudController::class)
            ->autocomplete()
            ->hideOnIndex();

        yield AssociationField::new('takeOffPoint')
            ->setCrudController(TakeOffPointCrudController::class)
            ->autocomplete()
            ->hideOnIndex();

        yield AssociationField::new('landingPoint')
            ->setCrudController(LandingPointCrudController::class)
            ->autocomplete()
            ->hideOnIndex();
    }
}
