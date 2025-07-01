<?php

declare(strict_types=1);

namespace App\Domain\Task\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'TaskNode')]
class TaskNode
{
    #[OA\Property(type: 'integer')]
    #[Groups(['list', 'detail'])]
    public int $id;

    #[OA\Property(type: 'integer')]
    #[Groups(['detail'])]
    public int $parentId;

    #[OA\Property(type: 'string')]
    #[Groups(['list', 'detail'])]
    public string $title;

    #[OA\Property(type: 'string', nullable: true)]
    #[Groups(['list', 'detail'])]
    public ?string $description = null;

    #[OA\Property(type: 'integer')]
    #[Groups(['list', 'detail'])]
    public int $priority;

    #[OA\Property(type: 'string')]
    #[Groups(['list', 'detail'])]
    public string $status;

    #[OA\Property(type: 'string', format: 'date-time', nullable: true)]
    #[Groups(['detail'])]
    public ?string $completedAt = null;

    #[OA\Property(type: 'string', format: 'date-time')]
    #[Groups(['list', 'detail'])]
    public string $createdAt;

    #[OA\Property(
        type: 'array',
        items: new OA\Items(ref: '#/components/schemas/TaskNode')
    )]
    #[Groups(['detail'])]
    public array $subtasks = [];

    public function __construct(
        int $id,
        int $parentId,
        string $title,
        string $description,
        int $priority,
        string $status,
        ?string $completedAt,
        string $createdAt,
        array $subtasks = []
    ) {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
        $this->completedAt = $completedAt;
        $this->createdAt = $createdAt;
        $this->subtasks = $subtasks;
    }
}

