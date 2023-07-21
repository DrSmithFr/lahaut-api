<?php

namespace App\Controller\Admin\Slot;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Slot\SlotLock;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class SlotLockCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return SlotLock::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('customer')
            ->autocomplete();

        yield AssociationField::new('slot')
            ->autocomplete();

        yield DateTimeField::new('until');
    }
}
