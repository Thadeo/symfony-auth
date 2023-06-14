<?php

namespace App\Repository;

use App\Entity\AuthWayProviderSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthWayProviderSettings>
 *
 * @method AuthWayProviderSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthWayProviderSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthWayProviderSettings[]    findAll()
 * @method AuthWayProviderSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthWayProviderSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthWayProviderSettings::class);
    }

    public function save(AuthWayProviderSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthWayProviderSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AuthWayProviderSettings[] Returns an array of AuthWayProviderSettings objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AuthWayProviderSettings
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
