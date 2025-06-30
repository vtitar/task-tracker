<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use Doctrine\ORM\QueryBuilder;
use App\Domain\User\Entity\User;
use App\Api\V1\RequestPayload\TaskListGet;
use Symfony\Contracts\EventDispatcher\Event;

class TaskFilterQueryBuildEvent extends Event
{
    public function __construct(
        public QueryBuilder $qb,
        public User $user,
        public TaskListGet $query
    ) {}
}
