<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

php artisan key:generate

php artisan jwt:secret

php artisan  storage:link

php artisan migrate 


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📂 **Autenticación (JWT)**

Estos endpoints permiten el manejo de autenticación para ambos roles (`Estudiantes` y `Empresas`).

| Método | URL           | Descripción                                       |
| ------- | ------------- | -------------------------------------------------- |
| POST    | /api/register | Registrar nuevo usuario (Estudiante o Empresa).    |
| POST    | /api/login    | Iniciar sesión y obtener JWT.                     |
| POST    | /api/logout   | Cerrar sesión (invalidar token).                  |
| GET     | /api/profile  | Obtener datos del usuario autenticado (Protegido). |

---

## 📂 **Usuarios (`Usuario`)**

Usado para manejar la autenticación, perfiles, y gestión de cuentas.

| Método | URL                | Descripción                    |
| ------- | ------------------ | ------------------------------- |
| GET     | /api/usuarios      | Listar todos los usuarios.      |
| POST    | /api/usuarios      | Crear un nuevo usuario.         |
| GET     | /api/usuarios/{id} | Obtener un usuario específico. |
| PUT     | /api/usuarios/{id} | Actualizar un usuario.          |
| DELETE  | /api/usuarios/{id} | Eliminar un usuario.            |

---

## 📂 **Estudiantes (`Estudiante`)**

Acciones específicas para estudiantes autenticados.

| Método | URL                   | Descripción                                |
| ------- | --------------------- | ------------------------------------------- |
| GET     | /api/estudiantes      | Listar todos los estudiantes.               |
| POST    | /api/estudiantes      | Crear un nuevo estudiante.                  |
| GET     | /api/estudiantes/{id} | Obtener datos de un estudiante específico. |
| PUT     | /api/estudiantes/{id} | Actualizar datos de un estudiante.          |
| DELETE  | /api/estudiantes/{id} | Eliminar un estudiante.                     |

---

## 📂 **Empresas (`Empresas`)**

Acciones específicas para empresas autenticadas.

| Método | URL                | Descripción                              |
| ------- | ------------------ | ----------------------------------------- |
| GET     | /api/empresas      | Listar todas las empresas.                |
| POST    | /api/empresas      | Crear una nueva empresa.                  |
| GET     | /api/empresas/{id} | Obtener datos de una empresa específica. |
| PUT     | /api/empresas/{id} | Actualizar datos de una empresa.          |
| DELETE  | /api/empresas/{id} | Eliminar una empresa.                     |

---

## 📂 **Trabajos (`Trabajo`)**

Publicación y gestión de trabajos por parte de las empresas.

| Método | URL                | Descripción                               |
| ------- | ------------------ | ------------------------------------------ |
| GET     | /api/trabajos      | Listar todos los trabajos disponibles.     |
| POST    | /api/trabajos      | Publicar un nuevo trabajo (Solo Empresas). |
| GET     | /api/trabajos/{id} | Obtener detalles de un trabajo.            |
| PUT     | /api/trabajos/{id} | Actualizar un trabajo.                     |
| DELETE  | /api/trabajos/{id} | Eliminar un trabajo.                       |

---

## 📂 **Postulaciones (`Postulacion`)**

Acciones que permiten a los estudiantes postularse a trabajos.

| Método | URL                     | Descripción                                     |
| ------- | ----------------------- | ------------------------------------------------ |
| GET     | /api/postulaciones      | Listar todas las postulaciones.                  |
| POST    | /api/postulaciones      | Crear una nueva postulación (Solo Estudiantes). |
| GET     | /api/postulaciones/{id} | Obtener detalles de una postulación.            |
| PUT     | /api/postulaciones/{id} | Actualizar una postulación.                     |
| DELETE  | /api/postulaciones/{id} | Eliminar una postulación.                       |

---

## 📂 **Fases (`Fase`)**

Manejo de procesos dentro de las postulaciones (Progresos, Etapas, etc.).

| Método | URL             | Descripción                              |
| ------- | --------------- | ----------------------------------------- |
| GET     | /api/fases      | Listar todas las fases.                   |
| POST    | /api/fases      | Crear una nueva fase en una postulación. |
| GET     | /api/fases/{id} | Obtener detalles de una fase.             |
| PUT     | /api/fases/{id} | Actualizar una fase.                      |
| DELETE  | /api/fases/{id} | Eliminar una fase.                        |

---

## 📂 **Curriculum (`Curriculum`)**

Creación y gestión del curriculum de un estudiante.

| Método | URL                   | Descripción                       |
| ------- | --------------------- | ---------------------------------- |
| GET     | /api/curriculums      | Listar todos los curriculums.      |
| POST    | /api/curriculums      | Crear un nuevo curriculum.         |
| GET     | /api/curriculums/{id} | Obtener un curriculum específico. |
| PUT     | /api/curriculums/{id} | Actualizar un curriculum.          |
| DELETE  | /api/curriculums/{id} | Eliminar un curriculum.            |

---

## 📂 **Testimonios (`Testimonios`)**

Manejo de testimonios que pueden dar los estudiantes sobre sus experiencias.

| Método | URL                   | Descripción                       |
| ------- | --------------------- | ---------------------------------- |
| GET     | /api/testimonios      | Listar todos los testimonios.      |
| POST    | /api/testimonios      | Crear un nuevo testimonio.         |
| GET     | /api/testimonios/{id} | Obtener un testimonio específico. |
| PUT     | /api/testimonios/{id} | Actualizar un testimonio.          |
| DELETE  | /api/testimonios/{id} | Eliminar un testimonio.            |

---
