<?php

namespace App\Form;

use App\Entity\Budget;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BudgetEmbeddedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year',DateType::class,[
                'widget' => 'single_text',
                'format' => 'yyyy'
            ])
            ->add('type',ChoiceType::class,[
                'choices'=>[
                    'Select Budget Type'=>null,
                    'Nonprofit'=>Budget::NONPROFIT,
                    'Profit'=>Budget::PROFIT
                ]
            ])
            ->add('budget');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
        ]);
    }
}
