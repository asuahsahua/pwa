<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $operation = true ? 'Create' : 'Update';
        $timezone = $options['timezone'];
        $tzCode = (new \DateTime())->setTimezone(new \DateTimeZone($timezone))->format('T');

        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'help' => "A useful, descriptive name",
                ],
            ])
            ->add('location', TextType::class, [
                'attr' => [
                    'help' => "Where the sign-ups will be going",
                ],
            ])
            ->add('slots', IntegerType::class, [
                'attr' => [
                    'help' => "How many you can take - will not prevent signups above this cap",
                ],
            ])
            ->add('start_time', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'view_timezone' => $timezone,
                'attr' => [
                    'help' => "In your configured timezone ({$tzCode})",
                ],
            ])
            ->add('duration_interval', DateIntervalType::class, [
                'label' => 'Duration',
                'with_years' => false,
                'with_months' => false,
                'with_days' => false,
                'with_hours' => true,
                'with_minutes' => true,
                'with_seconds' => false,
                'widget' => 'integer'
            ])
            ->add('save', SubmitType::class, ['label' => "$operation Event"])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'timezone' => 'America/New_York',
        ]);
    }


}