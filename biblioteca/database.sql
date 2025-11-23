CREATE DATABASE IF NOT EXISTS biblioteca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE biblioteca;

CREATE TABLE IF NOT EXISTS roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL
);

INSERT INTO roles (name) VALUES ('Administrator'), ('Librarian'), ('Reader')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role_id INT NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(50) NOT NULL,
  author VARCHAR(50) NOT NULL,
  year INT,
  genre VARCHAR(50),
  quantity INT NOT NULL
);

INSERT INTO books (title, author, year, genre, quantity) VALUES
('Cien años de soledad', 'Gabriel García Márquez', 1967, 'Realismo mágico', 8),
('1984', 'George Orwell', 1949, 'Distopía', 10),
('El nombre de la rosa', 'Umberto Eco', 1980, 'Misterio histórico', 6),
('Harry Potter y la piedra filosofal', 'J.K. Rowling', 1997, 'Fantasía', 12),
('El señor de los anillos', 'J.R.R. Tolkien', 1954, 'Fantasía épica', 7),
('Orgullo y prejuicio', 'Jane Austen', 1813, 'Romance clásico', 5),
('El alquimista', 'Paulo Coelho', 1988, 'Ficción espiritual', 9),
('Los juegos del hambre', 'Suzanne Collins', 2008, 'Ciencia ficción', 11),
('La sombra del viento', 'Carlos Ruiz Zafón', 2001, 'Misterio', 6),
('Crónica de una muerte anunciada', 'Gabriel García Márquez', 1981, 'Novela corta', 7),
('Sapiens: De animales a dioses', 'Yuval Noah Harari', 2011, 'Ensayo histórico', 5),
('El código Da Vinci', 'Dan Brown', 2003, 'Thriller', 8),
('La chica del tren', 'Paula Hawkins', 2015, 'Suspenso', 6),
('It', 'Stephen King', 1986, 'Terror', 4),
('El principito', 'Antoine de Saint-Exupéry', 1943, 'Fábula', 10);

CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  date_of_issue DATE,
  date_of_return DATE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (book_id) REFERENCES books(id)
);

INSERT INTO users (username, email, password, role_id)
SELECT 'admin', 'admin@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', id FROM roles WHERE name = 'Administrator'
    AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@biblioteca.local');

INSERT INTO users (username, email, password, role_id)
SELECT new_users.username, new_users.email, new_users.password_hash, roles.id
FROM (
    SELECT 'ana.garcia' AS username, 'ana.garcia@biblioteca.local' AS email, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' AS password_hash UNION ALL
    SELECT 'luis.martinez', 'luis.martinez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'sofia.lopez', 'sofia.lopez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'carlos.rivera', 'carlos.rivera@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'mariana.rojas', 'mariana.rojas@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'diego.torres', 'diego.torres@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'valeria.sanchez', 'valeria.sanchez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'andres.mendoza', 'andres.mendoza@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'paula.gonzalez', 'paula.gonzalez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'fernando.diaz', 'fernando.diaz@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'camila.ortega', 'camila.ortega@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'ricardo.perez', 'ricardo.perez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'isabela.silva', 'isabela.silva@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'julian.herrera', 'julian.herrera@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'renata.vargas', 'renata.vargas@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'mateo.castro', 'mateo.castro@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'ximena.flores', 'ximena.flores@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'sebastian.nunez', 'sebastian.nunez@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'alejandra.campos', 'alejandra.campos@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' UNION ALL
    SELECT 'roberto.molina', 'roberto.molina@biblioteca.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
) AS new_users
JOIN roles ON roles.name = 'Reader'
ON DUPLICATE KEY UPDATE username = VALUES(username);
