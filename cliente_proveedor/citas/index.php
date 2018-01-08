<?php
require '../../seguridad.php';
?>
<div class="row-fluid">
  <div class="span12">
    <h2>Generar Formato de Citas</h2>
  </div>
</div>
<div id="formato_citas__index">
  <div class="row-fluid">
    <div class="span12">
      <form class="form-horizontal" id="formato_citas__add_guia">
        <div class="input-append">
          <input class="input-small" name="guia[id]" type="text" placeholder="Guía">
          <button class="btn btn-primary" type="submit">Agregar</button>
        </div>
      </form>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span12">
      <form class="form-horizontal" id="formato_citas__generar" action="cliente_proveedor/citas/generar.php" method="post" target="_blank">
        <div class="tabbable">
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#tab_informacion">Información</a></li>
            <li><a data-toggle="tab" href="#tab_guias">Guías</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_informacion">
              <div class="control-group">
                <label class="control-label" for="cita__fecha_pedido">Fecha Pedido</label>
                <div class="controls">
                  <input type="text" name="cita[fecha_pedido]" id="cita__fecha_pedido">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="cita__fecha_cita">Fecha Cita</label>
                <div class="controls">
                  <input type="text" name="cita[fecha_cita]" id="cita__fecha_cita">
                </div>
              </div>
              <div class="control-group">
                <label class="control-label" for="cita__nombre_responsable">Nombre Responsable</label>
                <div class="controls">
                  <input type="text" name="cita[nombre_responsable]" id="cita__nombre_responsable">
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab_guias">
              <div class="row-fluid">
                <div class="span12">
                  <table class="table table-hover table-bordered table-condensed">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Remitente</th>
                        <th>Destinatario</th>
                        <th>Unds</th>
                        <th>Peso</th>
                        <th>Borrar</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div> <!-- /#tab_guias -->
          </div>
        </div> <!-- /.tabbable -->
        <div class="form-actions">
          <button type="submit" class="btn btn-primary"><i class="icon-th-list"></i> Generar Formato</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script type="text/html" id="guiaTableRowTemplate">
  <tr>
    <td>{{id}}<input type="hidden" name="guias[]" value="{{id}}" /></td>
    <td>
      {{cliente.nombre_completo}}<br><small class="muted">{{cliente.ciudad_nombre}}</small>
    </td>
    <td>
      {{contacto.nombre_completo}}<br><small class="muted">{{contacto.ciudad_nombre}}</small>
    </td>
    <td class="text-right">{{unidades}}</td>
    <td class="text-right">{{peso}}</td>
    <td><button class="remove btn btn-danger" data-id="{{id}}"><i class="icon-trash"></i></button></td>
  </tr>
</script>
<script>
(function() {
  LOGISTICA.FormatoCitas = function() {
    var $el = $('#formato_citas__index'),
        $formAddGuia = $el.find('#formato_citas__add_guia'),
        $addBtn = $formAddGuia.find('button'),
        guias = [],
        _template;

    var findGuia = function(form) {
      $addBtn.prop('disabled', true).text('Agregando...');
      $.ajax({
        url: 'helpers/ajax.php?',
        data: $(form).serialize(),
        success: function(response) {
          $addBtn.prop('disabled', false).text('Agregar');
          if (response.error) {
            alertify.error(response.message);
          } else {
            $formAddGuia.find('input').val('');
            renderRow(response);
            addGuia(response.id);
          }
        }
      });
    };

    var renderRow = function(guia) {
      var html = compiledTemplate().render( guia );
      $el.find('table tbody').append( html );
    };

    var compiledTemplate = function() {
      return _template || (_template = Hogan.compile( $('#guiaTableRowTemplate').html() ));
    };

    var initFormValidator = function() {
      $formAddGuia.validate({
        rules: {
          'guia[id]': { required: true,
            digits: true }
        },
        errorPlacement: function(er, el) {},
        submitHandler: findGuia
      });
    };

    var addGuia = function(id) {
      guias.push(id);
    };

    var removeGuia = function(id) {
      guias.splice(guias.indexOf(id), 1);
    };

    var enableRemoveButton = function() {
      $el.on('click', 'button.remove', function () {
        $(this).parents('tr').remove();
        removeGuia( $(this).data('id') );
      });
    };

    var initDatePicker = function() {
      $el.find('#cita__fecha_cita, #cita__fecha_pedido').datepicker({
        autoSize: true,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mm-dd-yy',
        buttonText: 'Seleccionar...'
      });
    };

    return {
      init: function() {
        initDatePicker();
        initFormValidator();
        enableRemoveButton();
      }
    };

  }();

  LOGISTICA.FormatoCitas.init();
})();
</script>
