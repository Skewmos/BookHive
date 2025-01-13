<?php

namespace App\Controller;

use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/authors')]
class AuthorController extends AbstractController
{
    private const AUTHOR_DELETED = 'Author deleted';
    private const AUTHOR_NOT_FOUND = 'Author not found';

    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer
    )
    {
    }

    #[Route('/', name: 'api_author', methods: ['GET'])]
    public function getAuthorList(): JsonResponse
    {
        $books = $this->authorRepository->findAll();
        $jsonBookList = $this->serializer->serialize($books, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse(data: $jsonBookList, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('/{id}', name: 'api_author_detail', methods: ['GET'])]
    public function getAuthorDetail(int $id): JsonResponse
    {
        $book = $this->authorRepository->find($id);

        if (!$book) {
            return new JsonResponse(data: ['message' => self::AUTHOR_NOT_FOUND], status: Response::HTTP_NOT_FOUND);
        }

        $jsonBook = $this->serializer->serialize($book, 'json', ['groups' => 'getAuthors']);

        return new JsonResponse(data: $jsonBook, status: Response::HTTP_OK, headers: [], json: true);
    }

    #[Route('/{id}', name: 'api_author_delete', methods: ['DELETE'])]
    public function deleteAuthor(int $id): JsonResponse
    {
        $author = $this->authorRepository->find($id);

        if (!$author) {
            return new JsonResponse(data: ['message' => self::AUTHOR_NOT_FOUND], status: Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return new JsonResponse(data: ['message' => self::AUTHOR_DELETED], status: Response::HTTP_OK);

    }
}
