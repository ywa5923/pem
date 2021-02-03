<?php

namespace App\Repository;

use App\Entity\ProjectUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProjectUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectUser[]    findAll()
 * @method ProjectUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectUserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProjectUser::class);
    }

    // /**
    //  * @return ProjectUser[] Returns an array of ProjectUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectUser
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
