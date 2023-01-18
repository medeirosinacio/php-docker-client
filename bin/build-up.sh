#!/usr/bin/env bash

printf "============> Start build \n"

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
timeout=30
start_time=$(date +%s)
while [ $status_code -ne 200 ]; do
  printf "."
  status_code=$(docker-compose exec -w /app php curl -s -o /dev/null -w "%{http_code}" http://docker-api:2375/version)
  sleep 2
  current_time=$(date +%s)
  if [ $((current_time - start_time)) -ge $timeout ]; then
    printf " Error!\n"
    printf "============> Timeout reached while waiting for API service initialization. Exiting.\n"
    exit 1
  fi
done

printf " Ready!\n"
printf "============> Build complete. Run bin/test-docker-run-inside-php.sh to test the implementation. \n"
