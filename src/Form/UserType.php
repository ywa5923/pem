<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('middleName')
            ->add('lastName')
            ->add('email')
            ->add('secondEmail')
            ->add('section')
            ->add('laboratory')
            ->add('isRetired')
            ->add('scrapperToken')
            ->add('emailToken')

            ->add('identificators',CollectionType::class,[
                'entry_type'=>UserIdentificatorEmbeddedType::class,
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false
            ])
            ->add('scientificTitles',CollectionType::class,[
                'entry_type'=>UserScientificTitleEmbeddedType::class,
                'allow_add'=>true,
                'allow_delete'=>true,
                'by_reference'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
