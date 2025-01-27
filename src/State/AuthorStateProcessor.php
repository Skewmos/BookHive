<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Author;
use App\Service\AuthorService;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthorStateProcessor implements ProcessorInterface
{
    public function __construct(private AuthorService $authorService)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Author
    {
        try {
            if ($operation instanceof Post) {
                return $this->authorService->createAuthor($data);
            } elseif ($operation instanceof Patch) {
                $author = $context['previous_data'] ?? null;
                if (!$author instanceof Author) {
                    throw new \RuntimeException('Author not found');
                }
                return $this->authorService->updateAuthor($author, $data);
            }
            throw new \RuntimeException('Unsupported operation');
        } catch (\Exception $exception) {
            throw new \RuntimeException(
                message: 'An error occurred while processing the author.',
                code: Response::HTTP_INTERNAL_SERVER_ERROR,
                previous: $exception
            );
        }
    }
}
