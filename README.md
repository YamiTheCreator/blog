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
