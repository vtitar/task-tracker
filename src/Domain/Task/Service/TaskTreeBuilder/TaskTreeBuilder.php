<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskTreeBuilder;

use App\Domain\Task\DTO\TaskNode;
use App\Domain\Task\Entity\Task;

class TaskTreeBuilder implements TaskTreeBuilderInterface
{
    /**
     * @param Task[] $tasks
     * @return TaskNode[]
     */
    public function build(array $tasks): array
    {
        $result = [];

        foreach ($tasks as $task) {
            $result[] = $this->buildNode($task);
        }

        return $result;
    }

    private function buildNode(Task $task): TaskNode
    {
        $children = [];

        foreach ($task->getSubtasks() as $subtask) {
            $children[] = $this->buildNode($subtask);
        }

        return new TaskNode(
            $task->getId(),
            $task->getParent() ? $task->getParent()->getId() : 0,
            $task->getTitle(),
            $task->getDescription(),
            $task->getPriority()->value,
            $task->getStatus()->value,
            $task->getCompletedAt() ? $task->getCompletedAt()->format('Y-m-d H:i:s') : '',
            $task->getCreatedAt()->format('Y-m-d H:i:s'),
            $children
        );
    }
}
