<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskSearchIndexer;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Entity\TaskSearchIndex;
use Doctrine\ORM\EntityManagerInterface;

class TaskSearchIndexer implements TaskSearchIndexerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function index(Task $task): void
    {
        $repository = $this->entityManager->getRepository(TaskSearchIndex::class);
        $index = $repository->find($task->getId()) ?? new TaskSearchIndex();

        $index->task = $task;
        $index->taskId = $task->getId();
        $index->content = mb_strtolower(
            trim($task->getTitle() . ' ' . $task->getDescription())
        );

        $this->entityManager->persist($index);
        $this->entityManager->flush();
    }

    public function remove(Task $task): void
    {
        $index = $this->entityManager->getRepository(TaskSearchIndex::class)->find($task->getId());
        if ($index) {
            $this->entityManager->remove($index);
            $this->entityManager->flush();
        }
    }
}
