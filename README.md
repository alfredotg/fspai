Контроллеры находятся в app/Http/Controllers/.
Тесты в tests/Feature/.

## Запуск тестов
```
docker run alfredotg/fsapi php artisan test
```

## Запуск сервера
```
docker run -p 127.0.0.1:8000:8000 alfredotg/fsapi
```

Пример использования api
```
php bin/api-usage-example.php
```

## Сборка docker-образа
```
composer install
docker build -t alfredotg/fsapi ./
```
