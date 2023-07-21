<?php

namespace App\Controller\Admin\Slot;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Slot\Slot;
use App\Field\DateIntervalField;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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

        yield DateIntervalField::new('averageActivityDuration')
            ->setFormTypeOptions([
                'with_minutes' => true,
                'with_hours' => false,
                'with_years' => false,
                'with_months' => false,
                'with_days' => false,
                'with_weeks' => false,
                'with_seconds' => false,
            ])
            ->hideOnIndex();

        yield AssociationField::new('booking')
            ->hideOnForm();
    }

    public function createEntity(string $entityFqcn)
    {
        return (new Slot())
            ->setLocks(new ArrayCollection());
    }
}
