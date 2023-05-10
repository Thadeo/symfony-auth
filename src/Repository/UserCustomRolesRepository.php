<?php

namespace App\Repository;

use App\Entity\UserCustomRoles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCustomRoles>
 *
 * @method UserCustomRoles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCustomRoles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCustomRoles[]    findAll()
 * @method UserCustomRoles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCustomRolesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCustomRoles::class);
    }

    public function save(UserCustomRoles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserCustomRoles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserCustomRoles[] Returns an array of UserCustomRoles objects
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

//    public function findOneBySomeField($value): ?UserCustomRoles
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
