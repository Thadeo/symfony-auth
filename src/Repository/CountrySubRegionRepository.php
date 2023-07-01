<?php

namespace App\Repository;

use App\Entity\CountrySubRegion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CountrySubRegion>
 *
 * @method CountrySubRegion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountrySubRegion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountrySubRegion[]    findAll()
 * @method CountrySubRegion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountrySubRegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountrySubRegion::class);
    }

    public function save(CountrySubRegion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CountrySubRegion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CountrySubRegion[] Returns an array of CountrySubRegion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CountrySubRegion
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
