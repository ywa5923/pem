<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\ArticleAuthorEbeddedType;

class ArticleType extends AbstractType
{

    const PATENT='patent';
    const SCIENTIFIC_PAPER='scientific_paper';
    const BOOK ='book';
    const BREVET_NATIONAL_DEPUS='BND';
    const BREVET_NATIONAL_ACORDAT='BNA';
    const BREVET_INTERNATIONAL_DEPUS='BID';
    const BREVET_INTERNATIONAL_ACORDAT='BIA';
    const BREVET_NATIONAL_PUBLICAT='BNP';
    const BREVET_INTERNATIONAL_PUBLICAT='BIP';



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $article_type=$options['article_type'];

        //dd($article_type);
        $builder
            ->add('title')
            ->add('type',ChoiceType::class,[
                'required'   => false,
                'choices'=>[
                    'Choose article type'=>null,
                    "Scientific paper"=>self::SCIENTIFIC_PAPER,
                    "Book"=>self::BOOK,
                    "Patent"=>self::PATENT
                ],
            ])
            ->add('pType',ChoiceType::class,[
                'required'   => false,
                'choices'=>[
                    'Choose p type (only for patents)'=>null,
                    "Brevet national (OSIM) depus"=>self::BREVET_NATIONAL_DEPUS,
                    "Brevet national (OSIM) publicat"=>self::BREVET_NATIONAL_PUBLICAT,
                    "Brevet national (OSIM) acordat"=>self::BREVET_NATIONAL_ACORDAT,
                    "Brevet international depus"=>self::BREVET_INTERNATIONAL_DEPUS,
                    "Brevet international publicat"=>self::BREVET_INTERNATIONAL_PUBLICAT,
                    "Brevet international acordat"=>self::BREVET_INTERNATIONAL_ACORDAT
                ],
            ])
            ->add('authors')
            ->add('abstract',TextareaType::class)
            ->add('miscellanous')
            ->add('pages')
            ->add('doi')
            ->add('journal')
            ->add('volume')
            ->add('effectiveAuthorsNumber',NumberType::class,[
                'scale'=>2
            ])
            ->add('AIS')
            ->add('corespondingAuthors',TextareaType::class)
            ->add('emailsOfCorespondingAuthors')
            ->add('primeAuthors')
            ->add('publicationDate',DateType::class,[
                //'widget' => 'single_text',
               // 'format' => 'yyyy'
            ])
            ->add('theNumberOfPrimeAuthors')
            ->add('theNumberOfCorrespondingAuthors')
            ->add('pages')
            ->add('totalPages')
            ->add('thetaFunction')
            ->add('isUpdatedByAdmin')
            ->add('miscellanous')
        ;

        $builder->add('articleAuthors', CollectionType::class, [
            'entry_type' => ArticleAuthorEbeddedType::class,
            'allow_add'=>true,
            'allow_delete'=>true,
            'by_reference'=>false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'article_type'=>null
        ]);
    }
}
