# Agenda de Contactos

## 1. Descripción del proyecto

Agenda de Contactos es una aplicación web desarrollada con PHP, MySQL, HTML y CSS, cuyo propósito es permitir a los usuarios administrar sus contactos personales mediante una plataforma accesible desde un navegador web.

El sistema cuenta con registro e inicio de sesión para identificar a cada usuario y mantener su información separada de la de los demás.

Cada usuario dispone de una agenda privada en la que puede registrar, consultar, modificar, eliminar, buscar y organizar sus contactos.

La aplicación también permite clasificar los contactos mediante categorías y marcar aquellos que sean favoritos.

**Aplicación publicada:**  
https://agenda-contactos-proyecto.infinityfreeapp.com/

## 2. Objetivo general

Desarrollar una aplicación web que permita gestionar información de contactos personales mediante operaciones CRUD (Crear, Leer, Actualizar y Eliminar), incorporando autenticación de usuarios, mecanismos de seguridad y herramientas para facilitar la organización de la información.

## 3. Funcionalidades principales

### Gestión de usuarios

- Registro de nuevas cuentas.
- Validación de los datos ingresados.
- Comprobación de correos electrónicos duplicados.
- Almacenamiento seguro de contraseñas mediante hashing.
- Inicio y cierre de sesión.

### Gestión de contactos

- Registrar nuevos contactos.
- Visualizar los contactos guardados.
- Editar la información de un contacto.
- Eliminar contactos.
- Registrar nombre, teléfono, correo electrónico, ciudad y país.

### Búsqueda de contactos

- Buscar contactos por nombre.
- Buscar contactos por correo electrónico.
- Buscar contactos por ciudad.

### Ordenamiento de contactos

- Ordenar los contactos alfabéticamente por nombre.
- Ordenar los contactos por ciudad.
- Ordenar por fecha de creación, mostrando primero los más recientes.

### Categorías

Permite clasificar los contactos mediante las siguientes categorías:

- Amigos.
- Familia.
- Trabajo.
- Universidad.
- Otro.

### Favoritos

- Marcar contactos como favoritos.
- Desmarcar contactos favoritos.
- Filtrar la agenda para mostrar únicamente los contactos favoritos.

### Administración de sesiones

- Acceso al panel principal únicamente para usuarios autenticados.
- Protección de las páginas privadas.
- Separación de los contactos según el usuario propietario.
- Restricción de operaciones sobre contactos pertenecientes a otros usuarios.

## 4. Tecnologías utilizadas

| Tecnología | Utilidad |
|---|---|
| PHP | Procesamiento y lógica del sistema. |
| MySQL / MariaDB | Almacenamiento de información. |
| HTML5 | Estructura de las páginas. |
| CSS3 | Diseño y presentación visual. |
| PDO | Conexión y consultas a la base de datos. |
| XAMPP | Entorno de desarrollo local. |
| Visual Studio Code | Edición del código fuente. |
| InfinityFree | Alojamiento web de la aplicación. |
| GitHub | Almacenamiento y publicación del código fuente. |

## 5. Estructura del proyecto

```text
agenda_contactos/
│
├── config/
│   └── conexion.php
│
├── css/
│   └── estilos.css
│
├── database/
│   └── agenda_contactos.sql
│
├── agregar_contacto.php
├── contactos.php
├── editar_contacto.php
├── eliminar_contacto.php
├── login.php
├── logout.php
├── panel.php
├── registro.php
└── README.md
```

### Descripción de los archivos

- **config/conexion.php:** establece la conexión con la base de datos mediante PDO.
- **css/estilos.css:** contiene los estilos visuales de la aplicación.
- **database/agenda_contactos.sql:** contiene las instrucciones SQL necesarias para crear la base de datos y sus tablas.
- **registro.php:** permite crear nuevas cuentas de usuario.
- **login.php:** permite autenticar a los usuarios registrados.
- **panel.php:** presenta el panel principal del usuario.
- **contactos.php:** muestra la agenda personal y permite buscar, filtrar y ordenar los contactos.
- **agregar_contacto.php:** procesa el registro de nuevos contactos.
- **editar_contacto.php:** permite modificar los datos de un contacto.
- **eliminar_contacto.php:** procesa la eliminación de contactos.
- **logout.php:** cierra la sesión del usuario.

## 6. Base de datos

