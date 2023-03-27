<?php

namespace App\Form\Fly;

use App\Entity\Fly\FlyLocation;
use App\Enum\FlyTypeEnum;
use App\Model\Fly\SlotModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'flyLocation',
                EntityType::class,
                [
                    'class' => FlyLocation::class,
                ]
            )
            ->add(
                'startAt',
                DateTimeType::class,
                [
                    'widget'       => 'single_text',
                    'input'        => 'datetime_immutable',
                    'input_format' => 'yyyy-MM-dd HH:mm',
                ]
            )
            ->add(
                'endAt',
                DateTimeType::class,
                [
                    'widget'       => 'single_text',
                    'input'        => 'datetime_immutable',
                    'input_format' => 'yyyy-MM-dd HH:mm',
                ]
            )
            ->add(
                'averageFlyDuration',
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
            )
            ->add(
                'type',
                EnumType::class,
                [
                    'class' => FlyTypeEnum::class,
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
