<?php

namespace App\DataFixtures;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Service\TaskBuilder\TaskBuilderInterface;
use App\Domain\User\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Domain\Task\Enum\TaskPriority;
use App\Domain\Task\Enum\TaskStatus;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TaskBuilderInterface $taskBuilder,
        private readonly TaskRepository $taskRepository,
    ) {

    }

    public function load(ObjectManager $manager): void
    {
        $user1 = $this->createUser('user1','pass1');
        $manager->persist($user1);
        $manager->flush();

        $user2 = $this->createUser('user2','pass2');
        $manager->persist($user2);
        $manager->flush();

        $this->createTasksFixtures($manager, $user1, $user2);
    }

    protected function createUser(string $username, string $password): User
    {
        $user = new User();
        $user->setUserkey($username);
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }

    protected function createTask(
        User $user,
        string $title,
        int $priority,
        string $description,
        string $status,
        ?int $parentId = null,
        ?string $completedAt = null,
    ): Task {
        $task = $this->taskBuilder
            ->setTitle($title)
            ->setUser($user)
            ->setPriority($priority)
            ->setDescription($description)
            ->setStatus($status)
            ->setParentId($parentId)
            ->setCompletedAt($completedAt)
            ->build();

        $this->taskRepository->save($task);

        return $task;
    }

    protected function createTasksFixtures(ObjectManager $manager, User $user1, User $user2): void
    {
        $conn = $manager->getConnection();

        $user1Id = $user1->getId();
        $user2Id = $user2->getId();

        $sql = "INSERT INTO task (id, user_id, title, priority, description, status, parent_id, completed_at, created_at) VALUES
            (1, $user1Id, 'Title task 1', 5, 'Long text description description1', 'todo', null, null, '2025-04-29 13:13:13'),
            (2, $user2Id, 'Title task 2', 4, 'Long text description description2', 'todo', null, null, '2025-04-29 13:13:13'),
            (3, $user1Id, 'Title task 3', 3, 'Long text description description3', 'todo', 1, '2025-04-29 18:13:13', '2025-04-29 13:13:13'),
            (4, $user1Id, 'Title task 4', 2, 'Long text description description4', 'done', null, null, '2025-04-29 13:13:13'),
            (5, $user1Id, 'Title task 5', 1, 'Long text description description5', 'todo', 3, null, '2025-04-29 13:13:13')";

        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();

        $sql = "INSERT INTO task_search_index (task_id, content) VALUES
            (1, 'Title task 1 long text description description1'),
            (2, 'Title task 2 long text description description2'),
            (3, 'Title task 3 long text description description3'),
            (4, 'Title task 4 long text description description4'),
            (5, 'Title task 5 long text description description5')";

        $stmt = $conn->prepare($sql);
        $stmt->executeStatement();
    }
}
