<?php
require '../../seguridad.php';
if (! isset($_GET['id'])) exit('Algo ha salido mal...');
if (! $mantenimiento = Mantenimiento::find($_GET['id'])) exit('No existe el mantenimiento.');
require '_form.php';
