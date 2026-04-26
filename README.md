# Полезные ссылки
- https://laravel.com/docs/13.x/installation
- https://phptherightway.com/

# Начало проекта
## Установка зависимостей
Для macos выполните в терминале следующие команды:

```bash
  brew install php
```
```bash
    brew install composer
```
```bash
composer global require/laravel installer
```
```bash
echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.szhrc
```
```bash
source ~/.zshrc
```
```bash
laravel new my-project
```
```bash
cd my-project
```
```bash
npm install & npm run build
```
```bash
composer run dev
```

Это позволит установить php, менеджер пакетов, фреймворк laravel, и запустить локальный сервер.

## Настройка базы данных
Используемая база: MySQL. Для её запуска будет использоваться Docker.

Создайте папку `.docker` в корне приложения, а в ней файл ```docker-compose.yml```.
В файле пропишите следующую конфигурацию:
```dockerfile
name: 'your-name'

services:
  mysql:
    image: 'mysql:8.0'
    container_name: 'your-container-name'
    restart: 'always'
    ports:
      - '3306:3306'
    environment:
      MYSQL_ROOT_PASSWORD: 'your-root-password'
      MYSQL_DATABASE: 'your-database-name'
      MYSQL_USER: 'your-user'
      MYSQL_PASSWORD: 'your-user-password'
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - app-network

networks:
  app-network:
    driver: bridge

volumes:
  mysql_data:
    driver: local

```

Эта конфигурация позволит поднять локально базу данных с помощью команды 
```bash
docker-compose up -d
```

Далее нам нужно создать новую модель - Post с помощью команды
```bash
php artisan make:model Post -a
```

Теперь нужно ввести обработку исключений. Будем использовать подход failfast - падать быстро и громко.
Для этого в `./app` заведем новую папку `./Exceptions` и создадим набор базовых исключений -
 - InvalidCredentialsException
 - UserNotFoundException
 - UserAlreadyExistsException

Для этого используем команду
```bash
php artisan make:exception 'exception-name'
```

Теперь нужно ввести регистрацию исключений в `./bootstrap/app.php` в Exceptions middleware - 
```php
->withExceptions(function (Exceptions $exceptions) {

        // HandleDomainException
        $exceptions->report(function (DomainException $e) {
            Log::warning("Business error: {$e->getErrorCode()} - {$e->getMessage()}", [
                'exception' => $e,
                'errorCode' => $e->getErrorCode()
            ]);
        })->stop(); // Предотвращает дублирование в основной лог

        // HandleException
        $exceptions->report(function (Throwable $e) {
            Log::error("Unhandled server error: " . $e->getMessage(), [
                'exception' => $e
            ]);
        });

        // WriteProblemDetails
        $exceptions->render(function (Throwable $e, Request $request) {
            
            // Если это доменное исключение
            if ($e instanceof DomainException) {
                return response()->json([
                    'status' => $e->getCode(), // HTTP статус (400)
                    'title'  => 'Business Error',
                    'detail' => $e->getMessage(),
                    'extensions' => [
                        'StatusCode' => $e->getErrorCode() // ErrorCode
                    ]
                ], $e->getCode());
            }

            // Internal Server Error
            return response()->json([
                'status' => 500,
                'title'  => 'Internal Server Error',
                'detail' => config('app.debug') ? $e->getMessage() : 'An unexpected error occurred.'
            ], 500);
        });
    })->create();
```


