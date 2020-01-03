<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('log');
    }

    public function findLatest(int $page = 1, int $amount = 6): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('log.createdAt', 'DESC')
            ->setMaxResults($amount);

        return $this->createPaginator($qb->getQuery(), $page, $amount);
    }

    private function createPaginator(Query $query, int $page, int $amountPerPage): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, true, true));
        $paginator->setMaxPerPage($amountPerPage);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
