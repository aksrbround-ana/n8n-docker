# Launch Instructions:
## For production with Traefik:
### 1. Create a .env file and set your passwords
cp .env.example .env

### 2. Install dependencies for Yii
```bash
cd site
composer install
```

### 3. Replace domain.com with your actual domain in docker-compose.yml

### 4. Make sure DNS records point to your server:
#### n8n.domain.com → server_IP
#### site.domain.com → server_IP
#### pgadmin.domain.com → server_IP

### 5. Start
```bash
docker compose up -d
```
## For local development (Linux Mint):
### 1. Create a .env file
```bash
cp .env.example .env
```

### 2. Install dependencies for Yii
```bash
cd site
composer install
```

### 3. Start
```bash
docker compose -f docker-compose-local.yml up -d
```

### 4. Access:
#### n8n: http://localhost:5678
#### Site: http://localhost:8081
#### pgAdmin: http://localhost:5050
