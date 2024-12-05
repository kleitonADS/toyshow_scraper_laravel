# **ToyShow Scraper**

## **Descrição**
O **ToyShow Scraper** é uma aplicação desenvolvida em Laravel que realiza o scraping de produtos diretamente do site **ToyShow**, permitindo a coleta, visualização e filtragem de dados de forma prática e eficiente. O sistema utiliza filas assíncronas para lidar com grandes volumes de dados, garantindo escalabilidade e performance.

---

## **Requisitos**
- **PHP** >= 8.1
- **Composer** >= 2.x
- **Node.js** >= 16.x
- **MySQL** >= 8.x
- **Extensões PHP**: 
  - `dom`
  - `curl`
  - `mbstring`
  - `json`

---

## **Instalação**

### 1. **Clone o repositório**
```bash
git clone https://github.com/kleitonADS/toyshow_scraper_laravel.git
cd toyshow-scraper
```
### 2. **Instale as dependências**
Back-end (Laravel):
```bash
composer install
```
Front-end:
```bash
npm install
npm run dev
```

### 3. ** Configure o .env**
Crie o arquivo .env com base no exemplo: Banco de dados
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=seu_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
Queue:
```bash
QUEUE_CONNECTION=database
```

### 4. ** Gere a chave de aplicação **
```bash
php artisan key:generate
```
### 5. ** Configure o banco de dados **
Execute o seguinte SQL no seu servidor MySQL para criar as tabelas necessárias:

```bash
CREATE DATABASE IF NOT EXISTS toyshow_scraper CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE toyshow_scraper;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    brand VARCHAR(255),
    description TEXT,
    price DECIMAL(10, 2) DEFAULT NULL,
    price_off DECIMAL(10, 2) DEFAULT NULL,
    image_src VARCHAR(255),
    image_alt VARCHAR(255),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    reserved_at INT UNSIGNED DEFAULT NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL
);

CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
Depois disso, execute:
```bash
php artisan migrate
```
