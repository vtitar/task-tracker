<?php

declare(strict_types=1);

namespace App\Api\V1\RequestHandler;

use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Response;

abstract readonly class AbstractTaskHandler
{
    public function validateUser(?User $user): void
    {
        if (!$user) {
            throw new \Exception('No user found.', Response::HTTP_UNAUTHORIZED);
        }
    }
}
