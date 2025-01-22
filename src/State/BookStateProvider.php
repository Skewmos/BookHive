<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Response\BookResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class BookStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private ProviderInterface $collectionProvider
    )
    {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var Paginator $paginator */
        $paginator = $this->collectionProvider->provide($operation, $uriVariables, $context);

        $items = iterator_to_array($paginator);
        $transformedItems = array_map(function ($item) {
            return BookResponseDto::createFromBook($item);
        }, $items);

        return new TraversablePaginator(
            new \ArrayIterator($transformedItems),
            $paginator->getCurrentPage(),
            $paginator->getItemsPerPage(),
            $paginator->getTotalItems()
        );
    }
}
