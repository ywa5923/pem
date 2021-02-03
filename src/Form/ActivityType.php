<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserSelectTextType::class)
            ->add('title')
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    "Choose Activity Type"=>null,
                    "A1"=>"A1",
                    "A2"=>"A2",
                    "A3"=>"A3",
                    "A4"=>"A4",
                    "A5"=>"A5",
                    "A6"=>"A6",
                    "A7"=>"A7",
                    "A8"=>"A8",
                    "A9"=>"A9",
                    "A10"=>"A10",
                ]
            ])
            ->add('year',DateType::class,[
                //'widget' => 'single_text',
                //'format' => 'yyyy'
            ])
            ->add('description',TextareaType::class,[
                'attr'=>[
                    'rows'=>10
                ]
            ])
            ->add('points')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
