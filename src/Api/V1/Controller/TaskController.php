<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Api\V1\RequestHandler\GetTasksHandler;
use App\Api\V1\RequestPayload\TaskListGet;


#[Route('/tasks', name: 'tasks_')]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface    $logger
    )
    {}

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function listTasks(
        Request $request,
        GetTasksHandler $getTasksHandler,
        #[CurrentUser] ?User $user,
        #[MapQueryString] TaskListGet $query,
    ): JsonResponse {
        try {

            if (!$user) {
                throw new \Exception('No user found.', Response::HTTP_FORBIDDEN);
            }

            $tasks = $getTasksHandler->getTasks($user, $query);

            return $this->json($tasks, Response::HTTP_OK, [], ['groups' => ['task:list']]);
        } catch (\Exception $e) {

            $this->logger->error('Error on fetching tasks.', [
                'user' => $user ? $user->getUserkey() : '',
                'query' => $query,
                $e
            ]);

            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

            if ($e instanceof HttpExceptionInterface) {
                $statusCode = $e->getStatusCode();
            }

            return $this->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}
