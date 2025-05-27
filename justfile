# Stripe
stripe-login:
    ./stripe login

stripe-listen:
    ./stripe listen --forward-to localhost:8080/webhook/stripe

stripe-init:
    ./stripe trigger checkout.session.completed



# Test
init-test:
    docker-compose exec php php bin/console doctrine:database:create --env=test

test:
    docker-compose exec php php bin/console doctrine:migrations:migrate --env=test --no-interaction
    docker-compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction
    docker-compose exec php ./vendor/bin/phpunit

# ⚙️ Docker
chown:
    sudo chown -R $(whoami):$(whoami) .

up:
    docker-compose up -d

down:
    docker-compose down

logs:
    docker-compose logs -f php

bash:
    docker-compose exec php bash

# 🧪 Symfony
cc:
    docker-compose exec php php bin/console cache:clear

cc-logs:
    docker-compose exec php rm -f var/log/dev.log

entity:
    docker-compose exec php php bin/console make:entity

form:
    docker-compose exec php php bin/console make:form

controller:
    docker-compose exec php php bin/console make:controller

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
    docker-compose exec php npm install --save-dev @symfony/webpack-encore
    docker-compose exec php npm run watch
