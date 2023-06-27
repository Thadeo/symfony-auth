<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\User;
use App\Entity\UserPhone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserPhone>
 *
 * @method UserPhone|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPhone|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPhone[]    findAll()
 * @method UserPhone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPhone::class);
    }

    public function save(UserPhone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserPhone $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One Phone
     * 
     * @param User user
     * @param string identifier
     */
    public function findOnePhone(
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
                  ->orWhere('a.phone = :identifier')
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
     * Find All Phone
     * 
     * @param User user
     * @param string search
     * @param string country
     * @param bool isPrimary
     */
    public function findAllPhone(
        User $user,
        string $search = null,
        string $country = null,
        bool $isPrimary = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->join(Country::class, 'co', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.country = co.id');

        // Find country
        if($country) {
            $query->andWhere('co.code = :code')
                  ->setParameter('code', $country);
        }

        // Find user
        if($user) {
            $query->andWhere('a.user = :user')
                  ->setParameter('user', $user);
        }

        // Find search
        if($search) {
            $query->andWhere('a.identifier = :identifier')
                  ->orWhere('a.phone = :identifier')
                  ->setParameter('identifier', $search);
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
