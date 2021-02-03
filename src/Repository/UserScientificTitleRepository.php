<?php

namespace App\Repository;

use App\Entity\UserScientificTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserScientificTitle|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserScientificTitle|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserScientificTitle[]    findAll()
 * @method UserScientificTitle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserScientificTitleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserScientificTitle::class);
    }

    // /**
    //  * @return UserScientificTitle[] Returns an array of UserScientificTitle objects
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
    public function findOneBySomeField($value): ?UserScientificTitle
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
