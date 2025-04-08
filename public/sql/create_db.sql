-- Crear la base de datos si no existe (solo para pruebas locales)
CREATE DATABASE IF NOT EXISTS khloe
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE khloe;

-- Crear la tabla users_data (sin el campo is_admin)
CREATE TABLE IF NOT EXISTS users_data (
    idUser INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    date_birth DATE NOT NULL,
    address TEXT,
    gender ENUM('male', 'female', 'other'),
    PRIMARY KEY (idUser)
) ENGINE=InnoDB;

-- Crear la tabla users_login (con el campo is_admin)
CREATE TABLE IF NOT EXISTS users_login (
    idLogin INT NOT NULL AUTO_INCREMENT,
    idUser INT NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'user') NOT NULL,
    is_admin TINYINT(1) DEFAULT 0, -- Movido desde users_data
    PRIMARY KEY (idLogin),
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Crear la tabla citas
CREATE TABLE IF NOT EXISTS citas (
    idCita INT NOT NULL AUTO_INCREMENT,
    idUser INT NOT NULL,
    fecha_cita DATE NOT NULL,
    motivo_cita TEXT NOT NULL,
    PRIMARY KEY (idCita),
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Crear la tabla noticias
CREATE TABLE IF NOT EXISTS noticias (
    idNoticia INT NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL UNIQUE, -- Cambiado de TEXT a VARCHAR(255)
    imagen TEXT NOT NULL,
    texto LONGTEXT NOT NULL,
    fecha DATE NOT NULL,
    idUser INT NOT NULL,
    PRIMARY KEY (idNoticia),
    FOREIGN KEY (idUser) REFERENCES users_data(idUser) ON DELETE CASCADE
) ENGINE=InnoDB;