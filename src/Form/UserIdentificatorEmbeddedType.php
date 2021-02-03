<?php
namespace App\Form;


use App\Entity\WosIdentificator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserIdentificatorEmbeddedType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type',ChoiceType::class,[
            'choices'=>[
                "Select type"=>null,
                "Patent"=>ArticleType::PATENT,
                "Scientific Paper"=>ArticleType::SCIENTIFIC_PAPER,
                "Book"=>ArticleType::BOOK
            ]
        ])
            ->add('identificator')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WosIdentificator::class
        ]);
    }
}