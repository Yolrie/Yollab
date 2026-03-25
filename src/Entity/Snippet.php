<?php

namespace App\Entity;

use App\Repository\SnippetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SnippetRepository::class)]
class Snippet
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_REVIEWED = 'reviewed';

    public const LANGUAGES = [
        'PHP'        => 'php',
        'JavaScript' => 'javascript',
        'TypeScript' => 'typescript',
        'Python'     => 'python',
        'Go'         => 'go',
        'Rust'       => 'rust',
        'Java'       => 'java',
        'C#'         => 'csharp',
        'HTML'       => 'html',
        'CSS'        => 'css',
        'SQL'        => 'sql',
        'Shell'      => 'bash',
        'Autre'      => 'plaintext',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $title = '';

    #[ORM\Column(length: 50)]
    private string $language = 'plaintext';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $code = '';

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'snippets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'snippets', cascade: ['persist'])]
    private Collection $tags;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'snippet', orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: LineComment::class, mappedBy: 'snippet', orphanRemoval: true)]
    private Collection $lineComments;

    public function __construct()
    {
        $this->createdAt   = new \DateTimeImmutable();
        $this->tags        = new ArrayCollection();
        $this->reviews     = new ArrayCollection();
        $this->lineComments = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getLanguage(): string { return $this->language; }
    public function setLanguage(string $language): static { $this->language = $language; return $this; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getAuthor(): ?User { return $this->author; }
    public function setAuthor(?User $author): static { $this->author = $author; return $this; }

    public function getTags(): Collection { return $this->tags; }
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }
    public function removeTag(Tag $tag): static { $this->tags->removeElement($tag); return $this; }

    public function getReviews(): Collection { return $this->reviews; }
    public function getLineComments(): Collection { return $this->lineComments; }

    public function getApprovedReviewsCount(): int
    {
        return $this->reviews->filter(fn(Review $r) => $r->getStatus() === Review::STATUS_APPROVED)->count();
    }
}
