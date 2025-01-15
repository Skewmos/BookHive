<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Book::class);

        $this->paginator = $paginator;
    }

    public function findPaginatedBookList(int $page, int $limit): PaginationInterface
    {
        $qb = $this->createQueryBuilder('b');

        return $this->paginator->paginate(
            $qb->getQuery(),
            $page,
            $limit
        );
    }
}
