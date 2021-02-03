<?php
namespace App\Form;


use App\Entity\UserScientificTitle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserScientificTitleEmbeddedType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginWith',DateType::class,[
            'widget'=>'single_text',

           ])
            ->add('endWith',DateType::class,[
                'widget'=>'single_text',

            ])
            ->add('grade')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserScientificTitle::class
        ]);
    }
}