<?php

declare(strict_types=1);

namespace App\Form\Register;

use App\Model\Register\RegisterMonitorModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterMonitorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('firstname')
            ->add('lastname')
            ->add('phone');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterMonitorModel::class,
            'csrf_protection' => false,
        ]);
    }
}
