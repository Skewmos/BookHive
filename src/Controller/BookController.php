<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/books')]
class BookController extends AbstractController
{
    private const BOOK_DELETED = 'Book deleted';
    private const BOOK_NOT_FOUND = 'Book not found';

    public function __construct(
        private readonly BookRepository         $bookRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface    $serializer
    )
    {
    }

    #[Route('/', name: 'api_book', methods: ['GET'])]
    public function getBookList(): JsonResponse
    {
        $books = $this->bookRepository->findAll();
        $jsonBookList = $this->serializer->serialize($books, 'json', ['groups' => 'getBooks']);

        return new JsonResponse(data: $jsonBookList, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('/{id}', name: 'api_book_detail', methods: ['GET'])]
    public function getBookDetail(int $id): JsonResponse
    {
     $book = $this->bookRepository->find($id);

     if (!$book) {
         return new JsonResponse(data: ['message' => self::BOOK_NOT_FOUND], status: Response::HTTP_NOT_FOUND);
     }

     $jsonBook = $this->serializer->serialize($book, 'json', ['groups' => 'getBooks']);

     return new JsonResponse(data: $jsonBook, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('/{id}', name: 'api_book_delete', methods: ['DELETE'])]
    public function deleteBook(int $id): JsonResponse
    {
        $book = $this->bookRepository->find($id);

        if (!$book) {
            return new JsonResponse(data: ['message' => self::BOOK_NOT_FOUND], status: Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return new JsonResponse(data: ['message' => self::BOOK_DELETED], status: Response::HTTP_OK);
    }
}
