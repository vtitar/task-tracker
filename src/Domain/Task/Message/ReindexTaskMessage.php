<?php

declare(strict_types=1);

namespace App\Domain\Task\Message;

class ReindexTaskMessage
{
    public function __construct(
        public int $taskId
    ) {

    }
}
