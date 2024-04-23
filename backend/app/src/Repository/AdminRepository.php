<?php

namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Admin>
 *
 * @method Admin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admin[]    findAll()
 * @method Admin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    public function countAll(): int
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function login($email, $password): ?Admin
    {
        $res = $this->createQueryBuilder('a')
            ->where("a.email = :email")->setParameter("email", $email)
            ->andWhere("a.password = :password")->setParameter("password", Admin::generatePassHash($password))
            ->andWhere("a.status = 1")
            ->getQuery()
            ->getOneOrNullResult();
        if ($res instanceof Admin) {
            $res->setPassword("", true);
        }
        return $res;
    }

    public function adminListing(
        string $orderField = "id",
        string $orderSort = "ASC",
        string $search = "",
        int $searchStatus = -1
    ) {
        $query = $this->createQueryBuilder('p')
            ->orderBy('p.' . $orderField, $orderSort);

        if ($searchStatus > -1) {
            $filterStatus = [$searchStatus];
            $query->andWhere($query->expr()->in('p.status', $filterStatus));
        }
        if ($search !== "") {
            $query->andWhere(
                $query->expr()->orX(
                    $query->expr()->like('LOWER(p.name)', ':term'),
                    $query->expr()->like('LOWER(p.email)', ':term'),
                )
            )
                ->setParameter('term', '%' . strtolower($search) . '%');
        }

        $query->getQuery();
        return $query;
    }
}
