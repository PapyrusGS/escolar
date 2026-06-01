INSERT INTO Roles (Nombre, Descripcion) VALUES
('Administrador', 'Control total del sistema'),
('Docente',       'Gestion de calificaciones y cursos'),
('Estudiante',    'Inscripcion y visualizacion de cursos');

INSERT INTO Modalidad (Nombre, DuracionSemanasxMaterias, MaxMaterias) VALUES
('Modular',   4,  2),
('Semestral', 24, 6),
('Anual',     48, 10);

INSERT INTO Pensum (IdModalidad, Nombre, NumMaterias, NumSemestres) VALUES
(1, 'SistemasModular', 54,9),
(2, 'SistemasSemestral', 54, 9),
(3, 'SistemasAnual', 52, 10),
(1, 'ContaduriaModular', 54, 9),
(2, 'ContaduriaSemestral', 54, 9),
(3, 'ContaduriaAnual', 52, 10),
(1, 'DisenoModular', 54, 9),
(2, 'DisenoSemestral', 54, 9),
(3, 'DisenoAnual', 52, 10),
(1, 'AdministracionModular', 54, 9),
(2, 'AdministracionSemestral', 54, 9),
(3, 'AdministracionAnual', 52, 10),
(1, 'DerechoModular', 54, 9),
(2, 'DerechoSemestral', 54, 9),
(3, 'DerechoAnual', 52, 10);


INSERT INTO Carreras (Nombre, Descripcion) VALUES
('Ingenieria de Sistemas', 'Carrera de tecnologia e informatica'),
('Contaduria Publica',     'Carrera de ciencias economicas'),
('Diseno Grafico',         'Carrera de artes visuales y comunicacion'),
('Administracion',         'Carrera de gestion empresarial'),
('Derecho',                'Carrera de ciencias juridicas');

INSERT INTO Materias (IdMateria, CodigoMateria, Nombre, IdMateriaPrevia, Descripcion) VALUES
-- --- PRIMER SEMESTRE (IDs: 1 - 6) ---
(1, 'SIS-101', 'Introducción a la Ingeniería de Sistemas', NULL, NULL),
(2, 'SIS-102', 'Matemática I', NULL, NULL),
(3, 'SIS-103', 'Introducción a la Programación', NULL, NULL),
(4, 'SIS-104', 'Fundamentos de Computación', NULL, NULL),
(5, 'SIS-105', 'Comunicación Oral y Escrita', NULL, NULL),
(6, 'SIS-106', 'Metodología de Estudio e Investigación', NULL, NULL),

-- --- SEGUNDO SEMESTRE (IDs: 7 - 12) ---
(7, 'SIS-201', 'Matemática II', 2, NULL),                  -- Requisito: SIS-102 (2)
(8, 'SIS-202', 'Física I', 2, NULL),                      -- Requisito: SIS-102 (2)
(9, 'SIS-203', 'Programación I', 3, NULL),                -- Requisito: SIS-103 (3)
(10, 'SIS-204', 'Arquitectura de Computadoras', 4, NULL),  -- Requisito: SIS-104 (4)
(11, 'SIS-205', 'Algoritmos y Estructuras de Datos', 9, NULL), -- Requisito: SIS-203 (9)
(12, 'SIS-206', 'Estadística Descriptiva', 2, NULL),       -- Requisito: SIS-102 (2)

-- --- TERCER SEMESTRE (IDs: 13 - 18) ---
(13, 'SIS-301', 'Matemática III', 7, NULL),               -- Requisito: SIS-201 (7)
(14, 'SIS-302', 'Programación II', 9, NULL),               -- Requisito: SIS-203 (9)
(15, 'SIS-303', 'Programación Orientada a Objetos', 14, NULL), -- Requisito: SIS-302 (14)
(16, 'SIS-304', 'Base de Datos I', 9, NULL),               -- Requisito: SIS-203 (9)
(17, 'SIS-305', 'Sistemas Operativos I', 10, NULL),        -- Requisito: SIS-204 (10)
(18, 'SIS-306', 'Física II', 8, NULL),                    -- Requisito: SIS-202 (8)

