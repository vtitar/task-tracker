<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Api\V1\RequestPayload\TaskListGet;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\User\Entity\User;

readonly class GetTaskListHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function getTasks(User $user, TaskListGet $query): array
    {
        return $this->taskRepository->filterUserTasks($user, $query);
    }
}
