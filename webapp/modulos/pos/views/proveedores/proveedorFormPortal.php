<?php

$fi=(new DateTime('first day of this month'))->format('Y-m-d');
$ff=(new DateTime('last day of this month'))->format('Y-m-d');

//echo json_encode($datosCliente);
function randpass() {
    $alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
 ?>
<style>
    .cursor {
        cursor: pointer
    }
</style>
<!-- <?php echo $datosProveedor['basicos'][0]['idtipo']; ?> -->

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Formulario de Proveedores</title>
        <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
        <script src="../../libraries/jquery.min.js"></script>
        <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../../libraries/numeric.js"></script>
        <script src="js/proveedores.js"></script>
        <script src="../../libraries/numeric.js"></script>
<!--Select 2 -->
        <script src="../../libraries/select2/dist/js/select2.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
<!-- Optional theme -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
        <script src="jquery-1.3.2.min.js" type="text/javascript"></script>  

            <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RCA -->
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="../../libraries/dataTable/css/buttons.dataTables.min.css">
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>

    <script language='javascript' src='../../libraries/datepicker/js/bootstrap-datepicker.min.js'></script>
    <script language='javascript' src='../../libraries/datepicker/js/bootstrap-datepicker.es.js'></script>
    

        <script>
                var exporta = (function() {
    var uri = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="1">{table}</table></body></html>', 
    base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }, 
    format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; })}

    return function() {

        var html='<table border="1">';
        html+=$('#vamosaver').html();
        var ctx = {worksheet: 'Hoja' || 'Worksheet', table: html}
        window.location.href = uri + base64(format(template, ctx))
    

        //if (!table.nodeType) table = document.getElementById(table)
        //var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
        //window.location.href = uri + base64(format(template, ctx))
    }
})()

            $(document).ready(function() {

        $('#inDate').datepicker({
            format: "yyyy-mm-dd"
        });

    var rfc = $('#rfc').val();
   
    if(rfc == 'XAXX010101000' || rfc == ''){
    
        $('#divxmls').hide();
        $('#divxmls2').hide();
        $('#DivExtranjero').show();
    
    }else{

        $('#divxmls').show();
        $('#divxmls2').show();
        $('#DivExtranjero').hide();
    }
 $('#analisisOrdenes').DataTable({
             autowidth: 'false',
             dom: 'Bfrtip',
             buttons: [ 'excel' ],
                             language: {
                             search: "Buscar:",
                             lengthMenu:"",
                             zeroRecords: "No hay datos.",
                             infoEmpty: "No hay datos que mostrar.",
                             info:"Mostrando del _START_ al _END_ de _TOTAL_ facturas",
                             paginate: {
                                 first:      "Primero",
                                 previous:   "Anterior",
                                 next:       "Siguiente",
                                 last:       "Último"
                             },
                          },
                           aaSorting : [[0,'desc' ]]
         });



    $('#fac').submit( function( e ) {
    console.log(this);

    //$('#verif').css('display','inline');
    $.ajax( {
      url: 'subexml.php',
      type: 'POST',
      data: new FormData(this),
      processData: false,
      contentType: false
    } ).done(function( data1 ) {
        console.log(data1)


            data1 = data1.split('-/-*');
            console.log(data1);

            $('#verif').css('display','none');

            if(parseInt(data1[0]))
            {   
                if(data1[0]==1450){
                    alert('El archivo no es valido, no pudes subir complementos de pagos.');
                    return false;
                }
                if(data1[0]==1451){
                    alert('La fecha de la factura es invalida, esta fuera de rango.');
                    return false;
                }
                if(data1[0]==1452){
                    alert('La fecha de la factura es invalida, no es del mes en cuerso.');
                    return false;
                }
                if(parseInt(data1[3]))
                {
                    alert('Los siguientes '+data1[3]+' archivos no son validos: \n'+data1[4])

                }

                if(parseInt(data1[1]))
                {


                    datosfac = data1[7].split('##');
                    fac_folio=datosfac[0];
                    fac_fecha=datosfac[1];
                    fac_total=datosfac[2];
                    fac_uuid=datosfac[3];
                    fac_desc_concepto=datosfac[4];
                    fac_subtotal=datosfac[5];
                    xmlfile=data1[6];
                    idoc = $('#pediid').val();//OC

                      var oTable = $('#tablePartidas').dataTable();
                      var allPages = oTable.fnGetNodes();

                      cadena='';
                      $('input:checked', allPages).each(function(){
                              cadena+=$(this,allPages).val()+',';
                          });
                      //alert(cadena);

                    $.ajax({
                        url:"ajax.php?c=portalproveedores&f=a_guardaXmlAdju",
                        type: 'POST',
                        data:{fac_folio:fac_folio,fac_fecha:fac_fecha,fac_total:fac_total,fac_uuid:fac_uuid,concepto:fac_desc_concepto,xmlfile:xmlfile,idoc:idoc,fac_subtotal:fac_subtotal,cadena:cadena},
                        success: function(r){
                            if(r>0){
                                $('#adju_recep').html('Cargando...');
                                $('#adju_xmls').html('Cargando...');
                                $('#modal-adju').modal('hide');
                                alert('XML adjuntado con exito');
                            }
                        }
                    });

                    
                }
                //alert(parseInt(data1[5]))
                if(parseInt(data1[5])){
                    abrefacturasrepetidas();
                    
                }else{
                  //  location.reload();
                }
            }
            else
            {
                alert("El archivo zip no cumple con el formato correcto\nDebe llamarse igual que la carpeta que contiene los xmls.\nSólo debe contener una carpeta.");

            }
        
    
    });
    e.preventDefault();
  });

//////Sube PDF extranjeros

    $('#pdfUp').submit( function( e ) {
    console.log(this);
    //alert('dded');
    var folio = $('#inNumber').val();
    var monto = $('#inAmmount').val();
    var fecha = $('#inDate').val();
    var moneda = $('#inCurrency').val();
    var oc = $('#pediid').val();
    if(folio==''){
        alert('Agrega un folio');
        return false;
    }
    if(monto==''){
        alert('Agrega un monto');
        return false;
    }
    if(fecha==''){
        alert('Agrega un fecha');
        return false;
    }
    //$('#verif').css('display','inline');
    $.ajax( {
      url: 'subepdf.php',
      type: 'POST',
      data: new FormData(this),
      processData: false,
      contentType: false,
      dataType: 'json',
    } ).done(function( data1 ) {
        console.log(data1)
        //alert(data1.nombre);

      var oTable = $('#tablePartidas').dataTable();
      var allPages = oTable.fnGetNodes();

      cadena='';
      $('input:checked', allPages).each(function(){
              cadena+=$(this,allPages).val()+',';
          });
      //alert(cadena);



        $.ajax({
            url: 'ajax.php?c=portalproveedores&f=guardaPdf',
            type: 'POST',
            dataType: 'json',
            data: {folio : folio,
                    monto : monto,
                    fecha : fecha,
                    moneda : moneda,
                    cadena : cadena,
                    pdfname : data1.nombre,
                    idOc : oc
                    },
        })
        .done(function(reso) {
            console.log(reso);
            if(reso.estatus==true){
                alert('Exito!!!');
                window.location.reload();
            }
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
        
    
    });
    e.preventDefault();
  });




/////







                Number.prototype.format = function() {
                    return this.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
                };
          // $('#numeros').numeric();
          // $('#tipoClas').select2({'width':'100%'});
          // $('#tipoDeCredito').select2({'width':'100%'});
          // $('#moneda').select2({'width':'100%'});
          // $('#banco').select2({'width':'100%'});
          // $('#vendedor').select2({'width':'100%'});
          // $('#cuentaCont').select2({'width':'100%'});
          // $(".numeros").numeric();

        //   $('#tabla-carfac').DataTable({
        //     autowidth: 'false',
        //     dom: 'Bfrtip',
        //     buttons: [ 'excel' ],
        //                     language: {
        //                     search: "Buscar:",
        //                     lengthMenu:"",
        //                     zeroRecords: "No hay datos.",
        //                     infoEmpty: "No hay datos que mostrar.",
        //                     info:"Mostrando del _START_ al _END_ de _TOTAL_ facturas",
        //                     paginate: {
        //                         first:      "Primero",
        //                         previous:   "Anterior",
        //                         next:       "Siguiente",
        //                         last:       "Último"
        //                     },
        //                  },
        //                   aaSorting : [[0,'desc' ]]
        // });
            

        $('#f_ini,#f_fin').datepicker({
                    format: "yyyy-mm-dd",
                    language: "es"
            });

        $('#f_fin').change(function(){
             if( (new Date(  $('#f_ini').val() ).getTime() > new Date( $('#f_fin').val() ).getTime())){
                  alert('La fecha final no puede ser menor a la fecha de inicio');
                  $('#f_fin').val('');
             }
        });

        $('#f_ini').change(function(){
             if( (new Date(  $('#f_ini').val() ).getTime() > new Date( $('#f_fin').val() ).getTime())){
                  alert('La fecha inicial no puede ser mayor a la fecha de fin');
                  $('#f_ini').val('');
             }
        });


          listaCargosFacturas();
          generar_reporte();
          listareq();
        });


function check_file()
    {
        var ext = $('#factura').val();
        ext = ext.split('.');
        ext = ext[1];
        if(ext != 'xml')
        {
            alert("Archivo Inválido. El archivo debe tener una extensión xml.");
            $("#factura").val('');
        }
    }

function verXml(id){
    var rfc = $('#rfc').val();
   
    if(rfc == 'XAXX010101000' || rfc == ''){
        window.open("../../modulos/facturas/portal/"+id);
    }else{
        window.open("../../modulos/cont/xmls/facturas/temporales/"+id+".xml");
    }
    
}
            

    function adjuntarxml(idoc){
                  $('#pediid').val(idoc);

        deten=0;
        $.ajax({
            async:false,
            url:"ajax.php?c=portalproveedores&f=a_verificarPagos",
            type: 'POST',
            dataType: 'json',
            data:{idoc:idoc},
            success: function(r){
                if(r>0){
                    alert('Esta orden de compra ya tiene pagos realizados, no puedes subir facturas.');
                    deten=1;
                }
            }
        });
        if(deten==1){
            return false;
        }
        $('#modal-adju').modal({
            backdrop: 'static',
            keyboard: false, 
            show: true
        });
        $('#idocadju').remove();
        $('body').append('<input id="idocadju" type="hidden" value="'+idoc+'">');

        $.ajax({
            url: 'ajax.php?c=portalproveedores&f=partidaOrden',
            type: 'POST',
            dataType: 'json',
            data: {idOc: idoc},
        })
        .done(function(respues) {
            console.log(respues);
            var table = $('#tablePartidas').DataTable();
            table.clear().draw();
            var xmlButton = '';
            $.each(respues.partidas, function(index, val) {

                if(val.estatus<21){
                    estatus = '<span class="label label-default">EN FIRME</span>';
                }else if(val.estatus>49 && val.estatus < 76){
                    estatus = '<span class="label label-primary">RECIBIDA</span>';
                }else if(val.estatus == 99){
                    estatus = '<span class="label label-danger">CANCELADA</span>';
                }else if(val.estatus == 100){
                    estatus = '<span class="label label-success">PAGADA</span>';
                }else{
                    estatus = '<span class="label label-info">PROGRAMADA PARA PAGO</span>';
                }
                if(val.xmlfile!=null){
                    xmlButton = '<!--<a class="btn btn-default" alt="Visualizar PDF" title="Visualizar PDF" onclick="verPdf();"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>--><a class="btn btn-default" alt="Visualizar XML" title="Visualizar XML" onclick="verXml(\''+val.xmlfile+'\');"><i class="fa fa-file-code-o" aria-hidden="true"></i></a>';
                }else{
                    xmlButton = '';
                }
                if(val.noreceptor==null){
                    val.noreceptor  = '';
                }
                x ='<tr class="filas">'+
                                '<td>'+val.codigo+'</td>'+
                                '<td>'+val.descripcion+'</td>'+
                                '<td>$'+parseFloat(val.monto).toFixed(2)+'</td>'+
                                '<td>'+val.noreceptor+'</td>'+
                                '<td>'+estatus+'</td>'+
                                '<td>'+xmlButton+'</td>'+
                                '<td><input class="checkPro" name="partidas" value="'+val.id+'" id="check_'+val.codigo+'" onclick="aaa();" type="checkbox"></td>';
                                '</tr>';
                    table.row.add($(x)).draw();
            });

        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });


        $.ajax({
            url:"ajax.php?c=portalproveedores&f=a_adjuntarxml",
            type: 'POST',
            dataType: 'json',
            data:{idoc:idoc},
            success: function(r){
                console.log(r);
                tabla='<table>\
                        <tr>\
                        <th width="420">No Recepcion</th>\
                        <th width="160">Fecha recepcion</th>\
                        <th width="100">Monto</th>\
                        </tr>';
                trecep='';
                $.each(r.rows, function(i,v) {
                    tabla+='<tr>\
                        <td style="padding: 2px;">ID Recepcion - '+v.idr+'</td>\
                        <td style="padding: 2px;">'+v.fechar+'</td>\
                        <td style="padding: 2px;">$'+v.total+'</td>\
                        </tr>';
                });
                tabla+='<tr>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;"><b>$'+r.total+'</b></td>\
                        </tr>';
                tabla+='</table>';

                tabla2='<table id="tablaxmladju">\
                        <tr>\
                        <th width="420">Xml/PDF archivo</th>\
                        <th width="160">Fecha subida</th>\
                        <th width="100">Monto</th>\
                        <th width="100">Folio</th>\
                        <th width="100">Moneda</th>\
                        </tr>';


                $.each(r.xmls, function(i,v) {
                    if(v.folio==null){
                        v.folio = '';
                    }
                    if(v.moneda==null){
                        v.moneda = '';
                    }


                    tabla2+='<tr>\
                        <td style="padding: 2px;">'+v.xmlfile+'</td>\
                        <td style="padding: 2px;">'+v.fecha_subida+'</td>\
                        <td style="padding: 2px;">$'+v.imp_factura+'</td>\
                        <td style="padding: 2px;">'+v.folio+'</td>\
                        <td style="padding: 2px;">'+v.moneda+'</td>\
                        </tr>';
                });
                tabla2+='<tr>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;">&nbsp;</td>\
                        <td style="padding: 2px;"><b>$'+r.totalxmls+'</b></td>\
                        </tr>';

                $('#adju_header').html('Orden de compra <b>'+idoc+'</b>');
                $('#adju_recep').html(tabla);
                if(r.xmls==0){
                    $('#adju_xmls').html('No hay facturas adjuntas');
                }else{
                    $('#adju_xmls').html(tabla2);
                }
                
            }
        });
    }


    function listareq(){

        idProveedor=$('#idProveedor').val();
       
            var table = $('#example').DataTable();
            table.destroy();
            $('#example').DataTable( {
            dom: 'Bfrtip',
            buttons: [ 'pageLength', 'excel'],
            language: {
                buttons:{
                            pageLength: "Mostrar %d filas"
                        },
                search: "Buscar:",
                lengthMenu:"Mostrar _MENU_ elementos",
                zeroRecords:"No hay datos",
                infoEmpty:"",
                info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                paginate: {
                    first:      "Primero",
                    previous:   "Anterior",
                    next:       "Siguiente",
                    last:       "Último"
                },
             },
             
                "aaSorting": [[0,'desc']],
                ajax: {
                    beforeSend: function() {  }, //Show spinner
                    complete: function() { $('#listareq_load').css('display','none'); }, //Hide spinner
                    url:"ajax.php?c=portalproveedores&f=a_listaOrdenesRecepcion&id="+idProveedor,
                    type: "POST",
                    data: function ( d )    {
                        //d.site = $("#nombredeusuario").val();
                    }  
                }
            });
            $('#listarec').css('display','none');
            $('#listareq').css('display','block');
        
        
    }

            function modificarPassPortal(){
                var cliente = $('#idProveedor').val();
                $('#btnenviarCorreo').prop('disabled',true); 
                $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-click') );


                passportal2=$('#passportal2').val();
                passportal3=$('#passportal3').val();


                if(passportal2=='' || passportal3==''){
                    alert('Los campos no pueden estar vacios.');
                    $('#btnenviarCorreo').prop('disabled',false); 
                    $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );
                    return false
                }

                if(passportal2!=passportal3){
                    alert('Las contraseñas no coinciden.');
                    $('#btnenviarCorreo').prop('disabled',false); 
                    $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );
                    return false
                }

                $.ajax({
                url:"ajax.php?c=cliente&f=modificarPortal",
                type: 'POST',
                data:{passportal2:passportal2,cliente:cliente,passportal3:passportal3},
                success: function(data){

                    alert('Contraseña modificada correctamente');
                    
                    $('#btnenviarCorreo').prop('disabled',false); 
                    $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );

                    location.reload(); 

                }
              });

            }

        function generar_reporte(){

       //graficar ('','','');
    //$("#ver_movs:checked").length
        $("#recio").css('display','none');
       $("#carga").css('display','block');

       if( $("#f_ini").val()=='' || $("#f_fin").val()==''  ){
        alert('Seleccione un rango de fechas');
        return false;
       }
       

       idPrvCli = $("#idProveedor").val();

        $.post('ajax.php?c=portalproveedores&f=prov_prod_reportem', 
        {
            idPrvs: idPrvCli,
            rango: '',
            f_ini: $("#f_ini").val(),
            f_fin: $("#f_fin").val(),
            tipo_doc: 1,
            sucursal: '',
            almacen: '',
            usuario: '-Seleccion un Usuario-',
            producto: '',
            departamento: 0,
            familia: 0,
            linea: 0,
            unidad_base: '- Todas -',
            unidad_base_conversion: '',
            status_doc: '- Todas -',
            moneda: 0,
            imp2: $("#imp2:checked").length,
            tipo_producto: '',
            caracteristica_padre: 0,
            caracteristica_hija1: 0,
            caracteristica_hija2: 0
        }, 
        function(data) 
        {

            $("#res_rep").html(data);

            $("#carga").css('display','none');
            $("#recio").css('display','block');
            
           

            var anchor  = '#resultados';
                    $('html, body').stop().animate({
                        scrollTop: jQuery(anchor).offset().top
                    }, 1000);
                    return false;
            
        });         
    }


    function listaCargosFacturas(){

        idPrvCli = $("#idProveedor").val();

    $.post('ajax.php?c=portalproveedores&f=listaCargosFacturas',
        {
            idPrvCli: idPrvCli,
            cobrar_pagar: $("#cobrar_pagar").val()
        },
        function(data)
        {
            //console.log(data)
           var datos = jQuery.parseJSON(data);
                $('#tabla-carfac').DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    language: {
                        search: "Buscar:",
                        lengthMenu:"Mostrar _MENU_ elementos",
                        zeroRecords: "No hay coincidencias.",
                        infoEmpty: "No hay coincidencias que mostrar.",
                        infoFiltered: "",
                        info:"Mostrando del _START_ al _END_ de _TOTAL_ cuentas",
                        paginate: {
                            first:      "Primero",
                            previous:   "Anterior",
                            next:       "Siguiente",
                            last:       "Último"
                        }
                     },
                     "order": [[ 0, "asc" ]],
                     data:datos,
                     columns: [
                        { data: 'fech_cargo' },
                        { data: 'fecha_venc' },
                        { data: 'concepto' },
                        { data: 'folio' },
                        { data: 'moneda' },
                        { data: 'monto' },
                        { data: 'abonado' },
                        { data: 'actual' },
                        { data: 'estatus' },
                        { data: 'ov' }
                    ]
                });
                var saldo = 0;
                
                for(i=0;i<=datos.length-1;i++)
                    saldo += parseFloat(datos[i].actual_im);
                
                $("#total_saldos").val("$ "+saldo.format())
                $("#tabla-carfac").before($("#saldos_div2"));
        });
}

            function listaCargosFacturax(){
    var idPrvCli = $('#idProveedor').val();

    $.post('ajax.php?c=portalclientes&f=listaCargosFacturas',
        {
            idPrvCli: idPrvCli,
            cobrar_pagar: $("#cobrar_pagar").val()
        },
        function(data)
        {


           var datos = jQuery.parseJSON(data);

                $('#tabla-carfac').DataTable( {
                    dom: 'Bfrtip',
                    buttons: ['excel'],
                    language: {
                        search: "Buscar:",
                        lengthMenu:"Mostrar _MENU_ elementos",
                        zeroRecords: "No hay coincidencias.",
                        infoEmpty: "No hay coincidencias que mostrar.",
                        infoFiltered: "",
                        info:"Mostrando del _START_ al _END_ de _TOTAL_ cuentas",
                        paginate: {
                            first:      "Primero",
                            previous:   "Anterior",
                            next:       "Siguiente",
                            last:       "Último"
                        }
                     },
                     "order": [[ 0, "asc" ]],
                     data:datos,
                     columns: [
                        { data: 'fech_cargo' },
                        { data: 'fecha_venc' },
                        { data: 'concepto' },
                        { data: 'monto' },
                        { data: 'abonado' },
                        { data: 'actual' },
                        { data: 'estatus' },
                        { data: 'ov' }
                    ]
                });
                var saldo = 0;
                $('.actual').each(function()
                {
                    saldo+=parseFloat($(this).attr('cantidad'))
                })
                $("#total_saldos").val("$ "+saldo.format())
                $("#tabla-carfac").before($("#saldos_div2"));
        });
}

            function guardaProveedorPortal(){


    //Datos Obligatorios



    // var codigo =  $('#codigo').val();
    // var nombre =  $('#nombre').val();
    // var pais =  $('#selectPais').val();
    // if( nombre == "" || pais == ""){
    //     alert("Verifica haber llenado todos los comapos oblicatorios (*)");
    //     return;
    // }


    var idProveedor =  $('#idProveedor').val();
    var calle =$('#calle').val();
    var no_ext =$('#no_ext').val();
    var no_int =$('#no_int').val();
    var cp =$('#cp').val();
    var colonia =$('#colonia').val();
    var selectPais =$('#selectPais').val();
    var selectEstado =$('#selectEstado').val();
    var selectMunicipio =$('#selectMunicipio').val();
    var ciudad =$('#ciudad').val();
    var nombre_contacto =$('#nombre_contacto').val();
    var email =$('#email').val();
    var telefono =$('#telefono').val();
    var web =$('#web').val();


    // var cumpleanos = $('#cumpleanos').val();
    /// Datos de Facturacion
    // var idComunFact = $('#idComunFact').val();
    // var rfc =  $('#rfc').val();
    // var curp =  $('#curp').val();
    // var razonSocial = $('#razonSocial').val();
    // var emailFacturacion = $('#emailFacturacion').val();
    // var direccionFact = $('#direccionFact').val();
    // var numextFact = $('#numextFact').val();
    // var numintFact = $('#numintFact').val();
    // var coloniaFact = $('#coloniaFact').val();
    // var cpFact = $('#cpFact').val();
    // var paisFact2 = $('#paisFact2').val();
    // var estadoFact = $('#estadoFact').val();
    // var municipiosFact = $('#municipiosFact').val();
    // var ciudadFact = $('#ciudadFact').val();
    // var paisFact = $('#paisFact').val();
    // var regimenFact = $('#regimenFact').val();


    // var vacios = false;
    // var llenos = false;

    // Expresion regular para validar el correo
    var regex = /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/;
    // VALIDACIONES
    // rfc
    // if(rfc != ''){
    //  if(isValidRfc(rfc) == false){ alert('RFC no valido!!'); $("#rfc").focus(); return 0; }
    // }
    // email
    if(email != ''){
        if (!regex.test(email.trim())) {
            alert('Email de facturación no valido!!'); $("#email").focus(); return 0;
        }
        //if(isValidEmail(email) == false){ alert('Email Basico no valido!!'); $("#emailemail").focus(); return 0; }
    }
    // if(emailFacturacion != ''){
    //  if (!regex.test(emailFacturacion.trim())) {
    //      alert('Email de facturación no valido!!'); $("#emailFacturacion").focus(); return 0;
    //  }
    //  //if(isValidEmail(emailFacturacion) == false){ alert('Email de facturación no valido!!'); $("#emailFacturacion").focus(); return 0; }
    // }

    // TODOS O NINGUNO
    // if(razonSocial == '' && rfc == '' && emailFacturacion == '' && direccionFact == '' && numextFact == '' && coloniaFact == '' && cpFact == '' && estadoFact == '0' && municipiosFact == '0' && ciudadFact == '' && paisFact2 == '0'){
    //  //TODOS VACIOS
    //  vacios = true;
    // }

    // if(razonSocial != '' && rfc != '' && emailFacturacion != '' && direccionFact != '' && numextFact != '' && coloniaFact != '' && cpFact != '' && estadoFact != '0' && municipiosFact != '0' && ciudadFact != '' && paisFact2 != '0' && vacios == false) {
    //  llenos = true;
    // }

    // if (vacios == false && llenos == false ) {
    //  alert('Todos los datos de Facturación son requeridos');
    //  return 0;
    // }

    // //Datos Credito
    // var tipoDeCredito = $('#tipoDeCredito').val();
    // var diasCredito =  $('#diasCredito').val();
    // var limiteCredito =  $('#limiteCredito').val();
    // var moneda =  $('#moneda').val();
    // var listaPrecio =  $('#listaPrecio').val();
    // var descuentoPP = $('#descuentoPP').val();
    // var interesesMoratorios = $('#interesesMoratorios').val();
    //    if($('#checkVc').is(':checked')){
    //      perVenCre = 1
    //    }else{
    //      perVenCre = 0;
    //    }
    //    if($('#checkLc').is(':checked')){
    //      perExLim = 1
    //    }else{
    //      perExLim = 0;
    //    }
    // var banco = $('#banco').val();
    // var numCuenta = $('#cuentaBanc').val();
    // //Datos Comision
    // var comisionVenta = $('#comisionVenta').val();
    // var comisionCobranza =  $('#comisionCobranza').val();
    // var empleado = $('#vendedor').val();
    // //Datos de Envio
    // var enviosDom = $('#enviosDom').val();

    // var tipoClas = $('#tipoClas').val();
    // var cuentaCont = $('#cuentaCont').val();

    // if(codigo==''){
    //  alert('No puedes dejar el codigo vacio.');
    //  return false;
    // }
    // if(nombre==''){
    //  alert('No puedes dejar el Nombre vacio.');
    //  return false;
    // }

    //alert('guardado');
    //return 0;

    $.ajax({
        url: 'ajax.php?c=portalproveedores&f=guardaProveedor',
        type: 'POST',
        dataType: 'json',
        data: {idProveedor: idProveedor,
                // codigo : codigo,
                calle : calle,
                no_ext : no_ext,
                no_int : no_int,
                cp : cp,
                colonia: colonia,
                selectPais : selectPais,
                selectEstado : selectEstado,
                selectMunicipio : selectMunicipio,
                ciudad : ciudad,
                nombre_contacto: nombre_contacto,
                email : email,
                telefono : telefono,
                web : web

                // rfc : rfc,
                // curp : curp,
                // diasCredito : diasCredito,
                // limiteCredito: limiteCredito,
                // moneda : moneda,
                // listaPrecio : listaPrecio,
                // razonSocial : razonSocial,
                // emailFacturacion : emailFacturacion,
                // direccionFact : direccionFact,
                // numextFact : numextFact,
                // numintFact : numintFact,
                // coloniaFact : coloniaFact,
                // cpFact : cpFact,
                // paisFact : paisFact2,
                // estadoFact : estadoFact,
                // municipiosFact : municipiosFact,
                // ciudadFact : ciudadFact,
                // tipoDeCredito : tipoDeCredito,
                // descuentoPP : descuentoPP,
                // interesesMoratorios : interesesMoratorios,
                // perVenCre : perVenCre,
                // perExLim : perExLim,
                // comisionVenta : comisionVenta,
                // comisionCobranza : comisionCobranza,
                // empleado : empleado,
                // enviosDom : enviosDom,
                // tipoClas : tipoClas,
                // idComunFact : idComunFact,
                // regimenFact : regimenFact,
                // banco : banco,
                // numCuenta : numCuenta,
                // cuentaCont : cuentaCont,
 
                // cumpleanos : cumpleanos
            },
    })
    .done(function(data) {
        console.log(data);
        if(data.idClienteInser!=''){
            $('#modalSuccess').modal({
                show:true,
            });
        }else{
            alert('Algo Paso');
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

}
        function enviarCorreoPortal(){
            $('#btnenviarCorreo').prop('disabled',true); 
            $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-click') );

            correoportal=$('#correoportal').val();
            userportal=$('#userportal').val();
            passportal=$('#passportal').val();
            nombre=$('#razon_social').val();

            if(correoportal=='' || userportal=='' || passportal==''){
                alert('Los campos no pueden estar vacios.');
                $('#btnenviarCorreo').prop('disabled',false); 
                $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );
                return false
            }

            $.ajax({
            url:"ajax.php?c=proveedores&f=correoPortal",
            type: 'POST',
            data:{correoportal:correoportal,userportal:userportal,passportal:passportal,nombre:nombre},
            success: function(data){
                if(data==1){
                    alert('Correo enviado al proveedor');
                }else{
                    alert('Error en el proceso de envio');
                }
                $('#btnenviarCorreo').prop('disabled',false); 
                $('#btnenviarCorreo').text( $('#btnenviarCorreo').attr('txt-original') );

            }
          });
        }
            $(document).ready(function() {
                var y = $('#cuentaCont').val();
                if (y==0) {
                    $('#divBP').hide();
                } else {
                    $('#divBP').show();
                }

                var x = $('#cmbDatosF').val();
                if (x==0) {
                    $("#divDF").hide();
                } else {
                    $("#divDF").show();
                    $("#ivasumir16").prop('checked', 'checked');
                    $('#otra1, #otra2').val('0.00').hide();
                    tasa();         
                }
                if($('#idProveedor').val()==''){
                    $("#divDF").hide();
                    $("#cmbDatosF").val('0')
                }

                $('#numeros').numeric();
                $('#tipoClas').select2({'width':'100%', 'heigth':'160%'});
                $('#pais').select2({'width':'100%'});
                $('#estado').select2({'width':'100%'});
                $('#municipios').select2({'width':'100%'});
                $('#paisR').select2({'width':'100%'});
                $('#cuenta,#cuentaCliente,#cuentas_gastos').select2({'width':'100%'});
                $('#nacionalidad').select2({'width':'100%'});
                $('#cuentaCliente').select2({'width':'100%'});
                $('#tipoTercero').select2({'width':'100%'});
                $('#tipoTerceroOperacion').select2({'width':'100%'});
                $('#idtipoiva').select2({'width':'100%'});
                $('#selectBanco').select2({'width':'100%'});
                $(".numeros").numeric();

                $("span[aria-labelledby='select2-i1690-container']").hide();
                $('input:checkbox').click(
                    function() {
                        $('input:radio[value="'+($(this).val())+'"]').prop('disabled',true); //habilita radio
                        ////////////////////////////////////////////////////////////////////////
                        if($(this).val()==1234) {
                            $('#otra1').val('0.00');
                            $('#otra1').hide();
                        }

                        if ($(this).val()==12345) {
                            $('#otra2').val('0.00');
                            $('#otra2').hide();
                            $('input:checkbox[value="'+($(this).val())+'"]:checked').each(
                                function() { //desabilita radio
                                    $('input:radio[value="'+($(this).val())+'"]').prop('disabled',false);
                                    //////////////////////OTRO IVA///////////////////////////////////////////////////////////
                                    if(($(this).val())==1234) { //text otra1
                                        $('#otra1').show();
                                    }

                                    if (($(this).val())==12345) {
                                        $('#otra2').show();
                                    }
                                    //////////////////////////////////////////////////////
                                }
                            );

                            if($('input:radio[value="'+($(this).val())+'"]').is(':checked')) {
                                $('input:radio[value="1"]').prop('checked',true);
                                if($(this).val()==1) {
                                    if($('input:radio[value="1"]').is(':checked')) {
                                        $('input:radio[value="1"]').prop('checked',false);
                                    }
                                }
                            }
                        } //del click en check
                    }
                );
            });
        </script>
    </head>

    <body>
        <div> <!--<?php echo json_encode($tasas); ?>--> </div>
        <div class="container-fluid well">
            <div class="row">
                
                <div class="col-sm-1">
                    <button onclick="guardaProveedorPortal();" type="button" class="btn btn-primary">
                        <span class="glyphicon glyphicon-floppy-disk"></span> Guardar
                    </button>
                </div>
                <div class="col-sm-1">
                    <?php 
                        if($idProveedor!=''){
                            echo '<span class="label label-warning">Editando</span>';
                        }else{
                            echo '<span class="label label-success">Nuevo</span>';
                        }
                    ?>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"><h4>Proveedor<?php if(isset($datosProveedor)){echo ' ('.$datosProveedor['basicos'][0]['razon_social'].')';}?> </h4> </div>
                <div class="panel-body">
                    <div style="heigth:400px;overflow:auto;">
                        <div id="tabsCliente">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#basicos">Datos generales</a></li>
                                <li><a data-toggle="tab" href="#divcompras">Compras</a></li>
                                <li><a data-toggle="tab" href="#divsaldos">Saldos</a></li>
                                <li><a data-toggle="tab" href="#divreportes">Reportes</a></li>
                                <li><a data-toggle="tab" href="#accesoPortal">Datos de acceso</a></li>
                                <li><a data-toggle="tab" href="#analisis">Análisis de factura</a></li>
                                <!-- <li><a data-toggle="tab" href="#direccionContactos">Directorio de contactos</a></li>
                                <li><a data-toggle="tab" href="#credito">Crédito</a></li>
                                <li><a data-toggle="tab" href="#datosFiscales">Datos fiscales</a></li>
                                <li><a data-toggle="tab" href="#bancoProvedores">Banco de proveedores</a></li>
                                <li><a data-toggle="tab" href="#accesoPortal">Acceso al portal</a></li>
                                <li><a data-toggle="tab" href="#datosFacturacion">Datos de Facturacion</a></li> -->
                            </ul>
                        </div>

                        <div class="tab-content" style="height:350px; width: 95%">
<!-- D A T O S   B A S I C O S  -->
                            <div id="basicos" class="tab-pane fade in active">
                                <div class="row"> <br>
                                    <div class="col-sm-1">
                                        <label class="control-label" for="email">ID</label>
                                        <input id="idProveedor" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idPrv'];}?>" readonly placeholder="(Autonumérico)">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label"> <font color="red">*</font> RFC</label>
                                        <input readonly="readonly" id="rfc" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['rfc'];}?>">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label"> <font color="red">*</font> Razón Social</label>
                                        <input readonly="readonly" id="razon_social" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['razon_social'];}?>">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label">Nombre Comercial </label>
                                        <input readonly="readonly" id="nombre_comercial" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['nombre_comercial'];}?>">
                                    </div>
                                    <!-- <div class="col-sm-2">
                                        <label class="control-label"> <font color="red">*</font> Código</label>
                                        <input disabled="disabled" id="codigo" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['codigo'];}?>">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Clasificador del Proveedor</label>
                                        <select id="tipoClas" type="text" class="form-control">
                                            <?php
                                                foreach ($clasificadores as $keyClas => $valueClas) {
                                                    if(isset($datosProveedor)){
                                                        if($datosProveedor['basicos'][0]['clasificacion']==$valueClas['id']){
                                                            echo '<option value="'.$valueClas['id'].'" selected>'.$valueClas['nombre'].'/'.$valueClas['clave'].'</option>';
                                                        }
                                                    }
                                                    echo '<option value="'.$valueClas['id'].'">'.$valueClas['nombre'].'/'.$valueClas['clave'].'</option>';
                                                }
                                            ?>
                                        </select>
                                    </div> -->
                                </div> <br>

                                <div class="row">
                                    
                                </div> <br>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label">Dirección</label>
                                        <input id="calle" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['calle'];}?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label">Exterior</label>
                                        <input id="no_ext" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['no_ext'];}?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label">Interior</label>
                                        <input id="no_int" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['no_int'];}?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label">Código Postal</label>
                                        <input id="cp" class="form-control numeros" type="text" value="<?php
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['cp'];}?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label">Colonia</label>
                                        <input id="colonia" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['colonia'];}?>">
                                    </div>
                                </div> <br>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="col-sm-12" style="padding: 0px;">
                                            <label class="control-label"> <font color="red">*</font> País</label>
                                            <select id="selectPais"  style="width: 20px;">
                                                <option value="<?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idpais'];} ?>">
                                                    <?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['descPais'];} ?>
                                                </option>
                                            </select>
                                        </div>
                                        <!--<div class="col-sm-1" style="padding: 2px; margin-top: 25px;">                                          
                                            <button type="button" data-toggle="modal" data-target="#nuevoPais" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button>
                                        </div> -->                                                                         
                                    </div>
