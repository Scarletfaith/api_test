## Информация

- Разработка производилась в виртуальной среде [VirtualBox](https://www.virtualbox.org/)
- Использовался Swagger-ui

## Установка и настройка

```bash
$ git clone https://github.com/Scarletfaith/api_test.git
$ cd api_test
$ cp .env.example .env
```

Обязательно открываем в редакторе файл .env и настраиваем доступ к базе данных:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1 [Или полный url, если настраиваете на сервере]
DB_PORT=3306 [Не меняете, если не используется другой порт]
DB_DATABASE=api [Название БД]
DB_USERNAME=root [Логин к БД]
DB_PASSWORD= [Пароль к БД]
```

Продолжаем настройку

```bash
$ composer install
$ php artisan key:generate
$ php artisan migrate:fresh
$ php artisan storage:link
```

## Добавление Seeds

- В БД внесено 3 пользователя
- В БД пользователей created_at и updated_at будут установлены на момент выгрузки сида

```bash
$ php artisan db:seed
