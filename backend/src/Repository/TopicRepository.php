<?php

namespace App\Repository;

use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTopicById(int $topicId)
    {
        return $this->findOneBy([
            'id' => $topicId
        ]);
    }

    public function adminListing(
        string $orderField = "id",
        string $orderSort = "ASC",
        string $search = "",
        string $searchStatus = "-1",
        string $searchUsername = ""
    ) {
        $userEM = $this->getEntityManager()->getRepository(User::class);
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.' . $orderField, $orderSort);

        if ($searchStatus > -1) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }

        if ($searchUsername !== "") {
            $queryBuilder = $userEM->createQueryBuilder("u");
            $queryBuilder
                ->where(
                    $query->expr()->like('LOWER(u.name)', ':name'),
                )
                ->setParameter('name', '%' . strtolower($searchUsername) . '%');

            $usersEntity = $queryBuilder->getQuery()->getResult();
            $query->andWhere('p.user IN (:searchUser)')->setParameter('searchUser', $usersEntity);
        }

        if ($search !== "") {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.name)', ':term'),
                    $query->expr()->like('LOWER(p.description)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();
        return $query;
    }
}
