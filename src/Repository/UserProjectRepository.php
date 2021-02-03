<?php

namespace App\Repository;

use App\Entity\UserProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProject[]    findAll()
 * @method UserProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserProject::class);
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

    public function getLastThreeYearsProjects($id,$year)
    {

        return $this->createQueryBuilder('up')
            ->andWhere('up.user=:userId')
            ->innerJoin('up.budgets','b',Join::WITH,'YEAR(b.year)>=:beginYear AND YEAR(b.year)<=:endYear')
            ->addSelect('b')
            ->innerJoin('up.project','p')
            ->addSelect('p')
            ->setParameter('userId',$id)
            ->setParameter('beginYear',$year-3)
            ->setParameter('endYear',$year-1)
            ->getQuery()
            ->getResult();

    }
}
