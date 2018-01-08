CREATE TABLE `vehiculos_mantenimientos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `vehiculo_placa` varchar(6) NOT NULL,
 `mantenimiento_id` int(11) NOT NULL,
 `fecha` date NOT NULL,
 `tipo` varchar(20) NOT NULL,
 `precio` double NOT NULL,
 `observacion` text NOT NULL,
 PRIMARY KEY (`id`),
 KEY `vehiculos_mantenimientos_ibfk_1` (`vehiculo_placa`),
 KEY `vehiculos_mantenimientos_ibfk_2` (`mantenimiento_id`),
 CONSTRAINT `vehiculos_mantenimientos_ibfk_1` FOREIGN KEY (`vehiculo_placa`) REFERENCES `camiones` (`placa`),
 CONSTRAINT `vehiculos_mantenimientos_ibfk_2` FOREIGN KEY (`mantenimiento_id`) REFERENCES `mantenimientos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8

DELETE FROM `logistica`.`configuracion` WHERE `configuracion`.`name` = 'siigo_cree_base'