-- --- CUARTO SEMESTRE (IDs: 19 - 24) ---
(19, 'SIS-401', 'Base de Datos II', 16, NULL),             -- Requisito: SIS-304 (16)
(20, 'SIS-402', 'Redes I', 10, NULL),                     -- Requisito: SIS-204 (10)
(21, 'SIS-403', 'Sistemas Operativos II', 17, NULL),       -- Requisito: SIS-305 (17)
(22, 'SIS-404', 'Ingeniería de Software I', 15, NULL),     -- Requisito: SIS-303 (15)
(23, 'SIS-405', 'Desarrollo Web I', 14, NULL),             -- Requisito: SIS-302 (14)
(24, 'SIS-406', 'Probabilidad y Estadística', 12, NULL),   -- Requisito: SIS-206 (12)

-- --- QUINTO SEMESTRE (IDs: 25 - 30) ---
(25, 'SIS-501', 'Redes II', 20, NULL),                    -- Requisito: SIS-402 (20)
(26, 'SIS-502', 'Ingeniería de Software II', 22, NULL),    -- Requisito: SIS-404 (22)
(27, 'SIS-503', 'Desarrollo Web II', 23, NULL),            -- Requisito: SIS-405 (23)
(28, 'SIS-504', 'Desarrollo Móvil I', 15, NULL),           -- Requisito: SIS-303 (15)
(29, 'SIS-505', 'Base de Datos Avanzada', 19, NULL),       -- Requisito: SIS-401 (19)
(30, 'SIS-506', 'Seguridad Informática I', 20, NULL),      -- Requisito: SIS-402 (20)

-- --- SEXTO SEMESTRE (IDs: 31 - 36) ---
(31, 'SIS-601', 'Arquitectura de Software', 26, NULL),     -- Requisito: SIS-502 (26)
(32, 'SIS-602', 'Desarrollo Móvil II', 28, NULL),          -- Requisito: SIS-504 (28)
(33, 'SIS-603', 'Seguridad Informática II', 30, NULL),     -- Requisito: SIS-506 (30)
(34, 'SIS-604', 'Administración de Servidores', 21, NULL), -- Requisito: SIS-403 (21)
(35, 'SIS-605', 'Inteligencia Artificial I', 13, NULL),    -- Requisito: SIS-301 (13)
(36, 'SIS-606', 'Gestión de Proyectos de Software', 26, NULL), -- Requisito: SIS-502 (26)

-- --- SÉPTIMO SEMESTRE (IDs: 37 - 42) ---
(37, 'SIS-701', 'Inteligencia Artificial II', 35, NULL),   -- Requisito: SIS-605 (35)
(38, 'SIS-702', 'Computación en la Nube', 34, NULL),       -- Requisito: SIS-604 (34)
(39, 'SIS-703', 'Auditoría de Sistemas', 33, NULL),        -- Requisito: SIS-603 (33)
(40, 'SIS-704', 'Minería de Datos', 29, NULL),             -- Requisito: SIS-505 (29)
(41, 'SIS-705', 'Redes Avanzadas', 25, NULL),              -- Requisito: SIS-501 (25)
(42, 'SIS-706', 'Sistemas Distribuidos', 31, NULL),        -- Requisito: SIS-601 (31)

-- --- OCTAVO SEMESTRE (IDs: 43 - 48) ---
(43, 'SIS-801', 'Desarrollo de APIs y Microservicios', 31, NULL), -- Requisito: SIS-601 (31)
(44, 'SIS-802', 'Ciberseguridad', 33, NULL),               -- Requisito: SIS-603 (33)
(45, 'SIS-803', 'Business Intelligence', 40, NULL),        -- Requisito: SIS-704 (40)
(46, 'SIS-804', 'Internet de las Cosas (IoT)', 41, NULL),  -- Requisito: SIS-705 (41)
(47, 'SIS-805', 'Gestión de Calidad de Software', 36, NULL), -- Requisito: SIS-606 (36)
(48, 'SIS-806', 'Taller de Investigación', 6, NULL),       -- Requisito: SIS-106 (6)

