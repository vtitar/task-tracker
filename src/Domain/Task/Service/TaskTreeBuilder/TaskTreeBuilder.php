<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskTreeBuilder;

use App\Domain\Task\Entity\Task;

class TaskTreeBuilder implements TaskTreeBuilderInterface
{
    /**
     * @param Task[] $tasks
     * @return array
     */
    public function build(array $tasks): array
    {
        $result = [];

        foreach ($tasks as $task) {
            $result[] = $this->buildNode($task);
        }

        return $result;
    }

    private function buildNode(Task $task): array
    {
        $children = [];

        foreach ($task->getSubtasks() as $subtask) {
            $children[] = $this->buildNode($subtask);
        }

        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus(),
            'completedAt' => $task->getCompletedAt()?->format('Y-m-d H:i:s'),
            'createdAt' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'subtasks' => $children,
        ];
    }
}
