!function(){var i={};i.Inicio={$formAdministrative:$("#form--administrative"),$formClients:$("#form--clients"),init:function(){this.initModalAutofocus(),this.initFormAdministrative(),this.initFormClients()},initModalAutofocus:function(){$(".modal").on("shown.bs.modal",function(){$(this).find("input.focus").focus()})},initFormAdministrative:function(){var i=this;i.$formAdministrative.validate({rules:{username:"required",password:"required"},highlight:function(i){$(i).closest(".form-group").addClass("has-error")},unhighlight:function(i){$(i).closest(".form-group").removeClass("has-error")},errorPlacement:function(){},submitHandler:function(t){var n=i.$formAdministrative.find("button.submit"),o=i.$formAdministrative.find(".label--error");n.button("loading"),o.html(""),$.ajax({type:"POST",url:"php/login.administrativo.php",data:$(t).serialize()}).done(function(i){return i?(o.html(i),void n.button("reset")):void(location.href="./#Inicio")})}})},initFormClients:function(){var i=this;i.$formClients.validate({rules:{numero_identificacion:"required",clave:"required"},highlight:function(i){$(i).closest(".form-group").addClass("has-error")},unhighlight:function(i){$(i).closest(".form-group").removeClass("has-error")},errorPlacement:function(){},submitHandler:function(t){var n=i.$formClients.find("button.submit"),o=i.$formClients.find(".label--error");n.button("loading"),o.html(""),$.ajax({type:"POST",url:"php/login.clientes.php",data:$(t).serialize()}).done(function(i){return i?(o.html(i),void n.button("reset")):void(location.href="clientes/#Inicio")})}})}},i.Inicio.init()}();