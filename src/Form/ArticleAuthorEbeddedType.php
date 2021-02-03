<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserArticle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleAuthorEbeddedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserSelectTextType::class)
            ->add('isPrimeAuthor')
            ->add('isCorrespondingAuthor');

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserArticle::class,
        ]);
    }
}
