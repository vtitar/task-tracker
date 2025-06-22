<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class GetTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function getTaskById(int $id, User $user): array
    {
        $task = $this->taskRepository->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if (!$task) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $this->buildTaskTree($task);
    }

    protected function buildTaskTree(Task $task): array
    {
        $tree = [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'priority' => $task->getPriority(),
            'createdAt' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'completedAt' => $task->getCompletedAt() ? $task->getCompletedAt()->format('Y-m-d H:i:s') : null,
            'subtasks' => [],
        ];

        foreach ($task->getSubtasks() as $subtask) {
            $tree['subtasks'][] = $this->buildTaskTree($subtask);
        }

        return $tree;
    }
}
