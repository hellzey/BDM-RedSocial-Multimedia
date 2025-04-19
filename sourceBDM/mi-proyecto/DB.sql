CREATE TABLE Usuarios (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NombreC VARCHAR(100),      -- Nombre completo
    Nick VARCHAR(50),          -- Apodo o nombre de usuario
    Genero VARCHAR(50),       -- Género del usuario
    Admin TINYINT,            -- Indica si es administrador (0 o 1)
    Estatus TINYINT,          -- Estado del usuario (activo/inactivo)
    N_intentos TINYINT,       -- Número de intentos fallidos
    Fecha_Nac DATE,           -- Fecha de nacimiento
    Foto MEDIUMBLOB,          -- Foto de perfil
    Email VARCHAR(100),       -- Correo electrónico
    Contra VARCHAR(100),      -- Contraseña cifrada
    Fecha_Reg DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Fecha de registro
    Fecha_Mod DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- Última modificación
    N_seguidores INT DEFAULT 0, -- Número de seguidores
    Biografia TEXT            -- Biografía
);
CREATE TABLE Categorias (
    categoriaID INT PRIMARY KEY AUTO_INCREMENT,
    categoria VARCHAR(255) -- Nombre de la categoría
);
CREATE TABLE Publicaciones (
    publiID INT PRIMARY KEY AUTO_INCREMENT,
    descripcion TEXT,           -- Texto de la publicación
    usuarioID INT,              -- Relación con Usuarios (autor de la publicación)
    estatus TINYINT,            -- Estado de la publicación (activo/inactivo)
    fechacreacion DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);
CREATE TABLE Comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    publiID INT NOT NULL,       -- Relación con Publicaciones
    usuarioID INT NOT NULL,     -- Relación con Usuarios
    comentario TEXT NOT NULL,   -- Comentario del usuario
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha del comentario
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);
CREATE TABLE MultimediaPublicaciones (
    mediaID INT PRIMARY KEY AUTO_INCREMENT,
    publiID INT NOT NULL,       -- Relación con Publicaciones
    tipo ENUM('imagen', 'video') NOT NULL,  -- Tipo de archivo
    archivo MEDIUMBLOB NOT NULL, -- Archivo multimedia
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE
);
CREATE TABLE Reacciones (
    reaccionID INT PRIMARY KEY AUTO_INCREMENT,
    usuarioID INT NOT NULL,     -- Usuario que reacciona
    publiID INT NOT NULL,       -- Publicación a la que reacciona
    tipo TINYINT NOT NULL,      -- Tipo de reacción (ejemplo: 1 = Me gusta)
    fecha_reaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE
);

CREATE TABLE Publicaciones_Categorias (
    publiID INT,                -- Relación con Publicaciones
    categoriaID INT,            -- Relación con Categorías
    FOREIGN KEY (publiID) REFERENCES Publicaciones(publiID) ON DELETE CASCADE,
    FOREIGN KEY (categoriaID) REFERENCES Categorias(categoriaID) ON DELETE CASCADE
);
CREATE TABLE Seguidores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    SeguidorID INT NOT NULL,    -- Usuario que sigue (el seguidor)
    SeguidoID INT NOT NULL,     -- Usuario que es seguido
    fecha_seguimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (SeguidorID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    FOREIGN KEY (SeguidoID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (SeguidorID, SeguidoID)  -- Evita duplicados
);

CREATE TABLE Mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emisorID INT NOT NULL,
    receptorID INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisorID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    FOREIGN KEY (receptorID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);




-- Tabla para los grupos de chat
CREATE TABLE GruposChat (
    grupoID INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    creadorID INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creadorID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);

-- Tabla para los miembros del grupo
CREATE TABLE MiembrosGrupo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grupoID INT NOT NULL,
    usuarioID INT NOT NULL,
    fecha_union DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupoID) REFERENCES GruposChat(grupoID) ON DELETE CASCADE,
    FOREIGN KEY (usuarioID) REFERENCES Usuarios(ID) ON DELETE CASCADE,
    UNIQUE KEY unique_member (grupoID, usuarioID)  -- Evita duplicados
);

-- Tabla para mensajes de grupo (separada de los mensajes directos)
CREATE TABLE MensajesGrupo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grupoID INT NOT NULL,
    emisorID INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupoID) REFERENCES GruposChat(grupoID) ON DELETE CASCADE,
    FOREIGN KEY (emisorID) REFERENCES Usuarios(ID) ON DELETE CASCADE
);