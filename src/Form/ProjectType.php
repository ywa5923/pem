<?php

namespace App\Form;

use App\Entity\Project;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{


    const NATIONAL='national';
    const INTERNATIONAL='international';
    const ECONOMIC='economic';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('beginDate',DateType::class,[
                'widget'=>'single_text'
            ])
            ->add('endDate',DateType::class,[
                'widget'=>'single_text'
            ])
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    'Chose Project Type'=>null,
                    'National'=>self::NATIONAL,
                    'International'=>self::INTERNATIONAL,
                    'Economic'=>self::ECONOMIC
                ]
            ])
            ->add('contract')
            ->add('category')
            ->add('description',TextareaType::class,[
                'attr'=>[
                    'rows'=>4
                ]
            ])
            ->add('projectUsers',CollectionType::class,[
                'entry_type'=>UserProjectEmbeddedType::class,
                'prototype_name' => '__userProjectEmbedded_name__',
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
