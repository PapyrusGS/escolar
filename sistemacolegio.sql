CREATE DATABASE sistema_academico;
USE sistema_academico;

CREATE TABLE Roles (
    IdRol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Descripcion VARCHAR(255),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE
);

CREATE TABLE Modalidad (
    IdModalidad INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    DuracionSemanas INT NOT NULL,
    MaxMaterias INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE
);

CREATE TABLE Turnos (
    IdTurno INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    HoraInicio TIME NOT NULL,
    HoraFin TIME NOT NULL,
    Dias VARCHAR(100) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE
);

CREATE TABLE Carreras (
    IdCarrera INT AUTO_INCREMENT PRIMARY KEY,
    IdModalidad INT NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdModalidad) REFERENCES Modalidad(IdModalidad)
);

CREATE TABLE Materias (
    IdMateria INT AUTO_INCREMENT PRIMARY KEY,
    IdCarrera INT NOT NULL,
    IdMateriaPrevia INT,
    Nombre VARCHAR(100) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdCarrera) REFERENCES Carreras(IdCarrera),
    FOREIGN KEY (IdMateriaPrevia) REFERENCES Materias(IdMateria)
);

CREATE TABLE Pensum (
    IdPensum INT AUTO_INCREMENT PRIMARY KEY,
    IdCarrera INT NOT NULL,
    IdMateria INT NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdCarrera) REFERENCES Carreras(IdCarrera),
    FOREIGN KEY (IdMateria) REFERENCES Materias(IdMateria)
);

CREATE TABLE Cursos (
    IdCurso INT AUTO_INCREMENT PRIMARY KEY,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE
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
    IdCarrera INT NULL,
    Semestre INT NULL,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdRol) REFERENCES Roles(IdRol)
    FOREIGN KEY (IdCarrera) REFERENCES Carreras(IdCarrera)
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
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdCurso) REFERENCES Cursos(IdCurso),
    FOREIGN KEY (IdMateria) REFERENCES Materias(IdMateria),
    FOREIGN KEY (IdDocente) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdTurno) REFERENCES Turnos(IdTurno)
);

CREATE TABLE Inscripciones (
    IdInscripcion INT AUTO_INCREMENT PRIMARY KEY,
    IdEstudiante INT NOT NULL,
    IdCursoMateria INT NOT NULL,
    Fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdEstudiante) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdCursoMateria) REFERENCES CursosMaterias(IdCursoMateria)
);

CREATE TABLE Notas (
    IdNota INT AUTO_INCREMENT PRIMARY KEY,
    IdEstudiante INT NOT NULL,
    IdCursoMateria INT NOT NULL,
    Nota DECIMAL(5,2) NOT NULL,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdEstudiante) REFERENCES Usuarios(IdUsuario),
    FOREIGN KEY (IdCursoMateria) REFERENCES CursosMaterias(IdCursoMateria)
);

CREATE TABLE Notificaciones (
    IdNotificacion INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario INT NOT NULL,
    Titulo VARCHAR(100) NOT NULL,
    Contenido TEXT NOT NULL,
    FechaEnvio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado BOOL DEFAULT TRUE,
    FOREIGN KEY (IdUsuario) REFERENCES Usuarios(IdUsuario)
);

INSERT INTO Roles (Nombre, Descripcion) VALUES
('Administrador', 'Control total del sistema'),
('Docente',       'Gestion de calificaciones y cursos'),
('Estudiante',    'Inscripcion y visualizacion de cursos');

INSERT INTO Modalidad (Nombre, DuracionSemanas, MaxMaterias) VALUES
('Modular',   4,  2),
('Semestral', 24, 6),
('Anual',     48, 10);

INSERT INTO Turnos (Nombre, HoraInicio, HoraFin, Dias) VALUES
('Manana', '07:00:00', '12:00:00', 'Lunes,Martes,Miercoles,Jueves,Viernes'),
('Tarde',  '13:00:00', '18:00:00', 'Lunes,Martes,Miercoles,Jueves,Viernes'),
('Noche',  '19:00:00', '22:00:00', 'Lunes,Martes,Miercoles,Jueves,Viernes');

INSERT INTO Carreras (IdModalidad, Nombre, Descripcion) VALUES
(1, 'Ingenieria de Sistemas', 'Carrera de tecnologia e informatica'),
(2, 'Contaduria Publica',     'Carrera de ciencias economicas'),
(1, 'Diseno Grafico',         'Carrera de artes visuales y comunicacion'),
(2, 'Administracion',         'Carrera de gestion empresarial'),
(3, 'Derecho',                'Carrera de ciencias juridicas');

