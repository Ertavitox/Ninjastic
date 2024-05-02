<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Topic;
use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function paginate(int $page, int $limit): mixed
    {
        $offset = ($page - 1) * $limit;
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('t.id,t.name,t.description,t.created_at,IDENTITY(t.user) as user_id,u.name as username,count(c.id) as comment_count')
            ->from(Topic::class, 't')
            ->innerJoin(User::class, 'u', \Doctrine\ORM\Query\Expr\Join::WITH, $queryBuilder->expr()->eq('t.user', 'u.id'))
            ->leftJoin(Comment::class, 'c', \Doctrine\ORM\Query\Expr\Join::WITH, $queryBuilder->expr()->eq('t.id', 'c.topic'))
            ->where('t.status = 1')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->groupBy('t.id, t.name')
            ->orderBy('t.created_at', 'DESC');

        return $queryBuilder->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
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
