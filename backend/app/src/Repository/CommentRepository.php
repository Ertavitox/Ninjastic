<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function paginate(int $topicId, int $page, int $limit): mixed
    {

        $offset = ($page - 1) * $limit;

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT 
                c.id,
                IDENTITY(c.user) as user_id,
                u.name as user_name,
                c.message,
                c.created_at,
                c.updated_at
            FROM App\Entity\Comment c
            JOIN c.user u
            WHERE c.topic = :topicId
            and c.status = 1'
        )
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('topicId', $topicId);

        return $query
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function adminListing(
        string $orderField = "id",
        string $orderSort = "ASC",
        string $search = "",
        int $searchStatus = -1,
        string $searchUsername = "",
        string $searchTopic = ""
    ) {
        $entityManager = $this->getEntityManager();
        $userEM = $entityManager->getRepository(User::class);
        $topicEM = $entityManager->getRepository(Topic::class);
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.' . $orderField, $orderSort);

        if ($searchStatus > -1) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }

        if ($searchUsername !== "") {
            $userQueryBuilder = $userEM->createQueryBuilder("u");
            $userQueryBuilder
                ->where(
                    $query->expr()->like('LOWER(u.name)', ':userName'),
                )
                ->setParameter('userName', '%' . strtolower($searchUsername) . '%');

            $usersEntity = $userQueryBuilder->getQuery()->getResult();
            $query->andWhere('p.user IN (:searchUser)')->setParameter('searchUser', $usersEntity);
        }

        if ($searchTopic !== "") {
            $topicQueryBuilder = $topicEM->createQueryBuilder("t");
            $topicQueryBuilder
                ->where(
                    $query->expr()->like('LOWER(t.name)', ':topicName'),
                )
                ->setParameter('topicName', '%' . strtolower($searchTopic) . '%');

            $topicsEntity = $topicQueryBuilder->getQuery()->getResult();
            $query->andWhere('p.topic IN (:searchTopic)')->setParameter('searchTopic', $topicsEntity);
        }

        if ($search !== "") {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.message)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();
        return $query;
    }
}
