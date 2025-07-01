<?php

declare(strict_types=1);

namespace App\Api\V1\RequestPayload;

use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Enum\TaskPriority;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TaskCreatePayload
{
    public function __construct(
        #[Assert\NotBlank]
        public string $title,

        public string $description = '',

        #[Assert\Choice([
            TaskPriority::ONE->value,
            TaskPriority::TWO->value,
            TaskPriority::THREE->value,
            TaskPriority::FOUR->value,
            TaskPriority::FIVE->value,
        ], message: 'Invalid priority')]
        public int $priority = TaskPriority::THREE->value,

        #[Assert\Choice([TaskStatus::TODO->value, TaskStatus::DONE->value], message: 'Invalid status')]
        public string $status = TaskStatus::TODO->value,

        #[Assert\Type('int')]
        public ?int $parentId = null,

        #[Assert\DateTime(format: 'Y-m-d\TH:i:s')]
        public ?string $completedAt = null
    ) {}

}
