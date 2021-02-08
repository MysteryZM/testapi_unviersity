Стек:
nginx
php-fpm 7.4
postgresql
symfony 5.2

ВАЖНО! ИЗМЕНИТЬ В .env ДОСТУПЫ К БАЗЕ ДАННЫХ И gmail

в App\Service\EventService изменить константу дефолтного email отправителя

после скачивания репозитория и изменения .env:
1. cd /path/to/repository
2. composer install
3. php bin/console make:migrations
4. php bin/console doctrine:migrations:migrate
