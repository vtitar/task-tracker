# TaskTracker

A Symfony-based task tracking app with fulltext search and asynchronous reindexing using Symfony Messenger and Dockerized environment.

---

## Features

- User-specific task filtering (status, priority, search, etc)
- Fulltext search with custom MySQL search index table
- Async message processing for task reindexing
- Docker Compose setup for PHP, Nginx, MySQL, and messenger worker

---

## Requirements

- Docker & Docker Compose
- PHP 8.2 (Dockerized)
- MySQL 8.0 (Dockerized)
- Symfony CLI (optional, for local dev)

---


## Setup

1. Clone repository

   ```bash
   git clone https://github.com/vtitar/task-tracker.git
   cd task-tracker

2. Build and start containers

    ```bash
    docker compose up -d --build
    
3. Install PHP dependencies (inside php container)

    ```bash
    docker compose exec php composer install
   
4. Run database migrations and fixtures

    ```bash
    docker compose exec php php bin/console doctrine:migrations:migrate
    docker compose exec php php bin/console doctrine:fixtures:load


5. Configuration environment variables live in .env

 - Database URL configured as:

    ```bash
    DATABASE_URL=mysql://app:app@db:3306/app
   
 - Messenger transport uses Doctrine:

    ```bash
    MESSENGER_TRANSPORT_DSN=doctrine://default
   

## Running

 - Access the app at http://localhost:8080

 - Worker consuming async messages runs as Docker service:

    ```bash
    docker compose logs -f messenger_worker

 - To manually run the worker:

    ```bash
    docker compose exec messenger_worker php bin/console messenger:consume async -vv


## Development notes

 - Fulltext search uses a dedicated MySQL table task_search_index
 - Messages dispatched on task create/update events trigger async reindexing
 - Symfony events and subscribers handle query modifications and message dispatch
 - Docker Compose includes:
     - php (Symfony app)
     - nginx
     - db (MySQL)
     - messenger_worker (Symfony Messenger consumer)


## Troubleshooting

 - If messages are not processed, check worker logs:

    docker compose logs messenger_worker

 - Verify environment variables in worker service (DATABASE_URL, MESSENGER_TRANSPORT_DSN)
 - Ensure .env file is mounted inside containers or environment vars are set explicitly


## Testing

 - This login details could be used for testing fixtures
    `{
        "userkey": "user1",
        "password": "pass1"
    }`
 - could be provided postman collection for testing


## TODO
 - investigate issue with openApi documentation groups 
currently `/api/v1/task/list` response is not correct
 - replace message bus with RabbitMQ
 - Add Elasticsearch for improved search capabilities
 - Add automated tests 
 - Add separate reindex command that could be executed via cron
 - Improve pagination and sorting options in API endpoints
 - Optimize Docker setup for production (multi-stage builds, environment separation)
 - Add monitoring and logging for message processing failures
 - Implement retry and failure handling for async messages


