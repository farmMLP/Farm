<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Orders>
 *
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function save(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Orders $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getOrder($orderId) {
      $qb = $this->createQueryBuilder('o')
                  ->where('o.id = :orderId');
                  // ->select('o.createdAt','o.memo','s.description');
      //             $qb->leftJoin('App\Entity\Status', 's', \Doctrine\ORM\Query\Expr\Join::WITH,
      // 's.id = o.id');
      $qb->setParameter('orderId', $orderId);
                  
      // $qb->leftJoin('App\Entity\User', 'u', \Doctrine\ORM\Query\Expr\Join::WITH,
      // 'p.author = u.id');
      // $qb->orderBy('p.id','ASC');
      return $qb->getQuery()->getResult();
    }

    public function findByStatusAndHealthCenter($status, $healthcenter)
   {
       return $this->createQueryBuilder('a')
           ->orderBy('a.id', 'ASC')
           ->where('a.healthCenter = :healthcenter AND a.status = :status')
           ->setParameter('healthcenter', $healthcenter)
           ->setParameter('status', $status)
           ->getQuery()->getResult()
       ;
   }

   public function defaultQuery($id)
   {
       return $this->createQueryBuilder('a')
           ->orderBy('a.id', 'ASC')
           ->where('a.healthCenter = :id')
           ->setParameter('id', $id)
           ->getQuery()

       ;
   }

   /**
     * @param string[] $criteria
     */
    public function findAllPaymentsOwner(string $merchant_code, array $criteria)
    {
        $qb = $this->createQueryBuilder('p')->Select(
            'p.id,
             p.amount,
             p.date,
             p.txe,
             c.id as charge');

        $qb->leftJoin(
            'App\Entity\Charge',
            'c',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'p.charge_id = c.id'
        );

        $qb->leftJoin(
            'App\Entity\Estate',
            'e',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'c.estate_id = e.id'
        );

        $qb->leftJoin(
            'App\Entity\RealEstate',
            'r',
            \Doctrine\ORM\Query\Expr\Join::WITH,
            'e.real_state_id = r.id'
        );

        if (isset($criteria['estate_owner_id']) and $criteria['estate_owner_id'] != "") {
            $qb->andWhere("e.estate_owner_id = :estate_owner_id")
                ->setParameter('estate_owner_id', $criteria['estate_owner_id']);
        }
        if (isset($criteria['estate_id']) and $criteria['estate_id'] != "") {
            $qb->andWhere("e.id = :estate_id")
                ->setParameter('estate_id', $criteria['estate_id']);
        }

        if (isset($criteria['start']) and $criteria['start'] != "") {
            $qb->andWhere("p.date >= :start")
                ->setParameter('start', $criteria['start'] . ' 00:00:00');
        }
        if (isset($criteria['end']) and $criteria['end'] != "") {
            $qb->andWhere("p.date <= :end")
                ->setParameter('end', $criteria['end'] . ' 23:59:59');
        }

        $qb->andWhere("r.merchantCode = :merchantCode")
            ->setParameter('merchantCode', $merchant_code);

        return $qb->getQuery();

    }
//    /**
//     * @return Orders[] Returns an array of Orders objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Orders
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
