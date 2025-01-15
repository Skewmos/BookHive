<?php

namespace App\Controller;

use App\Entity\Author;
use App\Enum\ApiMessageEnum;
use App\Enum\CrudMessage\AuthorCrudMessageEnum;
use App\Repository\AuthorRepository;
use App\Service\PaginatedResponse;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api/authors')]
class AuthorController extends AbstractController
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerInterface $serializer,
        private readonly TagAwareCacheInterface $cache,
        private readonly ValidationService $validationService
    )
    {
    }

    #[Route('/', name: 'api_author', methods: ['GET'])]
    public function getAuthorList(Request $request, PaginatedResponse $paginatedResponse): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = min(100, max(1, $request->query->getInt('limit', 10)));

        $cacheId = 'author_list_' . $page . '_' . $limit;

        try {
            $authors = $this->cache->get($cacheId, function (ItemInterface $item) use ($page, $limit) {
                $item->tag("author_list");
                return $this->authorRepository->findPaginatedAuthorList($page, $limit);
            });

            $data = $paginatedResponse->createResponse($authors, ['getAuthors']);

            return new JsonResponse(
                data : $data,
                status: Response::HTTP_OK,
                headers: [
                'Cache-Control' => 'public, max-age=3600'
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => ApiMessageEnum::API_MESSAGE_ERROR->value],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
       }

    #[Route('/{id}', name: 'api_author_detail', methods: ['GET'])]
    public function getAuthorDetail(int $id): JsonResponse
    {
        try {
            $author = $this->authorRepository->find($id);

            if (!$author) {
                return new JsonResponse(
                    data: ['message' => AuthorCrudMessageEnum::AUTHOR_NOT_FOUND],
                    status: Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse(
                data: $this->serializer->normalize($author, null, ['getAuthor']),
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => ApiMessageEnum::API_MESSAGE_ERROR->value],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'api_author_delete', methods: ['DELETE'])]
    public function deleteAuthor(int $id): JsonResponse
    {
        try {
            $author = $this->authorRepository->find($id);

            if (!$author) {
                return new JsonResponse(
                    data: ['message' => AuthorCrudMessageEnum::AUTHOR_NOT_FOUND],
                    status: Response::HTTP_NOT_FOUND
                );
            }

            $this->cache->invalidateTags(["author_list"]);

            $this->entityManager->remove($author);
            $this->entityManager->flush();

            return new JsonResponse(
                data: ['message' => AuthorCrudMessageEnum::AUTHOR_DELETED],
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => ApiMessageEnum::API_MESSAGE_ERROR->value],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/', name: 'api_author_create', methods: ['POST'])]
    public function createAuthor(Request $request, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        try {
            $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');

            $validationResponse = $this->validationService->validateEntity($author);
            if ($validationResponse !== null) {
                return $validationResponse;
            }

            $this->cache->invalidateTags(["author_list"]);

            $this->entityManager->persist($author);
            $this->entityManager->flush();

            return new JsonResponse(
                data: ['message' => AuthorCrudMessageEnum::AUTHOR_CREATED],
                status: Response::HTTP_CREATED,
                headers: ['Location' => $urlGenerator->generate('api_author_detail', ['id' => $author->getId()])]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => ApiMessageEnum::API_MESSAGE_ERROR->value],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'api_author_update', methods: ['PUT'])]
    public function updateAuthor(int $id, Request $request): JsonResponse
    {
        try {
            $authorToUpdate = $this->authorRepository->find($id);

            if (!$authorToUpdate) {
                return new JsonResponse(
                    data: ['message' => AuthorCrudMessageEnum::AUTHOR_NOT_FOUND],
                    status: Response::HTTP_NOT_FOUND
                );
            }

            $this->serializer->deserialize(
                $request->getContent(),
                Author::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $authorToUpdate]
            );

            $validationResponse = $this->validationService->validateEntity($authorToUpdate);
            if ($validationResponse !== null) {
                return $validationResponse;
            }

            $this->cache->invalidateTags(["author_list"]);

            $this->entityManager->flush();

            return new JsonResponse(
                data: ['message' => AuthorCrudMessageEnum::AUTHOR_UPDATED],
                status: Response::HTTP_OK
            );
        }catch (\Exception $e) {
            return new JsonResponse(
                data: ['message' => ApiMessageEnum::API_MESSAGE_ERROR->value],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
