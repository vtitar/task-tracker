<?php

declare(strict_types=1);

namespace App\Api\V1\RequestPayload;

use App\Domain\Task\Enum\TaskPriority;
use Symfony\Component\Validator\Constraints as Assert;

readonly class TaskUpdatePayload
{
    public function __construct(

        public ?string $title = null,

        public ?string $description = null,

        #[Assert\Choice([
            TaskPriority::ONE->value,
            TaskPriority::TWO->value,
            TaskPriority::THREE->value,
            TaskPriority::FOUR->value,
            TaskPriority::FIVE->value,
        ], message: 'Invalid priority')]
        public ?int $priority = null,

    ) {}

}
