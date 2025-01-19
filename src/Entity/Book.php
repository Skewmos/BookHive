<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'api_book_detail',
        parameters: ['id' => 'expr(object.getId())'],
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBooks'])
)]
#[Hateoas\Relation(
     'create',
        href: new Hateoas\Route(
            'api_book_create',
        ),
        exclusion: new Hateoas\Exclusion(groups: ['getBooks'], excludeIf: 'expr(not is_granted("ROLE_ADMIN"))')
)]
#[Hateoas\Relation(
    'update',
    href: new Hateoas\Route(
        'api_book_update',
        parameters: ['id' => 'expr(object.getId())'],
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBooks'], excludeIf: 'expr(not is_granted("ROLE_ADMIN"))')
)]
#[Hateoas\Relation(
    'delete',
    href: new Hateoas\Route(
        'api_book_delete',
        parameters: ['id' => 'expr(object.getId())'],
    ),
    exclusion: new Hateoas\Exclusion(groups: ['getBooks'], excludeIf: 'expr(not is_granted("ROLE_ADMIN"))')
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getBooks', 'getAuthors'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getBooks', 'getAuthors'])]
    #[Assert\NotBlank(message: 'Title cannot be blank')]
    #[Assert\Length(min:3, max: 255, maxMessage: 'Title cannot be longer than {{ limit }} characters')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['getBooks'])]
    private ?string $coverText = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[Groups(['getBooks'])]
    private ?Author $author = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function setCoverText(?string $coverText): static
    {
        $this->coverText = $coverText;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static
    {
        $this->author = $author;

        return $this;
    }
}
