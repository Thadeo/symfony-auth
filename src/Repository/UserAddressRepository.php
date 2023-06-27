<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\CountryState;
use App\Entity\User;
use App\Entity\UserAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserAddress>
 *
 * @method UserAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAddress[]    findAll()
 * @method UserAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAddress::class);
    }

    public function save(UserAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserAddress $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One Address
     * 
     * @param User user
     * @param string identifier
     */
    public function findOneAddress(
        User $user,
        string $identifier,
        bool $isActive = null
    )
    {
        $query = $this->createQueryBuilder('a');

        // Find user
        if($user) {
            $query->andWhere('a.user = :user')
                  ->setParameter('user', $user);
        }

        // Find search
        if($identifier) {
            $query->andWhere('a.identifier = :identifier')
                  ->setParameter('identifier', $identifier);
        }

        // Find isActive
        if(is_bool($isActive)) {
            $query->andWhere('a.active = :active')
                  ->setParameter('active', $isActive);
        }

        // Find One Result
        $query->setMaxResults(1);

        // Query Result
        $result = $query->getQuery()->getOneOrNullResult();

        // Return Result
        return $result;
    }

    /**
     * Find All Address
     * 
     * @param User user
     * @param string search
     * @param string country
     * @param string state
     * @param bool isPrimary
     */
    public function findAllAddress(
        User $user,
        string $search = null,
        string $country = null,
        string $state = null,
        bool $isPrimary = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->join(Country::class, 'co', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.country = co.id')
            ->join(CountryState::class, 'cs', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.state = cs.id');

        // Find country
        if($country) {
            $query->andWhere('co.code = :code')
                  ->setParameter('code', $country);
        }

        // Find state
        if($state) {
            $query->andWhere('cs.code = :state')
                  ->setParameter('state', $state);
        }

        // Find user
        if($user) {
            $query->andWhere('a.user = :user')
                  ->setParameter('user', $user);
        }

        // Find search
        if($search) {
            $query->andWhere('a.address LIKE :address')
                  ->orWhere('a.address2 LIKE :address')
                  ->orWhere('a.identifier LIKE :address')
                  ->setParameter('address', '%'.$search.'%');
        }

        // Find isPrimary
        if(is_bool($isPrimary)) {
            $query->andWhere('a.isPrimary = :isPrimary')
                  ->setParameter('isPrimary', $isPrimary);
        }

        // Find 10 Result
        $query->setMaxResults(10);

        // Query Result
        $result = $query->getQuery()->getResult();

        // Return Result
        return $result;
    }
}
