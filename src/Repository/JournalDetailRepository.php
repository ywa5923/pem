<?php

namespace App\Repository;

use App\Entity\JournalFactor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method JournalFactor|null find($id, $lockMode = null, $lockVersion = null)
 * @method JournalFactor|null findOneBy(array $criteria, array $orderBy = null)
 * @method JournalFactor[]    findAll()
 * @method JournalFactor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalDetailRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JournalFactor::class);
    }

    // /**
    //  * @return JournalDetail[] Returns an array of JournalDetail objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JournalDetail
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
