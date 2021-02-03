<?php
namespace App\Form;


use App\Entity\JournalFactor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalFactorEmbeddedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year',DateType::class,[
                'widget'=>'single_text',
                'format'=>'yyyy'
            ])
            ->add('AIS')
            ->add('impactFactor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JournalFactor::class
        ]);
    }

}