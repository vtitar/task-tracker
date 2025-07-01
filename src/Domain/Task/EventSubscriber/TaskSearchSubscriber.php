<?php

declare(strict_types=1);

namespace App\Domain\Task\EventSubscriber;

use App\Api\V1\RequestPayload\TaskListGet;
use App\Domain\Task\Event\TaskFilterQueryBuildEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskSearchSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            TaskFilterQueryBuildEvent::class => 'onTaskFilterQueryBuild',
        ];
    }

    public function onTaskFilterQueryBuild(TaskFilterQueryBuildEvent $event): void
    {
        $qb = $event->qb;
        $query = $event->query;

        if ($query->search === '') {
            return;
        }

        $taskIds = $this->getTasksIdsFromMatch($query);

        if (count($taskIds) === 0) {
            $taskIds = [0];
        }

        $qb->andWhere($qb->expr()->in('t.id', ':search_ids'))
            ->setParameter('search_ids', $taskIds);
    }

    protected function getTasksIdsFromMatch(TaskListGet $query): array
    {
        $conn = $this->entityManager->getConnection();

        $sql = <<<SQL
            SELECT tsi.task_id
            FROM task_search_index tsi
            WHERE MATCH(tsi.content) AGAINST(:search IN BOOLEAN MODE)
        SQL;

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['search' => $query->search]);
        $ids = $result->fetchAllAssociative();

        return array_column($ids, 'task_id');
    }
}