<!-- M O D A L   P A R A   A G R E G A R   U N   N U E V O   P A Í S -->
                                    <div class="modal fade" id="nuevoPais" tabindex="-1" role="dialog" aria-labelledby="nuevoPais" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" >Agregar nuevo País</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="text" id="inputNuevoPais" class="form-control" placeholder="Nombre de país">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoPais">Aceptar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<!-- F I N   D E L   M O D A L -->

                                    <div class="col-sm-3">
                                        <div class="col-sm-12" style="padding: 0px;">
                                            <label class="control-label"> <font color="red">*</font> Estado</label>
                                            <select id="selectEstado" class="form-control">
                                                <option value="<?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idestado'];} ?>">
                                                    <?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['descEstado'];} ?>
                                                </option>
                                            </select>
                                        </div>
                                        <!--<div class="col-sm-1" style="padding: 2px; margin-top: 25px;">                                              
                                            <button type="button" data-toggle="modal" data-target="#nuevoEstado" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button>
                                        </div>-->
                                    </div>
<!-- M O D A L   P A R A   A G R E G A R   U N   N U E V O   E S T A D O -->
                                    <div class="modal fade" id="nuevoEstado" tabindex="-1" role="dialog" aria-labelledby="nuevoEstado" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" >Agregar nuevo Estado</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <select id="selectPais2" class="form-control" ></select>
                                                    <input type="text" id="inputNuevoEstado" class="form-control" placeholder="Nombre de estado">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoEstado">Aceptar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<!-- F I N   D E L   M O D A L -->

                                    <div class="col-sm-3">
                                        <div class="col-sm-12" style="padding: 0px;">
                                            <label class="control-label">Municipio</label>
                                            <select id="selectMunicipio" class="form-control" >
                                                <option value="<?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idmunicipio'];} ?>">
                                                    <?php if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['descMunicipio'];} ?>
                                                </option>
                                            </select>
                                        </div>   
                                        <!--<div class="col-sm-1" style="padding: 2px; margin-top: 25px;">
                                            <button type="button" data-toggle="modal" data-target="#nuevoMunicipio" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button>
                                        </div>-->
                                    </div>

