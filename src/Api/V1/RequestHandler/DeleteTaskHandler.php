<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Repository\TaskRepository;
use App\Domain\User\Entity\User;

readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {}

    public function handle(int $id, User $user): void
    {
        $task = $this->taskRepository->findUserTask($id, $user);
        $this->taskRepository->remove($task);
    }

}
