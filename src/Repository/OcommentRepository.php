<?php

namespace App\Repository;

use App\Entity\Ocomment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Ocomment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ocomment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ocomment[]    findAll()
 * @method Ocomment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OcommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Ocomment::class);
    }

    // /**
    //  * @return Ocomment[] Returns an array of Ocomment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ocomment
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
