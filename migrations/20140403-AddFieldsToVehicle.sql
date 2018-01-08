ALTER TABLE `camiones`
  CHANGE `fecha_afiliacion` `fecha_afiliacion` DATE NULL DEFAULT NULL,
  ADD `fecha_matricula` DATE NULL DEFAULT NULL ,
  ADD `numero_chasis` VARCHAR(30) NOT NULL ,
  ADD `numero_motor` VARCHAR(55) NOT NULL ,
  ADD `numero_licencia_transito` VARCHAR(15) NOT NULL ,
  ADD `fecha_expedicion_soat` DATE NULL DEFAULT NULL ,
  ADD `numero_ficha_homologacion` INT(30) NOT NULL ;
