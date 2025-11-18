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
