<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        parent::configureCrud($crud);

        return $crud
            ->setSearchFields(['email', 'identity.firstName', 'identity.lastName', 'identity.phone'])
            ->setAutofocusSearch();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ...$this->configureUserFields(),
            ...$this->configureIdentityFields(),
            ...$this->configureAddressFields(),
            ...$this->configureBillingAddressFields(),
            ...$this->configureOtherFields(),
        ];
    }

    private function configureUserFields(): iterable
    {
        yield FormField::addPanel('User Details')
            ->setIcon('user');

        yield IdField::new('uuid')
            ->hideOnForm()
            ->hideOnIndex();

        yield EmailField::new('email');

        yield ArrayField::new('roles')
            ->onlyOnDetail();
    }

    private function configureIdentityFields(): iterable
    {
        yield FormField::addPanel('Contact')
            ->setIcon('phone');

        yield TextField::new('identity.firstName')
            ->setLabel('First name');

        yield TextField::new('identity.lastName')
            ->setLabel('Last name');

        yield DateField::new('identity.anniversary')
            ->setLabel('Anniversary')
            ->hideOnIndex();

        yield TelephoneField::new('identity.phone')
            ->setLabel('Mobile');

        yield CountryField::new('identity.nationality')
            ->setLabel('Nationality')
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

    private function configureBillingAddressFields(): iterable
    {
        yield FormField::addPanel('Billing Address')
            ->setIcon('map-marker-alt');

        yield TextField::new('billing.street')
            ->setLabel('Street')
            ->hideOnIndex();

        yield TextField::new('billing.zipCode')
            ->setLabel('Zip code')
            ->hideOnIndex();

        yield TextField::new('billing.city')
            ->setLabel('City')
            ->hideOnIndex();

        yield CountryField::new('billing.country')
            ->setLabel('Country')
            ->hideOnIndex();
    }

    private function configureOtherFields(): iterable
    {
        yield FormField::addPanel('Other Infos')
            ->onlyOnDetail();

        yield TextField::new('password')
            ->onlyOnDetail();

        yield TextField::new('salt')
            ->onlyOnDetail();

        yield TextField::new('passwordResetToken')
            ->onlyOnDetail();

        yield DateTimeField::new('passwordResetTokenValidUntil')
            ->onlyOnDetail();

        yield DateTimeField::new('createdAt')->onlyOnDetail();
        yield DateTimeField::new('updatedAt')->onlyOnDetail();
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->add(Action::INDEX, Action::DETAIL)
            ->add(Action::EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN');
        return $actions;
    }
}
