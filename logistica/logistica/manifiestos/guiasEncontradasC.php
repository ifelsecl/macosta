<?php
/**
 * Guias encontradas.
 * Este archivo es usado para buscar las guías encontradas en CREAR PLANILLA
 */

require_once "../../seguridad.php";

require Logistica::$root."class/guias.class.php";
$guias_encontradas = Guia::search_in_bodega($_GET['termino'], $_GET['opcion']);
$paging = new PHPPaging('GuiasEncontradas', $guias_encontradas);
$paging->porPagina(10);
$paging->ejecutar();
$cantidad = $paging->numTotalRegistros();
$configuracion = new Configuracion;
$constante = $configuracion->ObtenerConstanteKiloVolumen();
?>
<script>
$(function(){
  var remitente='', destinatario='', destino='', fecha='', volumen=0, peso=0, total=0;
  $('.asignar').click(function(event){
    event.preventDefault();
    var id=this.name;
    var existe=false;
    $('.ids').each(function(index) {
      if (id == $(this).val()) existe = true;
    });
    if(!existe){
      remitente     = $(this).closest('td').prev().prev().prev().prev().prev().prev().prev().prev().text();
      destinatario  = $(this).closest('td').prev().prev().prev().prev().prev().prev().prev().text();
      destino       = $(this).closest('td').prev().prev().prev().prev().prev().prev().text();
      peso          = parseFloat($(this).closest('td').prev().prev().prev().prev().text());
      volumen       = parseFloat($(this).closest('td').prev().prev().prev().text());
      total         = parseFloat($(this).closest('td').prev().prev().text());
      var fila = '<tr id="ids_'+id+'"><td><input type="hidden" class="ids" name="guias[]" value="'+id+'" />'+id+'</td>'+
        '<td>'+remitente+'</td><td>'+destinatario+'</td><td>'+destino+'</td>'+
        '<td align="right"><input type="hidden" class="peso" value="'+peso+'" />'+peso+'</td>'+
        '<td align="right"><input type="hidden" class="volumen" value="'+volumen+'" />'+volumen+'</td>'+
        '<td align="right"><input type="hidden" class="total" value="'+total+'" />'+total+'</td>'+
        '<td><div class="btn-group"><a class="btn ver btn-mini" name="id='+id+'" href="#"><i class="icon-search"></i></a>'+
        '<a class="btn quitar btn-danger btn-mini" href="quitar"><i class="icon-remove"></i></a></div></td></tr>';
      $('#GuiasAsignadas').append(fila);
    }

    Totales();
    $('.ver').click(function(e){
      e.preventDefault();
      LOGISTICA.Dialog.open('Guía', guias_path+'ver.php?'+$(this).attr('name'));
    });
    $(this).closest('tr').remove();
    $('.quitar').click(function(e){
      e.preventDefault();
      $(this).closest('tr').remove();
      Totales();
    });
  });
  $('.ver').click(function(e){
    e.preventDefault();
    LOGISTICA.Dialog.open('Guía', guias_path+'ver.php?'+$(this).attr('name'));
  });
  function Totales(){
    var t=0, p=0, v=0;
    $('input.peso').each(function(index, element){
      p+=parseFloat($(this).val());
    });
    $('input.volumen').each(function(index, element){
      v+=parseFloat($(this).val());
    });
    $('input.total').each(function(index, element){
      t+=parseFloat($(this).val());
    });
    $('#peso').text(p);
    $('#volumen').text(v);
    $('#total').text(t);
  }
});
function fn_paginar(d,url){
  $('#'+d).load(url);
}
</script>
<table class="table table-bordered table-hover table-condensed" style="font-size: 11px !important">
  <thead>
    <tr>
      <th>No.</th>
      <th>Remitente</th>
      <th>Destinatario</th>
      <th>Destino</th>
      <th>Dirección</th>
      <th>Peso (Kg)</th>
      <th>Vol (m3)</th>
      <th>Total</th>
      <th></th>
      <th>Acción</th>
    </tr>
  </thead>
  <tbody>
  <?php
  if ($cantidad > 0) {
    $hace_3_dias = date("Y-m-d", mktime(0,0,0, date("m"), date("d")-3, date("Y")));
    $hace_2_dias = date("Y-m-d", mktime(0,0,0, date("m"), date("d")-2, date("Y")));
    $hoy = date('Y-m-d');
    $guias_asignadas = isset($_REQUEST['guias']) ? $_REQUEST['guias'] : array();
    unset($_GET['guias']);
    while ($gui = $paging->fetchResultado()) {
      if (! in_array($gui['id'], $guias_asignadas)) { //Se comprueba con las guias agregadas a la planilla.
        echo '<td>'.$gui['id'].'</td>';
        $cliente = trim($gui['cliente_n'].' '.$gui['cliente_pa']);
        echo '<td title="'.$cliente.'">'.substr($cliente, 0, 20).'</td>';
        $contacto = trim($gui['nombrecontacto'].' '.$gui['primer_apellido_contacto'].' '.$gui['segundo_apellido_contacto']);
        echo '<td title="'.$contacto.'">'.substr($contacto, 0, 20).'</td>';
        echo '<td>'.$gui['nombreciudaddestino'].'</td>';
        echo '<td>'.$gui['direccion_contacto'].'</td>';
        echo '<td align="right">'.$gui['peso'].'</td>';
        echo '<td align="right">'.($gui['kilo_vol']/$constante).'</td>';
        echo '<td align="right">'.($gui['total']+$gui['valorseguro']).'</td>';
        echo '<td align="center" title="'.$gui['fecha_recibido_mercancia'].'">';
        //mas de 3 dias
        if ($gui['fecha_recibido_mercancia']<=$hace_3_dias) {
          echo '<img src="img/red16.png" alt="rojo" title="" />';
        }elseif($gui['fecha_recibido_mercancia']>$hace_2_dias and $gui['fecha_recibido_mercancia']<$hoy){
          //2 dias
          echo '<img src="img/yellow16.png" alt="amarillo" title="" />';
        }else{
          //mismo día
          echo '<img src="img/green16.png" alt="verde" title="" />';
        }
        echo '</td>';
        echo '<td style="width:32px" align="center"><div class="btn-group">';
        $name="id=".$gui["id"]."&".nonce_create_query_string($gui['id']);
        echo '<a class="btn ver btn-mini" name="'.$name.'" href="#" title="Ver guía"><i class="icon-search"></i></a>';
        echo '<a class="btn asignar btn-success btn-mini" name="'.$gui['id'].'" href="#" title="Asignar al Manifiesto"><i class="icon-plus"></i></a>';
        echo '</div></td>';
        echo '</tr>';
      }
    }
  }else{
    echo '<tr><td colspan="11" class="expand">No se encontraron guías...</td></tr>';
  }
  ?>
  </tbody>
</table>
<?= $paging->fetchNavegacion() ?>
<input type="hidden" id="c" name="c" value="<?= $constante ?>" />
<br>
