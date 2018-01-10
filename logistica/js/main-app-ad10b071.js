function abrirDialogo(t, a, o) {
    var e = {
        title: t,
        position: {
            my: "center",
            at: "center",
            of: window
        },
        width: "auto",
        height: "auto"
    };
    $("#dialog").dialog("option", e), o ? $("#dialog").html(a).dialog("open").dialog("option", e) : $("#dialog").html(c).dialog("open").load(a, function() {
        $("#dialog").dialog("option", e)
    })
}

function cerrarDialogo() {
    $("#dialog").dialog("close")
}

function cargarExtra(t) {
    $(".right_content").fadeOut(300, function() {
        $("#extra_content").html(c).show().load(t)
    })
}

function regresar() {
    $("#extra_content").fadeOut(300, function() {
        $(".right_content").show(), $("#extra_content").html("")
    })
}

function cargarPrincipal(t, a) {
    $(".right_content").load(t, a)
}

function Datepicker(t, a) {
    $(t).datepicker({
        autoSize: !0,
        showOn: "both",
        buttonImage: "css/images/calendar.gif",
        buttonImageOnly: !0,
        changeMonth: !0,
        changeYear: !0,
        dateFormat: "yy-mm-dd",
        buttonText: "Seleccionar...",
        gotoCurrent: !0,
        hideIfNoPrevNext: !0,
        minDate: a
    })
}

function calcular_dv(t) {
    if (isNaN(t) || t % 1 != 0) return "";
    var a, o, e = 0,
        i = 0,
        n = t.length;
    for (a = new Array(16), a[1] = 3, a[2] = 7, a[3] = 13, a[4] = 17, a[5] = 19, a[6] = 23, a[7] = 29, a[8] = 37, a[9] = 41, a[10] = 43, a[11] = 47, a[12] = 53, a[13] = 59, a[14] = 67, a[15] = 71, o = 0; n > o; o++) i = t.substr(o, 1), e += i * a[n - o];
    return i = e % 11, i > 1 ? 11 - i : i
}
var helpers_path = "helpers/",
    clientes_contactos_path = "cliente_proveedor/",
    contactos_path = clientes_contactos_path + "contactos/",
    clientes_path = clientes_contactos_path + "clientes/",
    vendedores_path = clientes_contactos_path + "vendedores/",
    citas_path = clientes_contactos_path + "citas/",
    lista_precios_path = clientes_path + "listaprecios/",
    facturacion_path = "facturacion/",
    relaciones_path = facturacion_path + "relaciones/",
    cartera_path = facturacion_path + "cartera/",
    logistica_path = "logistica/",
    manifiestos_path = logistica_path + "manifiestos/",
    productos_path = logistica_path + "productos/",
    vehiculos_path = logistica_path + "vehiculos/",
    mantenimientos_path = logistica_path + "mantenimientos/",
    terceros_path = logistica_path + "terceros/",
    guias_path = logistica_path + "guias/",
    ayudantes_path = logistica_path + "ayudantes/",
    conductores_path = logistica_path + "conductores/",
    rutas_locales_path = logistica_path + "rutas_locales/",
    ordenes_recogida_path = logistica_path + "ordenes_recogida/",
    talonarios_path = logistica_path + "talonarios/",
    configuracion_path = "configuracion/",
    usuarios_path = configuracion_path + "usuarios/",
    opciones_path = configuracion_path + "opciones/",
    embalajes_path = configuracion_path + "embalajes/",
    ciudades_path = configuracion_path + "ciudades/",
    resoluciones_path = configuracion_path + "resoluciones/",
    mantenimiento_path = "manten/",
    individual_path = mantenimiento_path + "individual/",
    general_path = mantenimiento_path + "general/",
    c = '<p class="expand"><img src="css/ajax-loader.gif" /> Cargando...</p>';
