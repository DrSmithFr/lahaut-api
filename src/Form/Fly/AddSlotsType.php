<?php

namespace App\Form\Fly;

use App\Model\Fly\AddSlotsModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddSlotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'slots',
                CollectionType::class,
                [
                    'entry_type' => SlotType::class,
                    'allow_add' => true,
                ]
            )
            ->add('overwrite', CheckboxType::class)
            ->add('wipe', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddSlotsModel::class,
            'csrf_protection' => false,
        ]);
    }
}
