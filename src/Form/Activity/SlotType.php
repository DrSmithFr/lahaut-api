<?php

namespace App\Form\Activity;

use App\Entity\Activity\ActivityType;
use App\Model\Activity\SlotModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'price',
                NumberType::class,
                [
                    'scale' => 2,
                ]
            )
            ->add(
                'activityType',
                EntityType::class,
                [
                    'class' => ActivityType::class,
                    'choice_value' => 'identifier',
                    'choice_label' => 'name',

                ]
            )
            ->add(
                'startAt',
                TimeType::class,
                [
                    'widget'       => 'single_text',
                    'input'        => 'datetime_immutable',
                    'input_format' => 'HH:mm',
                ]
            )
            ->add(
                'endAt',
                TimeType::class,
                [
                    'widget'       => 'single_text',
                    'input'        => 'datetime_immutable',
                    'input_format' => 'HH:mm',
                ]
            )
            ->add(
                'averageActivityDuration',
                DateIntervalType::class,
                [
                    'widget'       => 'single_text',
                    'with_minutes' => true,

                    'with_hours'   => false,
                    'with_years'   => false,
                    'with_months'  => false,
                    'with_days'    => false,
                    'with_weeks'   => false,
                    'with_seconds' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => SlotModel::class,
            'csrf_protection' => false,
        ]);
    }
}
