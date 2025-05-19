
# API Backend - Bolsa de Trabajo UTEPSA

Este proyecto es una API REST desarrollada en **Laravel** que gestiona la plataforma de bolsa de trabajo de UTEPSA. Proporciona funcionalidades para estudiantes, empresas y administradores, incluyendo autenticación mediante **Auth0**, uso de **JWT**, envío de correos, almacenamiento de archivos en **Cloudinary**, y análisis de texto con **Dialogflow y Perspective API**.

---

## Tecnologías

- **Laravel** 10+
- **Auth0** + JWT para autenticación social y tokens
- **MySQL** para persistencia de datos
- **Cloudinary** para almacenamiento de CVs
- **Dialogflow** + **Perspective API** para análisis de lenguaje
- **SMTP Gmail** para envío de correos

---

## Instalación

### 1. Clona el proyecto

```bash

git clone https://github.com/UTEPSADESARROLLOSISTEMAS/SISTEMA-BOLSA-DE-TRABAJO-BACKEND.git

cd SISTEMA-BOLSA-DE-TRABAJO-BACKEND

```

### 2. Instala dependencias

```bash

composer install

```

### 3. Crea el archivo .env

Copia el archivo .env.

example o usa este como base:

```bash


cp .env.example .env

Luego configura tus variables:


env

Copy

Edit

APP_NAME=Laravel

APP_ENV=local

APP_URL=http://localhost

APP_KEY= # Generado en el siguiente paso


DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=sistema_bolsa_de_trabajo_backend

DB_USERNAME=root

DB_PASSWORD=


# Auth0

AUTH0_DOMAIN=utepsa.us.auth0.com

AUTH0_CLIENT_SECRET=...


# JWT

JWT_SECRET=...


# Cloudinary

CLOUDINARY_URL=...

```

### 4. Genera la clave de la app

```bash


php artisan key:generate


```

#### Ejecutar el servidor local

```bash


php artisan serve

```

API disponible en: http://localhost:8000

# Documentación Swagger

La API está documentada con Swagger/OpenAPI.

Generar documentación:

```bash


php artisan l5-swagger:generate

```

Acceder en navegador:

```bash


http://localhost:8000/api/documentation

```

# Autenticación

Social login y tokens

El sistema usa Auth0 para login social (Google, Microsoft, GitHub), y genera JWT para proteger rutas.

Rutas protegidas usan middleware auth:api

El token se envía en los headers:

```http


Authorization: Bearer {token}

```

# Almacenamiento de archivos

Los CVs y currículums de los estudiantes se almacenan en Cloudinary

Los archivos se validan por tipo, tamaño y se guardan junto al perfil del usuario

# Integración con IA

Se utiliza Dialogflow para análisis semántico de preguntas

Perspective API para analizar lenguaje ofensivo o inapropiado en descripciones o mensajes

# Correo

El sistema envía notificaciones automáticas por correo a las empresas:

Cuando un estudiante se postula

Cuando se aprueba una oferta

Usa configuración SMTP de Gmail en .env:

```env


MAIL_MAILER=smtp

MAIL_HOST=smtp.gmail.com

MAIL_PORT=587

MAIL_USERNAME=utepsadesarrollosistemas@gmail.com

MAIL_PASSWORD=...

```

# Seguridad

Protegido con Auth0 y JWT

Validación robusta con Laravel Validator

Control de acceso por roles

Lógica separada para estudiantes, empresas y admins

# Estructura del proyecto

```bash


app/

├── Http/

│   ├── Controllers/

│   │   ├── Auth/

│   │   ├── PostulacionesController.php

│   │   ├── EmpresaController.php

│   │   ├── TrabajoController.php

│   │   └── ...

├── Models/

├── Services/

├── Providers/

routes/

├── api.php

database/

├── migrations/

├── seeders/

```

# Contacto

utepsadesarrollosistemas@gmail.com

Proyecto académico para la Universidad Tecnológica Privada de Santa Cruz – UTEPSA
