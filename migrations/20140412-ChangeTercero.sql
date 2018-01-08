ALTER TABLE `terceros`
  CHANGE `nombre` `nombre` VARCHAR(200) NOT NULL,
  CHANGE `tipo_identificacion` `tipo_identificacion` CHAR(1) NOT NULL,
  CHANGE `numero_identificacion` `numero_identificacion` VARCHAR(11) NOT NULL,
  CHANGE `nombre` `nombre` VARCHAR(200) NOT NULL,
  CHANGE `primer_apellido` `primer_apellido` VARCHAR(12) NOT NULL,
  CHANGE `segundo_apellido` `segundo_apellido` VARCHAR(12) NOT NULL,
  CHANGE `direccion` `direccion` VARCHAR(100) NOT NULL,
  CHANGE `telefono` `telefono` VARCHAR(10) NOT NULL,
  CHANGE `email` `email` VARCHAR(255) NOT NULL;
