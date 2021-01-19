<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
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
    /**
     * return all posts ordered by date
     *
     * @return Post[]
     */
    public function findAllOrderByDate()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * return all posts of an user
     *
     * @param User $user
     * @return Post[]
     */
    public function findAllByUser($user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->orderBy('p.created_at', 'DESC')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * return all posts from the subreddit
     *
     * @param Subreddit $subreddit
     * @return void
     */
    public function findAllBySubreddit($subreddit)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.subreddit = :subreddit')
            ->orderBy('p.created_at', 'DESC')
            ->setParameter('subreddit', $subreddit)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Post
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
