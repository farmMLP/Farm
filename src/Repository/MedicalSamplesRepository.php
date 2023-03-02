<?php

namespace App\Repository;

use App\Entity\MedicalSamples;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalSamples>
 *
 * @method MedicalSamples|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicalSamples|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicalSamples[]    findAll()
 * @method MedicalSamples[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicalSamplesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalSamples::class);
    }

    public function save(MedicalSamples $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicalSamples $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
//     * @return MedicalSamples[] Returns an array of MedicalSamples objects
//     */
    public function findIfExistsByHealthCenter($healthcenter, $product): ?MedicalSamples
    {
        return $this->createQueryBuilder('a')
            ->where('a.healthCenter = :healthcenter AND a.product = :product')
            ->setParameter('healthcenter', $healthcenter)
            ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    ;
    }

//    public function findOneBySomeField($value): ?MedicalSamples
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
