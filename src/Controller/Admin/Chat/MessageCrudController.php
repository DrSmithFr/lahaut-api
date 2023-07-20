<?php

namespace App\Controller\Admin\Chat;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Chat\Message;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

class MessageCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Message::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('conversation');

        yield AssociationField::new('user')
            ->autocomplete();

        yield Field::new('content');

        yield DateTimeField::new('sentAt');

        yield DateTimeField::new('createdAt')
            ->onlyOnDetail();

        yield DateTimeField::new('updatedAt')
            ->onlyOnDetail();
    }

    public function createEntity(string $entityFqcn)
    {
        $product = new Message();

        $product->setCreatedAt(new DateTime());
        $product->setUpdatedAt(new DateTime());

        return $product;
    }
}
