<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User\Identity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('phone', TextType::class)
            ->add(
                'anniversary',
                DateType::class,
                [
                    'input' => 'datetime_immutable',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                ]
            )
            ->add(
                'nationality',
                ChoiceType::class,
                [
                    'choices' => [
                        'French' => 'fr',
                        'English' => 'en',
                        'Spanish' => 'es',
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Identity::class,
            'csrf_protection' => false,
        ]);
    }
}
