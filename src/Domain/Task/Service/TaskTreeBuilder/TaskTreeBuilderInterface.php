<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskTreeBuilder;

use App\Domain\Task\Entity\Task;

interface TaskTreeBuilderInterface
{
    /**
     * @param Task[] $tasks
     * @return array
     */
    public function build(array $tasks): array;
}
