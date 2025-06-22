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
use App\Api\V1\RequestHandler\GetTaskListHandler;
use App\Api\V1\RequestPayload\TaskListGet;
use App\Api\V1\RequestHandler\GetTaskHandler;


#[Route('/task', name: 'task_')]
final class TaskController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface    $logger
    )
    {}

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function getTaskListAction(
        Request $request,
        GetTaskListHandler $getTaskListHandler,
        #[CurrentUser] ?User $user,
        #[MapQueryString] TaskListGet $query,
    ): JsonResponse {
        try {

            if (!$user) {
                throw new \Exception('No user found.', Response::HTTP_UNAUTHORIZED);
            }

            $tasks = $getTaskListHandler->getTasks($user, $query);

            return $this->json($tasks, Response::HTTP_OK, [], ['groups' => ['task:list']]);
        } catch (\Exception $e) {
            return $this->prepareError(
                'Error on fetching tasks list.',
                $e,
                $user,
                [
                    'query' => $query
                ]
            );
        }
    }

    #[Route('/{id}', name: 'get_task', methods: ['GET'])]
    public function getTask(
        int $id,
        #[CurrentUser] ?User $user,
        GetTaskHandler $getTaskHandler
    ): JsonResponse {

        try {

            if (!$user) {
                throw new \Exception('No user found.', Response::HTTP_UNAUTHORIZED);
            }

            $task = $getTaskHandler->getTaskById($id, $user);
            return $this->json($task, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->prepareError('Error on fetching task.', $e, $user, ['id' => $id]);
        }
    }

    protected function prepareError(string $message, \Exception $exception, User $user, array $context): JsonResponse
    {
        $context['user'] = $user ?  $user->getUserkey() : '';
        $context[] = $exception;

        $this->logger->error($message, $context);

        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        return $this->json([
            'error' => $exception->getMessage()
        ], $statusCode);
    }
}
