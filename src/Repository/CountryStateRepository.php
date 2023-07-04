<?php

namespace App\Repository;

use App\Entity\Country;
use App\Entity\CountryState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CountryState>
 *
 * @method CountryState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountryState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountryState[]    findAll()
 * @method CountryState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountryState::class);
    }

    public function save(CountryState $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CountryState $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find One State
     * 
     * @param string country
     * @param string state
     */
    public function findOneState(
        string $country,
        string $state
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

        // Find state
        if($state) {
            $query->andWhere('a.code = :state')
                  ->setParameter('state', $state);
        }

        // Find 10 Result
        $query->setMaxResults(1);

        // Query Result
        $result = $query->getQuery()->getOneOrNullResult();

        // Return Result
        return $result;
    }

    /**
     * Find All State
     * 
     * @param string country
     * @param string state
     * @param int page
     * @param int perPage
     * @param string orderBy
     * @param string orderColumn
     */
    public function findAllState(
        string $country,
        string $state = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
        $query = $this->createQueryBuilder('a')->select('DISTINCT a')
            ->join(Country::class, 'co', 
            \Doctrine\ORM\Query\Expr\Join::WITH, 'a.country = co.id');

        // Find state
        if($state) {
            $query->andWhere('a.code = :state')
                  ->orWhere('a.name LIKE :stateLike')
                  ->setParameter('state', $state)
                  ->setParameter('stateLike', '%'.$state.'%');
        }

        // Find country
        if($country) {
            $query->andWhere('co.code = :code')
                  ->setParameter('code', $country);
        }

        // Get currenct page
        --$page;

        // Set Page
        $query->setFirstResult($perPage * $page);

        // set Maximum Result
        $query->setMaxResults($perPage);

        // Set Order Column
        $orderColumn = ($orderColumn) ? $orderColumn : 'id';

        // Order By
        if($orderBy && $orderColumn) {
            $query->orderBy('a.'.$orderColumn, $orderBy);
        }

        $paginator = new Paginator($query, true);

        // Result
        $buldResult = [
            'data' => $paginator,
            'count' => count($paginator)
        ];

        // Return Result
        return $buldResult;
    }
}
