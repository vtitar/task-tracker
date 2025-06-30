<?php

declare(strict_types=1);

namespace App\Domain\Task\MessageHandler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Domain\Task\Message\ReindexTaskMessage;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskSearchIndexer\TaskSearchIndexerInterface;

#[AsMessageHandler]
class ReindexTaskMessageHandler
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly TaskSearchIndexerInterface $indexer
    ) {}

    public function __invoke(ReindexTaskMessage $message): void
    {
        $task = $this->taskRepository->find($message->taskId);

        if (!$task) {
            return;
        }

        $this->indexer->index($task);
    }
}
