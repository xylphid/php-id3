#!/bin/sh

docker run --rm -it \
  -v $(pwd):/app \
  -v "/path/to/media.mp3:/tmp/media.mp3" \
  php:cli-alpine3.17 sh -c "apk add ffmpeg; php /app/test.php"
