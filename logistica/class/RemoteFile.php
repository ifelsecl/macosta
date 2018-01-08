<?php
class RemoteFile {
  static $url = 'http://transma.ddns.net:8080';
  static $username = 'TransM';
  static $password = 'Cma#2013!';

  static function process($ids, $ip = null) {
    $params = '';
    foreach ($ids as $id) $params .= 'g[]='.$id.'&';
    $process = curl_init(self::$url.'?'.$params.'ip='.$ip);
    if (!$process) exit('Not Initialized!');
    curl_setopt($process, CURLOPT_USERPWD, self::$username.':'.self::$password);
    curl_setopt($process, CURLOPT_TIMEOUT, 10);
    curl_setopt($process, CURLOPT_USERAGENT, 'Logistica, PHP');
    curl_setopt($process, CURLOPT_REFERER, 'http://transmarioacosta.com');
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode( curl_exec($process) );
    curl_close($process);
    if (empty($response)) {
      $response = self::empty_response();
    }
    return $response->data;
  }

  static function empty_response() {
    $pdf = new stdClass();
    $response = new stdClass();
    $pdf->found = false;
    $response->data = array($pdf);
    return $response;
  }
}