<?php

namespace App\Dto\Response;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Book;

readonly class BookResponseDto
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false)]
        private string  $id,

        #[ApiProperty(readable: true, writable: false)]
        private string  $title,

        #[ApiProperty(readable: true, writable: false)]
        private ?string $coverText,

        #[ApiProperty(readable: true, writable: false)]
        private ?string $authorIri
    ) {}

    public static function createFromBook(Book $book): self
    {
        return new self(
            $book->getId(),
            $book->getTitle(),
            $book->getCoverText(),
            $book->getAuthor() ? '/api/authors/' . $book->getAuthor()->getId() : null
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function getAuthorIri(): ?string
    {
        return $this->authorIri;
    }
}
