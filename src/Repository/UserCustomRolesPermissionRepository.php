<?php

namespace App\Repository;

use App\Entity\UserCustomRolesPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCustomRolesPermission>
 *
 * @method UserCustomRolesPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCustomRolesPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCustomRolesPermission[]    findAll()
 * @method UserCustomRolesPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCustomRolesPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCustomRolesPermission::class);
    }

    public function save(UserCustomRolesPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCustomRolesPermission $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserCustomRolesPermission[] Returns an array of UserCustomRolesPermission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserCustomRolesPermission
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
