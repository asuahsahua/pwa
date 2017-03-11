<?php

namespace AppBundle\Form\Type;

use AppBundle\Enums\Roles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('is_dps', CheckboxType::class, ['required' => false]);
        $builder->add('is_heal', CheckboxType::class, ['required' => false]);
        $builder->add('is_tank', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Roles::class,
        ));
    }
}