<?php

namespace App\Controller\Admin\Place;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Activity\Place\PlacePoint;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

abstract class PlacePointCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlacePoint::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ...$this->configurePlaceFields(),
            ...$this->configureAddressFields(),
        ];
    }

    private function configurePlaceFields(): iterable
    {
        yield FormField::addPanel('Place Details')
            ->setIcon('map-location');

        yield IdField::new('uuid')
            ->hideOnForm()
            ->hideOnIndex();

        yield TextField::new('identifier');
        yield TextField::new('name');

        yield NumberField::new('latitude')
            ->setNumDecimals(8);

        yield NumberField::new('longitude')
            ->setNumDecimals(8);

        yield TextField::new('description')
            ->hideOnIndex();
    }

    private function configureAddressFields(): iterable
    {
        yield FormField::addPanel('Address')
            ->setIcon('map-marker-alt');

        yield TextField::new('address.street')
            ->setLabel('Street')
            ->hideOnIndex();

        yield TextField::new('address.zipCode')
            ->setLabel('Zip code')
            ->hideOnIndex();

        yield TextField::new('address.city')
            ->setLabel('City')
            ->hideOnIndex();

        yield CountryField::new('address.country')
            ->setLabel('Country')
            ->hideOnIndex();
    }
}
