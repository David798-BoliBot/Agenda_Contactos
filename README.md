# Agenda de Contactos

## 1. Descripción del proyecto

Agenda de Contactos es una aplicación web desarrollada con PHP, MySQL, HTML y CSS, cuyo propósito es permitir a los usuarios administrar sus contactos personales mediante una plataforma sencilla y accesible desde un navegador web.

El sistema cuenta con un mecanismo de registro e inicio de sesión que permite identificar a cada usuario y mantener su información separada de la de los demás.

Cada usuario dispone de una agenda privada en la que puede registrar, consultar, modificar y eliminar contactos.

## 2. Objetivo general

Desarrollar una aplicación web que permita gestionar información de contactos personales mediante operaciones CRUD (Crear, Leer, Actualizar y Eliminar), incorporando autenticación de usuarios y mecanismos de seguridad para proteger la información almacenada.

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

### Administración de sesiones
- Acceso al panel principal únicamente para usuarios autenticados.
- Protección de las páginas privadas.
- Separación de los contactos según el usuario propietario.

## 4. Tecnologías utilizadas

| Tecnología         | Utilidad                                 |

| PHP                | Procesamiento y lógica del sistema       |
| MySQL / MariaDB    | Almacenamiento de información            |
| HTML5              | Estructura de las páginas                |
| CSS3               | Diseño y presentación visual             |
| PDO                | Conexión y consultas a la base de datos  |
| XAMPP              | Entorno de desarrollo local              |
| Visual Studio Code | Edición del código fuente            |

## 5. Estructura del proyecto

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

### Descripción de los archivos

- **config/conexion.php:** establece la conexión con la base de datos mediante PDO.
- **css/estilos.css:** contiene los estilos visuales de la aplicación.
- **database/agenda_contactos.sql:** contiene las instrucciones SQL necesarias para crear la base de datos y sus tablas.
- **registro.php:** permite crear nuevas cuentas de usuario.
- **login.php:** permite autenticar a los usuarios registrados.
- **panel.php:** presenta el panel principal del usuario.
- **contactos.php:** muestra la agenda personal de contactos.
- **agregar_contacto.php:** procesa el registro de nuevos contactos.
- **editar_contacto.php:** permite modificar los datos de un contacto.
- **eliminar_contacto.php:** procesa la eliminación de contactos.
- **logout.php:** cierra la sesión del usuario.

## 6. Base de datos

El proyecto utiliza una base de datos llamada `agenda_contactos`, compuesta por dos tablas principales:

### Tabla usuarios

Almacena la información de las cuentas registradas.

- id: identificador único del usuario.
- nombre: nombre del usuario.
- correo: dirección de correo electrónico única.
- password: contraseña almacenada mediante hashing.
- fecha_registro: fecha de creación de la cuenta.

### Tabla contactos

Almacena los contactos personales de cada usuario.

- id: identificador único del contacto.
- id_usuario: identifica al propietario del contacto.
- nombre: nombre completo del contacto.
- telefono: número telefónico.
- correo: correo electrónico del contacto.
- ciudad: ciudad de residencia.
- pais: país de residencia.

La relación entre ambas tablas se establece mediante una clave foránea entre `contactos.id_usuario` y `usuarios.id`, con eliminación en cascada.

## 7. Instalación y ejecución

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
6. Revisar la configuración de conexión en `config/conexion.php`.
7. Acceder a la aplicación desde:

http://localhost/agenda_contactos/registro.php

8. Crear una cuenta y comenzar a utilizar la agenda.

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

## 9. Funcionamiento general

El funcionamiento de la aplicación sigue este proceso:

1. El usuario crea una cuenta mediante el formulario de registro.
2. Accede al sistema utilizando su correo electrónico y contraseña.
3. Una vez autenticado, ingresa al panel principal.
4. Desde la agenda puede administrar sus contactos.
5. El sistema verifica que cada operación corresponda al usuario que inició sesión.
6. Al finalizar, el usuario puede cerrar su sesión.

## 10. Autor

Proyecto académico desarrollado como práctica de programación y desarrollo de aplicaciones web utilizando PHP y MySQL.