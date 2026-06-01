
CREATE TABLE Roles (
    IdRol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Descripcion VARCHAR(255),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE
);

CREATE TABLE Modalidad (
    IdModalidad INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    DuracionSemanasxMaterias INT NOT NULL,
    MaxMaterias INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE
);

CREATE TABLE Turnos (
    IdTurno INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    HoraInicio TIME NOT NULL,
    HoraFin TIME NOT NULL,
    Lun bit Not Null,
    Mar bit Not Null,
    Mie bit Not Null,
    Jue bit Not Null,
    Vie bit Not Null,
    Sab bit Not Null,
    Dom bit Not Null,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE
);

CREATE TABLE Carreras (
    IdCarrera INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE
);

CREATE TABLE Materias (
    IdMateria INT AUTO_INCREMENT PRIMARY KEY,
    IdMateriaPrevia INT,
    CodigoMateria VARCHAR(20) NOT NULL UNIQUE,
    Nombre VARCHAR(100) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    Descripcion VARCHAR(255),
    FOREIGN KEY (IdMateriaPrevia) REFERENCES Materias(IdMateria)
);

CREATE TABLE Pensum (
    IdPensum INT AUTO_INCREMENT PRIMARY KEY,
    IdModalidad INT NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    NumMaterias INT NOT NULL,
    NumSemestres INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdModalidad) REFERENCES Modalidad(IdModalidad)
);

Create table carreraMateriaPensum(
    IdCarreraMateriaPensum INT AUTO_INCREMENT PRIMARY KEY,
    IdCarrera INT NOT NULL,
    IdMateria INT NOT NULL,
    IdPensum INT NOT NULL,
    Semestre INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdCarrera) REFERENCES Carreras(IdCarrera),
    FOREIGN KEY (IdMateria) REFERENCES Materias(IdMateria),
    FOREIGN KEY (IdPensum) REFERENCES Pensum(IdPensum),
    CONSTRAINT UC_CarreraMateriaPensum UNIQUE (IdCarrera, IdMateria, IdPensum)
);

CREATE TABLE Cursos (
    IdCurso INT AUTO_INCREMENT PRIMARY KEY,
    Piso int NOT NULL,
    Aula VARCHAR(50) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE
);

CREATE TABLE Usuarios (
    IdUsuario INT AUTO_INCREMENT PRIMARY KEY,
    IdRol INT NOT NULL,
    Nombre1 VARCHAR(50) NOT NULL,
    Nombre2 VARCHAR(50),
    Apellido1 VARCHAR(50) NOT NULL,
    Apellido2 VARCHAR(50),
    CI INT UNIQUE NOT NULL,
    Telefono INT,
    Correo VARCHAR(100) UNIQUE NOT NULL,
    Contrasena VARCHAR(255) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdRol) REFERENCES Roles(IdRol)
);


CREATE TABLE CursosMaterias (
    IdCursoMateria INT AUTO_INCREMENT PRIMARY KEY,
    IdCurso INT NOT NULL,
    IdMateria INT NOT NULL,
    IdDocente INT NOT NULL,
    IdTurno INT NOT NULL,
    FechaInicio DATETIME NOT NULL,
    FechaFin DATETIME NOT NULL,
    MaxInscritos INT NOT NULL DEFAULT 40,
    Inscritos INT NOT NULL DEFAULT 0,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdCurso) REFERENCES Cursos(IdCurso),
    FOREIGN KEY (IdMateria) REFERENCES Materias(IdMateria),
    FOREIGN KEY (IdDocente) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdTurno) REFERENCES Turnos(IdTurno)
);

Create table EstudianteCarrera(
    IdEstudianteCarrera INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario INT NOT NULL,
    IdCarrera INT NOT NULL,
    IdModalidad INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdCarrera) REFERENCES Carreras(IdCarrera),
    FOREIGN KEY (IdModalidad) REFERENCES Modalidad(IdModalidad)
);

CREATE TABLE Inscripciones (
    IdInscripcion INT AUTO_INCREMENT PRIMARY KEY,
    IdEstudiante INT NOT NULL,
    IdCursoMateria INT NOT NULL,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdEstudiante) REFERENCES EstudianteCarrera(IdEstudianteCarrera),
    FOREIGN KEY (IdCursoMateria) REFERENCES CursosMaterias(IdCursoMateria)
);

CREATE TABLE Notas (
    IdNota INT AUTO_INCREMENT PRIMARY KEY,
    IdInscripcion INT NOT NULL,
    Nota DECIMAL(5,2) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdInscripcion) REFERENCES Inscripciones(IdInscripcion)
);

-- Falta discutir esta tabla porque es probable que exista otra tabla intermedia para relacionar notificaciones con usuarios, ya que un usuario puede tener muchas notificaciones y una notificación puede ser para muchos usuarios (en caso de ser una notificación general)
CREATE TABLE Notificaciones (
    IdNotificacion INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario INT NOT NULL,
    Titulo VARCHAR(100) NOT NULL,
    Contenido TEXT NOT NULL,
    FechaEnvio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado bit DEFAULT TRUE,
    FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario)
);

