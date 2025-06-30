<?php

declare(strict_types=1);

namespace App\Domain\Task\Repository;

use App\Api\V1\RequestPayload\TaskListGet;
use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Domain\Task\Event\TaskFilterQueryBuildEvent;
use Doctrine\ORM\UnitOfWork;
use App\Domain\Task\Event\TaskCreatedEvent;
use App\Domain\Task\Event\TaskUpdatedEvent;
use App\Domain\Task\Event\TaskDeletedEvent;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($registry, Task::class);
    }

    public function filterUserTasks(User $user, TaskListGet $query)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user);

        $qb->andWhere('t.parent is null');

        if ($query->status !== '') {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $query->status);
        }

        if ($query->priority !== 0) {
            $qb->andWhere('t.priority = :priority')
                ->setParameter('priority', $query->priority);
        }

        $this->eventDispatcher->dispatch(new TaskFilterQueryBuildEvent($qb, $user, $query));

        foreach ($query->sort as $field => $direction) {
            $qb->addOrderBy('t.' . $field, strtoupper($direction));
        }

        // Pagination: example, calculate offset
        $offset = ($query->page - 1) * $query->page_size;
        $qb->setFirstResult($offset)
            ->setMaxResults($query->page_size);

        return $qb->getQuery()->getResult();
    }

    public function save(Task $task): void
    {
        $em = $this->getEntityManager();
        $uow = $em->getUnitOfWork();

        $isNew = $uow->getEntityState($task) === UnitOfWork::STATE_NEW;

        $em->persist($task);
        $em->flush();

        if ($isNew) {
            $this->eventDispatcher->dispatch(new TaskCreatedEvent($task));
        } else {
            $this->eventDispatcher->dispatch(new TaskUpdatedEvent($task));
        }
    }

    public function findUserTask(int $id, User $user): Task
    {
        $task = $this->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if (!$task) {
            throw new \Exception('Task not found.');
        }

        return $task;
    }

    public function remove(Task $task): void
    {
        if (!$task->canBeDeleted()) {
            throw new \Exception('Task cant be deleted.');
        }

        $this->getEntityManager()->remove($task);
        $this->getEntityManager()->flush();

        $this->eventDispatcher->dispatch(new TaskDeletedEvent($task));
    }
}
