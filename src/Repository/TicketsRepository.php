<?php

namespace App\Repository;

use App\Entity\TicketsTags;
use App\Entity\Tags;
use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tickets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tickets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tickets[]    findAll()
 * @method Tickets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    // /**
    //  * @return Tickets[] Returns an array of Tickets objects
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
    public function findOneBySomeField($value): ?Tickets
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     */ 
    public function findByTags($tags){
        $query = $this->getEntityManager()->createQuery(
        "SELECT e
        FROM App\Entity\Tags k
        JOIN App\Entity\TicketsTags l
        WITH l.Tag_id = k.id
        JOIN App\Entity\Tickets e
        WITH e.id = l.Ticket_id
        WHERE k.id = $tags"
        );
        return $query->execute();
    }
}
