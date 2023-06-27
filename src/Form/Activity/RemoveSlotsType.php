<?php

namespace App\Form\Activity;

use App\Entity\Activity\Slot;
use App\Model\Activity\RemoveSlotsModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemoveSlotsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'slots',
                EntityType::class,
                [
                    'class'    => Slot::class,
                    'multiple' => true,
                    'choice_value' => 'id',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => RemoveSlotsModel::class,
            'csrf_protection' => false,
        ]);
    }
}