<!-- M O D A L   P A R A   A G R E G A R   U N   N U E V O   M U N I C I P I O -->
                                    <div class="modal fade" id="nuevoMunicipio" tabindex="-1" role="dialog" aria-labelledby="nuevoMunicipio" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" >Agregar nuevo Municipio</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <select id="selectPais3" class="form-control" ></select>
                                                    <select id="selectEstado3" class="form-control" ></select>
                                                    <input type="text" id="inputNuevoMunicipio" class="form-control" placeholder="Nombre de municipio">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoMunicipio">Aceptar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
<!-- F I N   D E L   M O D A L -->
                                    <div class="col-sm-3">
                                        <label class="control-label">Ciudad</label>
                                        <input id="ciudad" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['ciudad'];}?>">
                                    </div>
                                </div> <br>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="control-label">Nombre de Contacto</label>
                                        <input id="nombre_contacto" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['nombre'];}?>">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label">Correo</label>
                                        <input id="email" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['email'];}?>"> 
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="control-label">Teléfono</label>
                                        <input id="telefono" class="form-control numeros" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['telefono'];}?>">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label">Página Web</label>
                                        <input id="web" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['web'];}?>">
                                    </div>
                                </div>
                            </div><!-- Fin del tab basicos -->





<!-- DIV COMPRAS -->
<div id="divcompras" class="tab-pane fade">
<div id="listareq"  style="display:block;margin-top:20px;font-size:12px;display:block;">
    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>

                <th>No. OC.</th>
                <th>Fecha</th>
                <!-- <th>Proveedor</th> -->
                <th>Solicitante</th>
                <th>Fecha recepcion</th>
                <!-- <th>Almacen</th> -->
                <th>Total</th>
                <!-- <th>Prioridad</th> -->
                <th>Estatus</th>
                <th class="no-sort" style="text-align: center;">Acciones</th>

            </tr>
        </thead>
        
        <tbody>
        </tbody>
    </table>
