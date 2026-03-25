<?php

namespace App\Entity;

use App\Repository\LineCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LineCommentRepository::class)]
class LineComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Snippet::class, inversedBy: 'lineComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Snippet $snippet = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lineComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive]
    private int $lineNumber = 1;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $content = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getSnippet(): ?Snippet { return $this->snippet; }
    public function setSnippet(?Snippet $snippet): static { $this->snippet = $snippet; return $this; }

    public function getAuthor(): ?User { return $this->author; }
    public function setAuthor(?User $author): static { $this->author = $author; return $this; }

    public function getLineNumber(): int { return $this->lineNumber; }
    public function setLineNumber(int $lineNumber): static { $this->lineNumber = $lineNumber; return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
