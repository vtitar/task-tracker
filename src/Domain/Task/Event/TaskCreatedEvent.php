<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use App\Domain\Task\Entity\Task;
use Symfony\Contracts\EventDispatcher\Event;


class TaskCreatedEvent extends Event
{
    public function __construct(
        public Task $task
    ) {}
}
