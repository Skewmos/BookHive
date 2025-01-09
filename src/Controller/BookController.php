<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/books')]
class BookController extends AbstractController
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('/', name: 'api_book', methods: ['GET'])]
    public function getBookList(): JsonResponse
    {
        $books = $this->bookRepository->findAll();
        $jsonBookList = $this->serializer->serialize($books, 'json');

        return new JsonResponse(data: $jsonBookList, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('/{id}', name: 'api_book_detail', methods: ['GET'])]
    public function getBookDetail(int $id): JsonResponse
    {
     $book = $this->bookRepository->find($id);

     if (!$book) {
         return new JsonResponse(data: ['message' => 'Book not found'], status: Response::HTTP_NOT_FOUND);
     }

     $jsonBook = $this->serializer->serialize($book, 'json');

     return new JsonResponse(data: $jsonBook, status: Response::HTTP_OK, headers: [], json: true);
    }
}
