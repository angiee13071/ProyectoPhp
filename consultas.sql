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
  genero VARCHAR(255),
  carrera VARCHAR(255),
  documento VARCHAR(255),
  estrato INT,
  localidad VARCHAR(255),
  genero_genero VARCHAR(255),
  tipo_inscripcion VARCHAR(255),
  estado VARCHAR(255),
  id_programa INT,
  promedio FLOAT,
  pasantia VARCHAR(255),
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
DELIMITER $$
CREATE PROCEDURE fill_total()
BEGIN
  DECLARE periodo_id INT;
  DECLARE programa_id INT;
  DECLARE matriculados INT DEFAULT 0;
  DECLARE primiparos INT DEFAULT 0;
  DECLARE graduados INT DEFAULT 0;
  DECLARE retirados INT DEFAULT 0;
  DECLARE total_matriculados_periodo_anterior INT DEFAULT 0;
  DECLARE total_graduados_periodo_anterior INT DEFAULT 0;
  DECLARE total_retirados INT DEFAULT 0;
 
  
  DECLARE done INT DEFAULT FALSE;
  
  DECLARE cur CURSOR FOR
    SELECT id_periodo, id_programa FROM periodo
    JOIN programa ON 1 = 1;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
  OPEN cur;
  read_loop: LOOP
    FETCH cur INTO periodo_id, programa_id;
    
    IF done THEN
        LEAVE read_loop;
    END IF;
    

    SELECT COUNT(*) INTO primiparos FROM primiparo WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO matriculados FROM matriculado WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO graduados FROM graduado WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO retirados FROM retirado WHERE id_periodo = periodo_id;
   -- Verificar si el período anterior existe
    SET total_matriculados_periodo_anterior = 0;
    SET total_graduados_periodo_anterior = 0;
    SELECT COUNT(*) INTO total_matriculados_periodo_anterior FROM matriculado WHERE id_periodo = periodo_id - 1;
    SELECT COUNT(*) INTO total_graduados_periodo_anterior FROM graduado WHERE id_periodo = periodo_id - 1;
    
    IF total_matriculados_periodo_anterior > 0 AND total_graduados_periodo_anterior > 0 THEN
        -- Calcular el valor de total_retirados solo si el período anterior existe
        SET total_retirados = ((total_matriculados_periodo_anterior - total_graduados_periodo_anterior) + primiparos - retirados);
        INSERT INTO retirado (id_periodo, total) VALUES (periodo_id, retirados);
    ELSE
        -- Si el período anterior no existe, asignar 0 al valor de total_retirados
        SET total_retirados = 0;
    END IF;
    
    INSERT INTO total (primiparos, matriculados, graduados, retirados, id_periodo, id_programa) 
    VALUES (primiparos, matriculados, graduados, retirados, periodo_id, programa_id);
   
  
  END LOOP;
  
  CLOSE cur;
  
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
select * from total;

select * from estudiante where estado='ESTUDIANTE MATRICULADO' and id_programa='678';
-- query para obtener permanencia por cohorte
SELECT
            CONCAT(p.anio, '-', p.semestre) AS periodo_actual,
            CONCAT(p.anio, '-', (p.semestre - 1)) AS periodo_anterior,
            p.id_periodo,
            p.cohorte,
            COUNT(DISTINCT m.id_estudiante) AS matriculado,
            FORMAT((COUNT(DISTINCT m.id_estudiante) / LAG(COUNT(DISTINCT m.id_estudiante)) OVER (ORDER BY p.anio, p.semestre)) * 100, 2) AS permanencia,
            e.carrera 
            FROM
            periodo p
            LEFT JOIN matriculado m ON p.id_periodo = m.id_periodo
            LEFT JOIN estudiante e ON m.id_estudiante = e.id_estudiante 
            WHERE
            m.estado_matricula = 'ESTUDIANTE MATRICULADO'
            -- AND e.carrera = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)'
            -- AND e.carrera = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)'
            GROUP BY
            periodo_actual, periodo_anterior, p.id_periodo, p.cohorte, e.carrera 
            ORDER BY
            p.anio, p.semestre;
-- query para obtener permanencia por año
SELECT
            CONCAT(p.anio) AS anio_actual,
            CONCAT(p.anio-1) AS anio_anterior,
            p.id_periodo,
            COUNT(DISTINCT m.id_estudiante) AS matriculado,
            FORMAT((COUNT(DISTINCT m.id_estudiante) / LAG(COUNT(DISTINCT m.id_estudiante)) OVER (ORDER BY p.anio, p.semestre)) * 100, 2) AS permanencia,
            e.carrera 
            FROM
            periodo p
            LEFT JOIN matriculado m ON p.id_periodo = m.id_periodo
            LEFT JOIN estudiante e ON m.id_estudiante = e.id_estudiante 
            WHERE
            m.estado_matricula = 'ESTUDIANTE MATRICULADO' 
			-- and e.carrera = 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)'
           --  AND e.carrera = 'INGENIERIA EN TELEMATICA (CICLOS PROPEDEUTICOS)'
            GROUP BY
            p.anio, p.semestre, p.id_periodo, e.carrera
        ORDER BY
            p.anio, p.semestre;
-- datos generales de permanencia
SELECT COUNT(*) AS total
FROM estudiante
WHERE estado = 'ESTUDIANTE MATRICULADO'
AND id_programa = '678';
select * from estudiante;
SELECT localidad, genero, tipo_inscripcion, estado FROM estudiante WHERE id_programa='578';
SELECT localidad, genero, tipo_inscripcion,estado, carrera FROM estudiante WHERE id_programa='578'