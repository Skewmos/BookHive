<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

readonly final class PaginatedResponse
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function createResponse($items, $serializationGroups = []): array
    {
        $context = SerializationContext::create();
        $context->setGroups($serializationGroups);

        return [
            'items' => json_decode(
                $this->serializer->serialize(
                    data: $items->getItems(),
                    format: 'json',
                    context: $context
                ),
                true
            ),
            'currentPage' => $items->getCurrentPageNumber(),
            'itemsPerPage' => $items->getItemNumberPerPage(),
            'totalItems' => $items->getTotalItemCount(),
        ];
    }
}