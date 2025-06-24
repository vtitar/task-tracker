<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskPriority;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskTreeBuilder\TaskTreeBuilderInterface;
use App\Domain\User\Entity\User;
use App\Api\V1\RequestPayload\TaskUpdatePayload;

readonly class UpdateTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskTreeBuilderInterface $taskTreeBuilder,
    ) {}

    public function handle(int $id, TaskUpdatePayload $payload, User $user): array
    {
        $task = $this->taskRepository->findUserTask($id, $user);

        $this->updateTask($task, $payload);

        return $this->taskTreeBuilder->build([$task]);
    }

    protected function updateTask(Task $task, TaskUpdatePayload $payload): void
    {
        if ($payload->title !== null) {
            $task->setTitle($payload->title);
        }

        if ($payload->description !== null) {
            $task->setDescription($payload->description);
        }

        if ($payload->priority !== null) {
            $task->setPriority(TaskPriority::from($payload->priority));
        }

        $this->taskRepository->save($task);
    }
}
