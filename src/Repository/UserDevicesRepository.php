<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserDevices;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserDevices>
 *
 * @method UserDevices|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserDevices|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserDevices[]    findAll()
 * @method UserDevices[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserDevicesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDevices::class);
    }

    public function save(UserDevices $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserDevices $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One Device
     * 
     * @param User user
     * @param string identifier
     * @param string token
     * @param string device
     * @param bool active
     */
    public function findOneDevice(
        User $user,
        string $device,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.userAgent = :userAgent')
            ->setParameter('userAgent', $device);

        // Find user
        if($user) {
            $query->andWhere('a.user = :user')
                  ->setParameter('user', $user);
        }

        // Find active
        if($active || $active == false) {
            $query->andWhere('a.active = :active')
                  ->setParameter('active', $active);
        }

        // Find One Result
        $query->setMaxResults(1);

        // Query Result
        $result = $query->getQuery()->getOneOrNullResult();

        // Return Result
        return $result;
    }
}