INSERT INTO Materias (IdCarrera, IdMateriaPrevia, Nombre) VALUES
(1, NULL, 'Matematicas I'),
(1, NULL, 'Programacion I'),
(1, NULL, 'Algebra Lineal'),
(1, NULL, 'Fisica I'),
(1, NULL, 'Base de Datos I'),
(2, NULL, 'Contabilidad I'),
(2, NULL, 'Matematica Financiera'),
(2, NULL, 'Auditoria I'),
(3, NULL, 'Diseno Basico'),
(3, NULL, 'Teoria del Color'),
(4, NULL, 'Administracion I'),
(5, NULL, 'Derecho Civil'),
(5, NULL, 'Derecho Penal');

INSERT INTO Materias (IdCarrera, IdMateriaPrevia, Nombre) VALUES
(1, 1,  'Matematicas II'),
(1, 2,  'Programacion II'),
(1, 3,  'Calculo I'),
(1, 4,  'Fisica II'),
(1, 5,  'Base de Datos II'),
(2, 6,  'Contabilidad II'),
(2, 8,  'Auditoria II'),
(4, 11, 'Administracion II');

INSERT INTO Pensum (IdCarrera, IdMateria) VALUES
(1, 1),  (1, 2),  (1, 3),  (1, 4),  (1, 5),
(1, 14), (1, 15), (1, 16), (1, 17), (1, 18),
(2, 6),  (2, 7),  (2, 8),
(2, 19), (2, 20),
(3, 9),  (3, 10),
(4, 11), (4, 21),
(5, 12), (5, 13);

INSERT INTO Cursos (FechaRegistro) VALUES
(NOW()), (NOW()), (NOW()), (NOW());

INSERT INTO Usuarios (IdRol, Nombre1, Nombre2, Apellido1, Apellido2, CI, Telefono, Correo, Contrasena) VALUES
(1, 'Carlos',   NULL,      'Mamani',    NULL,      12345678,  NULL,     'admin@sistema.edu',      SHA2('123', 256)),
(2, 'Luis',     NULL,      'Quispe',    NULL,      87654321,  NULL,     'docente@sistema.edu',    SHA2('123', 256)),
(3, 'Maria',    NULL,      'Flores',    NULL,      11223344,  NULL,     'estudiante@sistema.edu', SHA2('123', 256)),
(1, 'Favio',    'Andres',  'Gutierrez', 'Lopez',   55667788,  70123456, 'favio@gmail.com',        SHA2('123', 256)),
(2, 'Martin',   'Eduardo', 'Salazar',   'Quispe',  99887766,  71234567, 'martin@gmail.com',       SHA2('123', 256)),
(3, 'Kike',     'Enrique', 'Torrez',    'Mamani',  44556677,  72345678, 'kike@gmail.com',         SHA2('123', 256)),
(3, 'Ana',      'Maria',   'Condori',   'Huanca',  22334455,  73456789, 'ana@gmail.com',          SHA2('123', 256)),
(3, 'Pedro',    'Jose',    'Vargas',    'Choque',  33445566,  74567890, 'pedro@gmail.com',        SHA2('123', 256)),
(3, 'Lucia',    'Elena',   'Rios',      'Apaza',   44332211,  75678901, 'lucia@gmail.com',        SHA2('123', 256)),
(3, 'Diego',    'Fabian',  'Mendoza',   'Lima',    55443322,  76789012, 'diego@gmail.com',        SHA2('123', 256)),
(3, 'Sofia',    'Belen',   'Castillo',  'Romero',  66554433,  77890123, 'sofia@gmail.com',        SHA2('123', 256)),
(3, 'Carlos',   'Rodrigo', 'Pinto',     'Alarcon', 77665544,  78901234, 'carlos2@gmail.com',      SHA2('123', 256)),
(3, 'Valeria',  'Paola',   'Espinoza',  'Cruz',    88776655,  79012345, 'valeria@gmail.com',      SHA2('123', 256)),
(3, 'Fernando', 'Luis',    'Medina',    'Suarez',  99001122,  70112233, 'fernando@gmail.com',     SHA2('123', 256)),
(2, 'Patricia', 'Rosa',    'Montano',   'Vega',    11009988,  71223344, 'patricia@gmail.com',     SHA2('123', 256)),
(2, 'Roberto',  'Cesar',   'Villena',   'Tapia',   22110099,  72334455, 'roberto@gmail.com',      SHA2('123', 256));

UPDATE USUARIOS SET IdRol = 1 WHERE IdUsuario = 1;
UPDATE USUARIOS SET IdRol = 1 WHERE IdUsuario = 4;

UPDATE USUARIOS SET IdRol = 2 WHERE IdUsuario = 2;
UPDATE USUARIOS SET IdRol = 2 WHERE IdUsuario = 5;
UPDATE USUARIOS SET IdRol = 2 WHERE IdUsuario = 15;
UPDATE USUARIOS SET IdRol = 2 WHERE IdUsuario = 16;

