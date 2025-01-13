<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getBooks', 'getAuthors'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['getBooks', 'getAuthors'])]
    #[Assert\NotBlank(message: 'Last name cannot be blank')]
    #[Assert\Length(min:3, max: 50, maxMessage: 'Last name cannot be longer than {{ limit }} characters')]
    private string $lastName;

    #[ORM\Column(length: 50)]
    #[Groups(['getBooks', 'getAuthors'])]
    #[Assert\NotBlank(message: 'First name cannot be blank')]
    #[Assert\Length(min:3, max: 50, maxMessage: 'First name cannot be longer than {{ limit }} characters')]
    private string $firstName;

    #[ORM\Column(length: 40, nullable: true)]
    #[Groups(['getBooks', 'getAuthors'])]
    #[Assert\Length(min:3, max: 40, maxMessage: 'Pseudonym cannot be longer than {{ limit }} characters')]
    private ?string $pseudonym = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'author', cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['getAuthors'])]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPseudonym(): ?string
    {
        return $this->pseudonym;
    }

    public function setPseudonym(?string $pseudonym): static
    {
        $this->pseudonym = $pseudonym;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