El proyecto utiliza una base de datos llamada `agenda_contactos`, compuesta por tres tablas:

### Tabla usuarios

Almacena la información de las cuentas registradas.

- `id`: identificador único del usuario.
- `nombre`: nombre del usuario.
- `correo`: dirección de correo electrónico única.
- `password`: contraseña almacenada mediante hashing.
- `fecha_registro`: fecha de creación de la cuenta.

### Tabla contactos

Almacena los contactos personales de cada usuario.

- `id`: identificador único del contacto.
- `id_usuario`: identifica al propietario del contacto.
- `nombre`: nombre completo del contacto.
- `telefono`: número telefónico.
- `correo`: correo electrónico del contacto.
- `ciudad`: ciudad de residencia.
- `pais`: país de residencia.
- `id_categoria`: identifica la categoría asignada al contacto.
- `favorito`: indica si el contacto está marcado como favorito.
- `fecha_creacion`: fecha de creación del contacto.

### Tabla categorias

Almacena las categorías disponibles para clasificar los contactos.

- `id`: identificador único de la categoría.
- `nombre`: nombre de la categoría.

Las categorías disponibles son Amigos, Familia, Trabajo, Universidad y Otro.

La tabla `contactos` mantiene relaciones mediante claves foráneas con las tablas `usuarios` y `categorias`. Cada contacto pertenece a un usuario y tiene una categoría asignada.

## 7. Instalación y ejecución local

### Requisitos

- XAMPP instalado.
- Apache y MySQL/MariaDB.
- Navegador web.
- Editor de código (recomendado: Visual Studio Code).

### Pasos de instalación

1. Descargar o clonar el repositorio del proyecto.
2. Copiar la carpeta `agenda_contactos` dentro del directorio `htdocs` de XAMPP.
3. Iniciar los servicios Apache y MySQL desde el panel de XAMPP.
4. Abrir phpMyAdmin desde el navegador.
5. Importar el archivo `database/agenda_contactos.sql`.
6. Revisar la configuración de conexión en `config/conexion.php`, utilizando las credenciales correspondientes al entorno local.
7. Acceder a la aplicación desde:

   http://localhost/agenda_contactos/registro.php

8. Crear una cuenta e iniciar sesión para comenzar a utilizar la agenda.

## 8. Seguridad implementada

El sistema incorpora diferentes mecanismos para proteger la información y controlar el acceso:

- `password_hash()` para almacenar contraseñas de forma segura.
- `password_verify()` para comprobar las contraseñas durante el inicio de sesión.
- Consultas preparadas mediante PDO para reducir el riesgo de inyección SQL.
- Uso de sesiones para controlar la autenticación.
- Regeneración del identificador de sesión después del inicio de sesión.
- Tokens CSRF para proteger las operaciones correspondientes.
- Validación de datos en el servidor.
- Uso de `htmlspecialchars()` para escapar contenido mostrado en las páginas.
- Comprobación de propiedad para impedir que un usuario modifique o elimine contactos pertenecientes a otra cuenta.
- Restricción de acceso a las páginas privadas.
- Validación de los criterios de ordenamiento para evitar introducir valores no permitidos en las consultas SQL.

## 9. Publicación de la aplicación

La aplicación se encuentra alojada en InfinityFree y puede consultarse mediante el siguiente enlace:

**https://agenda-contactos-proyecto.infinityfreeapp.com/**

El código fuente y los archivos del proyecto se encuentran disponibles en el repositorio de GitHub:

**https://github.com/David798-BoliBot/Agenda_Contactos**

## 10. Funcionamiento general

El funcionamiento de la aplicación sigue este proceso:

1. El usuario crea una cuenta mediante el formulario de registro.
2. Accede al sistema utilizando su correo electrónico y contraseña.
3. Una vez autenticado, ingresa al panel principal.
4. Desde la agenda puede registrar y administrar sus contactos.
5. Puede buscar contactos por nombre, correo electrónico o ciudad.
6. Puede ordenar los registros y clasificarlos mediante categorías.
7. Puede marcar contactos como favoritos y consultar únicamente los favoritos.
8. El sistema verifica que cada operación corresponda al usuario que inició sesión.
9. Al finalizar, el usuario puede cerrar su sesión.

## 11. Autor

Proyecto académico desarrollado como práctica de programación y desarrollo de aplicaciones web utilizando PHP y MySQL.