-- --- NOVENO SEMESTRE (IDs: 49 - 54) ---
(49, 'SIS-901', 'Proyecto de Grado', 48, NULL),            -- Requisito: SIS-806 (48)
(50, 'SIS-902', 'Práctica Profesional', 48, NULL),         -- Requisito: SIS-806 (48)
(51, 'SIS-903', 'Ética Profesional', 1, NULL),             -- Requisito: SIS-101 (1)
(52, 'SIS-904', 'Gestión de Innovación Tecnológica', 36, NULL), -- Requisito: SIS-606 (36)
(53, 'SIS-905', 'Seminario de Actualización Tecnológica', 48, NULL), -- Requisito: SIS-806 (48)
(54, 'SIS-906', 'Formulación y Evaluación de Proyectos', 36, NULL); -- Requisito: SIS-606 (36)

INSERT INTO carreraMateriaPensum (IdCarrera, IdMateria, IdPensum, Semestre)
SELECT 
    1, -- Reemplaza este 1 por el IdCarrera guardada
    IdMateria, 
    1, -- Reemplaza este 1 por el IdPensum guardado 
    CAST(SUBSTRING(CodigoMateria, 5, 1) AS UNSIGNED)
FROM Materias 
WHERE IdMateria BETWEEN 1 AND 54;-- Cambiar el id de materias que entren al pensum dependiendo de la carrera


    INSERT INTO Turnos (Nombre, HoraInicio, HoraFin, Lun, Mar, Mie, Jue, Vie) VALUES 
    ('Mañana', '08:00:00', '12:00:00', 1, 1, 1, 1, 1),
    ('Tarde', '13:00:00', '17:00:00', 1, 1, 1, 1, 1),
    ('Noche', '18:00:00', '22:00:00', 1, 1, 1, 1, 1);




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


Insert INTO EstudianteCarrera (IdUsuario, IdCarrera, IdModalidad) VALUES
(3, 1, 1),  -- Maria Flores -> Ingenieria de Sistemas
(6, 1, 1),  -- Kike Torrez -> Ingenieria de Sistemas
(7, 1, 1),  -- Ana Condori -> Ingenieria de Sistemas
(8, 1, 1),  -- Pedro Vargas -> Ingenieria de Sistemas
(9, 1, 1);  -- Lucia Rios -> Ingenieria de Sistemas


Insert Into Cursos (Piso, Aula) VALUES
(1, 'Aula 101'),
(1, 'Aula 102'),
(2, 'Aula 201'),
(2, 'Aula 202');

INSERT INTO CursosMaterias (IdCurso, IdMateria, IdDocente, IdTurno, FechaInicio, FechaFin, MaxInscritos, Inscritos) VALUES
(1, 1,  2,  1, '2025-06-01', '2025-06-28', 40, 2),
(1, 2,  2,  3, '2025-06-01', '2025-06-28', 35, 2),
(2, 14, 5,  1, '2025-07-01', '2025-07-28', 40, 3),
(2, 15, 5,  2, '2025-07-01', '2025-07-28', 35, 2),
(2, 6,  15, 3, '2025-07-01', '2025-07-28', 30, 3),
(3, 3,  15, 1, '2025-08-01', '2025-08-28', 25, 2),
(3, 4,  16, 2, '2025-08-01', '2025-08-28', 40, 0),
(3, 5,  16, 3, '2025-08-01', '2025-08-28', 30, 1),
(4, 7,  5,  1, '2025-09-01', '2025-09-28', 35, 2),
(4, 8,  15, 2, '2025-09-01', '2025-09-28', 40, 2),
(4, 11, 16, 3, '2025-09-01', '2025-09-28', 20, 0);

INSERT INTO Inscripciones (IdEstudiante, IdCursoMateria) VALUES
(1,  1), (1,  2),
(2,  3),  (2,  4),
(3,  1),  (3,  2),
(4,  3),  (4,  5),
(5,  5),  (5,  6);


INSERT INTO Notas (IdInscripcion, Nota) VALUES
(1,  85.50),
(2,  92.00),
(3,  78.00),
(4,  65.50),
(5,  90.00),
(6,  88.50),
(7,  72.00),
(8,  95.00),
(9,  60.00),
(10,  55.50);


-- falta ver xd
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