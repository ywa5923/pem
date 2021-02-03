<?php

namespace App\Repository;

use App\Entity\Citation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Citation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Citation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Citation[]    findAll()
 * @method Citation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CitationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Citation::class);
    }

    // /**
    //  * @return Citation[] Returns an array of Citation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Citation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getWithSearchQueryBuilder(?string $searchTerm):QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.user','u')
            ->addSelect('u');

        if ($searchTerm) {

            $qb->andWhere('c.year LIKE :term OR u.firstName LIKE :term OR u.middleName LIKE :term OR u.lastName LIKE :term')

                ->setParameter('term', '%' . $searchTerm . '%')
            ;
        }
        return $qb->orderBy('c.createdAt', 'DESC');
    }

    public function getCitations($userId,$year)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user=:userId and YEAR(c.year)=:year')
            ->setParameter('userId',$userId)
            ->setParameter('year',$year)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
