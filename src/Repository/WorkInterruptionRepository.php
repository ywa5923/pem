<?php

namespace App\Repository;

use App\Entity\WorkInterruption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WorkInterruption|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkInterruption|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkInterruption[]    findAll()
 * @method WorkInterruption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkInterruptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WorkInterruption::class);
    }

     /**
      * @return WorkInterruption[] Returns an array of WorkInterruption objects
      *
      */

    public function findByUser($userId,$year)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :userId and YEAR(w.endDate)>=:endYear')
            ->setParameter('userId', $userId)
            ->setParameter('endYear', $year-3)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?WorkInterruption
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
