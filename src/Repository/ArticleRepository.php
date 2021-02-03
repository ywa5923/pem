<?php

namespace App\Repository;

use App\Entity\Article;
use App\Form\ArticleType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWithSearchQueryBuilder(string $articleType,?string $searchTerm):QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');

        if ($searchTerm) {

            $qb->andWhere('a.title LIKE :term OR a.authors LIKE :term OR a.journal LIKE :term OR a.doi LIKE :term')
                ->setParameter('term', '%' . $searchTerm . '%')
            ;
        }


        return $qb->andWhere('a.type = :type')
                ->setParameter('type', $articleType)
                ->addOrderBy('a.publicationDate', 'DESC')
               ->addOrderBy('a.title', 'ASC');



    }

    public function findArticlesByIdentifier($identifier,$articleType)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.authors LIKE :identifier AND a.type=:type')
            ->setParameter('identifier', trim($identifier) )
            ->setParameter('type', $articleType)
            ->getQuery()
            ->getResult();
    }

    public function getYearIntervalArticles($beginYear,$endYear,$scrapperToken,$articleType=ArticleType::SCIENTIFIC_PAPER)
    {
       $qb=$this->createQueryBuilder('a');
        return  $qb
            ->andWhere('YEAR(a.publicationDate)>=:beginYear and YEAR(a.publicationDate)<=:endYear')
           ->andWhere('a.type=:articleType')
            ->andWhere('a.scrapperToken != :scrapperToken OR a.scrapperToken IS NULL')
            ->setParameter("beginYear",$beginYear)
            ->setParameter("endYear",$endYear)
            ->setParameter("articleType",$articleType)
            ->setParameter("scrapperToken",$scrapperToken)
            ->getQuery()
            ->getResult();

        /**
         * ->setParameters([
        "beginYear"=>$beginYear,
        "endYear"=>$endYear,
        "articleType"=>$articleType,
        "scrapperToken"=>$scrapperToken
        ])
         */
        //getSQLQuery()

    }



}
