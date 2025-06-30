<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskBuilder\TaskBuilderInterface;
use App\Domain\Task\Service\TaskTreeBuilder\TaskTreeBuilderInterface;
use App\Domain\User\Entity\User;
use App\Api\V1\RequestPayload\TaskCreatePayload;

readonly class CreateTaskHandler extends AbstractTaskHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly TaskTreeBuilderInterface $taskTreeBuilder,
        private readonly TaskBuilderInterface $taskBuilder
    ) {}

    public function handle(TaskCreatePayload $payload, User $user): array
    {
        $task = $this->createTask($payload, $user);

        return $this->taskTreeBuilder->build([$task]);
    }

    protected function createTask(TaskCreatePayload $payload, User $user): Task
    {
        $task = $this->taskBuilder
            ->setTitle($payload->title)
            ->setUser($user)
            ->setPriority($payload->priority)
            ->setDescription($payload->description)
            ->setStatus($payload->status)
            ->setParentId($payload->parentId)
            ->setCompletedAt($payload->completedAt)
            ->build();

        $this->taskRepository->save($task);

        return $task;
    }
}
