# ⚙️ Docker
up:
    docker-compose up -d

down:
    docker-compose down

logs:
    docker-compose logs -f php

# 🧪 Symfony
cc:
    docker-compose exec php php bin/console cache:clear

migration:
    docker-compose exec php php bin/console make:migration

migrate:
    docker-compose exec php php bin/console doctrine:migrations:migrate

fixtures:
    docker-compose exec php php bin/console doctrine:fixtures:load --no-interaction

mail:
    docker-compose exec php php bin/console app:send-test-email

worker:
    docker-compose exec php php bin/console messenger:consume async -vv

# 📦 Front
dev:
    docker-compose exec php npm run dev

watch:
    docker-compose exec php npm run watch
