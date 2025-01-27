<?php

namespace App\Service;

use App\Dto\Author\Request\AuthorPatchDto;
use App\Dto\Author\Request\AuthorPostDto;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;

readonly class AuthorService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createAuthor(AuthorPostDto $authorPostDto): Author
    {
        $author = new Author();

        $this->updateAuthorData($author, $authorPostDto);

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $author;
    }


    public function updateAuthor(Author $author, AuthorPatchDto $data): Author
    {
        $this->updateAuthorData($author, $data);

        $this->entityManager->flush();

        return $author;
    }

    private function updateAuthorData(Author $author, AuthorPostDto|AuthorPatchDto $data): void
    {
        if ($data->lastName !== null) {
            $author->setLastName($data->lastName);
        }

        if ($data->firstName !== null) {
            $author->setFirstName($data->firstName);
        }

        if ($data->pseudonym !== null) {
            $author->setPseudonym($data->pseudonym);
        }
    }
}