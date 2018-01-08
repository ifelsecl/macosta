<?php
/**
 * Este archivo contiene funciones utilizadas en la parte administrativa de la aplicación.
 */

function sesionIniciada() {
  if (! isset($_SESSION)) session_start();
  return (isset($_SESSION['username']) && isset($_SESSION['userid']));
}

function redireccionar($url) {
  header("Location: $url");
  exit;
}

function IP() {
  if (isset($_SERVER['HTTP_CLIENT_IP'])) { //share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { //pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

function locale() {
  setlocale(LC_ALL, array('Spanish_Colombia.1252','Spanish', 'es_ES'));
  date_default_timezone_set("America/Bogota");
}

function time_ago_es($tm, $rcs = 0) {
  $cur_tm = time();
  $dif = $cur_tm-$tm;
  $pds = array('segundo', 'minuto', 'hora', 'dia', 'semana', 'mes', 'año', 'decada');
  $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
  for ($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if ($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
  $no = floor($no);
  if ($v == 5) $pds[$v] .='es';
  elseif ($no > 1) $pds[$v] .='s'; $x=sprintf("%d %s", $no, $pds[$v]);
  if (($rcs > 0) && ($v >= 1) && (($cur_tm-$_tm) > 0)) $x .= TimeAgoEs($_tm, --$rcs);
  return $x;
}

/**
 * Calcula el mayor de 3 valores.
 */
function Mayor($u, $k, $v) {
  if ($u==$k and $u==$v) return $u;
  if ($u>=$k and $u>=$v) return $u;
  if ($k>=$u and $k>=$v) return $k;
  if ($v>=$k and $v>=$k) return $v;
}

/**
 * Genera una cadena con valores aleatorios.
 *
 * @param int $lenght la longitud de la cadena.
 * @param boolean $uc true para incluir mayúsculas.
 * @param boolean $n true para incluir números.
 * @param boolean $sc true para incluir carácteres especiales.
 * @since Diciembre 1, 2011
 */
function RandomString($length=15,$uc=true,$n=true,$sc=false) {
  $source = 'abcdefghijklmnopqrstuvwxyz';
  if ($uc == 1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  if ($n == 1) $source .= '1234567890';
  if ($sc == 1) $source .= '|@#~$%()=^*+[]{}-_';
  if ($length>0) {
    $rstr = "";
    $source = str_split($source,1);
    for ($i=1; $i<=$length; $i++) {
      mt_srand((double)microtime() * 1000000);
      $num = mt_rand(1,count($source));
      $rstr .= $source[$num-1];
    }

  }
  return $rstr;
}

                            