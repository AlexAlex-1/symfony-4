<?php

namespace App\Repository;

use App\Entity\TicketsTags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TicketsTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketsTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketsTags[]    findAll()
 * @method TicketsTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketsTagsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TicketsTags::class);
    }

    // /**
    //  * @return TicketsTags[] Returns an array of TicketsTags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TicketsTags
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
