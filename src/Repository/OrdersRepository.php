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

   public function paginationQuery($id)
   {
       return $this->createQueryBuilder('a')
           ->orderBy('a.id', 'ASC')
           ->where('a.healthCenter = :id')
           ->setParameter('id', $id)
           ->getQuery()

       ;
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
