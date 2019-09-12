<?php

namespace App\Repository;

use App\Entity\Pcomments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pcomments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pcomments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pcomments[]    findAll()
 * @method Pcomments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PcommentsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pcomments::class);
    }

    // /**
    //  * @return Pcomments[] Returns an array of Pcomments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pcomments
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
