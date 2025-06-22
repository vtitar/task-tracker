<?php

declare(strict_types=1);

namespace App\Api\V1\RequestPayload;

use Symfony\Component\Validator\Constraints\Choice;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Enum\TaskPriority;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TaskListGet
{
    const ALLOWED_SORT_FIELDS = ['priority', 'createdAt', 'completedAt'];

    public function __construct(
        public int $page = 1,
        public int $page_size = 10,

        public string $search = '',

        #[Choice([TaskStatus::DONE->value, TaskStatus::TODO->value, ''], message: 'Invalid status')]
        public string $status = '',

        #[Choice([
            TaskPriority::ONE->value,
            TaskPriority::TWO->value,
            TaskPriority::THREE->value,
            TaskPriority::FOUR->value,
            TaskPriority::FIVE->value,
            0], message: 'Invalid priority')]
        public int $priority = 0,

        /**
         * @var array<string, string> Sort fields and directions, e.g. ['priority' => 'desc', 'createdAt' => 'asc']
         */
        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Choice(
                choices: ['asc', 'desc'],
                message: 'Sort direction must be "asc" or "desc".'
            )
        ])]
        public array $sort = [
            'priority' => 'desc',
            'createdAt' => 'asc',
            'completedAt' => 'desc',
        ],

    ) {
        foreach ($sort as $field => $direction) {
            if (!in_array($field, self::ALLOWED_SORT_FIELDS, true)) {
                throw new \InvalidArgumentException("Invalid sort field: {$field}");
            }
        }
    }

}
