-- Tabla de Usuarios
CREATE TABLE Usuarios (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NombreC VARCHAR(100),      -- Nombre completo
    Nick VARCHAR(50),         -- Apodo o nombre de usuario
    Genero VARCHAR(50),       -- Género del usuario
    Admin TINYINT,            -- Indica si es administrador (0 o 1)
    Estatus TINYINT,          -- Estado del usuario (activo/inactivo)
    N_intentos TINYINT,       -- Número de intentos fallidos
    Fecha_Nac DATE,           -- Fecha de nacimiento
    Foto MEDIUMBLOB,          -- Foto de perfil
    Email VARCHAR(100),       -- Correo electrónico
    Contra VARCHAR(100),      -- Contraseña cifrada
    Fecha_Reg DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Fecha de registro
    Fecha_Mod DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  -- Última modificación
);

-- Tabla de Categorías
CREATE TABLE Categorias (
    categoriaID INT PRIMARY KEY AUTO_INCREMENT,
    categoria VARCHAR(255) -- Nombre de la categoría
);

-- Tabla de Publicaciones
CREATE TABLE Publicaciones (
    publiID INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT,           -- Texto de la publicación
    categoriaID INT,            -- Relación con Categorías
    usuarioID INT,              -- Relación con Usuarios (autor de la publicación)
    estatus TINYINT,            -- Estado de la publicación (activo/inactivo)
    fechacreacion DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación
    FOREIGN KEY (categoriaID) REFERENCES Categorias(categoriaID) ON DELETE CASCADE,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);

-- Tabla de Comentarios
CREATE TABLE Comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    publiID INT NOT NULL,       -- Relación con Publicaciones
    usuarioID INT NOT NULL,     -- Relación con Usuarios
    comentario TEXT NOT NULL,   -- Comentario del usuario
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha del comentario
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);

-- Tabla de Multimedia de Publicaciones (para imágenes y videos)
CREATE TABLE MultimediaPublicaciones (
    mediaID INT PRIMARY KEY AUTO_INCREMENT,
    publiID INT NOT NULL,       -- Relación con Publicaciones
    tipo ENUM('imagen', 'video') NOT NULL,  -- Tipo de archivo
    archivo MEDIUMBLOB NOT NULL, -- Archivo multimedia
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE
);

-- Tabla de Reacciones (solo un tipo de reacción por usuario)
CREATE TABLE Reacciones (
    reaccionID INT PRIMARY KEY AUTO_INCREMENT,
    usuarioID INT NOT NULL,     -- Usuario que reacciona
    publiID INT NOT NULL,       -- Publicación a la que reacciona
    tipo TINYINT NOT NULL,      -- Tipo de reacción (ejemplo: 1 = Me gusta)
    fecha_reaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE
);

-- Tabla de Amistades (seguimientos entre usuarios)
CREATE TABLE Amistades (
    amistadID INT PRIMARY KEY AUTO_INCREMENT,
    usuario1ID INT NOT NULL,  -- Usuario que envía la solicitud
    usuario2ID INT NOT NULL,  -- Usuario que recibe la solicitud
    estado ENUM('pendiente', 'aceptado', 'rechazado') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario1ID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    FOREIGN KEY (usuario2ID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);
    