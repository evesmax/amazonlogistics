
<script type="text/javascript" charset="utf-8">
    $(document).ready(function() 
    {
        $("#listaProveedores,#tipo_doc,#sucursal,#almacen,#usuario,#producto,#unidad_base").select2({'width':'100%'});
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        $('#f_ini,#f_fin').datepicker({
                    format: "yyyy-mm-dd",
                    language: "es"
            });
        $("#resultados").hide();
        Number.prototype.format = function() {
        return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
    };

        $(".caracs").hide();
        $(".clasifs").show();
        
    });
    function generar_reporte()
    {
    //$("#ver_movs:checked").length
       $("#resultados").show();

        $.post('ajax.php?c=reportes_compras&f=prov_prod_reporte', 
        {
            idPrvs: $("#listaProveedores").val(),
            rango: $("#rango:checked").length,
            f_ini: $("#f_ini").val(),
            f_fin: $("#f_fin").val(),
            tipo_doc: $("#tipo_doc").val(),
            sucursal: $("#sucursal").val(),
            almacen: $("#almacen").val(),
            usuario: $("#usuario").val(),
            producto: $("#producto").val(),
            departamento: $("#departamento").val(),
            familia: $("#familia").val(),
            linea: $("#linea").val(),
            unidad_base: $("#unidad_base").val(),
            unidad_base_conversion: $("#unidad_base_conversion").val(),
            status_doc: $("#status_doc").val(),
            moneda: $("#moneda").val(),
            imp2: $("#imp2:checked").length,
            tipo_producto: $("#tipo_producto").val(),
            caracteristica_padre: $("#caracteristica_padre").val(),
            caracteristica_hija1: $("#caracteristica_hija1").val(),
            caracteristica_hija2: $("#caracteristica_hija2").val()
        }, 
        function(data) 
        {

            $("#res_rep").html(data);
           

            var anchor  = '#resultados';
                    $('html, body').stop().animate({
                        scrollTop: jQuery(anchor).offset().top
                    }, 1000);
                    return false;
            
        });         
    }

    function almacenes()
    {
        $.post("ajax.php?c=reportes_compras&f=listaAlmacenes",
        {
            idSuc : $("#sucursal").val()
        },
        function(data)
        {
            $("#almacen").html(data).trigger("change");
        });
    }

    function tipo_prod()
    {
        if(parseInt($("#tipo_producto").val()) == 1)
        {
            $(".caracs").hide();
            $(".clasifs").show();
        }

        if(parseInt($("#tipo_producto").val()) == 2)
        {
            $(".caracs").show();
            $(".clasifs").hide();
        }

    }

    function familias()
    {
        $.post("ajax.php?c=reportes_compras&f=listaFamilias",
        {
            id_departamento : $("#departamento").val()
        },
        function(data)
        {
            $("#familia").html(data).trigger("change");
        });
    }

    function lineas()
    {
        $.post("ajax.php?c=reportes_compras&f=listaLineas",
        {
            id_familia : $("#familia").val()
        },
        function(data)
        {
            $("#linea").html(data).trigger("change");
        });
    }

    function conversion()
    {
        $.post("ajax.php?c=reportes_compras&f=listaMedida",
        {
            id_base : $("#unidad_base").val()
        },
        function(data)
        {
            $("#unidad_base_conversion").html(data).trigger("change");
        });
    }

    function caracs_hija()
    {
        $.post("ajax.php?c=reportes_compras&f=listaCaracteristicasHija",
        {
            id_padre : $("#caracteristica_padre").val()
        },
        function(data)
        {
            $("#caracteristica_hija1").html(data).trigger("change");
            $("#caracteristica_hija2").html(data).trigger("change");
        });
    }
    </script>
    <style>
.row
{
    margin-bottom:20px;
}
.container
{
    margin-top:20px;
}

.linea_prov
{
    background-color:#A4A4A4; 
}
.linea_final
{
    background-color:white; 
}

