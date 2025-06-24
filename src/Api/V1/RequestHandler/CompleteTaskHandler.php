<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskTreeBuilder\TaskTreeBuilderInterface;
use App\Domain\User\Entity\User;

readonly class CompleteTaskHandler extends AbstractTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskTreeBuilderInterface $taskTreeBuilder,
    ) {}

    public function handle(int $id, User $user): array
    {
        $task = $this->taskRepository->findUserTask($id, $user);

        $this->completeTask($task);

        return $this->taskTreeBuilder->build([$task]);
    }

    protected function completeTask(Task $task): void
    {
        if ($task->getStatus() === TaskStatus::DONE) {
            throw new \Exception('Task already completed.');
        }

        if (!$task->areAllSubtasksCompleted()) {
            throw new \Exception('Cannot complete task. Some subtasks are not completed yet.');
        }

        $task->setStatus(TaskStatus::DONE);
        $task->setCompletedAt(new \DateTimeImmutable());

        $this->taskRepository->save($task);
    }

}
