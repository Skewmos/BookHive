<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/books')]
class BookController extends AbstractController
{
    private const BOOK_DELETED = 'Book deleted';
    private const BOOK_NOT_FOUND = 'Book not found';
    private const BOOK_UPDATED = 'Book updated';

    public function __construct(
        private readonly AuthorRepository       $authorRepository,
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

    #[Route('/', name: 'api_book_create', methods: ['POST'])]
    public function createBook(Request $request, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $newBook = $this->serializer->deserialize($request->getContent(), Book::class, 'json');

        $requestContent = $request->toArray();
        $authorId = $requestContent['author_id'] ?? -1;
        $newBook->setAuthor($this->authorRepository->find($authorId));

        $this->entityManager->persist($newBook);
        $this->entityManager->flush();

        $jsonBook = $this->serializer->serialize($newBook, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator->generate(
            'api_book_detail',
            ['id' => $newBook->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse(
            data: $jsonBook,
            status: Response::HTTP_CREATED,
            headers: ['Location' => $location],
            json: true
        );
    }

    #[Route('/{id}', name: 'api_book_update', methods: ['PUT'])]
    public function updateBook(int $id, Request $request): JsonResponse
    {
        $bookToUpdate = $this->bookRepository->find($id);

        if (!$bookToUpdate) {
            return new JsonResponse(
                data: ['message' => self::BOOK_NOT_FOUND],
                status: Response::HTTP_NOT_FOUND
            );
        }

        $this->serializer->deserialize(
            $request->getContent(),
            Book::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $bookToUpdate]
        );

        $requestContent = $request->toArray();
        $authorId = $requestContent['author_id'] ?? -1;
        $bookToUpdate->setAuthor($this->authorRepository->find($authorId));

        $this->entityManager->flush();

        return new JsonResponse(
            data: ['message' => self::BOOK_UPDATED],
            status: Response::HTTP_OK
        );
    }
}
