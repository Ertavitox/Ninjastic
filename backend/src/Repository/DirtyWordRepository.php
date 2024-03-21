<?php

namespace App\Repository;

use App\Entity\DirtyWord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DirtyWord>
 *
 * @method DirtyWord|null find($id, $lockMode = null, $lockVersion = null)
 * @method DirtyWord|null findOneBy(array $criteria, array $orderBy = null)
 * @method DirtyWord[]    findAll()
 * @method DirtyWord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DirtyWordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DirtyWord::class);
    }

    //    /**
    //     * @return DirtyWord[] Returns an array of DirtyWord objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DirtyWord
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
