<?php

declare(strict_types=1);

namespace App\Api\V1\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Api\V1\RequestHandler\GetTaskListHandler;
use App\Api\V1\RequestPayload\TaskListGet;
use App\Api\V1\RequestHandler\GetTaskHandler;
use App\Api\V1\RequestHandler\CreateTaskHandler;
use App\Api\V1\RequestPayload\TaskCreatePayload;
use App\Api\V1\RequestHandler\UpdateTaskHandler;
use App\Api\V1\RequestPayload\TaskUpdatePayload;
use App\Api\V1\RequestHandler\DeleteTaskHandler;


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

            $this->validateUser($user);

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
    public function getTaskAction(
        int $id,
        #[CurrentUser] ?User $user,
        GetTaskHandler $getTaskHandler
    ): JsonResponse {

        try {

            $this->validateUser($user);

            $taskData = $getTaskHandler->getTaskDataById($id, $user);
            return $this->json($taskData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->prepareError('Error on fetching task.', $e, $user, ['id' => $id]);
        }
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createTaskAction(
        Request $request,
        #[CurrentUser] ?User $user,
        CreateTaskHandler $createTaskHandler,
        #[MapRequestPayload] TaskCreatePayload $payload
    ): JsonResponse {
        try {

            $this->validateUser($user);

            $taskData = $createTaskHandler->handle($payload, $user);
            return $this->json($taskData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->prepareError('Error on task creation.', $e, $user, ['payload' => $payload]);
        }
    }

    #[Route('/{id}', name: 'task_update', methods: ['PUT'])]
    public function updateTaskAction(
        Request $request,
        int $id,
        #[CurrentUser] ?User $user,
        UpdateTaskHandler $updateTaskHandler,
        #[MapRequestPayload] TaskUpdatePayload $payload
    ): JsonResponse {
        try {

            $this->validateUser($user);

            $taskData = $updateTaskHandler->handle($id, $payload, $user);
            return $this->json($taskData, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->prepareError('Error updating task.', $e, $user, [
                'payload' => $payload,
                'id' => $id,
            ]);
        }
    }

    #[Route('/{id}', name: 'task_delete', methods: ['DELETE'])]
    public function deleteTaskAction(
        Request $request,
        int $id,
        #[CurrentUser] ?User $user,
        DeleteTaskHandler $deleteTaskHandler,
    ): JsonResponse {
        try {

            $this->validateUser($user);

            $deleteTaskHandler->handle($id, $user);
            return $this->json([], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->prepareError('Error deleting task.', $e, $user, ['id' => $id]);
        }
    }

    //TODO: move to separate own validator
    protected function validateUser(User $user): void
    {
        if (!$user) {
            throw new \Exception('No user found.', Response::HTTP_UNAUTHORIZED);
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
