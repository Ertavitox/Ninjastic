<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull()]
    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $user = null;

    #[Assert\NotNull()]
    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Topic $topic = null;

    #[Assert\Type('string')]
    #[Assert\NotNull()]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[Assert\Type('string')]
    #[Assert\NotNull()]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $original = null;

    #[Assert\Type('int')]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(1)]
    #[Assert\NotNull()]
    #[ORM\Column(options: ["default" => 1])]
    private int $status = self::STATUS_ACTIVE;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @Groups({"comments"})
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups({"topics"})
     */
    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getOriginal(): ?string
    {
        return $this->original;
    }

    public function setOriginal(string $original): static
    {
        $this->original = $original;

        return $this;
    }

    public function showStatusText(): string
    {
        return $this->status == 1 ? "Active" : "Inactive";
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {

        if (!in_array($status, [self::STATUS_ACTIVE, self::STATUS_INACTIVE])) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
