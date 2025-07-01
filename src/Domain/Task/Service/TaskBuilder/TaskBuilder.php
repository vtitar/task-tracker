<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskBuilder;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskPriority;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\User\Entity\User;

class TaskBuilder implements TaskBuilderInterface
{
    private Task $task;

    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {
        $this->task = new Task();
        $this->task->setCreatedAt(new \DateTimeImmutable());
    }

    public function setUser(User $user): self
    {
        $this->task->setUser($user);
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->task->setTitle($title);
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->task->setDescription($description);
        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->task->setPriority(TaskPriority::from($priority));
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->task->setStatus(TaskStatus::from($status));
        return $this;
    }

    public function setCompletedAt(?string $completedAt): self
    {
        if (!$completedAt) {
            return $this;
        }

        if ($this->task->getStatus() !== TaskStatus::DONE) {
            throw new \Exception('CompletedAt is available only for done status.');
        }

        $this->task->setCompletedAt(new \DateTimeImmutable($completedAt));

        return $this;
    }

    public function setParentId(?int $parentId): self
    {
        if (!$parentId) {
            return $this;
        }

        $parentTask = $this->taskRepository->findOneBy([
            'id' => $parentId,
            'user' => $this->task->getUser()
        ]);

        if (!$parentTask) {
            throw new \Exception('Invalid parent task.');
        }

        if ($parentTask->getStatus() === TaskStatus::DONE) {
            throw new \Exception('Parent task already completed.');
        }

        $this->task->setParent($parentTask);

        return $this;
    }

    public function build(): Task
    {
        return $this->task;
    }
}