$.validator.setDefaults({
    ignore: []
}), $.validator.addMethod("placa", function(t) {
    return /^[A-Z]{3}[0-9]{3}$/.test(t)
}, "Escribe una placa válida."), $.validator.addMethod("placa_semiremolque", function(t) {
    return "" == t ? !0 : /^\R[0-9]{5}$/.test(t)
}, "Escribe una placa de semiremolque válida."), $.validator.addMethod("length", function(t, a, o) {
    return this.optional(a) || t.length == o
}, $.validator.format("Escribe {0} caracteres.")), $(function() {
    function t(t, o) {
        var e = "";
        switch (t) {
            case "Inicio":
                e = "menu.php";
                break;
            case "Clientes":
                e = "cliente_proveedor/";
                break;
            case "Facturacion":
                e = "facturacion/";
                break;
            case "Logistica":
                e = "logistica/";
                break;
            case "Configuracion":
                e = "configuracion/";
                break;
            case "Manten":
                e = "manten/";
                break;
            default:
                return void(location.hash = "Inicio")
        }
        $("#topmenu a").removeClass("current"), $("#m" + t).addClass("current").attr("href", "#" + t + "?" + Math.random().toFixed(2)), $(".center_content").load(e, function() {
            o && a(t, o)
        })
    }

    function a(t, a) {
        $("ul#menu a").removeClass("actual"), $("#m" + t).addClass("current");
        var o = "",
            e = "";
        if (a = a.split("?")[0], "Clientes" == t)
            if (o = "cliente_proveedor/", "Clientes" == a) e = "clientes";
            else if ("Contactos" == a) e = "contactos";
        else if ("Vendedores" == a) e = "vendedores";
        else if ("Cartas" == a) e = "cartas";
        else {
            if ("Citas" != a) return;
            e = "citas"
        } else if ("Facturacion" == t) o = "facturacion/", "NotasCredito" == a ? e = "notas_credito" : "Relaciones" == a ? e = "relaciones" : "Cartera" == a && (e = "cartera");
        else if ("Manten" == t) o = "manten/", "Individual" == a ? e = "individual" : "General" == a ? e = "general" : "";
        else if ("Logistica" == t)
            if (o = "logistica/", "Guias" == a) e = "guias";
            else if ("Manifiestos" == a) e = "manifiestos";
        else if ("Productos" == a) e = "productos";
        else if ("Vehiculos" == a) e = "vehiculos";
        else if ("Conductores" == a) e = "conductores";
        else if ("Terceros" == a) e = "terceros";
        else if ("OrdenesRecogida" == a) e = "ordenes_recogida";
        else if ("RutasLocales" == a) e = "rutas_locales";
        else if ("Ayudantes" == a) e = "ayudantes";
        
        else {
            if ("Talonarios" != a) return;
            e = "talonarios"
        } else {
            if ("Configuracion" != t) return;
            o = "configuracion/", e = a.toLowerCase()
        }
        o += e + "/", $("#" + e).addClass("actual").attr("href", "#" + t + "/" + a + "?" + Math.random().toFixed(2)), $("#extra_content").html("").hide(), $(".right_content").html(c).show().load(o)
    }
    var o = "Inicio";
    $(window).hashchange(function() {
        var e = location.hash.replace(/^#/, "").split("/");
        if (e[0] = e[0].split("?")[0], o == e[0]) {
            if (e[1]) return void a(e[0], e[1])
        } else o = e[0];
        t(e[0], e[1])
    }), $(window).hashchange(), $("#dialog").dialog({
        autoOpen: !1,
        dialogClass: "dialog-shadow",
        modal: !0
    }), $("#cambiar_mes").click(function(t) {
        t.preventDefault(), abrirDialogo("Cambiar Mes", "cambiar_mes.php")
    })
});
var LOGISTICA = {};
LOGISTICA.terceros = {}, LOGISTICA.facturacion = {}, LOGISTICA.logistica = {}, LOGISTICA.configuracion = {}, LOGISTICA.manten = {}, LOGISTICA.support = {}, LOGISTICA.Dialog = function(t) {
    var a = $(t),
        o = {
            modal: !0,
            autoOpen: !1,
            height: "auto",
            width: "auto",
            closeText: "Cerrar"
        },
        e = function() {
            return $dialog = a.dialog(o), this
        };
    this.open = function(t, a, o) {
        var e = {
            title: t,
            position: {
                my: "center",
                at: "center",
                of: window,
                collision: "fit"
            }
        };
        return o ? $dialog.html(a).dialog("open").dialog("option", e) : $dialog.html(c).dialog("open").load(a, function() {
            $dialog.dialog("option", e)
        }), this
    }, this.close = function() {
        return $dialog.dialog("close"), this
    }, e()
}, LOGISTICA.Dialog = new LOGISTICA.Dialog("#dialog"), LOGISTICA.Content = function() {
    var t = 300;
    this.loadExtra = function(a) {
        return $(".right_content").fadeOut(t, function() {
            $("#extra_content").html(c).show().load(a)
        }), this
    }, this.loadMain = function(t, a) {
        return $(".right_content").load(t, a), this
    }, this.returnToMain = function() {
        return $("#extra_content").fadeOut(t, function() {
            $(".right_content").show(), $("#extra_content").html("")
        }), this
    }
}, LOGISTICA.Content = new LOGISTICA.Content;