ALTER TABLE  `log_logistica` ADD  `usuario` VARCHAR( 50 ) NOT NULL;

-- 2013-03-10 12:26pm
UPDATE `configuraciones` SET `id` = 50,`configuracion` = '2',`tipo` = 'Rígido',`activo` = 'si' WHERE `configuraciones`.`id` = 50;
UPDATE `configuraciones` SET `id` = 51,`configuracion` = '3',`tipo` = 'Rígido',`activo` = 'si' WHERE `configuraciones`.`id` = 51;
UPDATE `configuraciones` SET `id` = 52,`configuracion` = '4',`tipo` = 'Rígido',`activo` = 'si' WHERE `configuraciones`.`id` = 52;
UPDATE `configuraciones` SET `id` = 53,`configuracion` = '2S',`tipo` = 'Cabezote',`activo` = 'si' WHERE `configuraciones`.`id` = 53;
UPDATE `configuraciones` SET `id` = 54,`configuracion` = '3S',`tipo` = 'Cabezote',`activo` = 'si' WHERE `configuraciones`.`id` = 54;
UPDATE `configuraciones` SET `id` = 55,`configuracion` = '4S',`tipo` = 'Cabezote',`activo` = 'si' WHERE `configuraciones`.`id` = 55;
UPDATE `configuraciones` SET `id` = 56,`configuracion` = '5',`tipo` = 'Rígido',`activo` = 'si' WHERE `configuraciones`.`id` = 56;
UPDATE `configuraciones` SET `id` = 61,`configuracion` = 'S1',`tipo` = 'Semirremolque',`activo` = 'si' WHERE `configuraciones`.`id` = 61;
UPDATE `configuraciones` SET `id` = 62,`configuracion` = 'S2',`tipo` = 'Semirremolque',`activo` = 'si' WHERE `configuraciones`.`id` = 62;
UPDATE `configuraciones` SET `id` = 63,`configuracion` = 'S3',`tipo` = 'Semiremolque',`activo` = 'si' WHERE `configuraciones`.`id` = 63;
UPDATE `configuraciones` SET `id` = 64,`configuracion` = 'S4',`tipo` = 'Semiremolque',`activo` = 'si' WHERE `configuraciones`.`id` = 64;
UPDATE `configuraciones` SET `id` = 71,`configuracion` = 'R2',`tipo` = 'Remolque',`activo` = 'si' WHERE `configuraciones`.`id` = 71;
UPDATE `configuraciones` SET `id` = 72,`configuracion` = 'R3',`tipo` = 'Remolque',`activo` = 'si' WHERE `configuraciones`.`id` = 72;
UPDATE `configuraciones` SET `id` = 73,`configuracion` = 'R4',`tipo` = 'Remolque',`activo` = 'si' WHERE `configuraciones`.`id` = 73;
UPDATE `configuraciones` SET `id` = 74,`configuracion` = 'R5',`tipo` = 'Remolque',`activo` = 'si' WHERE `configuraciones`.`id` = 74;
UPDATE `configuraciones` SET `id` = 81,`configuracion` = 'B1',`tipo` = 'Remolque Balanceado',`activo` = 'si' WHERE `configuraciones`.`id` = 81;
UPDATE `configuraciones` SET `id` = 82,`configuracion` = 'B2',`tipo` = 'Remolque Balanceado',`activo` = 'si' WHERE `configuraciones`.`id` = 82;
UPDATE `configuraciones` SET `id` = 83,`configuracion` = 'B3',`tipo` = 'Remolque Balanceado',`activo` = 'si' WHERE `configuraciones`.`id` = 83;
UPDATE `configuraciones` SET `id` = 84,`configuracion` = 'B4',`tipo` = 'Remolque Balanceado',`activo` = 'si' WHERE `configuraciones`.`id` = 84;
UPDATE `configuraciones` SET `id` = 85,`configuracion` = 'B5',`tipo` = 'Remolque Balanceado',`activo` = 'si' WHERE `configuraciones`.`id` = 85;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 9;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 10;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 12;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 13;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 14;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 15;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 16;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 18;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 19;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 20;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 21;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 22;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 23;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 25;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 26;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 27;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 28;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 29;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 30;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 31;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 32;
DELETE FROM `logistica`.`configuraciones` WHERE `configuraciones`.`id` = 33;

