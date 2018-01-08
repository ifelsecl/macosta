<?php
;
$is_js_asset  = strpos($_SERVER['REQUEST_URI'], '.js') !== false;
$is_css_asset = strpos($_SERVER['REQUEST_URI'], '.css') !== false;
$is_ajax      = isset($_SERVER['HTTP_X_REQUESTED_WITH']);
if ($is_ajax or $is_js_asset or $is_css_asset) {
  echo '<h1>404 - Not Found</h1>';
} else {
  header('location: /logistica/inicio');
}
