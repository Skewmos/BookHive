<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Author::class);

        $this->paginator = $paginator;
    }

    public function findPaginatedAuthorList(int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('a');

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }
}
