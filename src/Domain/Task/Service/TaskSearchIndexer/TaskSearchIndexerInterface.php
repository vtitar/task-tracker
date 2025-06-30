<?php

declare(strict_types=1);

namespace App\Domain\Task\Service\TaskSearchIndexer;

use App\Domain\Task\Entity\Task;

interface TaskSearchIndexerInterface
{
    public function index(Task $task): void;
    public function remove(Task $task): void;
}
