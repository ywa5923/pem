<?php

namespace App\Form;

use App\Entity\UserProject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProjectEmbeddedType extends AbstractType
{
    const DIRECTOR='director';
    const RESPONSABLE='responsabil';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserSelectTextType::class)
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    "Choose user role"=>null,
                    "Director"=>self::DIRECTOR,
                    "Responsable"=>self::RESPONSABLE
                ]
            ])
            ->add('budgets',CollectionType::class,[
                'entry_type'=>BudgetEmbeddedType::class,
                'prototype_name' => '__budget_name__',
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserProject::class,
        ]);
    }
}
