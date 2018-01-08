<?php
require 'helpers/html.php';
$images = array("img/1.jpg", "img/2.jpg", "img/3.jpg", "img/4.jpg", "img/5.jpg", "img/6.jpg");
$texts  = array("Barranquilla", "Cartagena", "Santa Marta", "Barranquilla", "Cartagena", "Santa Marta");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
  <meta name="description" content="Transportes Mario Acosta">
  <meta name="author" content="Transportes Mario Acosta">
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <title>Transportes Mario Acosta ─ Inicio</title>
  <?php
  echo bower_css_component('lumen/lumen.min');
  echo bower_css_component('font-awesome/css/font-awesome.min');
  echo css_tag('full-slider');
  ?>
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <?php
  echo bower_js_component('jquery/dist/jquery.min');
  echo bower_js_component('lumen/bootstrap.min');
  echo bower_js_component('jquery-validation/dist/jquery.validate.min');
  echo bower_js_component('jquery-validation/src/localization/messages_es');
  ?>
</head>
<body>
  <nav class="navbar navbar-fixed-top navbar-default" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php"><b>Transportes Mario Acosta & CIA LTDA</b></a>
      </div>

      <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav pull-right">
          <li>
            <a href="#" data-toggle="modal" data-target="#modal-administrative" onclick="ga('send', 'event', 'Inicio', 'click', 'Administrativo');"><i class="icon-user"></i> Acceso Administrativo</a>
          </li>
          <li>
            <a href="#" data-toggle="modal" data-target="#modal-clients" onclick="ga('send', 'event', 'Inicio', 'click', 'Clientes');"><i class="icon-user"></i> Acceso Clientes</a>
          </li>
        </ul>
      </div>
      <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
  </nav>

  <div id="carousel" class="carousel slide" data-interval="5000" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
    <?php
    $images_count = count($images);
    for ($i = 0; $i < $images_count; $i++) { ?>
      <li data-target="#carousel" data-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>"></li>
    <?php } ?>
    </ol>
    <!-- /Indicators -->

    <!-- Wrapper for slides -->
    <div class="carousel-inner">
    <?php foreach ($images as $key => $image) { ?>
      <div class="item <?= $key == 0 ? 'active' : '' ?>">
        <div class="fill" style="background-image:url('<?= $image ?>');"></div>
        <div class="carousel-caption">
          <h1><?= $texts[$key] ?></h1>
        </div>
      </div>
    <?php } ?>
    </div>
    <!-- /Wrapper for slides -->

    <!-- Controls -->
    <a class="left carousel-control" href="#carousel" data-slide="prev">
      <span class="icon-prev"></span>
    </a>
    <a class="right carousel-control" href="#carousel" data-slide="next">
      <span class="icon-next"></span>
    </a>
    <!-- /Controls -->
  </div>

      <div class="container">

    <div class="row section" id="contact">
      <div class="col-sm-6">
        <a href="https://facebook.com/TransportesMarioAcosta" target="_blank" class="btn btn-primary"><i class="icon-facebook-sign"></i> Facebook</a>
      </div>
   
         
      
      <div class="col-sm-6">
        <p><b>Barranquilla, Colombia</b></p>
        <p>Carrera 26 # 30-09</p>
        <p>3700808 - 3794880 - 3707478 Cel.: 3186158664</p>
        <p>info@transmarioacosta.com</p>
        <p>transportesmarioacosta@gmail.com</p>
      </div>
    </div>

    <hr>

    <footer id="footer">
      <div class="row">
        <div class="col-sm-6 col-sm-offset-3 text-center">
          <p>Copyright &copy; Transportes Mario Acosta <?= date('Y') ?></p>
        </div>
      </div>
    </footer>

  </div>
  <!-- /.container -->

  <!-- Dialogs -->
  <div class="modal fade" id="modal-administrative" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="form--administrative" class="form-horizontal" method="post" action="#">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h3 class="modal-title">Acceso Administrativo</h3>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="user__username" class="col-sm-4 control-label">Usuario</label>
              <div class="col-sm-7">
                <input type="text" class="form-control focus" id="user__username" name="username" placeholder="Usuario" autofocus>
              </div>
            </div>
            <div class="form-group">
              <label for="user__password" class="col-sm-4 control-label">Contraseña</label>
              <div class="col-sm-7">
                <input type="password" class="form-control" id="user__password" name="password" placeholder="Contraseña">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <span class="label label-danger label--error pull-left"></span>
            <button type="submit" class="btn btn-primary submit" data-loading-text="Ingresando...">Iniciar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modal-clients" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post" id="form--clients" class="form-horizontal" method="post" action="#">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h3 class="modal-title">Acceso Clientes</h3>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="client__identification-number" class="col-sm-4 control-label">Número Identificación</label>
              <div class="col-sm-7">
                <input type="text" class="form-control focus" id="client__identification-number" name="numero_identificacion" placeholder="Número Identificación" autofocus>
              </div>
            </div>
            <div class="form-group">
              <label for="client__password" class="col-sm-4 control-label">Contraseña</label>
              <div class="col-sm-7">
                <input type="password" class="form-control" id="client__password" name="clave" placeholder="Contraseña">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <span class="label label-danger label--error pull-left"></span>
            <button type="submit" class="btn btn-primary">Iniciar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Dialogs -->

  <script src="js/login-app-e1c45d44.js"></script>
  <?= google_analytics() ?>
</body>
</html>

                            