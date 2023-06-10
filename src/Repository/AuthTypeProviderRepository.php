<?php

namespace App\Repository;

use App\Entity\Auth;
use App\Entity\AuthType;
use App\Entity\AuthTypeProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthTypeProvider>
 *
 * @method AuthTypeProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthTypeProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthTypeProvider[]    findAll()
 * @method AuthTypeProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthTypeProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthTypeProvider::class);
    }

    public function save(AuthTypeProvider $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthTypeProvider $entity, bool $flush = false): void
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
    public function findAllProvider(
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
    }

    /**
     * Find One Provider
     * 
     * @param string auth
     * @param bool isPrimary
     * @param bool active
     */
    public function findOneProvider(
        string $auth,
        bool $isPrimary = null,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->leftJoin(AuthType::class, 'au', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.type = au.id')
            ->andWhere('au.code = :code')
            ->setParameter('code', $auth);

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
