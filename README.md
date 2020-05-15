
docker run -p 127.0.0.1:8000:8000 alfredotg/fsapi
docker run alfredotg/fsapi php artisan test
php bin/api-usage-example.php

composer install
docker build -t alfredotg/fsapi ./
