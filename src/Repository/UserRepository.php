<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * @param $val
     * @param $column
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findUserByVal($val, $column): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere("u.$column = :$column")
            ->setParameter($column, $val)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @return number of non admin user
     * @throws NonUniqueResultException
     */
    public function countUsers()
    {
        return $this->createQueryBuilder('u')
            ->select('count(u) as count')
            ->where('u.roles != :role')
            ->setParameter('role', '["ROLE_ADMIN"]')
            ->getQuery()
            ->getSingleScalarResult();
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
