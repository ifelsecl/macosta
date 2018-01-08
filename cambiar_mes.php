<?php
include_once 'seguridad.php';
if(isset($_REQUEST['cambiar'])){
  $mes=explode($_REQUEST['mes']);
  echo $_SESSION['mes'] = $mes[1];
  echo $_SESSION['ano'] = $mes[0];
  echo 'ok';
  exit;
}
?>
<script type="javascript">
$(function(){
  $('#cambiar').click(function(e){
    e.preventDefault();
    $.ajax({
      url: 'cambiar_mes.php',
      data: 'cambiar=1&'+$('#form_cambiar_mes').serialize(),
      success: function(m){
        $('#cambiar_mes').text($('#mes').text());
        $('#dialog').html(m);
      }
    });
  });
});
</script>
<p>Mes actual:</p>
<h2><?= ucfirst(strftime('%B %Y',strtotime($_SESSION['ano'].'-'.$_SESSION['mes'].'-01'))); ?></h2>
<form action="#" method="post" id="form_cambiar_mes">
<table>
  <tr>
    <td>Cambiar el mes de trabajo:</td>
    <td>
      <select name="mes" id="mes" class="ui-widget-content">
        <?php
        $mes=$_SESSION['mes']-1;
        $ano=$_SESSION['ano'];
        if($mes==0){
          $mes=12;
          $ano--;
        }
        for ($i=0; $i < 6; $i++) {
          echo '<option value="'.$ano.'-'.$mes.'-01">'.strftime('%B %Y', mktime(0,0,0, $mes--,1,$ano)).'</option>';
          if($mes==0){
            $ano--;
            $mes=12;
          }
        }
        ?>
      </select>
    </td>
    <td><button id="cambiar" type="submit">Cambiar</button></td>
  </tr>
</table>
