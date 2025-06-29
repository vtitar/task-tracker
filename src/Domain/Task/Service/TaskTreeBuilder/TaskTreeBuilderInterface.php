<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskTreeBuilder;

use App\Domain\Task\Entity\Task;
use OpenApi\Attributes as OA;
use App\Domain\Task\DTO\TaskNode;

interface TaskTreeBuilderInterface
{
    /**
     * @param Task[] $tasks
     * @return TaskNode[]
     */
    public function build(array $tasks): array;
}
