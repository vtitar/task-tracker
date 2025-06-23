<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskBuilder;

use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;


interface TaskBuilderInterface
{
    public function setUser(User $user): self;
    public function setTitle(string $title): self;
    public function setDescription(string $description): self;
    public function setPriority(int $priority): self;
    public function setStatus(string $status): self;
    public function setCompletedAt(?string $completedAt): self;
    public function setParent(?Task $parent): self;
    public function setParentId(?int $parentId): self;

    public function build(): Task;
}
