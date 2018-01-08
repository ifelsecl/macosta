<?php
$raiz='../../';
require_once $raiz.'seguridad.php';
require_once $raiz."class/guias.class.php";
$objGuia = new Guias;
$num = mysql_num_rows($objGuia->Abiertas());
$ultimo_cierre = $objGuia->UltimoCierre();
$num = number_format($num);
if($num == 0){
  $texto = '<b>Todas las guías han sido cerradas!<b>';
}else{
  $texto = '<b>'.$num == 1 ? 1 : $num.' guías por cerrar!</b>';
}
if(!$ultimo_cierre) $texto.='<br>No se ha realizado un cierre';
else $texto.='<br>El último cierre fue '.$ultimo_cierre;
?>
<script>
$(function(){
  var b=$('#continuar')
    .button({icons: {primary: 'ui-icon-circle-arrow-e'}})
    .click(function(e){
      b.button('disable').button('option','label','Cerrando...');
      $.ajax({
        url: 'logistica/guias/ajax.php',
        data: 'realizar_cierre=1',
        type: 'POST',
        success: function(r){
          $('#dialog').html(r);
        }
      });
    });
  if( $('#num').val()==0 ){
    b.button('disable');
  }
});
</script>
<h3>Realizar Cierre</h3>
<p class="muted">
Las guías creadas hasta la fecha serán cerradas.<br>
Cuando se importe una nueva lista de precios, los precios de las
guías no serán modificados.
</p>
<p><?= $texto ?></p>
<div class="text-center"><button id="continuar">Continuar</button></div>
<input type="hidden" name="num" id="num" value="<?= $num ?>" />
