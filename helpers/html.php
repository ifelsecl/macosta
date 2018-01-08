<?php
function google_analytics() {
  return "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', 'UA-35698054-1', 'auto');ga('send', 'pageview');</script>";
}

function script_tag($file, $v = false) {
  return '<script src="js/'.$file.'.js'.($v ? "?_=$v" : '').'"></script>'.PHP_EOL;
}

function css_tag($file, $v = false) {
  return '<link rel="stylesheet" media="all" href="css/'.$file.'.css'.($v ? "?_=$v" : '').'"/>'.PHP_EOL;
}

function bower_js_component($file) {
  return '<script src="bower_components/'.$file.'.js"></script>'.PHP_EOL;
}

function bower_css_component($file) {
  return '<link rel="stylesheet" media="all" href="bower_components/'.$file.'.css" />'.PHP_EOL;
}
