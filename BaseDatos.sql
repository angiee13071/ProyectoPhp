-- Se crea el drop database 
drop database if exists Actividadphp;
-- Crear la base de datos
CREATE DATABASE Actividadphp;

-- Use actividadphp
USE Actividadphp;

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


-- Inserting period data
INSERT INTO periodo (anio, semestre, cohorte)
VALUES
(2022, 1, 578);

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
  FOREIGN KEY (id_programa) REFERENCES programa(id_programa)
);

select * from estudiante;

-- Crear la tabla 'retirado'
CREATE TABLE retirado (
  id_retiro INT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  estado VARCHAR(255),
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);



-- Crear la tabla 'graduado'
CREATE TABLE graduado (
  id_graduado INT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  fecha_grado DATE,
  promedio FLOAT,
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);



-- Crear la tabla 'primiparo'
CREATE TABLE primiparo (
  id_primiparo INT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);



-- Crear la tabla 'matriculado'
CREATE TABLE matriculado (
  id_matricula INT PRIMARY KEY,
  id_estudiante BIGINT,
  id_periodo INT,
  estado_matricula VARCHAR(255),
  FOREIGN KEY (id_estudiante) REFERENCES estudiante(id_estudiante),
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo)
);

-- Crear la tabla 'total'
CREATE TABLE total (
  id_cohorte_total INT PRIMARY KEY auto_increment,
  total_primiparos INT,
  total_matriculados INT,
  total_graduados INT,
  total_retirados INT,
  id_periodo INT,
  id_programa INT,
  FOREIGN KEY (id_periodo) REFERENCES periodo(id_periodo),
  FOREIGN KEY (id_programa) REFERENCES programa(id_programa)
);


DELIMITER $$
CREATE PROCEDURE fill_total()
BEGIN
  DECLARE total_prim INT DEFAULT 0;
  DECLARE total_matric INT DEFAULT 0;
  DECLARE total_grad INT DEFAULT 0;
  DECLARE total_retir INT DEFAULT 0;
  DECLARE periodo_id INT;
  DECLARE programa_id INT;
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
    
    SELECT COUNT(*) INTO total_prim FROM primiparo WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO total_matric FROM matriculado WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO total_grad FROM graduado WHERE id_periodo = periodo_id;
    SELECT COUNT(*) INTO total_retir FROM retirado WHERE id_periodo = periodo_id;
    
    INSERT INTO total (total_primiparos, total_matriculados, total_graduados, total_retirados, id_periodo, id_programa) 
    VALUES (total_prim, total_matric, total_grad, total_retir, periodo_id, programa_id);
  END LOOP;
  
  CLOSE cur;
  
END$$

DELIMITER ;


-- Agregar datos a la tabla 'programa'
INSERT INTO programa (id_programa, nombre)
VALUES
(1, 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)');

-- Agregar datos a la tabla 'periodo'
INSERT INTO periodo (anio, semestre, cohorte)
VALUES
(2022, 1, 578);

-- Agregar datos a la tabla 'estudiante'
INSERT INTO estudiante (id_estudiante, nombres, genero, carrera, documento, estrato, localidad, genero_genero, tipo_inscripcion, estado, id_programa)
VALUES
(20112345678, 'GARCIA PEREZ JUAN ANTONIO', 'male', 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)', '101234567890', 2, 'No registra', 'male', 'CREDITOS', 'ESTUDIANTE MATRICULADO', 1),
(20123456789, 'LOPEZ RODRIGUEZ MARIA ISABEL', 'female', 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)', '102345678901', 2, 'CIUDAD BOLIVAR', 'female', 'CREDITOS', 'ESTUDIANTE MATRICULADO', 1),
(20134567890, 'MARTINEZ GOMEZ LUIS MIGUEL', 'male', 'TECNOLOGIA EN SISTEMATIZACION DE DATOS (CICLOS PROPEDEUTICOS)', '103456789012', 2, 'No registra', 'male', 'CREDITOS', 'ESTUDIANTE MATRICULADO', 1);

-- Agregar datos a la tabla 'retirado'
INSERT INTO retirado (id_retiro, id_estudiante, id_periodo, estado)
VALUES
(1, 20134567890, 1, 'RETIRADO');

-- Agregar datos a la tabla 'graduado'
INSERT INTO graduado (id_graduado, id_estudiante, id_periodo, fecha_grado, promedio)
VALUES
(1002, 20112345678, 1, '2022-05-05', 4.6),
(1001, 20123456789, 1, '2022-05-05', 4.2);

-- Agregar datos a la tabla 'primiparo'
INSERT INTO primiparo (id_primiparo, id_estudiante, id_periodo)
VALUES
(1001, 20112345678, 1),
(2002, 20123456789, 1),
(3003, 20134567890, 1);

-- Agregar datos a la tabla 'matriculado'
INSERT INTO matriculado (id_matricula, id_estudiante, id_periodo, estado_matricula)
VALUES
(1123, 20112345678, 1, 'MATRICULADO'),
(2123, 20123456789, 1, 'MATRICULADO'),
(3123, 20134567890, 1, 'MATRICULADO');

-- Llamar al procedimiento almacenado para llenar la tabla 'total'
CALL fill_total();

SELECT * FROM total;
SELECT * FROM total LIMIT 0, 1000;
select * from programa;
select * from periodo;
select * from estudiante;
select * from estudiante where id_estudiante='20150000000';
select * from retirado;
select * from graduado;
select * from primiparo;
select * from matriculado;
select * from total;