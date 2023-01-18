#!/usr/bin/env bash

APP_PATH="$(
  cd -- "$(dirname "$0")" >/dev/null 2>&1 || exit
  pwd -P
)" && cd "$APP_PATH"/../ || exit

printf "============> Installing dependencies \n"
docker run --rm --interactive --tty -v $PWD:/app --dns 1.1.1.1 --user $(id -u):$(id -g) composer install

printf "============> Starting containers \n"
docker-compose up -d --build --force-recreate

printf "============> Waiting for API service initialization \n"
status_code=0
while [ $status_code -ne 200 ]; do
  status_code=$(docker-compose run -w /app php curl -s -o /dev/null -w "%{http_code}" http://docker-api:2375/version)
  sleep 5
done
