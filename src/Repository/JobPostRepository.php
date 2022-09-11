<?php

namespace App\Repository;

use App\Entity\JobPost;
use App\Entity\JobSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method JobPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPost[]    findAll()
 * @method JobPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, JobPost::class);
    }

    /**
     * @return Query
     */

    public function findJobCardQuery(): Query
    {
        return $this->createQueryBuilder('j')
            ->select('j.id, j.title, j.company, j.slug ,j.contract, j.experience, j.salary, j.location, j.filename, j.createdAt')
            ->orderBy('j.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     *  jobcard data
     * @param JobSearch $search
     * @return Query
     */
    public function findJobCard(JobSearch $search): Query
    {
        $query = $this->createQueryBuilder('j')
            ->select('j.id, j.title, j.company, j.slug ,j.contract, j.location, j.filename, j.createdAt')
            ->orderBy('j.id', 'DESC');
        if ($search->getTitle()) {
            $query = $query->where('j.title LIKE :search OR j.company LIKE :search')
                ->setParameter('search', '%' . $search->getTitle() . '%');
        }
        return $query->getQuery();
    }

    /**
     * jobcard data latest 3 job cards home page query
     * @return jobpost[]
     */
    public function findLatestJobCard()
    {
        return $this->createQueryBuilder('j')
            ->select('j.id, j.title, j.company, j.slug ,j.contract, j.experience, j.salary, j.location, j.filename, j.createdAt')
            ->orderBy('j.id', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return number of job posts
     * @throws NonUniqueResultException
     */
    public function countJobs()
    {
        return $this->createQueryBuilder('j')
            ->select('count(j) as count')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /*
    public function findOneBySomeField($value): ?JobPost
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
