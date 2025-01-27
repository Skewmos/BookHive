<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Author\Response\AuthorResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AuthorStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $result = $this->collectionProvider->provide($operation, $uriVariables, $context);

        if (!$result instanceof Paginator) {
            throw new \RuntimeException('Expected Paginator instance.');
        }

        $items = iterator_to_array($result);
        $transformedItems = array_map(
            fn($item) => AuthorResponseDto::createFromAuthor($item),
            $items
        );

        return new TraversablePaginator(
            new \ArrayIterator($transformedItems),
            $result->getCurrentPage(),
            $result->getItemsPerPage(),
            $result->getTotalItems()
        );
    }
}
