<?php

namespace App\Controller\Admin\User;

use App\Controller\Admin\AdminCrudController;
use App\Entity\User;
use App\Enum\RoleEnum;
use App\Service\User\UserService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use RuntimeException;

class UserCrudController extends AdminCrudController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

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

        yield BooleanField::new('enable');

        yield DateTimeField::new('deletedAt');

        yield EmailField::new('email');

        yield TextField::new('plainPassword')
            ->onlyWhenCreating();

        $roles = ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->renderExpanded();

        if ($this->getUser()->hasRole(RoleEnum::SUPER_ADMIN)) {
            $roles->setChoices(RoleEnum::cases());
        } else {
            $roles->setChoices(
                array_filter(RoleEnum::cases(), fn($case) => $case !== RoleEnum::SUPER_ADMIN)
            );
        }

        yield $roles;
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
        return $actions
            ->add(Action::INDEX, Action::DETAIL)
            ->add(Action::EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('enable'))
            ->add(
                ChoiceFilter::new('roles')
                    ->setChoices([
                        'Customer' => RoleEnum::CUSTOMER->value,
                        'Monitor' => RoleEnum::MONITOR->value,
                        'Admin' => RoleEnum::ADMIN->value,
                        'Super Admin' => RoleEnum::SUPER_ADMIN->value,
                    ])
                    ->canSelectMultiple()
            );
    }

    public function createEntity(string $entityFqcn)
    {
        $user = new User();

        $user->setMessages(new ArrayCollection());
        $user->setSlots(new ArrayCollection());
        $user->setBookings(new ArrayCollection());

        $user->setCreatedAt(new DateTime());
        $user->setUpdatedAt(new DateTime());

        $user->setPassword('');
        $user->setPlainPassword($this->randomPassword());

        return $user;
    }

    private function randomPassword(): string
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $password = [];

        for ($i = 0; $i < 20; $i++) {
            $n = rand(0, strlen($alphabet) - 1);
            $password[$i] = $alphabet[$n];
        }

        return implode($password);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        $this->userService->updatePassword($entityInstance);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    // For soft delete
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            throw new RuntimeException('UserCrudController::deleteEntity() only accepts User instances.');
        }

        if ($entityInstance->getDeletedAt() === null) {
            $entityInstance->setDeletedAt(new DateTime());
        } else {
            $entityInstance->setDeletedAt();
        }

        $entityManager->flush();
    }
}
