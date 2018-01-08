<?php
require '../seguridad.php';
if (! isset($_SESSION['permisos'][MANTEN_ENTRAR])) {
  include Logistica::$root.'mensajes/permiso.php';
  exit;
}

<div class="span2 left_content">
  <ul id="menu" class="sidebarmenu">
    <?php
       ?>
      
  </ul>
  <div class="sidebar_box visible-desktop">
    <div class="sidebar_box_top">
      <img src="css/images/info.png" alt="Info" class="pull-right" />
      <h4>User Help Desk</h4>
    </div>
    <div class="sidebar_box_content">
      <p>Puede gestionar la logistica de su empresa, administrar los
conductores, propietarios, tenedores, titulares, vehículos, manifiestos, guías
 y las ordenes de recogida.</p>
    </div>
  </div>
</div>

<?php require("DBManager.php"); ?>

<?php 
$sql = "select * from productos;";
?>

<?php include("templates/header.php"); ?>
<h1>Crear Filtros de Productos en Php y Mysql</h1>
<h3>Listado de Productos</h3>
<div id="filtros">
</div>
<div id="productos">
<?php
$result = mysql_query($sql, $link);
if(!$result )
{
die('Ocurrio un error al obtener los valores de la base de datos: ' . mysql_error());
}
echo "<center><table><th>Id</th><th>Nombre</th><th>Descripcion</th><th>Precio</th><th>Fecha de Registro</th>";

while($row = mysql_fetch_array($result, MYSQL_ASSOC))
{
echo "<tr><td>{$row['id']}</td> ".
"<td>{$row['nombre']} </td> ".
"<td>{$row['descripcion']} </td> ".
"<td>{$row['precio']} </td> ".
"<td>{$row['fecha_registro']} </td></tr>";
} 
echo "</table></center>";
mysql_close($link);
?>
</div>
<?php include("templates/footer.php"); ?>

<div id="filtros">
Selecciona los filtros deseados para encontrar los productos <form action="index.php" method="post"><select name="filtro"><option value="todos"></option><option value="recientes">Mas Recientes</option><option value="antiguos">Mas Antiguos</option><option value="caros">Mas Caros</option><option value="economicos">Mas Economicos</option></select> <button type="submit">Filtrar</button></form>
</div>
<div class="span10 right_content"></div><!-- end of right content-->
<div id="extra_content" class="span10" style="display:none"></div><!-- end of extra content-->



