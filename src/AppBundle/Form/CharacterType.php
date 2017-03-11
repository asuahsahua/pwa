<?php

namespace AppBundle\Form;

use AppBundle\Form\Type\RolesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isNew = false;
        $label = $isNew ? 'Create character' : 'Update character';

        $builder
            ->add('characterName', TextType::class)
            ->add('server', TextType::class)
            ->add('roles', RolesType::class)
            ->add('save', SubmitType::class, ['label' => $label]);
    }
}