# Sistema de Biblioteca Online

Aplicación web en PHP para administrar una biblioteca con roles diferenciados (Administrador, Bibliotecario y Lector). Incluye gestión de usuarios, catálogo, préstamos y devoluciones.

## Requisitos

- XAMPP o entorno equivalente con PHP 8+, Apache y MySQL
- phpMyAdmin para administrar la base de datos (opcional)

## Instalación

1. Copia la carpeta `biblioteca` dentro del directorio `htdocs` de XAMPP.
2. Inicia los servicios de Apache y MySQL desde el panel de XAMPP.
3. Importa el archivo `database.sql` desde phpMyAdmin para crear la base de datos y los datos iniciales.
4. Accede desde el navegador a `http://localhost/biblioteca/login.php`.
5. Ingresa con el usuario inicial:
   - Correo: `admin@biblioteca.local`
   - Contraseña: `password`

Se recomienda cambiar la contraseña del administrador desde phpMyAdmin o actualizando el registro directamente en la tabla `users`.

## Estructura de roles

- **Administrator**: CRUD de usuarios, visualización total de operaciones y catálogo completo.
- **Librarian**: Gestión de libros y préstamos.
- **Reader**: Consulta el catálogo, solicita préstamos y marca devoluciones de sus libros.

## Archivos principales

- `config.php`: Conexión a base de datos y utilidades globales.
- `includes/`: Componentes compartidos (layout, autenticación).
- `login.php`: Inicio de sesión.
- `dashboard.php`: Panel general con métricas.
- `users.php`: Mantenimiento de usuarios (solo administrador).
- `books.php`: Gestión del catálogo (administrador y bibliotecario).
- `catalog.php`: Consulta del catálogo (todos los roles).
- `transactions.php`: Préstamos y devoluciones.

## Notas

- Ajusta las credenciales de la base de datos en `config.php` si es necesario.
- Añade validaciones adicionales y manejo de errores según tus necesidades.
- Personaliza estilos en `assets/css/style.css`.
