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
     */
    public function findAllCountry(
        string $country = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = 'desc'
    )
    {
        $query = $this->createQueryBuilder('a');

        // Find country
        if($country) {
            $query->andWhere('a.name = :country')
                  ->orWhere('a.code = :country')
                  ->orWhere('a.iso = :country')
                  ->orWhere('a.dialCode = :country')
                  ->orWhere('a.capital = :country')
                  ->setParameter('country', $country);
        }

        // Get currenct page
        --$page;

        // Set Page
        $query->setFirstResult($perPage * $page);

        // set Maximum Result
        $query->setMaxResults($perPage);

        // Order By
        $query->orderBy('a.id', $orderBy);

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
