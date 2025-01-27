<?php

namespace App\Dto\Author\Response;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Author;

class AuthorResponseDto
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false)]
        public string $lastName,

        #[ApiProperty(readable: true, writable: false)]
        public string $firstName,

        #[ApiProperty(readable: true, writable: false)]
        public ?string $pseudonym,

        #[ApiProperty(readable: true, writable: false)]
        public array $books
    ) {
    }

    public static function createFromAuthor(Author $author): self
    {
        $booksIri = array_map(
            fn($book) => '/api/books/' . $book->getId(),
            $author->getBooks()->toArray()
        );

        return new self(
            $author->getLastName(),
            $author->getFirstName(),
            $author->getPseudonym(),
            $booksIri
        );
    }
}