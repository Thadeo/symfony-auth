<?php

namespace App\Repository;

use App\Entity\AuthWay;
use App\Entity\AuthWayProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthWayProvider>
 *
 * @method AuthWayProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthWayProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthWayProvider[]    findAll()
 * @method AuthWayProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthWayProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthWayProvider::class);
    }

    public function save(AuthWayProvider $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthWayProvider $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find All Provider
     * 
     * @param string auth
     */
    /*public function findAllProvider(
        string $auth,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->leftJoin(Auth::class, 'au', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.auth = au.id')
            ->andWhere('au.code', $auth);

        // Find active
        if($active || $active == false) {
            $query->andWhere('a.active', $active);
        }

        // Query Result
        $query->getQuery()->getResult();

        // Return Result
        return $query;
    }*/

    /**
     * Find One Provider
     * 
     * @param string identifier
     * @param bool isPrimary
     * @param bool active
     */
    public function findOneProvider(
        string $identifier,
        bool $isPrimary = null,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->join(AuthWay::class, 'aw', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.way = aw.id')
            ->andWhere('aw.identifier = :identifier')
            ->setParameter('identifier', $identifier);

        // Find primary
        if($isPrimary || $isPrimary == false) {
            $query->andWhere('a.isPrimary = :isPrimary')
                  ->setParameter('isPrimary', $isPrimary);
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
