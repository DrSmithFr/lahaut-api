<?php

namespace App\Controller\Admin\Booking;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Booking\Booking;
use App\Enum\BookingStatusEnum;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class BookingCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield AssociationField::new('customer');

        yield AssociationField::new('slot');

        yield ChoiceField::new('status')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions([
                'class' => BookingStatusEnum::class,
                'choices' => BookingStatusEnum::cases(),
            ])
            ->renderExpanded();
    }

    public function createEntity(string $entityFqcn)
    {
        $booking = new Booking();

        $booking->setCreatedAt(new DateTime());
        $booking->setUpdatedAt(new DateTime());

        return $booking;
    }
}