</div>
</div>
<!-- FIN DIV COMPRAS -->
<!-- DIV SALDOS -->
<div id="divsaldos" class="tab-pane fade">
<div class='row' id='listaCargosFac'>
    <div class="col-xs-12 col-md-12 table-responsive"><br>
      <div id='saldos_div2'>&nbsp;&nbsp;&nbsp;<span style='font-size:14px;'>Saldo total del proveedor</span> <input type='text' id='total_saldos' readonly="readonly" style='text-align:center;font-weight:bold;font-size:16px;'></div>
        <table id="tabla-carfac" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th>Fecha de Cargo / Factura</th>
              <th>Fecha de Vencimiento</th>
              <th>Concepto</th>
              <th>Folio</th>
              <th>Moneda</th>
              <th>Monto</th>
              <th>Saldo Abonado MXN</th>
              <th>Saldo Actual MXN</th>
              <th>Estatus</th>
              <th>OC</th>
            </tr>
          </thead>
          <tbody id='trs_carfac'>
          </tbody>
        </table>
      </div>
    </div>
</div>
<!-- FIN DIV SALDOS  -->
<!-- DIV REPORTES -->
<div id="divreportes" class="tab-pane fade">
    <div class="row">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <label>Desde</label>
                        <div id="datetimepicker1" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="f_ini" class="form-control" type="text" placeholder="Fecha de Entrega" value="<?php echo $fi; ?>">
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label>Hasta</label>
                        <div id="datetimepicker2" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>   
                            <input id="f_fin" class="form-control" type="text" placeholder="Fecha de Entrega" value="<?php echo $ff; ?>"> 
                        </div>
                        
                        
                        <div class="row"></div>
                    </div>
                    <div class="col-sm-2" style="margin-top:30px;">
                        <input type='radio' id='imp2' name='imp2' class='imp2' value='0' checked> Global &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>

                    <div class="col-sm-2" style="margin-top:30px;">
                        <input type='radio' name='imp2' class='imp2' value='1'> Detalle
                    </div>

                     <div class="col-sm-2" style="margin-top:24px;">
                       <button class="btn btn-default" onclick="generar_reporte();">Buscar</button>
                    </div>
    
                </div>


            </div>
        </div>
    </div> 
    <div id="carga" class="row" style="display: none;">
        <div class="col-xs-12 col-md-12">
        Cargando resultados...
        </div>
    </div>
    <div id="recio" class="row">
    <button style="margin-left: 15px;" onclick="exporta();" class="btn btn-default">Exportar</button>
        <div class="col-xs-12 col-md-12 table-responsive">
            <div>
            <table id="vamosaver" class="table table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr><th>Caracteristicas</th><th>Fecha</th><th>Docto</th><th>Cantidad</th><th>Unidad</th><th>$ Unitario</th><th>Importe</th><th>Impuestos</th><th>Total</th></tr>
                </thead>
                <tbody id='res_rep'>
                                    
                </tbody>
            </table>
        </div>
    </div> 
    </div>
