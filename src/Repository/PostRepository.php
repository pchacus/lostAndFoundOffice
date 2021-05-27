<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function findAllActive(){

        return $this->createQueryBuilder('p')
            ->andWhere('p.status = 1')
            ->orderBy('p.createDate','DESC')
            ->getQuery()
            ->getResult()
            ;

    }
    public function findAllUserPosts($userId){

        return $this->createQueryBuilder('p')
            ->andWhere('p.userId = :val')
            ->setParameter('val', $userId)
            ->orderBy('p.createDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function search($keywords){

        return $this->createQueryBuilder('p')
            ->Where('p.description like :val or p.title like :val')
            ->andWhere('p.status = 1')
            ->setParameter('val', '%'.$keywords.'%')
            ->orderBy('p.createDate', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllPostsWithUsers(){

        $conn = $this->getEntityManager()->getConnection();

        $sql = ' SELECT post.id, post.title, post.description, post.status ,post.create_date, post.place_of_found, 
                place_of_pick, user.username from post inner JOIN user on post.user_id = user.id 
                order by create_date DESC';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
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