UPDATE Usuarios SET IdCarrera = 1, Semestre = 1 WHERE IdUsuario = 3;
UPDATE Usuarios SET IdCarrera = 1, Semestre = 2 WHERE IdUsuario = 6;
UPDATE Usuarios SET IdCarrera = 1, Semestre = 1 WHERE IdUsuario = 7;
UPDATE Usuarios SET IdCarrera = 1, Semestre = 2 WHERE IdUsuario = 8;
UPDATE Usuarios SET IdCarrera = 2, Semestre = 1 WHERE IdUsuario = 9;
UPDATE Usuarios SET IdCarrera = 2, Semestre = 3 WHERE IdUsuario = 10;
UPDATE Usuarios SET IdCarrera = 1, Semestre = 4 WHERE IdUsuario = 11;
UPDATE Usuarios SET IdCarrera = 2, Semestre = 2 WHERE IdUsuario = 12;
UPDATE Usuarios SET IdCarrera = 1, Semestre = 3 WHERE IdUsuario = 13;
UPDATE Usuarios SET IdCarrera = 2, Semestre = 1 WHERE IdUsuario = 14;

INSERT INTO CursosMaterias (IdCurso, IdMateria, IdDocente, IdTurno, FechaInicio, FechaFin, MaxInscritos) VALUES
(1, 1,  2,  1, '2025-06-01', '2025-06-28', 40),
(1, 2,  2,  3, '2025-06-01', '2025-06-28', 35),
(2, 14, 5,  1, '2025-07-01', '2025-07-28', 40),
(2, 15, 5,  2, '2025-07-01', '2025-07-28', 35),
(2, 6,  15, 3, '2025-07-01', '2025-07-28', 30),
(3, 3,  15, 1, '2025-08-01', '2025-08-28', 25),
(3, 4,  16, 2, '2025-08-01', '2025-08-28', 40),
(3, 5,  16, 3, '2025-08-01', '2025-08-28', 30),
(4, 7,  5,  1, '2025-09-01', '2025-09-28', 35),
(4, 8,  15, 2, '2025-09-01', '2025-09-28', 40),
(4, 11, 16, 3, '2025-09-01', '2025-09-28', 20);

INSERT INTO Inscripciones (IdEstudiante, IdCursoMateria) VALUES
(3,  1),  (3,  2),
(6,  3),  (6,  4),
(7,  1),  (7,  2),
(8,  3),  (8,  5),
(9,  5),  (9,  6),
(10, 9),  (10, 10),
(11, 3),  (11, 4),
(12, 5),  (12, 9),
(13, 6),  (13, 8),
(14, 10);

INSERT INTO Notas (IdEstudiante, IdCursoMateria, Nota) VALUES
(3,  1,  85.50),
(3,  2,  92.00),
(6,  3,  78.00),
(6,  4,  65.50),
(7,  1,  90.00),
(7,  2,  88.50),
(8,  3,  72.00),
(8,  5,  95.00),
(9,  5,  60.00),
(9,  6,  55.50),
(10, 9,  83.00),
(10, 10, 77.50),
(11, 3,  91.00),
(11, 4,  68.00),
(12, 5,  74.50),
(12, 9,  82.00),
(13, 6,  88.00),
(13, 8,  79.50),
(14, 10, 66.00);

INSERT INTO Notificaciones (IdUsuario, Titulo, Contenido) VALUES
(3,  'Inscripcion exitosa',      'Te has inscrito correctamente a Matematicas I'),
(3,  'Inscripcion exitosa',      'Te has inscrito correctamente a Programacion I'),
(6,  'Inscripcion exitosa',      'Te has inscrito correctamente a Matematicas II'),
(6,  'Inscripcion exitosa',      'Te has inscrito correctamente a Programacion II'),
(7,  'Inscripcion exitosa',      'Te has inscrito correctamente a Matematicas I'),
(8,  'Nota registrada',          'Tu docente ha registrado una nota en Matematicas II'),
(8,  'Inscripcion exitosa',      'Te has inscrito correctamente a Contabilidad I'),
(9,  'Inscripcion exitosa',      'Te has inscrito a Contabilidad I'),
(9,  'Inscripcion exitosa',      'Te has inscrito a Algebra Lineal'),
(10, 'Nota registrada',          'Tu docente ha registrado una nota en Matematica Financiera'),
(10, 'Inscripcion exitosa',      'Te has inscrito correctamente a Matematica Financiera'),
(11, 'Bienvenido al sistema',    'Tu cuenta ha sido activada correctamente'),
(12, 'Inscripcion exitosa',      'Te has inscrito correctamente a Contabilidad I'),
(13, 'Nota registrada',          'Tu docente ha registrado una nota en Base de Datos I'),
(14, 'Bienvenido al sistema',    'Tu cuenta ha sido activada correctamente'),
(4,  'Nuevo usuario registrado', 'Se ha registrado un nuevo estudiante en el sistema'),
(5,  'Nueva inscripcion',        'Un estudiante se ha inscrito en tu materia'),
(2,  'Nueva inscripcion',        'Un estudiante se ha inscrito en tu materia');