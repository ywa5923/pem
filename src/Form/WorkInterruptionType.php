<?php

namespace App\Form;

use App\Entity\WorkInterruption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkInterruptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserSelectTextType::class)
            ->add('description',TextareaType::class,[
                'attr'=>[
                    'rows'=>10
                ]
            ])
            ->add('beginDate')
            ->add('endDate')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkInterruption::class,
        ]);
    }
}
