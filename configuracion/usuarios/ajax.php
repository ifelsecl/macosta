<?php
require "../../seguridad.php";
$usuario = new Usuario;
if (isset($_POST['guardar'])) {//nuevo usuario
	if (! isset($_REQUEST['permisos'])) {
		echo '<table class="no_resultados">';
		echo '<tr><td><img src="css/images/alert.png" alt="Alerta"/></td>';
		echo '<td>Selecciona por lo menos un permiso.</td></tr></table>';
		exit;
	}
	$nombre = htmlspecialchars($_POST['nombre']);
	$u = strtolower(htmlspecialchars($_POST['usuario']));
	$clave = $_POST['clave'];
	$cedula = $_POST['cedula'];
	$email = strtolower($_POST['email']);
	$idperfil = $_POST['idperfil'];
	$usuario = new Usuario;
	if (! $usuario->Existe($u)) {
		$permisos = '';
		foreach($_REQUEST['permisos'] as $permiso){
			$permisos .= $permiso.';';
		}
		$permisos=substr($permisos, 0, -1);
		if ($usuario->Agregar($nombre,$cedula, $u, $clave, $email, $idperfil, $permisos)) {
			$logger = new Logger;
			$logger->log($_SERVER['REMOTE_ADDR'], 'ha creado el usuario <b>'.$nombre.'</b>.', "Usuarios", date("Y-m-d H:i:s"), $_SESSION['userid']);
			echo "ok";
		} else {
			include Logistica::$root.'mensajes/guardando_error.php';
		}
	} else {
		echo "<table><tr><td><img src='css/images/alert.png' alt='Alerta'/></td><td>Existe un usuario registrado con ese nombre de usuario, prueba con otro.</td></tr></table>";
	}
	exit;
}
if (isset($_POST['editar'])) {
	if (! isset($_REQUEST['permisos'])) {
		echo '<table class="no_resultados">';
		echo '<tr><td><img src="css/images/alert.png" alt="Alerta"/></td>';
		echo '<td>Selecciona por lo menos un permiso.</td></tr></table>';
		exit;
	}
	$nombre = htmlspecialchars($_POST['nombre']);
	$cedula = $_POST['cedula'];
	$clave = $_POST['clave'];
	$email = strtolower($_POST['email']);
	$idperfil = $_POST['idperfil'];
	$id = $_POST['idusuario'];
	$permisos = "";
	foreach ($_POST['permisos'] as $permiso) {
		$permisos.= $permiso.';';
	}
	$permisos=substr($permisos, 0,-1);
	$usuario = new Usuario;
	if ($usuario->Actualizar($id, $nombre, $cedula, $clave, $email, $idperfil, $permisos)) {
		Logger::usuario($id, 'editó el usuario');
		echo 'ok';
	} else {
		include Logistica::$root.'mensajes/guardando_error.php';
	}
	exit;
}
