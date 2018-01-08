<?php
/**
 * Inicio de archivo
 */
function xlsBOF() {
	return pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
}
/**
 * Fin de archivo
 */
function xlsEOF() {
	return pack("ss", 0x0A, 0x00);
}
/**
 * Escribe un número.
 */
function xlsWriteNumber($Row, $Col, $Value) {
	$n = pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	$n.= pack("d", $Value);
	return $n;
}
/**
 * Escribe una cadena.
 */
function xlsWriteLabel($Row, $Col, $Value ) {
	$L = strlen($Value);
	$s=pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	$s.=$Value;
	return $s;
}
?>