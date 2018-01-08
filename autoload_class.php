<?php
function autoload_class($class) {
  if (in_array($class, array('Camiones', 'Guias', 'PlanillasC'))) {
    $class = strtolower($class);
    require_once "class/$class.class.php";
  } else {
    require_once "class/$class.php";
  }
}
spl_autoload_register('autoload_class');
