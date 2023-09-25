-- Se crea el drop database 
drop database if exists Permanencia_Desercion;
-- Crear la base de datos
CREATE DATABASE Permanencia_Desercion;

-- Use actividadphp
USE Permanencia_Desercion;

-- Crear la tabla 'programa'
CREATE TABLE programa (
  id_programa INT PRIMARY KEY,
  nombre VARCHAR(255)
);

-- Crear la tabla 'periodo'
CREATE TABLE periodo (
  id_periodo INT PRIMARY KEY auto_increment,
  anio INT,
  semestre INT,
  cohorte INT
);

-- Crear la tabla 'estudiante'
CREATE TABLE estudiante (
  id_estudiante BIGINT PRIMARY KEY,
  nombres VARCHAR(255),
  -- genero VARCHAR(255),
  carrera VARCHAR(255),
  documento VARCHAR(255),
  estrato INT,
  localidad VARCHAR(255),
  -- genero_genero VARCHAR(255),
  tipo_inscripcion VARCHAR(255),
  estado VARCHAR(255),
  id_programa INT,
  promedio FLOAT,
  pasantia VARCHAR(255),
  tipo_icfes VARCHAR(255),
  puntaje_icfes FLOAT,
  FOREIGN KEY (id_programa) REFERENCES programa(id_programa)
);

-- Crear la tabla 'retirado'
CREATE TABLE retirado (
  id_retiro INT AUTO_INCREMENT PRIMARY KEY,
  id_periodo INT,
  total INT,
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);

-- Crear la tabla 'graduado'
CREATE TABLE graduado (
  id_graduado INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  fecha_grado DATE,
   promedio FLOAT,
  pasantia VARCHAR(255),
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);
-- Crear la tabla 'primiparo'
CREATE TABLE primiparo (
  id_primiparo INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);
-- Crear la tabla 'admitidos'
CREATE TABLE admitido (
  id_admitido INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  tipo_inscripcion VARCHAR(255),
  tipo_icfes VARCHAR(255),
  puntaje_icfes FLOAT,
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);
-- Crear la tabla 'matriculado'
CREATE TABLE matriculado (
  id_matricula INT AUTO_INCREMENT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  estado_matricula VARCHAR(255),
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);

-- Crear la tabla 'total'
CREATE TABLE total (
  id_cohorte_total INT PRIMARY KEY auto_increment,
  admitidos INT,
  primiparos INT,
  matriculados INT,
  graduados INT,
  retirados INT,
  id_periodo INT,
  id_programa INT,
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo),
  FOREIGN KEY (id_programa) REFERENCES programa(id_programa)
);
-- Procedimiento almacenado para calcular totales:
-- procedimiento almacenado totales:
-- procedimiento almacenado totales:
DELIMITER $$
CREATE PROCEDURE fill_total()
BEGIN
  DECLARE done INT DEFAULT FALSE;
  DECLARE periodo_id INT;
  DECLARE programa_id INT;
  DECLARE matriculados_count INT;
  DECLARE graduados_count INT;
  DECLARE primiparos_count INT;
  DECLARE total_retirados INT;

  -- Declare cursor for periodos and programas
  DECLARE cur CURSOR FOR
    SELECT p.id_periodo, pr.id_programa
    FROM periodo p
    CROSS JOIN programa pr;

  -- Declare handlers for NOT FOUND condition
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  -- Clear the table 'total'
  TRUNCATE TABLE total;

  -- Open cursor and start loop
  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO periodo_id, programa_id;
    IF done THEN
      LEAVE read_loop;
    END IF;

    -- Calculate matriculados
    SELECT COUNT(*) INTO matriculados_count
    FROM matriculado m
    JOIN estudiante e ON m.id_estudiante = e.id_estudiante
    WHERE m.id_periodo = periodo_id AND e.id_programa = programa_id;

    -- Calculate graduados
    SELECT COUNT(*) INTO graduados_count
    FROM graduado g
    JOIN estudiante e ON g.id_estudiante = e.id_estudiante
    WHERE g.id_periodo = periodo_id AND e.id_programa = programa_id;

    -- Calculate primiparos
    SELECT COUNT(*) INTO primiparos_count
    FROM primiparo p
    JOIN estudiante e ON p.id_estudiante = e.id_estudiante
    WHERE p.id_periodo = periodo_id AND e.id_programa = programa_id;

    -- Calculate retirados using the formula
SET total_retirados = (
  (
    (SELECT IFNULL(matriculados_count_prev, 0) FROM (
      SELECT matriculados AS matriculados_count_prev
      FROM total
      WHERE id_periodo = periodo_id - 1 AND id_programa = programa_id
    ) AS prev) -
    (SELECT IFNULL(graduados_count_prev, 0) FROM (
      SELECT graduados AS graduados_count_prev
      FROM total
      WHERE id_periodo = periodo_id - 1 AND id_programa = programa_id
    ) AS prev) + primiparos_count
  ) -
  matriculados_count
);

    -- Ensure retirados is not negative
    SET total_retirados = CASE WHEN total_retirados < 0 THEN 0 ELSE total_retirados END;

    -- Insert calculated values into 'total' table
    INSERT INTO total (admitidos, primiparos, matriculados, graduados, retirados, id_periodo, id_programa)
    VALUES (0, primiparos_count, matriculados_count, graduados_count, total_retirados, periodo_id, programa_id);
    INSERT INTO retirado (id_periodo,total)
    VALUES (periodo_id,total_retirados);
  END LOOP;

END$$
DELIMITER ;


-- Llamar al procedimiento almacenado para llenar la tabla 'total'
-- CALL fill_total();
-- Consultar tablas:
SELECT * FROM total;
SELECT * FROM total LIMIT 0, 1000;
select * from programa;

select * from periodo;
select * from estudiante;
select * from retirado;
select * from graduado;
select * from primiparo;
select * from matriculado;
select * from admitido;
select * from total;

SELECT DISTINCT estado FROM estudiante;
-- graduado
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN graduado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo;
-- matriculado
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN matriculado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo;
-- 
-- admitido
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN admitido g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo;

-- union
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN graduado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN matriculado m ON e.id_estudiante = m.id_estudiante
JOIN periodo p ON m.id_periodo = p.id_periodo
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN admitido a ON e.id_estudiante = a.id_estudiante
JOIN periodo p ON a.id_periodo = p.id_periodo;
-- union prueba
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN graduado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN matriculado m ON e.id_estudiante = m.id_estudiante
JOIN periodo p ON m.id_periodo = p.id_periodo
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
       p.anio
FROM estudiante e
JOIN admitido a ON e.id_estudiante = a.id_estudiante
JOIN periodo p ON a.id_periodo = p.id_periodo;
-- torta
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN graduado g ON e.id_estudiante = g.id_estudiante
JOIN periodo p ON g.id_periodo = p.id_periodo
WHERE p.anio = 2023
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN matriculado m ON e.id_estudiante = m.id_estudiante
JOIN periodo p ON m.id_periodo = p.id_periodo
WHERE p.anio = 2023
UNION ALL
SELECT e.carrera, e.estrato, e.localidad, e.tipo_inscripcion, e.estado, e.promedio, e.pasantia, e.tipo_icfes, e.puntaje_icfes,
p.anio
FROM estudiante e
JOIN admitido a ON e.id_estudiante = a.id_estudiante
JOIN periodo p ON a.id_periodo = p.id_periodo
WHERE p.anio = 2023;