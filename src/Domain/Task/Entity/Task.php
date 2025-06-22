<?php

declare(strict_types=1);

namespace App\Domain\Task\Entity;

use App\Domain\Task\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Task\Enum\TaskPriority;
use App\Domain\Task\Enum\TaskStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Domain\User\Entity\User;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Index(columns: ['priority'])]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['created_at'])]
#[ORM\Index(columns: ['completed_at'])]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: TaskStatus::class)]
    private ?TaskStatus $status = null;

    #[ORM\Column(enumType: TaskPriority::class)]
    private ?TaskPriority $priority = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subtasks')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    private ?Task $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'])]
    private Collection $subtasks;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->priority = TaskPriority::THREE;
        $this->status = TaskStatus::TODO;

        $this->subtasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    public function setPriority(TaskPriority $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubtasks(): Collection
    {
        return $this->subtasks;
    }

    public function addSubtask(self $subtask): self
    {
        if (!$this->subtasks->contains($subtask)) {
            $this->subtasks->add($subtask);
            $subtask->setParent($this);
        }

        return $this;
    }

    public function removeSubtask(self $subtask): self
    {
        if ($this->subtasks->removeElement($subtask)) {
            if ($subtask->getParent() === $this) {
                $subtask->setParent(null);
            }
        }

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
