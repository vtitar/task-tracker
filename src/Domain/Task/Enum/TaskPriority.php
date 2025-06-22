<?php

declare(strict_types=1);

namespace App\Domain\Task\Enum;

enum TaskPriority: int
{
    case ONE   = 1;
    case TWO   = 2;
    case THREE = 3;
    case FOUR  = 4;
    case FIVE  = 5;
}
