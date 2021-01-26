<?php

namespace App\Repository;

use App\Entity\Subreddit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subreddit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subreddit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subreddit[]    findAll()
 * @method Subreddit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubredditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subreddit::class);
    }

    /**
     * return a subreddit by its name
     *
     * @param string $title
     * @return Subreddit|null
     */
    public function findOneByTitle($title): ?Subreddit
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function search($search, $first, $limit){
        return $this->createQueryBuilder('s')
        ->andWhere('s.title LIKE :search')
        ->orderBy('s.userCounter', 'DESC')
        ->setParameter('search', '%'.$search.'%')
        ->setFirstResult($first)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }

    // /**
    //  * @return Subreddit[] Returns an array of Subreddit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subreddit
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
