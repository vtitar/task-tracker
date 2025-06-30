<?php

declare(strict_types=1);

namespace App\Domain\Task\EventSubscriber;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Event\TaskCreatedEvent;
use App\Domain\Task\Event\TaskUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Domain\Task\Message\ReindexTaskMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskLifecycleSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly MessageBusInterface $bus
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            TaskCreatedEvent::class => 'onCreated',
            TaskUpdatedEvent::class => 'onUpdated',
        ];
    }

    public function onCreated(TaskCreatedEvent $event): void
    {
        $task = $event->task;
        $this->searchReindex($task);
    }

    public function onUpdated(TaskUpdatedEvent $event): void
    {
        $task = $event->task;
        $this->searchReindex($task);
    }

    protected function searchReindex(Task $task): void
    {
        $this->bus->dispatch(new ReindexTaskMessage($task->getId()));
    }

}
