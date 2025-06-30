<?php

declare(strict_types=1);

namespace App\Domain\Task\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'task_search_index')]
class TaskSearchIndex
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    public int $taskId;

    #[ORM\OneToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    public Task $task;

    #[ORM\Column(type: 'text')]
    public string $content;
}
