<?php

namespace App\Controller\Admin\Slot;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Slot\SlotProposed;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class SlotProposedCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return SlotProposed::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('activityType')
            ->autocomplete();

        yield TimeField::new('startAt');
        yield TimeField::new('endAt');

        yield TextField::new('duration');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add('activityType');
    }
}
