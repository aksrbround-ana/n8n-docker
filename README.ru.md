# Инструкции по запуску:
## Для продакшена с Traefik:
### 1. Создайте .env файл и укажите свои пароли
cp .env.example .env

### 2. Установите зависимости для Yii
```bash
cd site
composer install
```

### 3. Замените domain.com на ваш реальный домен в docker-compose.yml

### 4. Убедитесь, что DNS записи указывают на ваш сервер:
#### n8n.domain.com → IP_сервера
#### site.domain.com → IP_сервера
#### pgadmin.domain.com → IP_сервера

### 5. Запустите
```bash
docker compose up -d
```
## Для локальной разработки (Linux Mint):
### 1. Создайте .env файл
```bash
cp .env.example .env
```

### 2. Установите зависимости для Yii
```bash
cd site
composer install
```

### 3. Запустите
```bash
docker compose -f docker-compose-local.yml up -d
```

### 4. Доступ:
#### n8n: http://localhost:5678
#### Сайт: http://localhost:8081
#### pgAdmin: http://localhost:5050
