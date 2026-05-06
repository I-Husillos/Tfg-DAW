# SmartBudget — TFG DAW

Aplicación web de gestión financiera personal desarrollada como Proyecto de Fin de Grado del ciclo formativo de Desarrollo de Aplicaciones Web (DAW).

SmartBudget permite registrar ingresos y gastos, organizar categorías, definir presupuestos mensuales con alertas automáticas y consultar informes visuales. Incluye un asistente de IA integrado (Ollama) que responde preguntas sobre los datos financieros del usuario y ayuda a navegar la aplicación.

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8 / Laravel 11 |
| Frontend | Blade + AdminLTE, Vite, Chart.js, DataTables |
| Base de datos | MySQL 8 |
| Caché / Colas | Redis |
| Servidor web | Apache |
| IA local | Ollama (`llama3.2:3b`) |
| Correo (dev) | Mailpit |
| Contenerización | Docker & Docker Compose |

## Servicios y puertos

| Servicio | URL / Puerto |
|---|---|
| Aplicación web | http://localhost:8080 |
| phpMyAdmin | http://localhost:8081 |
| Mailpit (bandeja dev) | http://localhost:8025 |
| MySQL | localhost:3306 |
| Redis | localhost:6379 |
| Ollama API | localhost:11434 |

> Los puertos son configurables desde el archivo `.env` en la raíz del proyecto.

## Requisitos previos

- [Docker](https://www.docker.com/get-started) (con Compose v2)

No se necesita PHP, Node ni Composer instalados localmente. Todo corre dentro de los contenedores.

---

## Instalación y puesta en marcha

### 1. Clonar el repositorio

```bash
git clone <URL-del-repositorio>
cd TFG-DAW
```

### 2. Levantar los contenedores

```bash
docker compose up -d --build
```

Esto arranca todos los servicios: PHP, Apache, MySQL, Redis, Ollama, Mailpit y phpMyAdmin.

### 3. Instalar dependencias de PHP

```bash
docker compose run --rm service-composer install
```

### 4. Configurar el entorno de Laravel

```bash
docker compose exec service-php cp .env.example .env
docker compose exec service-php php artisan key:generate
```

El `.env.example` ya viene preconfigurado para el entorno Docker (hosts, puertos, credenciales). En la mayoría de casos no es necesario modificar nada más.

### 5. Instalar dependencias frontend y compilar assets

```bash
docker compose exec service-php npm ci
docker compose exec service-php npm run build
```

Si queremos que los cambios se compilen automaticamente sin tener que ejecutar npm run build cada vez que hacemos cambios ejecutamos:
```bash
docker compose exec service-php npm run build
```

### 6. Ejecutar las migraciones

```bash
docker compose exec service-php php artisan migrate
```

Opcionalmente, poblar la base de datos con datos de prueba:

```bash
docker compose exec service-php php artisan db:seed
```

### 7. Descargar el modelo de IA (Ollama)

El asistente de IA requiere descargar el modelo una sola vez:

```bash
docker compose exec service-ollama ollama pull llama3.2:3b
```

> El modelo ocupa ~2 GB. El asistente funciona completamente en local, sin conexión a servicios externos.

La aplicación estará disponible en **http://localhost:8080**.

---

## Configuración del `.env` de Laravel

El archivo `src/.env.example` ya contiene todos los valores necesarios para el entorno Docker. Estos son los más relevantes si necesitas ajustarlos:

**Base de datos (MySQL)**
```
DB_CONNECTION=mysql
DB_HOST=service-mysql
DB_PORT=3306
DB_DATABASE=baseDatosMysql
DB_USERNAME=user
DB_PASSWORD=1234
```

**Caché y colas (Redis)**
```
REDIS_HOST=service-redis
REDIS_PORT=6379
```

**Correo (Mailpit — solo desarrollo)**
```
MAIL_MAILER=smtp
MAIL_HOST=service-mailpit
MAIL_PORT=1025
```

**IA (Ollama)**
```
OLLAMA_HOST=http://service-ollama:11434
OLLAMA_MODEL=llama3.2:3b
```

> Las credenciales de MySQL en `src/.env` deben coincidir con las del `.env` raíz del proyecto (variables `DOCKER_MYSQL_*`).

---

## Estructura del proyecto

```
TFG-DAW/
├── docker-compose.yml          # Orquestación de servicios
├── .env                        # Variables de Docker (puertos, credenciales)
├── .docker/                    # Dockerfiles y configuración de servicios
└── src/                        # Código fuente Laravel
    ├── app/
    │   ├── Http/Controllers/   # Controladores web y API
    │   ├── Models/             # Modelos Eloquent
    │   ├── Services/           # Lógica de negocio (SRP)
    │   └── Notifications/      # Notificaciones de alertas
    ├── database/
    │   └── migrations/         # Estructura de la base de datos
    ├── resources/
    │   ├── views/              # Vistas Blade
    │   ├── js/                 # JavaScript (DataTables, charts, chat IA)
    │   └── lang/               # Traducciones (es / en)
    └── routes/
        ├── web.php             # Rutas web
        └── api.php             # Rutas API (DataTables)
```

## Base de datos

| Tabla | Descripción |
|---|---|
| `users` | Credenciales de autenticación |
| `profiles` | Datos personales y preferencias (moneda, idioma, zona horaria) |
| `categories` | Categorías y subcategorías del usuario |
| `transactions` | Registros de ingresos y gastos |
| `budgets` | Límites de gasto mensuales por categoría |
| `audit_logs` | Historial de acciones (creación, edición, eliminación) |
| `cache`, `jobs` | Tablas auxiliares de Laravel |

