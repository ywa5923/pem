<?php

namespace App\Repository;

use App\Entity\Journal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Journal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journal[]    findAll()
 * @method Journal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JournalRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Journal::class);
    }

    // /**
    //  * @return Journal[] Returns an array of Journal objects
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
    public function findOneBySomeField($value): ?Journal
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWithSearchQueryBuilder(?string $searchTerm):QueryBuilder
    {
        $qb = $this->createQueryBuilder('j')
            ->leftJoin('j.journalFactors','jf')
            ->addSelect('jf');

        if ($searchTerm) {

            $qb->andWhere('j.name LIKE :term')

                ->setParameter('term', '%' . $searchTerm . '%')
            ;
        }


        return $qb;

    }

    public function getJournalByYear($journal,$y)
    {

        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');

        return $this->createQueryBuilder('j')
            ->andWhere('j.name=:name AND YEAR(jf.year)=:yr')
            ->innerJoin('j.journalFactors','jf')
            ->addSelect('jf')
            ->setParameter('name',$journal)
            ->setParameter('yr',$y)
            ->getQuery()
            ->getOneOrNullResult();

    }
}
