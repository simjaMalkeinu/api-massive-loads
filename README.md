# Proyecto API Massive Loads - Laravel 11

Este proyecto es una API construida con [Laravel 11](https://laravel.com/) y tiene como objetivo gestionar cargas masivas de datos.

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas:

- [PHP](https://www.php.net/) (versión 8.2 o superior).
- [Composer](https://getcomposer.org/) (gestor de dependencias para PHP).
- [MySQL](https://www.mysql.com/) u otro gestor de base de datos compatible.
- [Node.js](https://nodejs.org/) (opcional, para el frontend o manejo de assets).

Puedes verificar si tienes instalados PHP y Composer ejecutando los siguientes comandos:

```bash
php -v
composer -v
```

## Consideraciones

En este proyecto se usa Laravel-Passport por lo que se require  que las siguientes extensiones de PHP estén habilitadas:

**OpenSSL**
   - Necesario para el manejo de claves públicas y privadas en la autenticación OAuth2.
   - Habilita la siguiente línea en tu archivo `php.ini`:

     ```ini
     extension=openssl
     ```
   * **Reinicia tu servidor web** para aplicar los cambios en el archivo `php.ini`.

## Instalación

Sigue estos pasos para configurar el proyecto en tu máquina local:

1. Clona el repositorio:

   ```bash
   git clone "https://github.com/simjaMalkeinu/api-massive-loads.git"
   ```

2. Accede al directorio del proyecto:

   ```bash
   cd api-massive-loads
   ```

3. Instala las dependencias de Laravel usando Composer:

   ```bash
   composer install
   ```

4. Copia el archivo de configuración por defecto `.env`:

   ```bash
   cp .env.example .env
   ```

5. Configura la conexión a la base de datos en el archivo `.env`:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nombre_de_tu_base
   DB_USERNAME=usuario
   DB_PASSWORD=contraseña
   ```

5. Habilita la carga de archivos en MySQL. Asegúrate de que el parámetro local_infile esté activado en la configuración de MySQL:

    * En el archivo `my.cnf` o `my.ini` de MySQL 

    * Para entrar a este archivo desde windows puedes precionar las teclas `win + R` y teclear lo siguiente

      ```bash
      %PROGRAMDATA%
      ```
    * Esto te abrira una carpeta deberas ingresar a la siguiente ruta

      ```
         mysql/MYSQL SERVER 8.*.*/my.ini o my.cnf
      ```

    * Dentro de este archivo deberas cambiar lo siguiente

        ```ini
        [mysqld]
        local_infile=1
        ```
    * También configura la ruta de subida de archivos en mysql, por ejemplo:
        ```ini
        [mysqld]
        secure-file-priv=""
        ```

6. Genera la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

7. Ejecuta las migraciones para crear las tablas en la base de datos:

   ```bash
   php artisan migrate
   ```

8. Ejecuta los Seeders para crear los roles y el usuario administrador:

   ```bash
   php artisan db:seed
   ```

9. Crea la clave para la los clientes, esto es para que puedan hacer peticiones
    ```bash
    php artisan passport:client --personal
    ```

## Servidor de Desarrollo

Inicia el servidor de desarrollo de Laravel ejecutando:

```bash
php artisan serve
```

Por defecto, la aplicación estará disponible en [http://localhost:8000](http://localhost:8000).

## Ejecución de Tests

Si quieres ejecutar las pruebas del proyecto, utiliza el siguiente comando:

```bash
php artisan test
```

## Compilación de Assets (opcional)

Si tu proyecto incluye assets frontend (CSS, JS), asegúrate de tener instaladas las dependencias de Node.js:

1. Instala las dependencias con npm o yarn:

   ```bash
   npm install
   # o
   yarn install
   ```

2. Compila los assets:

   ```bash
   npm run dev
   # o
   yarn dev
   ```

3. Para assets optimizados en producción:

   ```bash
   npm run build
   # o
   yarn build
   ```

## Estructura del Proyecto

```
api-massive-loads
├── app             # Código fuente principal (modelos, controladores, etc.)
├── bootstrap       # Archivos de arranque
├── config          # Configuración de la aplicación
├── database        # Migraciones y seeders
├── public          # Punto de entrada público
├── resources       # Vistas, assets (CSS, JS)
├── routes          # Archivos de rutas (API, Web)
├── storage         # Archivos y logs generados
├── tests           # Pruebas unitarias y funcionales
├── .env            # Configuración del entorno
├── artisan         # CLI de Laravel
├── composer.json   # Dependencias de PHP
└── package.json    # Dependencias frontend (opcional)
```

## Scripts de Artisan útiles

- `php artisan serve`: Inicia el servidor de desarrollo.
- `php artisan migrate`: Ejecuta las migraciones.
- `php artisan db:seed`: Rellena la base de datos con datos iniciales.
- `php artisan test`: Ejecuta las pruebas.
- `php artisan route:list`: Muestra todas las rutas registradas.

## Contribución

Si deseas contribuir a este proyecto, por favor realiza un fork del repositorio y envía un pull request con tus cambios.

## Licencia

Este proyecto está bajo la licencia [MIT](./LICENSE).
