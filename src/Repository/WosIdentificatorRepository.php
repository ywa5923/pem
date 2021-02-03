<?php

namespace App\Repository;

use App\Entity\WosIdentificator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method WosIdentificator|null find($id, $lockMode = null, $lockVersion = null)
 * @method WosIdentificator|null findOneBy(array $criteria, array $orderBy = null)
 * @method WosIdentificator[]    findAll()
 * @method WosIdentificator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WosIdentificatorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, WosIdentificator::class);
    }

    // /**
    //  * @return WosIdentificator[] Returns an array of WosIdentificator objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WosIdentificator
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getIdentifiersByUser($userId,$identifierType)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.user=:userId')
            ->andWhere('i.type=:identifierType')
            ->setParameter('userId',$userId)
            ->setParameter('identifierType',$identifierType)
            ->getQuery()
            ->getResult();
    }


}
