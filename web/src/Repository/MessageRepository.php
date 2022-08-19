<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Message;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @return Message[] Returns a paginated array of Message objects
     */
    
    public function getMessageOfAPostPaginated(Post $post, int $pagination, int $page)
    {
        return $this->createQueryBuilder('m')
            ->select("m, pS, p, pa")
            ->join("m.postBody", "p", "WITH", "p = :post")
            ->join("p.author", 'pa')
            ->leftJoin("m.author", 'a')
            ->leftJoin("m.post", "pS")
            ->setParameter("post", $post)
            ->orderBy('m.createdAt', 'ASC')
            ->setFirstResult(($page-1)*$pagination)
            ->setMaxResults($pagination)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Message[] Returns an array of Message objects
     */

    public function getMessageOfAPost(Post $post)
    {
        return $this->createQueryBuilder('m')
            ->select("m, pS")
            ->join("m.postBody", "p", "WITH", "p = :post")
            ->leftJoin("m.author", 'a')
            ->leftJoin("m.post", "pS")
            ->setParameter("post", $post)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return int Returns number of Message objects link to a post
     */
    
    public function getMessageOfAPostNb(Post $post):int
    {
        return $this->createQueryBuilder('m')
            ->select("COUNT(p) as nb")
            ->join("m.postBody", "p", "WITH", "p = :post")
            ->setParameter("post", $post)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getAnswerMessage(Post $post)
    {
        return $this->createQueryBuilder('m')
            ->addSelect("p")
            ->join("m.postBody", "p", "WITH", "p = :post")
            ->setParameter("post", $post)
            ->where("m.isAnswer = :isAnswer")
            ->setParameter('isAnswer', true)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAnswerMessageNb(Post $post):int
    {
        return $this->createQueryBuilder('m')
            ->select("COUNT(p) as nb")
            ->join("m.postBody", "p", "WITH", "p = :post")
            ->setParameter("post", $post)
            ->where("m.isAnswer = :isAnswer")
            ->setParameter('isAnswer', true)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getMyMessages(User $user, int $pagination, int $page)
    {
        return $this->createQueryBuilder('m')
            ->addSelect("p")
            ->orderBy("m.createdAt", 'DESC')
            ->innerJoin("m.author", "u", "WITH", "u IN(:user)")
            ->leftJoin("m.post", "p")
            ->setParameter("user", $user)
            ->setFirstResult(($page-1)*$pagination)
            ->setMaxResults($pagination)
            ->getQuery()
            ->getResult()
        ;
    }

    public function nbMyMessages(User $user)
    {
        return $this->createQueryBuilder('m')
            ->select("COUNT(m.id)")
            ->orderBy("m.createdAt", 'DESC')
            ->innerJoin("m.author", "u", "WITH", "u IN(:user)")
            ->leftJoin("m.post", "p")
            ->setParameter("user", $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
