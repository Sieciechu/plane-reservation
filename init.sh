#!/bin/bash
docker compose exec -it app php artisan migrate
docker compose exec -it app php artisan db:seed

