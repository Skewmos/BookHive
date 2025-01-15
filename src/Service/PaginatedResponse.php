<?php

namespace App\Service;

use Symfony\Component\Serializer\SerializerInterface;

readonly final class PaginatedResponse
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function createResponse($items, $serializationGroups = []): array
    {
        return [
            'items' => json_decode(
                $this->serializer->serialize($items->getItems(),
                    'json',
                    [
                        'groups' => $serializationGroups
                    ]
                ),
                true
            ),
            'currentPage' => $items->getCurrentPageNumber(),
            'itemsPerPage' => $items->getItemNumberPerPage(),
            'totalItems' => $items->getTotalItemCount(),
        ];
    }
}