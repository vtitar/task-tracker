<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Domain\Task\Service\TaskTreeBuilder\TaskTreeBuilderInterface;

readonly class GetTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskTreeBuilderInterface $taskTreeBuilder,
    ) {}

    public function getTaskDataById(int $id, User $user): array
    {
        $task = $this->taskRepository->findUserTask($id, $user);

        return $this->taskTreeBuilder->build([$task]);
    }

}