</div>
<!-- FIN DIV REPORTES  -->
<!-- DIV accesoPortal -->
<div id="accesoPortal" class="tab-pane fade">
          <div class="row">
          <div class="col-sm-12" style="margin-top: 20px;">
               <div class="col-sm-2">
                    <b>Correo:</b>
               </div>  
               <div class="col-sm-10">
                    <input style="width:300px;" id="correoportal" class="form-control" type="text" value="<?php
                        if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['email'];}?>" readonly>
               </div>

           </div>
           <div class="col-sm-12" style="margin-top: 10px;">
               <div class="col-sm-2">
                    <b>Usuario:</b>
               </div>  
               <div class="col-sm-10">
                    <input style="width:300px;" id="userportal" class="form-control" type="text" value="usuarioProveedor_<?php
                        if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idPrv'];}?>" readonly>
               </div>

           </div>
           <div class="col-sm-12" style="margin-top: 10px;">
               <div class="col-sm-2">
                    <b>Contraseña:</b>
               </div>  
               <div class="col-sm-10">
                    <input style="width:300px;" id="passportal" class="form-control" type="password" value="<?php echo randpass(); ?>" readonly>
               </div>
               
           </div>

           <div class="col-sm-12" style="margin-top: 10px;">
               &nbsp;
           </div>

           <div class="col-sm-12" style="margin-top: 10px;">
               <div class="col-sm-2">
                    <b>Nueva contraseña:</b>
               </div>  
               <div class="col-sm-10">
                    <input style="width:300px;" id="passportal2" class="form-control" type="password" value="">
               </div>
               
           </div>
           <div class="col-sm-12" style="margin-top: 10px;">
               <div class="col-sm-2">
                    <b>Escribir de nuevo:</b>
               </div>  
               <div class="col-sm-10">
                    <input style="width:300px;" id="passportal3" class="form-control" type="password" value="">
               </div>
               
           </div>

           <div class="col-sm-12" style="margin-top: 10px;">
                <div class="col-sm-2">
                &nbsp;
               </div> 
               <div class="col-sm-10">
                    <button id="btnenviarCorreo" txt-original='Modificar contraseña' txt-click='Procesando...' type="button" class="btn btn-default" onclick="modificarPassPortal();">Modificar contraseña</button>
               </div>  
               
           </div>
        
          </div>
        </div>
<!-- Fin del tab accesoPortal-->

<!-- Inicio Analisis -->
<div id="analisis" class="tab-pane fade">
    <!--<div class="row">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <label>Desde</label>
                        <div id="datetimepicker1" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            <input id="f_ini" class="form-control" type="text" placeholder="Fecha de Entrega" value="<?php echo $fi; ?>">
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <label>Hasta</label>
                        <div id="datetimepicker2" class="input-group date">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>   
                            <input id="f_fin" class="form-control" type="text" placeholder="Fecha de Entrega" value="<?php echo $ff; ?>"> 
                        </div>
                        
                        
                        <div class="row"></div>
                    </div>
                    <div class="col-sm-2" style="margin-top:30px;">
                        <input type='radio' id='imp2' name='imp2' class='imp2' value='0' checked> Global &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>

                    <div class="col-sm-2" style="margin-top:30px;">
                        <input type='radio' name='imp2' class='imp2' value='1'> Detalle
                    </div>

                     <div class="col-sm-2" style="margin-top:24px;">
                       <button class="btn btn-default" onclick="generar_reporte();">Buscar</button>
                    </div>
    
                </div>


            </div>
        </div>
    </div> -->
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered table-hover" id="analisisOrdenes">
                <thead>
                    <tr>
                        <th>OC</th>
                        <th>Partida</th>
                        <th>Serie</th>
                        <th>UUID</th>
                        <th>Fecha Factura</th>
                        <th>Monto</th>
                        <th>Importe Fac.</th>
                        <th>Estatus</th>
                        <th>Fecha Pago</th>
                    </tr>
                </thead>
                <tbody>
                  <?php 

                    foreach ($res2['data'] as $key => $value) {
                        echo '<tr>';
                            echo '<td>'.$value['idocompra'].'</td>';
                            echo '<td>'.$value['codigo'].'</td>';
                            echo '<td>'.$value['folio'].'</td>';
                            echo '<td>'.$value['uuid'].'</td>';
                            echo '<td>'.$value['fechaFac'].'</td>';
                            echo '<td>'.$value['ocTotal'].'</td>';
                            echo '<td>'.$value['importe'].'</td>';
                            echo '<td>'.$value['estatus'].'</td>';
                            echo '<td>'.$value['fechaPago'].'</td>';
                        echo '</tr>';
                    }

                  ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- fin analisis -->
<!-- D I R E C T O R I O   D E   C O N T A C T O S  -->
                            <div id="direccionContactos" class="tab-pane fade">
                                <div class="row"> <br>
                                    <div class="col-sm-6"> <blockquote> Si los datos del contacto son los mismos de los Datos básicos; transfiérelos con el botón Transferir </blockquote> </div>
                                    <div class="col-sm-3" "> <br>
                                        <button class="btn btn-info btn-block" onclick="transferirDat();"><i class="fa fa-exchange" aria-hidden="true"></i> Transferir de datos básicos</button>
                                    </div>
                                </div> <br>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="control-label">Nombre</label>
                                        <input type="text" id="nombreC" class="form-control">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label">Cargo</label>
                                        <input type="text" id="cargoC" class="form-control">
                                    </div>

                                    <div class="col-sm-4">
                                        <label class="control-label">Correo Electrónico</label>
                                        <input type="text" id="emailC" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                            <label class="control-label">Teléfono y extensión</label>
                                            <input id="telefonoC" class="form-control datFc" type="text"> 
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="control-label">Celular</label>
                                            <input type="text" id="celularC" class="form-control">
                                        </div>
                                        <br><br><br><br>
                                        <div class="col-sm-4">
                                            <button type="button" class="btn btn-success" onclick="agregarContacto();">
                                                <i  class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button>
                                            <!--<button onclick="savelist();">Guardar Lista</button> -->
                                        </div>
                                </div> <br>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <table id="contacList" class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Nombre</th>
                                                    <th>Cargo</th>
                                                    <th>Correo</th>
                                                    <th>Teléfono</th>
                                                    <th>Celular</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    foreach ($datosProveedor['contactos'] as $keyx => $valuex) {
                                                        echo '<tr id="cont_'.$valuex['nombre'].'" idRel="'.$valuex['idCont'].'" nombre="'.$valuex['nombre'].'" cargo="'.$valuex['cargo'].'" email="'.$valuex['email'].'" telefono="'.$valuex['telefono'].'" celular="'.$valuex['celular'].'" >';
                                                        echo '<td><span class="glyphicon glyphicon-remove" onclick="borraContactoProve(\''.$valuex['idCont'].'\');"></span></td>';
                                                        echo '<td>'.$valuex['nombre'].'</td>';
                                                        echo '<td>'.$valuex['cargo'].'</td>';
                                                        echo '<td>'.$valuex['email'].'</td>';
                                                        echo '<td>'.$valuex['telefono'].'</td>';
                                                        echo '<td>'.$valuex['celular'].'</td>';
                                                        echo '<td></td>';
                                                        echo '</tr>';
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>  
                                </div>
                            </div><!-- fin del Tab Directorio de contactos -->

<!-- C R E D I T O  -->
                            <div id="credito" class="tab-pane fade">
                                <div class="row"> </div> <br>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="control-label">Días de Crédito</label>
                                        <input id="diasCredito" class="form-control numeros" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['diascredito'];}?>">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Saldo</label>
                                        <input id="saldo" class="form-control" type="text" readonly="readonly" value="<?php 
                                            if(isset($saldoProv)){echo number_format($datosProveedor['basicos'][0]['limite_credito'] - $saldoProv[0]['saldoGral'],2) ;}?>">
                                    </div>
                                </div> <br>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <label class="control-label">Límite de Crédito</label>
                                        <input id="limiteCredito" class="form-control numeros" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo number_format($datosProveedor['basicos'][0]['limite_credito'],2) ;}?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Mínimo de piezas por pedido</label>
                                        <input id="minimoPieza" class="form-control numeros" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo number_format($datosProveedor['basicos'][0]['minimo_piezas'],2) ;}?>">
                                    </div>
                                    <div class="col-sm-3">
                                        <label>Importe mínimo por pedido</label>
                                        <input id="minimoImportePedido" class="form-control numeros" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo number_format($datosProveedor['basicos'][0]['minimo_importe_pedido'],2) ;}?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Lugar de entrega</label>
                                        <input id="lugarEntrega" class="form-control" type="text" value="<?php 
                                            if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['lugar_entrega'];}?>">
                                    </div>
                                </div>
                            </div><!-- Fin de tab credito -->

