<?php

namespace App\Controller\Admin\Slot;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Slot\Slot;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class SlotCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Slot::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('activityType')
            ->autocomplete();

        yield AssociationField::new('monitor')
            ->autocomplete();

        yield MoneyField::new('price')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);

        yield DateTimeField::new('startAt');
        yield DateTimeField::new('endAt');

        yield Field::new('averageActivityDuration')
            ->hideOnIndex();

        yield AssociationField::new('booking')
            ->hideOnForm();
    }
}
