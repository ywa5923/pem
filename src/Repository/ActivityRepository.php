<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    // /**
    //  * @return Activity[] Returns an array of Activity objects
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
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getWithSearchQueryBuilder(?string $searchTerm):QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.user','u')
            ->addSelect('u');

        if ($searchTerm) {

            $qb->andWhere('a.type LIKE :term OR a.year LIKE :term OR u.firstName LIKE :term OR u.middleName LIKE :term OR u.lastName LIKE :term')

                ->setParameter('term', '%' . $searchTerm . '%')
            ;
        }


      return $qb->orderBy('a.createdAt', 'DESC');



    }

    public function getLastThreeYearsActivities($userId,$year)
    {
        $beginYear=$year-3;
        $endYear=$year-1;
        return $this->createQueryBuilder('a')
            ->andWhere('a.user=:userId')
            ->andWhere('YEAR(a.year)>=:beginYear and YEAR(a.year)<=:endYear')
            ->setParameter('beginYear',$beginYear)
            ->setParameter('endYear',$endYear)
            ->setParameter('userId',$userId)
            ->getQuery()
            ->getResult();
    }




}
