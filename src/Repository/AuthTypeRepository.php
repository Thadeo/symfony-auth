<?php

namespace App\Repository;

use App\Entity\AuthType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuthType>
 *
 * @method AuthType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthType[]    findAll()
 * @method AuthType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthType::class);
    }

    public function save(AuthType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AuthType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One Type
     * 
     * @param string identifier
     * @param bool active
     */
    public function findOneType(
        string $identifier,
        bool $active = null
    )
    {
        $query = $this->createQueryBuilder('a')
            ->andWhere('a.identifier = :identifier')
            ->setParameter('identifier', $identifier);

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
