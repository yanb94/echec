<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    /**
     * @return Post Returns a Post objects with a slug
     */
    public function getPostBySlug(string $slug)
    {
        return $this->createQueryBuilder('p')
            ->addSelect("a, startMsg, c, u")
            ->leftJoin("p.author", "a")
            ->leftJoin("p.startMsg", "startMsg")
            ->leftJoin("p.categories", "c")
            ->leftJoin("p.users", "u")
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult()
        ;
    }

    /**
     * @return Post[] Returns a list of all Post objects
     */
    public function getAllPost()
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.lastMsgAt", 'DESC')
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[] Returns a paginate array Post objects
     */
    public function getPaginatedList(int $pagination, int $page)
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.lastMsgAt", 'DESC')
            ->setFirstResult(($page-1)*$pagination)
            ->setMaxResults($pagination)
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[] Returns a paginate array Post objects by Category
     */
    public function getPaginatedListByCategory(int $pagination, int $page, int $idCategory)
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c', 'WITH', 'c.id = :idCategory')
            ->setParameter('idCategory', $idCategory)
            ->orderBy("p.lastMsgAt", 'DESC')
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->setFirstResult(($page-1)*$pagination)
            ->setMaxResults($pagination)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return int Returns number of Post
     */
    public function getNbPost(): int
    {
        return $this->createQueryBuilder('p')
            ->select("COUNT(p.id)")
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return int Returns number of Post for a category
     */
    public function getNbPostByCategory(int $idCategory): int
    {
        return $this->createQueryBuilder('p')
            ->select("COUNT(p.id)")
            ->join('p.categories', 'c', 'WITH', 'c.id = :idCategory')
            ->setParameter('idCategory', $idCategory)
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return Post[] Returns a list of post for homepage
     */
    public function getLastSubjectForHomepage(int $limit)
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.lastMsgAt", 'DESC')
            ->where("p.isModerate = :isModerate")
            ->setParameter("isModerate", false)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[] Return a list of followed post
     */
    public function getFollowedSubject(User $user, int $pagination, int $page,)
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.lastMsgAt", 'DESC')
            ->innerJoin("p.users", "u", "WITH", "u IN(:user)")
            ->setParameter("user", $user)
            ->setFirstResult(($page-1)*$pagination)
            ->setMaxResults($pagination)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return int Return a number of followed post
     */
    public function getNbFollowedSubject(User $user): int
    {
        return $this->createQueryBuilder('p')
            ->select("COUNT(p.id)")
            ->orderBy("p.lastMsgAt", 'DESC')
            ->innerJoin("p.users", "u", "WITH", "u IN(:user)")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult()
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
