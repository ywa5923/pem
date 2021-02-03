<?php

namespace App\Form;

use App\Entity\Citation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserSelectTextType::class)
            ->add('year',DateType::class,[
               'widget'=>'single_text',
                'format'=>'yyyy'
            ])
            ->add('wosCitations')
            ->add('printScreenUrl')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Citation::class,
        ]);
    }
}
