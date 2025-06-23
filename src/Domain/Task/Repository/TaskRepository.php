<?php

declare(strict_types=1);

namespace App\Domain\Task\Repository;

use App\Api\V1\RequestPayload\TaskListGet;
use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
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

        //TODO: replace with search index
        if ($query->search !== '') {
            $qb->andWhere('t.title LIKE :search OR t.description LIKE :search')
                ->setParameter('search', '%' . $query->search . '%');
        }

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
        $this->getEntityManager()->persist($task);
        $this->getEntityManager()->flush();
    }
}