.linea_fac
{
    background-color:#D8D8D8; 
}
</style>
<div class="container well">
    <div class="row">
        <div class="col-xs-12 col-md-12"><h3>Compras por proveedor y por producto.</h3></div>
    </div>
    <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Fecha Inicial
        </div>
        <div class='col-xs-12 col-md-3'>
            <input type='text' id='f_ini' class='form-control'>
        </div>
         <div class='col-xs-12 col-md-2'>
            Tipo de Documento
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='tipo_doc'>
                <option value='1'>Orden de Compra</option>
                <option value='2'>Recepcion</option>
                <option value='3'>Requisicion</option>
            </select>
        </div>
    </div> 
     <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Fecha Final
        </div>
        <div class='col-xs-12 col-md-3'>
            <input type='text' id='f_fin' class='form-control'>
        </div>
         <div class='col-xs-12 col-md-2'>
            Sucursal
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='sucursal' onchange='almacenes()'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaSucursales->fetch_assoc())
                    echo "<option value='".$l['idSuc']."'>".$l['nombre']."</option>";
                ?>
            </select>
        </div>
    </div> 
    <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Proveedor(es):
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='listaProveedores' multiple>
                <option value='0'>Todos</option>
                <?php
                    while($l = $listaProveedores->fetch_assoc())
                        echo "<option value='".$l['idPrv']."'>(".$l['codigo'].") ".$l['razon_social']."</option>";
                ?>
            </select>
        </div>
        <div class='col-xs-12 col-md-2'>
            Almacen
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='almacen'>
                <option value='0'>Todos</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Es Rango
        </div>
        <div class='col-xs-12 col-md-3'>
            <input type='checkbox' id='rango' value='1'>
        </div>
        <div class='col-xs-12 col-md-2'>
            Usuario
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='usuario'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaUsuarios->fetch_assoc())
                    echo "<option value='".$l['idEmpleado']."'>".$l['Usuario']."</option>";
                ?>
            </select>
        </div>
    </div> 
    <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Producto
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='producto'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaProductos->fetch_assoc())
                    echo "<option value='".$l['id']."'>".$l['Producto']."</option>";
                ?>
            </select>
        </div>
         <div class='col-xs-12 col-md-2'>
            Tipo Producto
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='tipo_producto' class='form-control' onchange='tipo_prod()'>
                <option value='1'>Con Clasificación</option>
                <option value='2'>Con Caracteristicas</option>
            </select>
        </div>
    </div>
    <div class='row clasifs'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Departamento
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='departamento' class='form-control' onchange='familias()'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaDepartamentos->fetch_assoc())
                    echo "<option value='".$l['id']."'>".$l['nombre']."</option>";
                ?>
            </select>
        </div>
    </div>
    <div class='row clasifs'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Familia
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='familia' class='form-control' onchange='lineas()'>
                <option value='0'>Todos</option>
            </select>
        </div>
    </div>
    <div class='row clasifs'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Línea
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='linea' class='form-control'>
                <option value='0'>Todos</option>
            </select>
        </div>
    </div>

    <div class='row caracs'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Caracteristica
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='caracteristica_padre' class='form-control' onchange='caracs_hija()'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaCaracteristicas->fetch_assoc())
                    echo "<option value='".$l['id']."'>".$l['nombre']."</option>";
                ?>
            </select>
        </div>
    </div>

    <div class='row caracs'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Rango Sub-Caracteristicas
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='caracteristica_hija1' class='form-control'>
                <option value='0'>Todos</option>
            </select>
            <select id='caracteristica_hija2' class='form-control'>
                <option value='0'>Todos</option>
            </select>
        </div>
    </div>


    <div class='row'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Unidad Base
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='unidad_base' class='form-control' onchange='conversion()'>
                <option value='0'>Todos</option>
                <?php
                while($l = $listaUnidadesBase->fetch_assoc())
                    echo "<option value='".$l['id']."'>(".$l['clave'].") ".$l['nombre']."</option>";
                ?>
            </select>
        </div>
        <div class='col-xs-12 col-md-2'>
            Conversion
        </div>
         <div class='col-xs-12 col-md-3'>
            <select id='unidad_base_conversion' class='form-control'>
            </select>
        </div>
    </div>

    <div class='row'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Estatus del Documento
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='status_doc' class='form-control'>
            <option value='1'>Autorizados</option>
            <option value='0'>Inactivas</option>
            <option value='2'>Cancelados</option>
            </select>
        </div>
        <div class='col-xs-12 col-md-6'>
                <b>Imprimir</b>
        </div>
        <!--<div class='col-xs-12 col-md-2'>
            <input type='radio' name='imp1' class='imp1' value='0' checked>Todos
        </div>
        <div class='col-xs-12 col-md-2'>
            <input type='radio' name='imp1' class='imp1' value='1'>Sólo con movimientos
        </div>-->
    </div>

    <div class='row'>
        <div class='col-xs-12 col-md-2 col-md-offset-1'>
            Moneda
        </div>
        <div class='col-xs-12 col-md-3'>
            <select id='moneda' class='form-control'>
            <option value='1'>(MXN) Peso Mexicano</option>
            <option value='2'>(USD) Dólar estadounidense</option>
            </select>
        </div>
        <div class='col-xs-12 col-md-2'>
                <input type='radio' id='imp2' name='imp2' class='imp2' value='0' checked>Global
            </div>
            <div class='col-xs-12 col-md-2'>
                <input type='radio' name='imp2' class='imp2' value='1'>Detalle
            </div>
    </div>
   
    <div class="row">
        <div class='col-xs-12 col-md-2 col-md-offset-4'>
        </div>
        <div class='col-xs-12 col-md-3'>
            <button id='generar' onclick="generar_reporte()" class='btn btn-primary'>Generar</button>
        </div>
        <div class='col-xs-12 col-md-3'>
            <button id='generar' onclick="cancelar_reporte()" class='btn btn-danger'>Cancelar</button>
        </div>
    </div>    
</div>

<div class="container well" id='resultados'>
    <div class="row">
        <div class="col-xs-12 col-md-12 table-responsive">
            <table class="table table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr><th>Caracteristicas</th><th>Fecha</th><th>Docto</th><th>Cantidad</th><th>Unidad</th><th>$ Unitario</th><th>Importe</th><th>Impuestos</th><th>Total</th></tr>
                </thead>
                <tbody id='res_rep'>
                                    
                </tbody>
            </table>
        </div>
    </div>
</div>

<script language='javascript' src='js/bootstrap-datepicker.min.js'></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap-datepicker.min.css">
<script src="../../libraries/select2/dist/js/select2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

