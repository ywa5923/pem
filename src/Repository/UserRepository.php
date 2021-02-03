<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getWithSearchQueryBuilder(?string $searchTerm, int $limit = 20): QueryBuilder
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.scientificTitles', 'sf')
            ->addSelect('sf');

        if ($searchTerm) {
            $qb->andWhere('u.firstName LIKE :term OR u.lastName LIKE :term OR u.middleName LIKE :term OR u.email LIKE :term')
                ->setParameter(':term', '%' . $searchTerm . '%');

        }

       return $qb->setMaxResults($limit);
    }

    public function getUsersForWosScrapping($articleType, $scrapperToken)
    {
        return $this->getScientistsQB()
            ->innerJoin('u.identificators', 'idn')
            ->addSelect('idn')
            ->andWhere('idn.type=:type')
            ->andWhere('u.scrapperToken !=:token')
            ->setParameter('type', $articleType)
            ->setParameter('token', $scrapperToken)
            ->getQuery()
            ->getResult();
    }

    public function getScientistsQB()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('sf.grade=:CS1 OR 
            sf.grade=:CS2 OR 
            sf.grade=:CS3 OR
             sf.grade=:ACS OR
             sf.grade=:CS
              ')
            ->andWhere('u.isRetired=0 or u.isRetired is null')
            ->innerJoin('u.scientificTitles', 'sf')
            ->addSelect('sf')
            ->orderBy('sf.grade', 'DESC')
            ->setParameter('CS', 'CERCET STIINT')
            ->setParameter('CS1', 'CERCET ST GR1')
            ->setParameter('CS2', 'CERCET ST GR2')
            ->setParameter('CS3', 'CERCET ST GR3')
            ->setParameter('ACS', 'AS CERCET ST');
    }

    public function getScientists()
    {
        return $this->getScientistsQB()
            ->getQuery()
            ->getResult();
    }

    public function getUserArticlesWithJoin($userID, $year)
    {

        //not used
        return $this->createQueryBuilder('u')
            ->andWhere('u.id=:id')
            ->innerJoin('u.userArticles', 'ua')
            ->addSelect('ua')
            ->innerJoin('ua.article', 'article',
                Join::WITH,
                'YEAR(article.publicationDate)>=:beginYear AND YEAR(article.publicationDate)<=:endYear  ')
            ->addSelect('article')
            ->setParameter('id', $userID)
            ->setParameter('beginYear', $year - 3)
            ->setParameter('endYear', $year - 1)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getUsersByEmail($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email=:email OR u.secondEmail=:email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }


}
