<?php

namespace App\Repository;

use App\Entity\Inbox;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Inbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inbox[]    findAll()
 * @method Inbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InboxRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Inbox::class);
    }

    public function paginate($query, $page, $limit) {

        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit  * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    public function getMessages($user, $page = 1, $limit = 20, $sent = false) {

        $query = $this->createQueryBuilder('msg')
            ->where('msg.user = :user AND msg.in_out = :sent')
            ->setParameter('user', $user)
            ->setParameter('sent', $sent)
            ->orderBy('msg.id', 'DESC')
            ->getQuery();

        return array(
            'paginator' => $this->paginate($query, $page, $limit),
            'query' => $query
        );

    }

    // /**
    //  * @return Inbox[] Returns an array of Inbox objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inbox
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
