ALTER TABLE `terceros` ADD `celular` VARCHAR(10) NOT NULL ;
update camiones set fecha_expedicion_soat=NULL where fecha_expedicion_soat = '0000-00-00';
update camiones set fecha_matricula=NULL where fecha_matricula = '0000-00-00';
