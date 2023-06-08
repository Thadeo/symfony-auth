<?php

namespace App\Repository;

use App\Entity\AuthTypeProviderSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthTypeProviderSettings>
 *
 * @method AuthTypeProviderSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthTypeProviderSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthTypeProviderSettings[]    findAll()
 * @method AuthTypeProviderSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthTypeProviderSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthTypeProviderSettings::class);
    }

    public function save(AuthTypeProviderSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthTypeProviderSettings $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return AuthTypeProviderSettings[] Returns an array of AuthTypeProviderSettings objects
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

//    public function findOneBySomeField($value): ?AuthTypeProviderSettings
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
