<?php

namespace App\Repository;

use App\Entity\AuthVerify;
use App\Entity\AuthType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthVerify>
 *
 * @method AuthVerify|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthVerify|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthVerify[]    findAll()
 * @method AuthVerify[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthVerifyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthVerify::class);
    }

    public function save(AuthVerify $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthVerify $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One Verify
     * 
     * @param string auth
     * @param ?string token
     * @param ?string device
     * @param ?bool active
     */
    public function findOneVerify(
        string $auth,
        string $token = null,
        string $device = null,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->join(AuthType::class, 'au', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.authType = au.id')
            ->andWhere('au.code = :code')
            ->setParameter('code', $auth);

        // Find token
        if($token) {
            $query->andWhere('a.token = :token')
                  ->setParameter('token', $token);
        }

        // Find device
        if($device) {
            $query->andWhere('a.device = :device')
                  ->setParameter('device', $device);
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
