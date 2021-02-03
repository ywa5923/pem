<?php

namespace App\Repository;

use App\Entity\UserArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProject[]    findAll()
 * @method UserProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserArticle::class);
    }

    // /**
    //  * @return UserProject[] Returns an array of UserProject objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserProject
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getLastThreeYearsArticles_to_trash($userID,$year,$articleType){

        return   $this->createQueryBuilder('ua')
            ->andWhere('ua.user=:userId')
            ->innerJoin('ua.article','article',
                Join::WITH,
                '((YEAR(article.publicationDate)>=:beginYear AND YEAR(article.publicationDate)<=:endYear))
                  AND article.type=:articleType')

            ->orderBy('article.publicationDate','DESC')
            ->addSelect('article')
            ->setParameter('userId',$userID)
            ->setParameter('beginYear',$year-3)
            ->setParameter('endYear',$year-1)
            ->setParameter('articleType',$articleType)
            ->getQuery()
            ->getResult();

    }
    public function getLastThreeYearsArticles($userID,$year,$articleType)
    {
      return $this->getArticlesByYearsInterval($userID,$year-3,$year-1,$articleType);
    }
    public function getLastFiveYearsArticles($userID,$year,$articleType)
    {
        return $this->getArticlesByYearsInterval($userID,$year-5,$year-1,$articleType);
    }

    public function getArticlesByYearsInterval($userID,$beginYear,$endYear,$articleType)
    {
        return   $this->createQueryBuilder('ua')
            ->andWhere('ua.user=:userId')
            ->innerJoin('ua.article','article',
                Join::WITH,
                '((YEAR(article.publicationDate)>=:beginYear AND YEAR(article.publicationDate)<=:endYear))
                  AND article.type=:articleType')

            ->orderBy('article.publicationDate','DESC')
            ->addSelect('article')
            ->setParameter('userId',$userID)
            ->setParameter('beginYear',$beginYear)
            ->setParameter('endYear',$endYear)
            ->setParameter('articleType',$articleType)
            ->getQuery()
            ->getResult();
    }

}
