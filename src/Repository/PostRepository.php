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
     * return all posts order by date
     *
     * @param int $first
     * @param int $limit
     * @return Post[]
     */
    public function findAllOrderByDateAjax($first, $limit)
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->setFirstResult($first)
            ->setMaxResults($limit)
            ->getResult()
        ;
    }

    /**
     * find all posts in an array of subreddits with first and limits
     *
     * @param int $first
     * @param int $limit
     * @param Subreddits[] $subreddits
     * @return Post[]
     */
    public function findAllInSubredditsOrderByDateAjax($first, $limit, $subreddits){
        return $this->createQueryBuilder('p')
            ->andWhere('p.subreddit IN (:subreddits)')
             ->setParameter('subreddits', $subreddits)
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->setFirstResult($first)
            ->setMaxResults($limit)
            ->getResult()
        ;
    }

    /**
     * find all post in a subreddit with first and limits
     *
     * @param int $first
     * @param int $limit
     * @param Subreddit $subreddit
     * @return Post[]
     */
    public function findAllInSubredditOrderByDateAjax($first, $limit, $subreddit)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.subreddit = :subreddit')
            ->orderBy('p.created_at', 'DESC')
            ->setParameter('subreddit', $subreddit)
            ->getQuery()
            ->setFirstResult($first)
            ->setMaxResults($limit)
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
     * return a post by its id
     *
     * @param int $id
     * @return Post
     */
    public function findOneByID($id){
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->select('p.content')
            ->orderBy('p.created_at', 'DESC')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * return all posts from the subreddit
     *
     * @param Subreddit $subreddit
     * @return Post[]
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

    /**
     * return posts between $first and $limit that contains the $search
     *
     * @param string $search
     * @param int $first
     * @param int $limit
     * @return Post[]
     */
  public function search($search, $first, $limit){
      return $this->createQueryBuilder('p')
        ->andWhere('p.title LIKE :search')
        ->orderBy('p.created_at', 'DESC')
        ->setParameter('search', '%'.$search.'%')
        ->setFirstResult($first)
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }
}
