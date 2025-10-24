# Library Catalog - Yii2 Application

Каталог книг с системой авторизации, подпиской на авторов и отправкой SMS-уведомлений.

## Требования

- PHP 8.0 или выше
- MySQL 8.0 / MariaDB 10.3+
- Composer
- Расширения PHP: pdo, pdo_mysql, mbstring, intl, fileinfo, gd/imagick

## Функциональность

### Основные возможности

- **Каталог книг**
- **Авторы**
- **Роли пользователей**:
   - **Гость** (неаутентифицированный): просмотр книг/авторов, подписка на авторов
   - **Пользователь** (аутентифицированный): полный CRUD для книг и авторов
- **Подписка на авторов**: Гости могут подписаться по телефону на получение уведомлений о новых книгах
- **SMS-уведомления**: через SmsPilot API (с использованием очередей Yii2 Queue)
- **Отчет**: ТОП-10 авторов, выпустивших больше всего книг за конкретный год (доступен всем)


**Установленные пакеты:**
- `vlucas/phpdotenv` - для работы с .env файлами
- `yiisoft/yii2-imagine` - для обработки изображений
- `yiisoft/yii2-queue` - для очередей задач (SMS)

### 3. Создание .env файл по примеру .env.example

### 4. Создание базы данных с именем, прописанным в .env

### 5. Выполните команды
- `php yii migrate`
- `php yii rbac/init`


### 6. Создание директории для загрузок
```bash
mkdir web\uploads\books
```

Убедитесь, что у веб-сервера есть права на запись в эту директорию.

### Работа с очередью SMS

Для обработки очереди SMS запустите воркер в отдельном терминале:

```bash
php yii queue/listen
```

#### Альтернативно: обработка через cron

```bash
# Добавьте в crontab (обрабатывать каждую минуту)
* * * * * php /path/to/project/yii queue/run
```


Сервис `components/services/FileUploadService.php` подготовлен для миграции на S3.


Кратко:
1. Установите AWS SDK: `composer require aws/aws-sdk-php`
2. Настройте S3 client в `config/web.php`
3. Добавьте credentials в `.env`
4. Измените `storageType` на `'external'` в конфигурации сервиса
5. Обновите метод `getCoverImageUrl()` в модели `Book`

### Переход на RabbitMQ для очередей

В `config/web.php` и `config/console.php` замените конфигурацию `queue`:

```php
'queue' => [
    'class' => \yii\queue\amqp_interop\Queue::class,
    'host' => getenv('RABBITMQ_HOST'),
    'port' => getenv('RABBITMQ_PORT'),
    'user' => getenv('RABBITMQ_USER'),
    'password' => getenv('RABBITMQ_PASSWORD'),
    'queueName' => 'sms_notifications',
],
```

Добавьте в `.env`:
```env
RABBITMQ_HOST=localhost
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
```

## Команды консоли

```bash
# Миграции
php yii migrate                    # Применить миграции
php yii migrate/down               # Откатить последнюю миграцию

# RBAC
php yii rbac/init                  # Инициализация RBAC
php yii rbac/assign <userId> user  # Назначить роль пользователю

# Очередь
php yii queue/listen               # Запустить воркер (постоянно)
php yii queue/run                  # Обработать очередь (разово)
php yii queue/info                 # Информация об очереди
```