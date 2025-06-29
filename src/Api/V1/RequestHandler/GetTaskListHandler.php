<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Api\V1\RequestPayload\TaskListGet;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskTreeBuilder\TaskTreeBuilderInterface;
use App\Domain\User\Entity\User;

readonly class GetTaskListHandler extends AbstractTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskTreeBuilderInterface $taskTreeBuilder,
    ) {}

    public function getTasks(User $user, TaskListGet $query): array
    {
        $tasks = $this->taskRepository->filterUserTasks($user, $query);
        return $this->taskTreeBuilder->build($tasks);
    }
}
