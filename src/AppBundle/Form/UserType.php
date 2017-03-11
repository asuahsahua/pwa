<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timezone', TimezoneType::class, [
            'preferred_choices' => [
                'America/New_York',
                'America/Chicago',
                'America/Denver',
                'America/Phoenix',
                'America/Los_Angeles',
                'America/Anchorage',
                'America/Adak',
                'Pacific/Honolulu',
            ],
        ])
            ->add('save', SubmitType::class, ['label' => 'Save Settings']);
    }
}