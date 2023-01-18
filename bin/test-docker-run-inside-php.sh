#!/usr/bin/env bash

APP_PATH="$(
  cd -- "$(dirname "$0")" >/dev/null 2>&1 || exit
  pwd -P
)" && cd "$APP_PATH"/../ || exit

docker-compose run -w /app php bash -c "php src/test-docker-run.php"
