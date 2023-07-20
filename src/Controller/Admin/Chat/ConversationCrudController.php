<?php

namespace App\Controller\Admin\Chat;

use App\Controller\Admin\AdminCrudController;
use App\Entity\Chat\Conversation;
use App\Form\Chat\NewParticipantType;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ConversationCrudController extends AdminCrudController
{
    public static function getEntityFqcn(): string
    {
        return Conversation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('uuid')
            ->hideOnForm();

        yield CollectionField::new('participants')
            ->setEntryType(NewParticipantType::class)
            ->setEntryIsComplex()
            ->renderExpanded();
    }
}
