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

    /**
     * Gets a Paginator with the query specified
     *
     * @param $query the query generated
     * @param $page the current page
     * @param $limit the limit of elements per page
     * @return Paginator
     */
    public function paginate($query, $page, $limit) {

        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($limit  * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }

    /**
     * Gets the list of Inbox rows with the specified parameters
     *
     * @param $user the User linked to the Inbox
     * @param int $page the current page
     * @param int $limit the limit of elements per page
     * @param bool $sent whether asking for sent or received messages
     * @return array
     */
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

}
