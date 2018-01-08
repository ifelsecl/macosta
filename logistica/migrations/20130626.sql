CREATE TABLE `talonarios` (
 `id` int(11) NOT NULL AUTO_INCREMENT COMMENT ' ',
 `conductor_numero_identificacion` bigint(11) NOT NULL,
 `inicio` int(11) NOT NULL,
 `fin` int(11) NOT NULL,
 `fecha_entrega` date DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `conductor_numero_identificacion` (`conductor_numero_identificacion`),
 CONSTRAINT `control_guias_ibfk_1` FOREIGN KEY (`conductor_numero_identificacion`) REFERENCES `conductores` (`numero_identificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `logistica`.`tareas` (`nombre` , `descripcion` , `modulo`)
VALUES ('Talonarios_Entrar', 'Permite administrar el control de guías', 'Talonarios');

ALTER TABLE `guias`
  ADD `id_tipo_operacion` ENUM( 'G', 'P', 'C', 'V' ) NOT NULL DEFAULT 'G',
  DROP `imagen`,
  DROP `historial_cambios`;

CREATE TABLE `resoluciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('manifiestos','facturacion') NOT NULL DEFAULT 'manifiestos',
  `numero` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `inicio` int(11) NOT NULL,
  `fin` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `logistica`.`tareas` (`nombre` , `descripcion` , `modulo`)
VALUES ('Resoluciones_Entrar', 'Permite administrar las resoluciones adquiridas por la empresa.', 'Resoluciones');

DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'facturacion_fecha_resolucion';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'facturacion_fin_rango';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'facturacion_inicio_rango';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'facturacion_numero_resolucion';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'fecha_resolucion';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'fin_rango';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'inicio_rango';
DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'numero_resolucion';

UPDATE guias SET id_estado_anterior=1 WHERE id_estado_anterior = 0;
UPDATE  `logistica`.`configuracion` SET  `name` =  'siigo_cuenta_contable_descuento' WHERE  `configuracion`.`name` =  'siigo_cuenta_contable_credito';
UPDATE  `logistica`.`configuracion` SET  `name` =  'siigo_cuenta_contable_total_credito' WHERE  `configuracion`.`name` =  'siigo_cuenta_contable_total_neto';
INSERT INTO  `logistica`.`configuracion` (`name` , `value`) VALUES ('siigo_cuenta_contable_total_contado',  '1105050000');
ALTER TABLE  `facturas` ADD  `tipo` ENUM(  'CREDITO',  'CONTADO' ) NOT NULL DEFAULT  'CREDITO';
INSERT INTO  `logistica`.`configuracion` (`name`, `value`) VALUES
  ('siigo_cuenta_contable_cree_credito',  '2365830000'),
  ('siigo_cuenta_contable_cree_debito',  '1355210000'),
  ('siigo_cree_base',  '107000'),
  ('siigo_cree_porcentaje',  '0.006');

ALTER TABLE  `listaprecios` ADD  `descuento3` DECIMAL NOT NULL ,
  ADD  `descuento6` DECIMAL NOT NULL ,
  ADD  `descuento8` DECIMAL NOT NULL ;

ALTER TABLE  `embalajes` CHANGE  `tipo_cobro`  `tipo_cobro`
  ENUM(  'Caja',  'Unidad',  'Kilo', 'Kilo Volumen',  'Porcentaje',  'Viaje Convenido',  'Descuento' ) CHARACTER
  SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'Caja';

ALTER TABLE  `rutas_locales` CHANGE  `placa_vehiculo`  `placa_vehiculo` VARCHAR( 6 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;
ALTER TABLE  `rutas_locales` ADD  `placa_vehiculo_2` VARCHAR( 6 ) NOT NULL ;

ALTER TABLE  `embalajes` CHANGE  `tipo_cobro`  `tipo_cobro` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'Caja';
