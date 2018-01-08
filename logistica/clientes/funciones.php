<?php
/*
Este archivo contiene funciones usadas en la parte de los clientes.
*/
function sesionIniciada(){
  session_name('Logistica_Clientes');
  if( ! isset($_SESSION) ) session_start();
  return isset($_SESSION['numero_identificacion']);
}

function redireccionar($url){
  header("Location: $url");
  exit;
}

function IP(){
  if (isset($_SERVER['HTTP_CLIENT_IP'])){
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  }elseif ( isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}

function locale(){
  setlocale(LC_ALL, array('Spanish_Colombia.1252','Spanish',''));
  date_default_timezone_set("America/Bogota");
}

function mayor($u, $k, $v){
  if($u==$k && $u==$v) return $u; //iguales
  if($u>=$k && $u>=$v) return $u;
  if($k>=$u && $k>=$v) return $k;
  if($v>=$u && $v>=$k) return $v;
}
