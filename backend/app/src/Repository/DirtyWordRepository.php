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

    public function adminListing(
        string $orderField = "id",
        string $orderSort = "ASC",
        string $search = "",
        string $searchStatus = "-1"
    ) {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.' . $orderField, $orderSort);

        if ($searchStatus !== "-1") {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.type', $filterStatus));
        }
        if ($search !== "") {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.word)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();
        return $query;
    }
}