--
INSERT INTO `logistica`.`lineas` (`codigomarca`, `codigo`, `descripcion`) VALUES ('CH', '767', 'NQR');

--
ALTER TABLE `guias` CHANGE `fechaentrega` `fechaentrega` DATE NULL;
ALTER TABLE `guias` CHANGE `fecha_recibido_mercancia` `fecha_recibido_mercancia` DATE NULL;
ALTER TABLE `guias` CHANGE `fechadespacho` `fechadespacho` DATE NULL;
UPDATE guias SET `fechaentrega`=NULL WHERE `fechaentrega`='0000-00-00';
UPDATE guias SET `fecha_recibido_mercancia`=NULL WHERE `fecha_recibido_mercancia`='0000-00-00';
UPDATE guias SET `fechadespacho`=NULL WHERE `fechadespacho`='0000-00-00';

-- Marzo 27, 2013
ALTER TABLE  `log_logistica` CHANGE  `id_modulo`  `id_modulo` BIGINT NOT NULL;

-- Abril 9, 2013
CREATE TABLE `relaciones` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `fecha_emision` date NOT NULL,
 `id_cliente` int(11) NOT NULL,
 `periodo` varchar(50) NOT NULL,
 `guias` text NOT NULL,
 PRIMARY KEY (`id`),
 KEY `id_cliente` (`id_cliente`),
 CONSTRAINT `relaciones_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Abril 27, 2013
ALTER TABLE rutas_locales
	ADD  `observaciones` TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	ADD FOREIGN KEY (placa_vehiculo) REFERENCES camiones(placa),
	ADD FOREIGN KEY (id_ciudad) REFERENCES ciudades(id),
	ADD FOREIGN KEY (numero_identificacion_conductor) REFERENCES conductores(numero_identificacion);

ALTER TABLE `ordenesrecogida`
	ADD FOREIGN KEY (id_ciudad) REFERENCES ciudades(id),
	ADD FOREIGN KEY (placa_vehiculo) REFERENCES camiones(placa),
	ADD FOREIGN KEY (numero_identificacion_conductor) REFERENCES conductores(numero_identificacion);

-- Mayo 29, 2013
ALTER TABLE  `conductores`
	ADD  `celular` VARCHAR( 10 ) NOT NULL,
	CHANGE  `nombre`  `nombre` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	CHANGE  `primer_apellido`  `primer_apellido` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	CHANGE  `segundo_apellido`  `segundo_apellido` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE  `telefono`  `telefono` VARCHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `clientes`
	-- CHANGE  `telefono3`  `celular` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE  `nombre`  `nombre` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	CHANGE  `primer_apellido`  `primer_apellido` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
	CHANGE  `segundo_apellido`  `segundo_apellido` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE  `telefono2`  `telefono2` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE  `celular`  `celular` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	ADD  `condicion_pago` INT( 2 ) NOT NULL DEFAULT  '15';

ALTER TABLE  `terceros`
	ADD  `celular` VARCHAR( 10 ) NOT NULL,
	CHANGE  `numero_identificacion`  `numero_identificacion` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

ALTER TABLE  `camiones`
	CHANGE  `codigo_Marcas`  `codigo_Marcas` CHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	CHANGE  `codigo_linea`  `codigo_linea` INT( 10 ) NOT NULL,
	CHANGE  `codigo_colores`  `codigo_colores` INT( 5 ) NOT NULL,
	CHANGE  `capacidadcarga`  `capacidadcarga` INT( 5 ) NOT NULL,
	ADD  `unidad_medida_capacidad_carga` INT( 1 ) NOT NULL DEFAULT  '1',
	CHANGE  `serie`  `serie` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
