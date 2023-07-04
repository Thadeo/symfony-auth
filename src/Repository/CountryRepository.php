<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 *
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function save(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Country $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find All Country
     * 
     * @param string country
     * @param int page
     * @param int perPage
     * @param string orderBy
     * @param string orderColumn
     */
    public function findAllCountry(
        string $country = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
        $query = $this->createQueryBuilder('a');

        // Find country
        if($country) {
            $query->andWhere('a.code = :country')
                  ->orWhere('a.name LIKE :countryLike')
                  ->orWhere('a.iso = :country')
                  ->orWhere('a.dialCode = :country')
                  ->orWhere('a.capital LIKE :countryLike')
                  ->setParameter('country', $country)
                  ->setParameter('countryLike', '%'.$country.'%');
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

        $paginator = new Paginator($query, false);

        // Result
        $buldResult = [
            'data' => $paginator,
            'count' => count($paginator)
        ];

        // Return Result
        return $buldResult;
    }
}
