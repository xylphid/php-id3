#!/bin/sh

docker run --rm -it \
  -v $(pwd):/app \
  -v "/home/xylphid/Musique/Animes/[Nipponsei] Mai-Otome Original Soundtrack Vol.2 - Otome no Inori (320 kbps)/10 - Kindan no Hanazono.mp3:/tmp/media.mp3" \
  php:cli-alpine3.17 php /app/test.php
#  -v "/path/to/media.mp3:/tmp/media.mp3" \
#  -v "/home/xylphid/Musique/Lana Del Rey/Norman Fucking Rockwell/03-lana_del_rey-venice_bitch.mp3:/tmp/media.mp3" \
