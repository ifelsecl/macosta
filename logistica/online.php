<?php
if (! defined('LOGS_DATABASE_NAME')) exit;

/*
Requirements:
- a database connection stablished
- a logged in user
*/
$timeout          = 5;
$connected_at     = time();
$table            = LOGS_DATABASE_NAME.".online";
$ip               = $_SESSION['ip'];
$limit_time       = $connected_at - ($timeout * 60);
if (isset($_SESSION['numero_identificacion'])) {
  $user = $_SESSION['numero_identificacion'];
  $user_type = 'Cliente';
} else {
  $user_type = 'Usuario';
  $user = $_SESSION['username'];
}
DBManager::execute("DELETE FROM $table WHERE tiempo < $limit_time");
$sql = "SELECT count(*) count FROM $table WHERE ip='$ip' AND tipo='$user_type'";
if (DBManager::count($sql) != 0) {
  $sql = "UPDATE $table SET tiempo='$connected_at' WHERE ip='$ip' AND tipo='$user_type'";
} else {
  $user_name = $_SESSION['nombre'];
  $sql = "INSERT INTO $table (ip,tiempo,nombre,tipo) VALUES ('$ip','$connected_at','$user_name','$user_type')";
}
DBManager::execute($sql);
unset($sql);
unset($table);
