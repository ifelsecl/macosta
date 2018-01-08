ALTER TABLE `facturas`
  ADD `total_pagos` DOUBLE NOT NULL,
  DROP `reportada`,
  DROP `fecha_pago`,
  DROP `observaciones_pago`;
UPDATE `facturas` SET `total_pagos` = `total`;

CREATE TABLE `pagos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `factura_id` bigint(11) unsigned zerofill NOT NULL,
 `fecha` date NOT NULL,
 `tipo` enum('cheque','efectivo','consignacion') NOT NULL DEFAULT 'efectivo',
 `notas` text NOT NULL,
 `valor` double NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE pagos ADD FOREIGN KEY (`factura_id`) REFERENCES facturas(id);