<!-- D A T O S   F I S C A L E S  -->
                            <div id="datosFiscales" class="tab-pane fade" style="align-self: center;"> <br>
                                <div class="row"></div>
                                <div class="col-sm-12">
                                    <div class="col-sm-1"> <label>Datos fiscales</label> </div>
                                    <div class="col-sm-1">
                                        <select id="cmbDatosF" class="form-control" onchange="ocultaDiv();">
                                            <option value="0">No</option>
                                            <option value="1" <?php 
                                                //if(isset($datosProveedor['basicos'][0]['idtipo'])){
                                                    if($datosProveedor['basicos'][0]['idtipo'] != 1 || $datosProveedor['basicos'][0]['cuenta'] != 0){echo 'selected';
                                                    } 
                                                //}
                                            ?>
                                            >Sí</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row"> </div> <br>

                                <div id="divE">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div style="background-color: rgb(30, 90, 223); top: 40px; left: 10px; height: 5%; width: 100%; text-align: left; color: rgb(248, 236, 224); display: block;  font-size: 18px;">Proveedor extranjero</div>
                                        </div>
                                    </div> <br>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label class="control-label">Nombre del Proveedor</label>
                                            <input type="text" id="nombrextranjero" class="form-control" value="<?php 
                                                if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['nombrextranjero'];}?>">
                                        </div>

                                        <div class="col-sm-2">
                                            <label class="control-label">Número ID Fiscal</label>
                                            <input type="text" id="numidfiscal" class="form-control" value="<?php 
                                                if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['numidfiscal'];}?>">
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="control-label">País de residencia </label>
                                            <select id="paisR" class="form-control" onchange="estadosF();">
                                                <option value="0">Selecciona un país</option>
                                                <?php
                                                    foreach ($paises as $key => $value) {
                                                        if(isset($datosProveedor)){
                                                            if($datosProveedor['basicos'][0]['idpais']==$value['idpais']){
                                                                echo '<option value="'.$value['idpais'].'" selected>'.$value['pais'].'</option>';
                                                            }
                                                        }
                                                        echo '<option value="'.$value['idpais'].'">'.$value['pais'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-sm-1">
                                            <label class="control-label"></label> <br>
                                            <button type="button" data-toggle="modal" data-target="#nuevoPaisRes" class="btn btn-success btn-sm">
                                                <i class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button>
                                        </div>
<!-- M O D A L   P A R A   A G R E G A R   U N   N U E V O   P A Í S -->    
                                        <div class="modal fade" id="nuevoPaisRes" tabindex="-1" role="dialog" aria-labelledby="nuevoPaisRes" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" >Agregar nuevo País</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="text" id="inputNuevoPais" class="form-control" placeholder="Nombre de país">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnNuevoPais">Aceptar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-2">
                                            <label class="control-label">Nacionalidad </label>
                                            <input type="text" id="nacionalidad" class="form-control" value="<?php 
                                                if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['nacionalidad'];}?>">
                                        </div>
                                    </div>
                                </div>
                                <br><br>

                                <div id="divDF" <?php if($datosProveedor['basicos'][0]['idTasaPrvasumir'] != 0){echo 'style="display:block"';}else{ echo 'style="display:none"';} ?> >
                                    <div id="divC">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div style="background-color: rgb(30, 90, 223); top: 40px; left: 10px; height: 5%; width: 100%; text-align: left; color: rgb(248, 236, 224); display: block;  font-size: 18px;">Datos contables</div>
                                            </div>
                                        </div> <br>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="control-label"> <font color="red">*</font> Tipo</label>
                                                <select id="tipo" class="form-control">
                                                    <?php
                                                        echo($datosProveedor['basicos'][0]['idtipo']);
                                                        foreach ($tipoProveedor as $key => $value) {
                                                            if(isset($datosProveedor)){
                                                                if($datosProveedor['basicos'][0]['idtipo']==$value['idtipo']){
                                                                    echo '<option value="'.$value['idtipo'].'" selected>'.$value['tipo'].'</option>';
                                                                }
                                                            }
                                                            echo '<option value="'.$value['idtipo'].'">'.$value['tipo'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Beneficiario/Pagador</label>
                                                <select id="beneficiario" class="form-control" onchange="beneficiario();">
                                                    <option value="0">Si </option>
                                                    <option value="1">No  </option>
                                                </select>
                                            </div>

                                            <div class="col-sm-3" id="divCuenta">
                                                <label class="control-label"> <font color="red">*</font> Cuenta Proveedor</label>
                                                <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'>
                                                <img src='images/reload.png' class="btn_cuentas_act" onclick='actualizaCuentas()' title='Actualizar Cuentas' style='vertical-align:middle;'>
                                                <select id="cuenta" class="form-control">
                                                    <option value="0">Selecciona una cuenta</option>
                                                    <?php
                                                        while($cuentapa = $cuentap->fetch_assoc()){
                                                            $id     = $cuentapa['account_id'];
                                                            $nombre = $cuentapa['nombre_cuenta'];

                                                            $selected = '';
                                                            if($datosProveedor['basicos'][0]['cuenta'] == $id)
                                                                $selected = 'selected';

                                                            echo("<option value='$id' $selected>$nombre</option>");
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3" id="divCuentaCliente">
                                                <label class="control-label">Cuenta Cliente</label>
                                                <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'>
                                                <img src='images/reload.png' class="btn_cuentas_act" onclick='actualizaCuentas()' title='Actualizar Cuentas' style='vertical-align:middle;'>
                                                <select id="cuentaCliente" class="form-control">
                                                    <option value="0">Selecciona una cuenta</option>
                                                    <?php 
                                                        foreach ($cuentaCliente as $key => $value) {
                                                            if(isset($datosProveedor)){
                                                                if($datosProveedor['basicos'][0]['cuentacliente']==$value['account_id']){
                                                                    echo '<option value="'.$value['account_id'].'" selected>'.$value['nombre_cuenta'].'</option>';
                                                                }
                                                            }
                                                            echo '<option value="'.$value['account_id'].'">'.$value['nombre_cuenta'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top:.25em;">
                                            <div class="col-sm-3" id="preopolizas_prov_container">
                                                <label for="prepolizas_provision">Prepoliza de provision:</label>
                                                <i class="material-icons" onclick="iraprepolizas();" title="Agregar Cuenta"
                                                style="font-size:1.3em;vertical-align:middle; color:#96BE33;">add_circle</i>
                                                <img src='images/reload.png' class="btn_prepol_prov" onclick='obtener_prepol_prov();' title='Actualizar Cuentas' style='vertical-align:middle;'>
                                                <select id="prepolizas_provision" class="form-control">
                                                    <option value="0">Selecciona una prepoliza</option>
                                                    <?php
                                                        while($prepoliza_pr = $prepolizas_prov->fetch_assoc()){
                                                            $id     = $prepoliza_pr['id'];
                                                            $nombre = $prepoliza_pr['nombre'];

                                                            $selected = '';
                                                            if($datosProveedor['basicos'][0]['id_prepoliza'] == $id)
                                                                $selected = 'selected';

                                                            echo("<option value='$id' $selected>$nombre</option>");
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3" id="preopolizas_pago_container">
                                                <label for="prepolizas_pago">Prepoliza de pago:</label>
                                                <i class="material-icons" onclick="iraprepolizas();" title="Agregar Cuenta"
                                                style="font-size:1.3em;vertical-align:middle; color:#96BE33;">add_circle</i>
                                                <img src='images/reload.png' class="btn_prepol_pago" onclick='obtener_prepol_pago();' title='Actualizar Cuentas' style='vertical-align:middle;'>
                                                <select id="prepolizas_pago" class="form-control">
                                                    <option value="0">Selecciona una prepoliza</option>
                                                    <?php
                                                        while($prepoliza_pa = $prepolizas_pago->fetch_assoc()){
                                                            $id     = $prepoliza_pa['id'];
                                                            $nombre = $prepoliza_pa['nombre'];

                                                            $selected = '';
                                                            if($datosProveedor['basicos'][0]['id_prepoliza_pagos'] == $id)
                                                                $selected = 'selected';

                                                            echo("<option value='$id' $selected>$nombre</option>");
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-3" id="cuentas_gastos_container">
                                                <label for="cuentas_gastos">Seleccione una cuenta</label>
                                                <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'>
                                                <img src='images/reload.png' class="btn_cuentas_act" onclick='actualizaCuentas()' title='Actualizar Cuentas' style='vertical-align:middle;'>
                                                <select id="cuentas_gastos" class="form-control">
                                                    <option value="0">Seleccione una cuenta de gasto.</option>
                                                    <?php 
                                                    if(intval($cuentasGastos->num_rows))
                                                    {
                                                        while ($cuenta_gasto = $cuentasGastos->fetch_assoc()) 
                                                        {
                                                            $id     = $cuenta_gasto['id'];
                                                            $nombre = $cuenta_gasto['nombre'];

                                                            $selected = '';
                                                            if($datosProveedor['basicos'][0]['id_cuenta_gasto'] == $id)
                                                                $selected = 'selected';

                                                            echo("<option value='$id' $selected>$nombre</option>");
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <br>

                                    <div id="divtT">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div style="background-color: rgb(30, 90, 223); top: 40px; left: 10px; height: 5%; width: 100%; text-align: left; color: rgb(248, 236, 224); display: block;  font-size: 18px;">Tipo de Tercero y Operación</div>
                                            </div>
                                        </div> <br>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label class="control-label"> <font color="red">*</font> Tipo Tercero</label>
                                                <select id="tipoTercero" class="form-control" onchange="tipoTerceroOperacion2();">
                                                    <option value="0">Selecciona un tipo</option>
                                                    <?php
                                                        foreach ($tipoTercero as $key => $value) {
                                                            if(isset($datosProveedor)){
                                                                if($datosProveedor['basicos'][0]['idtipotercero']==$value['id']){
                                                                    echo '<option value="'.$value['id'].'" selected>'.$value['tipotercero'].'</option>';
                                                                }
                                                            }
                                                            echo '<option value="'.$value['id'].'">'.$value['tipotercero'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-sm-3">
                                                <input type="hidden" id="idComunFact" value="<?php 
                                                    if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['id'];}?>">
                                                <label class="control-label"> <font color="red">*</font> Tipo Operacion</label>
                                                <select id="tipoTerceroOperacion" class="form-control">
                                                    <option value="0">Selecciona un tipo de operacion</option>
                                                    <?php
                                                        foreach ($tipoOpercaion as $key => $value) {
                                                            if(isset($datosProveedor)){
                                                                if($datosProveedor['basicos'][0]['idtipoperacion']==$value['id']){
                                                                    echo '<option value="'.$value['id'].'" selected>'.$value['tipoOperacion'].'</option>';
                                                                }
                                                            }
                                                            echo '<option value="'.$value['id'].'">'.$value['tipoOperacion'].'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="divI">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <br>
                                                <div style="background-color: rgb(30, 90, 223); top: 40px; left: 10px; height: 5%; width: 100%; text-align: left; color: rgb(248, 236, 224); display: block;  font-size: 18px;">Control de IVA</div>
                                                <br>
                                            </div>
                                        </div> <br>

                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label class="control-label">IVA Retenido %</label>
                                                    <input type="text" id="ivaretenido" class="form-control" value="<?php 
                                                        if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['ivaretenido'];}?>">
                                            </div>
                                            <div class="col-sm-2">
                                                <label class="control-label">ISR Retenido %</label>
                                                    <input type="text" id="isretenido" class="form-control" value="<?php 
                                                        if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['isretenido'];}?>">
                                            </div>
                                            <div class="col-sm-3">
                                                <label class="control-label">Tipo IVA</label>
                                                <select id="idtipoiva" class="form-control" onchange="estadosFc();">
                                                    <option value="0">Selecciona un tipo de iva</option>
                                                    <?php
                                                        foreach ($tipoIva as $key => $value) {
                                                            if(isset($datosProveedor)){
                                                                if($datosProveedor['basicos'][0]['idtipoiva']==$value['id']){
                                                                    echo '<option value="'.$value['id'].'" selected>'.$value['tipoiva'].'</option>';
                                                                }
                                                            }
                                                            echo '<option value="'.$value['id'].'">'.$value['tipoiva'].'</option>';
                                                        }
                                                    ?>
                                                </select> 
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="btn-group" data-toggle="buttons" id="ivas" style="top: 30px; left: 10px; width: 40%; text-align: left; display: block;">
                                                    <input type="hidden" id="idtasaAsumir" value="<?php 
                                                        if(isset($datosProveedor)){echo $datosProveedor['basicos'][0]['idTasaPrvasumir'];}?>">
                                                    <label class="control-label"> <font color="red">*</font> Asumir tasa %</label>
                                                    <br>

                                                    <?php
                                                        $tasa1 = 0;
                                                        $tasa2 = 0;
                                                        $tasa3 = 0;
                                                        $tasa4 = 0;
                                                        $tasa5 = 0;
                                                        $tasa6 = 0;

                                                        switch ($tasas['tasasAsumir'][0]['tasa']) {
                                                            case '16%':
                                                                $tasa1 = 1;
                                                                break;
                                                            case '11%':
                                                                $tasa2 = 1;
                                                                break;
                                                            case '0%':
                                                                $tasa3 = 1;
                                                                break;
                                                            case 'Exenta':
                                                                $tasa4 = 1;
                                                                break;
                                                            case '15%':
                                                                $tasa5 = 1;
                                                                break;
                                                            case '10%':
                                                                $tasa6 = 1;
                                                                break;
                                                        }

                                                        if ($tasa1 ==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumir16" type="radio" checked="checked" value="1" name="ivasumir" ra="1" autocomplete="off"> 16% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumir16" type="radio" checked="checked" value="1" name="ivasumir" ra="1" autocomplete="off"> 16% </label>';
                                                        }
                                                        if ($tasa2==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumir11" type="radio" value="2" name="ivasumir" disabled="true" ra="2" autocomplete="off"> 11% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumir11" type="radio" value="2" name="ivasumir" disabled="true" ra="2" autocomplete="off"> 11% </label>';
                                                        }
                                                        if ($tasa3==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumir0" type="radio" value="3" name="ivasumir" disabled="true" ra="3" autocomplete="off"> 0% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumir0" type="radio" value="3" name="ivasumir" disabled="true" ra="3" autocomplete="off"> 0% </label>';
                                                        }
                                                        if ($tasa4==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumirex" type="radio" value="4" name="ivasumir" disabled="true" ra="4" autocomplete="off"> Exenta </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumirex" type="radio" value="4" name="ivasumir" disabled="true" ra="4" autocomplete="off"> Exenta </label>';
                                                        }
                                                        if ($tasa5==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumir15" type="radio" value="5" name="ivasumir" disabled="true" ra="5" autocomplete="off"> 15% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumir15" type="radio" value="5" name="ivasumir" disabled="true" ra="5" autocomplete="off"> 15% </label>';
                                                        }
                                                        if ($tasa6==1) {
                                                            echo '<label class="btn btn-info active"> <input id="ivasumir10" type="radio" value="6" name="ivasumir" disabled="true" ra="6" autocomplete="off"> 10% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-info"> <input id="ivasumir10" type="radio" value="6" name="ivasumir" disabled="true" ra="6" autocomplete="off"> 10% </label>';
                                                        }
                                                    ?>

<!--                                                    <label class="btn btn-info"> <input id="ivasumirotra1" type="radio" value="1234" name="ivasumir" disabled="true" ra="1234" autocomplete="off"> &nbsp;</label>
                                                    <label class="btn btn-info"> <input id="ivasumirotra2" type="radio" value="12345" name="ivasumir" disabled="true" ra="12345" autocomplete="off"> &nbsp;</label>
-->                                                 <br> <br>

                                                    <?php
                                                        $tasa1 = 0;
                                                        $tasa2 = 0;
                                                        $tasa3 = 0;
                                                        $tasa4 = 0;
                                                        $tasa5 = 0;
                                                        $tasa6 = 0;

                                                        $registros = count($tasas);
                                                        for ($i=0; $i <= $registros ; $i++) { 
                                                            switch ($tasas['tasas'][$i]['tasa']) {
                                                                case '16%':
                                                                    $tasa1 = 1;
                                                                    break;
                                                                case '11%':
                                                                    $tasa2 = 1;
                                                                    break;
                                                                case '0%':
                                                                    $tasa3 = 1;
                                                                    break;
                                                                case 'Exenta':
                                                                    $tasa4 = 1;
                                                                    break;
                                                                case '15%':
                                                                    $tasa5 = 1;
                                                                    break;
                                                                case '10%':
                                                                    $tasa6 = 1;
                                                                    break;
                                                            }
                                                        }
                                                    
                                                        if ($tasa1 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="1" ch="1" autocomplete="off"> 16% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="1" ch="1" autocomplete="off"> 16% </label>';
                                                        }
                                                        if ($tasa2 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="2" ch="2" autocomplete="off"> 11% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="2" ch="2" autocomplete="off"> 11% </label>';
                                                        }
                                                        if ($tasa3 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="3" ch="3" autocomplete="off"> 0% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="3" ch="3" autocomplete="off"> 0% </label>';
                                                        }
                                                        if ($tasa4 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="4" ch="4" autocomplete="off"> Exenta </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="4" ch="4" autocomplete="off"> Exenta </label>';
                                                        }
                                                        if ($tasa5 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="5" ch="5" autocomplete="off"> 15% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="5" ch="5" autocomplete="off"> 15% </label>';
                                                        }
                                                        if ($tasa6 == 1) {
                                                            echo '<label class="btn btn-primary active"> <input id="tasas" type="checkbox" value="6" ch="6" autocomplete="off"> 10% </label>';
                                                        } else {
                                                            echo '<label class="btn btn-primary"> <input id="tasas" type="checkbox" value="6" ch="6" autocomplete="off"> 10% </label>';
                                                        }
                                                    ?>
<!--                                                    <label class="btn btn-primary"> <input id="tasas" type="checkbox" value="1234" ch="1234" autocomplete="off"> Otra 1
                                                        <input id="otra1" type="text" placeholder="0.00%" style="width: 60px; display: inline;">
                                                    </label>

                                                    <label class="btn btn-primary"> <input id="tasas" type="checkbox" value="12345" ch="12345" autocomplete="off"> Otra 2
                                                        <input id="otra2" type="text" style="width: 60px; display: inline;" placeholder="0.00%">
                                                    </label>
-->                                             </div>
<!--                                                <button onclick="probar();">Probar </button> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- fin del Tab datos fiscales -->

<!-- B A N C O   D E   P R O V E E D O R E S  -->
                            <div id="bancoProvedores" class="tab-pane fade"> <br>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="col-sm-2">
                                            <label>Información de banco de proveedor:</label>
                                        </div>
                                        <div class="col-sm-1">
                                            <select id="cuentaCont" class="form-control" onchange="ocultaDiv();">
                                                <option value="0">No</option>
                                                <option value="1" <?php if($datosProveedor['bancos'][0]['idbanco'] != 0){echo 'selected';} ?> >Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> <br>

                                <div class="row" id="divBP" style="display: none;">
                                    <div class="col-sm-12">
                                        <div class="col-sm-4">
                                            <label> <font color="red">*</font> Banco</label>
                                            <select id="selectBanco" class="form-control">
                                                <?php 
                                                    foreach ($bancos as $keyClas => $valueClas) {
                                                        if(isset($datosProveedor)){
                                                            if($datosProveedor['basicos'][0]['idbanco']==$valueClas['idbanco']){
                                                                echo '<option value="'.$valueClas['idbanco'].'" selected>'.$valueClas['nombre'].'/'.$valueClas['Clave'].'</option>';
                                                            }
                                                        }
                                                        echo '<option value="'.$valueClas['idbanco'].'">'.$valueClas['nombre'].'/'.$valueClas['Clave'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="col-sm-4"> <br>
                                            <label> <font color="red">*</font> No. Tarjeta / Cuenta bancaria</label>
                                            <input id="noCuentaBan" class="form-control numeros" type="text" value=""> <br>

                                            <button type="button" class="btn btn-success" onclick="agregarBanco();">
                                                <i class="fa fa-plus cursor" aria-hidden="true"></i>
                                            </button> 
                                            <!--<button onclick="savebancos();">Guardar Lista</button> -->
                                        </div>
                                    </div> <br>

                                    <div class="col-sm-6">
                                        <table id="bancoList" class="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Banco</th>
                                                    <th>No. Tarjeta o Cuenta bancaria</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach ($datosProveedor['bancos'] as $keyx => $valuex) {
                                                        echo '<tr idbanco="'.$valuex['idbanco'].'" id="idBan_'.$valuex['idbanco'].'" idRel="'.$valuex['id'].'" numct="'.$valuex['numCT'].'">';
                                                        echo '<td><span class="glyphicon glyphicon-remove" onclick="removeBanco('.$valuex['idbanco'].');"></span></td>';
                                                        echo '<td>'.$valuex['nombre'].'</td>';
                                                        echo '<td>'.$valuex['numCT'].'</td>';
                                                        echo '</tr>';
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                                
                            </div><!-- Fin del tab banco de proveedores -->
                            
        <!--- TAB de DATOS de FACTURACION -->
        <div id="datosFacturacion" class="tab-pane fade">
          <div class="row"><br>
            <div class="col-sm-8">
              <blockquote>
                <p>Si los datos de Facturación son los mismos que los básicos, transfiérelos de los básicos a facturación con el botón de Transferir.</p>
              </blockquote>
            </div>
            <div class="col-sm-3">
              <button class="btn btn-info btn-block" onclick="trans();"><i class="fa fa-exchange" aria-hidden="true"></i> Transferir</button>
            </div>

          </div>
                  <div class="row">
                    <div class="col-sm-6">
                      <input type="hidden" id="idComunFact" value="<?php
                                if(isset($datosProveedor)){echo $datosProveedor['fact'][0]['id'];}?>">

                      <label class="control-label"><span style="color:red;">*</span> Razón Social</label>
                      <input disabled="disabled" type="text" id="razonSocialF" class="form-control datFc" value="<?php
                                if(isset($datosProveedor)){echo $datosProveedor['fact'][0]['razon_social'];}?>">
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label"><span style="color:red;">*</span> RFC</label>
                        <input id="rfcF" class="form-control datFc" type="text" value="<?php
                                  if(isset($datosProveedor)){echo $datosProveedor['fact'][0]['rfc'];}?>">
                      </div>
                     <!-- <div class="col-sm-3">
                        <label class="control-label">CURP</label>
                        <input id="curp" class="form-control datFc" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['curp'];}?>">
                      </div> -->
                      <div class="col-sm-3">
                        <label class="control-label"><span style="color:red;">*</span> Email</label>
                        <input type="text" id="emailFacturacion" class="form-control" value="<?php
                                if(isset($datosProveedor)){echo $datosProveedor['fact'][0]['correo'];}?>">
                      </div>
                  </div>
                <!--  <div class="row">
                    <div class="col-sm-6">
                      <label class="control-label"><span style="color:red;">*</span> Dirección de Facturación</label>
                      <input id="direccionFact" class="form-control datFc" type="text" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['domicilio'];}?>">
                    </div>
                    <div class="col-sm-3">
                      <label class="control-label"><span style="color:red;">*</span> Exterior e Interior F.</label>
                      <input id="numextFact" class="form-control datFc" type="text" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['num_ext'];}?>">
                    </div>
                   <!-- <div class="col-sm-3">
                      <label class="control-label">Interior F.</label>
                      <input id="numintFact" class="form-control" type="text" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['num_int'];}?>">
                    </div> 
                  </div> -->
                  <!--<div class="row">
                    <div class="col-sm-2">
                      <label class="control-label"><span style="color:red;">*</span> Colonia</label>
                      <input id="coloniaFact" class="form-control datFc" type="text" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['colonia'];}?>">
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label"><span style="color:red;">*</span> Código Postal</label>
                        <input id="cpFact" class="form-control numeros datFc" type="text" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['cp'];}?>">
                    </div>
                    <div class="col-sm-2">
                        <label class="control-label"><span style="color:red;">*</span> País</label>
                        <select id="paisFact2" class="form-control" onchange="estadosFc();">
                          <option value="0">-Selecciona un pais</option>
                          <?php
                            foreach ($paises as $key => $value) {
                              if(isset($datosCliente)){
                                if($datosClienteFact['fact'][0]['idPais']==$value['idpais']){
                                  echo '<option value="'.$value['idpais'].'" selected>'.$value['pais'].'</option>';
                                }
                              }
                              echo '<option value="'.$value['idpais'].'">'.$value['pais'].'</option>';
                            }
                          ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label"><span style="color:red;">*</span> Estado</label>
                        <select id="estadoFact" class="form-control datFc" onchange="municipiosFc();">
                          <option value="0">-Selecciona un estado</option>
                            <?php
                                foreach ($estados as $key => $value) {
                                    if(isset($datosClienteFact)){
                                        if($datosClienteFact['fact'][0]['estado']==$value['idestado']){
                                            echo '<option value="'.$value['idestado'].'" selected>'.$value['estado'].'</option>';
                                        }
                                    }
                                    echo '<option value="'.$value['idestado'].'">'.$value['estado'].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label class="control-label"><span style="color:red;">*</span> Municipio</label>
                        <select  id="municipiosFact" class="form-control datFc">
                          <option value='0'>-Selecciona un municipio--</option>
                          <?php
                            foreach ($municipiosFc as $keyMu => $valueMu) {
                                if(isset($datosClienteFact)){
                                  if($datosClienteFact['fact'][0]['idMunicipio']==$valueMu['idmunicipio']){
                                    echo '<option value="'.$valueMu['idmunicipio'].'" selected>'.$valueMu['municipio'].'</option>';
                                  }
                                }
                                echo '<option value="'.$valueMu['idmunicipio'].'">'.$valueMu['municipio'].'</option>';
                            }
                          ?>
                        </select>
                    </div>
                  </div> -->
                <!--  <div class="row">
                    <div class="col-sm-3">
                      <label><span style="color:red;">*</span> Ciudad</label>
                      <input id="ciudadFact" type="text" class="form-control datFc"value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['ciudad'];}?>">
                    </div>
                    <div class="col-sm-3" style="display: none;">
                       <label><span style="color:red;">*</span> País</label>
                      <input id="paisFact" type="text" class="form-control datFc" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['pais'];}?>">
                    </div>
                    <div class="col-sm-3">
                       <label>Régimen Fiscal</label>
                      <input id="regimenFact" type="text" class="form-control datFc" value="<?php
                                if(isset($datosClienteFact)){echo $datosClienteFact['fact'][0]['regimen_fiscal'];}?>">
                    </div>
                  </div> -->
        </div><!-- fin del Tab de facturacion -->
<!-- FIN DEL TAB DE DATOS DE FACTURACION -->    
                        </div>  <!-- Fin del div tab-contents -->
                    </div><!-- fin de contenedor overflow -->
                </div> <!-- Fin del Panel Body -->
            </div>
        </div>

        <!-- Modal Success -->
        <div id="modalSuccess" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content panel-success">
                    <div class="modal-header panel-heading">
                        <h4 id="modal-label">Exito!</h4>
                    </div>
                    <div class="modal-body">
                        <p>Tu Proveedor se guardo exitosamente</p>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-btnconf2-uno" type="button" class="btn btn-default" onclick="cierrafiv();">Continuar</button> 
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal adju -->
        <div id="modal-adju" class="modal sfade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content panel-default">
                    <div class="modal-header panel-heading">
                        <h4 id="modal-label">Adjuntar XML'S</h4>
                    </div>
                    <div id="bodyespecialxx" class="modal-body">
                        <input type="hidden" id="pediid">
                        <div id="adju_header" class="col-sm-12" style="padding:10px 0 10px 0;">
                            &nbsp;
                        </div>
                        <label>Partidas</label>
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover" id="tablePartidas">
                                <thead>
                                    <tr>
                                        <th>Codigo</th>
                                        <th>Descripcion</th>
                                        <th>Monto</th>
                                        <th>Receptor</th>
                                        <th>Estatus</th>
                                        <th>Archivos</th>
                                        <th>.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            
                        </div>
                       <!-- <div class="col-sm-12" style="padding:10px 0 10px 0;">
                            <b>Recepciones</b>
                        </div>
                        <div id="adju_recep" class="col-sm-12" style="padding:10px 0 10px 0;">
                            Cargando...
                        </div>-->
                        <div class="col-sm-12" style="padding:10px 0 10px 0;">
                            <b>Xml's/PDF's Adjuntos</b>
                        </div>
                        <div id="adju_xmls" class="col-sm-12" style="padding:10px 0 10px 0;">
                            Cargando...
                        </div>

                        <div class="col-sm-12" style="padding:10px 0 10px 0;" id="divxmls2">
                            <b>Subir archivos xml</b>
                        </div>
                        <div id="divxmls" class="col-sm-12" style="padding:0px;">
                        <div class="form-group"  style="padding:0px;">
                            
                            <form name='fac' id='fac' action='' method='post' enctype='multipart/form-data'>
                            <div class="col-sm-10" style="padding:0px;">
                                
                                <input type='file' name='factura[]' id='factura' onchange='check_file()'>
                                <input type='hidden' name='plz' id='plz' value='lala'>
                                
                            </div>
                            <div class="col-sm-10" style="margin-top:10px; padding:0px;">
                                <input type='submit' id='buttonFactura' value='Asociar Factura'>
                                <div id="resultasoc" style="margin-top:10px;">
                                    
                                </div>
                                
                                <span id='verif' style='color:green;display:none;'>Verificando...</span>
                            </div>
                            </form>
                        </div>
                        </div>
                        <div id="DivExtranjero" class="col-sm-12" style="padding:0px;">
                            <button class="btn btn-default" data-toggle="modal" href='#modalFormEx'>Subir PDF</button>
                        </div>

                        <div class="row">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-adju-uno" type="button" class="btn btn-default" data-dismiss="modal">Salir</button> 
                    </div>
                </div>
            </div> 
        </div> 
<div class="modal fade" id="modalFormEx">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-default">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Invoice Information</h4>
            </div>
            <div class="modal-body">
                
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Invoice Number:</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inNumber">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Invoice Ammount:</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inAmmount">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Invoice Date:</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="inDate">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Currency:</label>
                        </div>
                        <div class="col-sm-6">
                            <select id="inCurrency" class="form-control">
                                <option value="1">(MXN)Peso</option>
                                <option value="2">(USD)Dolar</option>
                                <option value="1">(EUR)Euro</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <form name='pdfUp' id='pdfUp' action="" method="post" enctype="multipart/form-data">
                                <input type="file" name="archivo" id="archivo"></input>
                                <input class="btn btn-primary" type="submit" value="Upload File"></input>
                            </form>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
    </body>
</html>

<script>
function cierrafiv(){
    $('#modalSuccess').modal('hide');
}

function beneficiario() {
    var beneficiario = $('#beneficiario').val();
    if(beneficiario == 0 ) {
        $("#divCuentaCliente").show();
    } else {
        $("#divCuentaCliente ").hide(); 
    }
}

function ocultaDiv(){
    var x = $('#cmbDatosF').val();

    if (x==0) {
        $("#divDF").hide();
    } else {
        $("#divDF").show();
        $("#ivasumir16").prop('checked', 'checked');
        $('#otra1, #otra2').val('0.00').hide();
        tasa();         
    }

    var y = $('#cuentaCont').val();
    if (y==0) {
        $('#divBP').hide();
    } else {
        $('#divBP').show();
    }
}

function actualizaCuentas(n){
    $('.btn_cuentas_act').fadeOut();
    if(typeof n === 'undefined'){
        n=0;
    }

    $.post("../cont/ajax.php?c=CaptPolizas&f=actualizaCuentas",
        {
            resultados : n
        },
        function(datos) {
            var op0 = '<option value="0">Selecciona una cuenta</option>';
            $('#cuenta, #cuentaCliente, #cuentas_gastos').html(op0);
            $('#cuenta, #cuentaCliente, #cuentas_gastos').append(datos);
            $('.btn_cuentas_act').fadeIn();
        });
        //buscacuentaext($('#cuenta').val());
        //alert(datos)
}

function iracuenta(){
    window.parent.agregatab('../../modulos/cont/index.php?c=arbol&f=index','Cuentas','',145)
    //window.location='../../modulos/cont/index.php?c=AccountsTree';
}

function obtener_prepol_prov(){
    $('.btn_prepol_prov').fadeOut();

    $.post("ajax.php?c=proveedores&f=obtener_prepolizas_provision",
        function(datos) {
            var op0 = '<option value="0">Selecciona una prepoliza</option>';
            $('#prepolizas_provision').html(op0);

            var options = '';
            $.each(datos, function(index, arr){
                options += "<option value='"+arr.id+"'>"+arr.nombre+"</option>";
            });

            $('#prepolizas_provision').append(options);
            $('.btn_prepol_prov').fadeIn();
        }, "JSON");
}

function obtener_prepol_pago(){
    $('.btn_prepol_pago').fadeOut();

    $.post("ajax.php?c=proveedores&f=obtener_prepolizas_pago",
        function(datos) {
            var op0 = '<option value="0">Selecciona una prepoliza</option>';
            $('#prepolizas_pago').html(op0);
            
            var options = '';
            $.each(datos, function(index, arr){
                options += "<option value='"+arr.id+"'>"+arr.nombre+"</option>";
            });

            $('#prepolizas_pago').append(options);
            $('.btn_prepol_pago').fadeIn();
        }, "JSON");
}

function iraprepolizas(){
    window.parent.agregatab('../../modulos/cont/index.php?c=almacen&f=polizas','TPL Generacion de Polizas','',2433)
    //window.location='../../modulos/cont/index.php?c=AccountsTree';
}
</script>
