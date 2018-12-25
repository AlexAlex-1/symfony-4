<?php

namespace App\Repository;

use App\Entity\Tickets;
use App\Entity\TicketsTags;
use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Tags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tags[]    findAll()
 * @method Tags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tags::class);
    }

    // /**
    //  * @return Tags[] Returns an array of Tags objects
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
    public function findOneBySomeField($value): ?Tags
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
     */
    public function findByTickets($tickets){
        $query = $this->getEntityManager()->createQuery(
            "SELECT e
            FROM App\Entity\Tickets k
            JOIN App\Entity\TicketsTags l
            WITH l.Ticket_id = k.id
            JOIN App\Entity\Tags e
            WITH e.id = l.Tag_id
            WHERE k.id = $tickets"
            );
            return $query->execute();
    }
    public function findByString($string){
        $query = $this->getEntityManager()->createQuery(
            "SELECT p
            FROM App\Entity\Tags p
            WHERE p.Name
            LIKE :$string"
        );
        $query->setParameter($string, "$string%");
        return $query->execute();
    }
}
