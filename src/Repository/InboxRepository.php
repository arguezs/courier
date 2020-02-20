<?php

namespace App\Repository;

use App\Entity\Inbox;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Inbox|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inbox|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inbox[]    findAll()
 * @method Inbox[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InboxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Inbox::class);
    }

    public function findReceivedMessages(User $user){
        $qb = $this->createQueryBuilder('received')
            //->select('received.message')
            ->where('received.user = :user')
            ->setParameter('user', $user)
            ->andWhere('received.in_out = :sent')
            ->setParameter('sent', false);

        return $qb->getQuery()->execute();
    }

    public function findSentMessages(User $user){
        return $this->createQueryBuilder('sent')
            ->select('sent.message')
            ->where('sent.user = :user')
            ->setParameter('user', $user)
            ->andWhere('sent.in_out = :sent')
            ->setParameter('sent', true);
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
