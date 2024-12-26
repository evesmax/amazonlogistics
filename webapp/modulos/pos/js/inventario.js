function buscar(){
    var idProducto = $('#producto').val();
    var almacen = $('#almacen').val();
    var tipo = $('#tipo').val();
    var desde = $('#desde').val();
    var hasta = $('#hasta').val();

    $.ajax({
        url: 'ajax.php?c=inventario&f=kardex',
        type: 'post',
        dataType: 'json',
        data: {idProducto: idProducto,
               almacen : almacen,
               tipo : tipo,
               desde : desde,
               hasta : hasta,
            },
    })
    .done(function(data) {
        console.log(data);
            var table = $('#tableKardex').DataTable();
    
            //$('.filas').empty();
            table.clear().draw();
            var x ='';
            var tipo = '';
            $.each(data, function(index, val) {
                if(val.tipo_traspaso=='0'){
                    tipo = '<span class="label label-warning">Salida</span>';
                }else if(val.tipo_traspaso=='1'){
                    tipo = '<span class="label label-success">Entrada</span>';
                }else{
                    tipo = '<span class="label label-primary">Traspaso</span>';
                }

                if(val.destino==null){
                    destino = '';
                }else{
                    destino = val.destino;
                }

                if(val.origen==null){
                    origen = '';
                }else{
                    origen = val.origen;
                }

                x ='<tr>'+
                                '<td>'+val.id+'</td>'+
                                '<td>'+val.nombre+'/'+val.codigo+'</td>'+
                                '<td>'+val.cantidad+'</td>'+
                                '<td>'+val.importe+'</td>'+
                                '<td>'+origen+'</td>'+
                                '<td>'+destino+'</td>'+
                                '<td>'+tipo+'</td>'+
                                '<td>'+val.costo+'</td>'+
                                '<td>'+val.referencia+'</td>'+
                                '<td>'+val.usuario+'</td>'+
                                '<td>'+val.fecha+'</td>'+
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
    
}
function mermaDetalle(idMerma){
    mensaje('Procesando...');
    var titulo = '';
    $.ajax({
        url: 'ajax.php?c=inventario&f=detalleMerma',
        type: 'POST',
        dataType: 'json',
        data: {idMerma: idMerma},
    })
    .done(function(resp) {
        console.log(resp);
        var contenido = '';
        $('#divDetalle').empty();
            $.each(resp.productos, function(index, val) {

                if(val.lote > 0 && val.caracteristicas == 0){
                    titulo = 'Producto con Lotes';

                    contenido += '<div class="col-sm-12">';
                    contenido += '<div class="col-sm-5">';
                    contenido += '<img src="'+val.imagen+'" height="250" width="250">';
                    contenido += '</div>';
                    contenido += '<div class="col-sm-7">';
                    contenido +=    '<h5>Nombre:</h5><label>'+val.proName+'</label>'+
                                    '<h5>Proveedor:</h5><label>'+val.razon_social+'</label>'+
                                    '<h5>Numero de lote:</h5><input type="text" value="'+val.lote+'" readonly/>'+
                                    '<h5>Fecha de fabricacion:</h5><input type="text" value="'+val.fecha_fabricacion+'" readonly/>'+
                                    '<h5>Fecha de caducidad:</h5><input type="text" value="'+val.fecha_caducidad+'" readonly/>';
                    contenido += '</div>';
                    contenido += '</div>';


                }else if(val.caracteristicas != 0 && val.lote == 0){

                    titulo = 'Producto con Caracteristicas';

                    contenido += '<div class="col-sm-12">';
                    contenido += '<div class="col-sm-5">';
                    contenido += '<img src="'+val.imagen+'" height="250" width="250">';
                    contenido += '</div>';
                    contenido += '<div class="col-sm-7">';
                    contenido +=    '<h5>Nombre:</h5><label>'+val.proName+'</label>'+
                                    '<h5>Proveedor:</h5><label>'+val.razon_social+'</label>'+
                                    '<h5>Caracteristicas:</h5><textarea cols="65" rows="2">'+val.caracteristicas+'</textarea>';
                    contenido += '</div>';
                    contenido += '</div>';

                }else{
                    titulo = 'Producto Normal';                    

                    contenido += `  <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            <img src="`+val.imagen+`" height="250" width="250">
                                        </div>
                                        <div class="col-sm-7">
                                            <label>Nombre:</label>              &nbsp <label>`+val.proName+`</label><br/>
                                            <label>Proveedor:</label>           &nbsp <label>`+val.razon_social+`</label><br/>
                                            <label>Almacen:</label>             &nbsp <label>`+val.almacen+`</label><br/>
                                            <label>Usuario:</label>             &nbsp <label>`+val.usuario+`</label><br/>
                                            <label>Cantidad:</label>            &nbsp <label>`+val.cantidad+`</label><br/>
                                            <label>Costo Unitario:</label>      &nbsp <label>$`+val.costo+`</label><br/>
                                            <label>Costo Total:</label>         &nbsp <label>$`+val.costoTotal+`</label><br/>
                                            <label>Precio:</label>              &nbsp <label>$`+val.precioR+`</label><br/>
                                            <label>Tipo:</label>                &nbsp <label>`+val.tipo_merma+`</label><br/>
                                            <label>Observaciones:</label>       &nbsp <label>`+val.observaciones+`</label><br/>
                                     
                                        </div>
                                    </div>`;

                    
                /*
                    contenido +='<table class="table table-bordered table-hover">';
                    contenido +='<thead>';
                    contenido +='<tr>';
                    contenido +='<th>Producto</th><th>Cantidad</th><th>Costo</th><th>Usuario</th><th>Almacen</th><th>Observaciones</th>';
                    contenido +='</tr></thead>';
                    contenido +='<tbody>';

                    contenido +='<tr>';
                    contenido +='<td>'+val.proName+'</td>';
                    contenido +='<td>'+val.cantidad+'</td>';
                    contenido +='<td>$'+val.precio+'</td>';
                    contenido +='<td>'+val.usuario+'</td>';
                    contenido +='<td>'+val.almacen+'</td>';
                    contenido +='<td>'+val.observaciones+'</td>'; 
                    contenido +='</tr>';
                    contenido +='</tbody></table>';
                */

                }                
            });
                    

        $('#divDetalle').append(contenido);
        $('#modalIdMerma').text(titulo);
        $('#modalDetalle').modal('show');
        //$('#totalMerma').text('$'+parseFloat(resp.total).toFixed(2));
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}


function agregaMerma(){    
    var producto = $('#producto').val();
    var cantidad = $('#cantidad').val();    
    var almacen = $('#almacen').val();
    var precio  = $('#costo').val();
    var usuario  = $('#usuario').val();
    var obse = $('#obse').val();
    var carac = $('#hiddenCarac_input').val();

    var idnewmerma = $('#idnewmerma').val();
    var proveedor = $('#proveedor').val();
    var tipo = $('#tipo').val();
    var tipoT = $("#tipo option:selected").text();
    var precio2  = $('#precio').val();

    var lote = $("#producto option:selected").attr('lote'); // para saber si tiene lote el producto
    var idlote = $('#hiddenLote_input').val();  // id de lote selecionado

    var costoT  = $('#costoT').val();    

    if(cantidad==0 || cantidad==''){
        alert('No puedes dejar vacio el campo cantidad o tiene que ser mayor a cero.');
        return false;
    }
    if(producto==0){
        alert('Debes de seleccionar un producto.');
        return false;
    }
    if(usuario==0){
        alert('Debes de seleccionar un usuario.');
        return false;
    }
    if(almacen==0){
        alert('Debes de seleccionar un almacen del que se dara de baja.');
        return false;
    }
    if(obse==''){
        alert('Debes de agregar observaciones.');
        return false;
    }

    if(proveedor==0){
        alert('Debes de seleccionar un proveedor.');
        return false;
    }
    if(tipo==0){
        alert('Debes de seleccionar un tipo.');
        return false;
    }



    $('#guardaDiv2').hide();
    $('#sded2').show();
    $.ajax({
        url: 'ajax.php?c=inventario&f=agregaMerma',
        type: 'POST',
        dataType: 'json',
        data: { producto: producto,
                cantidad : cantidad,
                almacen : almacen,
                precio : precio,
                usuario : usuario,
                obse : obse,
                carac : carac,
                proveedor : proveedor,
                tipo : tipo,
                tipoT : tipoT,
                precio2 : precio2,
                idlote : idlote
            },
    })
    .done(function(data) { 

        var TotalCosto = 0;       
        //var costoT = (precio*1) * (cantidad*1);    
        var i = 0;

        $("#tableMermas tr").each(function (index) 
        {         
            i++;          
        }); 
        
        $('#tableDiv').show('slow');
                $('#tableMermas tr:last').after('<tr idProducto="'+producto+'" caracteristicas="'+carac+'" idLote="'+idlote+'" proveedor="'+proveedor+'" id="'+i+'">'+
                                //'<td><span class="glyphicon glyphicon-remove" onclick="elimina('+producto+');"></span></td>'+
                                '<td><input id="usu_'+producto+'" type="hidden" value="'+usuario+'">'+data.usuario+'</td>'+
                                '<td>'+data.producto+'</td>'+
                                '<td><input id="almacen_'+producto+'" type="hidden" value="'+almacen+'">'+data.almacen+'</td>'+
                                '<td><input type="hidden" id="cant_'+producto+'" value="'+cantidad+'">'+cantidad+'</td>'+
                                '<td><input type="hidden" id="pre_'+producto+'" value="'+precio2+'"><input type="hidden" id="cost_'+producto+'" value="'+precio+'">'+precio+'</td>'+                                                                
                                '<td><input type="hidden" id="costT_'+producto+'" value="'+costoT+'">'+costoT+'</td>'+                        
                                '<td><input type="hidden" id="tipo_'+producto+'" value="'+tipo+'">'+tipoT+'</td>'+
                                '<td><input id="obser_'+producto+'" type="hidden" value="'+obse+'">'+obse+'</td>'+
                                '<td><input type="hidden" id="imagenDir_'+idnewmerma+'_'+i+'_'+producto+'" />  <button onclick="removeMerma('+i+')">X</button></td>'+
                                '</tr>'); 
        $('#sded2').hide();
        $('#guardaDiv2').show();
        $('#cantidad').val(1);
        $('#precio').val('');
        $('#obse').val('');
        $('#costo').val('');
        $('#costoT').val('');

        $('#producto > option[value="0"]').prop('selected', true);
        $('#almacen > option[value="0"]').prop('selected', true);
        $('#usuario > option[value="0"]').prop('selected', true);

        $('#proveedor > option[value="0"]').prop('selected', true);
        $('#tipo > option[value="0"]').prop('selected', true);

        $('#producto').select2();
        $('#usuario').select2();
        $('#almacen').select2();

        $('#proveedor, #tipo').select2();


        //i = 0;
        $("#tableMermas tr").each(function (index) 
        { 
            idProducto = $(this).attr('idProducto');         
            //i++;
            if(idProducto !== undefined){
                TotalCosto += ($('#costT_'+idProducto).val()) * 1;                
            }            
        });            
        $("#costoTotal").val(TotalCosto);

    })
    .fail(function(resp) {
        console.log("error");
        console.log(resp);
    })
    .always(function() {
        console.log("complete");
    });
    
}
function removeMerma(i){
    $("#"+i).remove();
    var TotalCosto = 0;
    $("#tableMermas tr").each(function (index) 
        { 
            idProducto = $(this).attr('idProducto');         
            i++;
            if(idProducto !== undefined){
                TotalCosto += ($('#costT_'+idProducto).val()) * 1;                
            }            
        });            
    $("#costoTotal").val(TotalCosto);
}
function imageMerma(i,producto){    
    $("#modalimage").modal('show');
    $("#i").val(i);
    $("#productoi").val(producto);
    var idnewmerma = $("#idnewmerma").val();    
    var src = $("#imagenDir_"+idnewmerma+"_"+i+"_"+producto).val();
    $('#imagenMerma').attr("src",src);
    
}
function saveMerma(){
    var productos = '';
    var f = 0;
    var idnewmerma = $("#idnewmerma").val();
    $("#tableMermas tr").each(function (index) 
    {   //console.log($("#tablita input:hidden"));
        f++;
        carac = $(this).attr('caracteristicas') != '' ? $(this).attr('caracteristicas') : '0';
        idLote = $(this).attr('idlote') != '' ? $(this).attr('idlote') : '0';
        idProducto = $(this).attr('idProducto');        
        cantidad = $('#cant_'+idProducto).val();
        precio = $('#cost_'+idProducto).val();
        precio2 = $('#pre_'+idProducto).val();
        usuario = $('#usu_'+idProducto).val();
        almacen = $('#almacen_'+idProducto).val();
        obser = $('#obser_'+idProducto).val();
        
        costoT = $('#costT_'+idProducto).val();
        //imagen = $('#imagenDir_'+idnewmerma+'_'+f+'_'+idProducto  ).val();
        proveedor = $(this).attr('proveedor');
        
        tipo = $('#tipo_'+idProducto).val();        
        if(cantidad > 0){
           
            subtotal = parseFloat(precio) * parseFloat(cantidad);
            productos +=idProducto+'-'+cantidad+'-'+precio+'-'+usuario+'-'+almacen+'-'+obser+'-'+carac+'-'+tipo+'-'+idLote+'-'+proveedor+'-'+precio2+'-'+costoT+'|';
        }
    });
    if(f < 2){
        alert('debe agregar merma');
        return false;
    }
    
    //alert(productos);  
    $('#guardaDiv').hide();
    $('#sded').show();
    $.ajax({
        url: 'ajax.php?c=inventario&f=guardaMerma',
        type: 'POST',
        dataType: 'json',
        data: {productos: productos, idnewmerma:idnewmerma},
    })
    .done(function(data) {
        console.log(data);
        if(data.idMerma!=0){
            alert('Se guardo la merma');
            cambia2();
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}
function cambia1(){
    var pathname = window.location.pathname;
    window.location = window.location.protocol + '//'+document.location.host+pathname+'?c=inventario&f=mermasForm';
}
function cambia2(){
    var pathname = window.location.pathname;
    window.location = window.location.protocol + '//'+document.location.host+pathname+'?c=inventario&f=indexGridMermas';
}
function inicial(){

    var  idProducto = $('#producto').val();
    var idalmacen = $('#almacen').val();
    var desde = '';
    var hasta = '';
    var R1 = '';
    var iddep = '';
    var idfa = '';
    var idli = '';

    $.ajax({
        url: 'ajax.php?c=inventario&f=inicialInventario',
        type: 'post',
        dataType: 'json',
        data: {idProducto: idProducto,
               idalmacen : idalmacen,
               desde : desde,
               hasta : hasta,
               R1 : R1,
               iddep : iddep,
               idfa : idfa,
               idli : idli
        }
    })
    .done(function(data) {
        console.log(data);
           var table = $('#tableKardex').DataTable();
    
            //$('.filas').empty();
            table.clear().draw();
            var x ='';
            var tipo = '';
            $.each(data.invent, function(index, val) {
               /* if(val.tipo_traspaso=='0'){
                    tipo = '<span class="label label-warning">Salida</span>';
                }else if(val.tipo_traspaso=='1'){
                    tipo = '<span class="label label-success">Entrada</span>';
                }else{
                    tipo = '<span class="label label-primary">Traspaso</span>';
                }

                if(val.destino==null){
                    destino = '';
                }else{
                    destino = val.destino;
                }

                if(val.origen==null){
                    origen = '';
                }else{
                    origen = val.origen;
                } */

                x ='<tr>'+
                                '<td>'+val.id+'</td>'+
                                '<td>'+val.nombre+'</td>'+
                                '<td>'+val.codigo+'</td>'+
                                '<td>'+val.unidad+'</td>'+
                                '<td>'+val.existencia+'</td>'+
                                '<td>'+val.almacenNombre+'</td>'+
                                '<td><button class="btn btn-primary btn-block" onclick="seeMovs('+val.idProd+');" type="button"><i class="fa fa-list-ul"></i> Detalle</button></td>'+
                               /* '<td>'+destino+'</td>'+
                                '<td>'+tipo+'</td>'+
                                '<td>'+val.costo+'</td>'+
                                '<td>'+val.referencia+'</td>'+
                                '<td>'+val.usuario+'</td>'+
                                '<td>'+val.fecha+'</td>'+ */
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
    
}
function seeMovs(id){
    //alert(id);
    mensaje('Procesando...');
    $.ajax({
        url: 'ajax.php?c=inventario&f=movsProducto',
        type: 'post',
        dataType: 'json',
        data: {id: id},
    })
    .done(function(data) {
        console.log(data);
        $('#prodNombre').text(data.producto);
        $('#idProdIn').val(id);
            var table = $('#tableMovs').DataTable();
    
            //$('.filas').empty();
            table.clear().draw();
            var x ='';
            var tipo = '';

            $.each(data.movimientos, function(index, val) {
                if(val.tipo_traspaso=='0'){
                    tipo = '<span class="label label-warning">Salida</span>';
                }else if(val.tipo_traspaso=='1'){
                    tipo = '<span class="label label-success">Entrada</span>';
                }else{
                    tipo = '<span class="label label-primary">Traspaso</span>';
                }

                if(val.destino==null){
                    destino = '';
                }else{
                    destino = val.destino;
                }

                if(val.origen==null){
                    origen = '';
                }else{
                    origen = val.origen;
                }

                x ='<tr>'+       
                                '<td>'+val.id+'</td>'+
                                '<td>'+tipo+'</td>'+
                                '<td align="center">'+val.cantidad+'</td>'+
                                //'<td>'+val.importe+'</td>'+
                                '<td>'+origen+'</td>'+
                                '<td>'+destino+'</td>'+
                                '<td>$'+val.costo+'</td>'+
                                '<td>'+val.referencia+'</td>'+
                                '<td>'+val.usuario+'</td>'+
                                '<td>'+val.fecha+'</td>'+
                                '</tr>';  
                    table.row.add($(x)).draw();                          
            }); 
        eliminaMensaje();
        $('#modalMovimientosDetalle').modal({
            show:true,
        });
        

    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}
    function mensaje(mensaje) {

        $('#lblMensajeEstado').text(mensaje);
        $('#modalMensajes').modal({
                        show:true,
                        keyboard: false,
                    });
    }    
    function eliminaMensaje() {

        $('#modalMensajes').modal('hide');
    }
function ajustarInveModal(){
    var producto = $('#idProdIn').val();

    $('#prodAjustar').val(producto);
    $('#modalAjuste').modal({
        show:true,
    });

}
function ajustarInve(){
    var producto  = $('#prodAjustar').val();
    var almacen = $('#almacen2').val(); 
    var movimiento = $('#tipoMov').val();
    var cantidad = $('#cantidad').val();
    var costo = $('#costo').val();
    var obser = $('#obser').val();
    if(almacen==0){
        alert('Selecciona el Almacen a afectar.');
        return false;
    }
    if(costo==0 || costo==''){
        alert('El costo debe ser mayor a 0.');
        return false;
    }
    if(cantidad==0 || cantidad==''){
        alert('El costo debe ser mayor a 0.');
        return false;
    }
    if(obser==''){
        alert('Debes de agregar un comentario.');
        return false;
    }

    mensaje('Procesando...');
    $.ajax({
        url: 'ajax.php?c=inventario&f=ajustarInve',
        type: 'POST',
        dataType: 'json',
        data: {producto: producto,
                almacen : almacen,
                movimiento : movimiento,
                cantidad : cantidad,
                costo : costo,
                obser : obser
            },
    })
    .done(function(resp) {
        console.log(resp);
        eliminaMensaje();
        if(resp.estatus==true){
            $('#modalMovimientosDetalle').modal('hide');
            inicial();
            $('#modalAjuste').modal('hide');

        }else{
            alert('Erro al realizar el movieminto, contacta al area de soporte.');
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}

function etiquetar(){
    mensaje('Procesando...');

    setTimeout(function(){ 
        var oTable = $('#tableGrid').dataTable();
        var allPages = oTable.fnGetNodes();
        var proString = '';
        var contenido = ''
        var cLabel = $('#colorLabel').val();
        var cLetter = $('#colorLetter').val();
        var cantidad = $('#cantidad').val();

        if(cantidad < 1){
            alert('Tienes que hacer almenos una etiqueta');
            return false;
        }

        if($('#checkLabel').is(':checked')){
            checkEtiquetaCon = 1
        }else{
            checkEtiquetaCon = 0;
        }
        if($('#checkNombre').is(':checked')){
            checkNombreCon = 1
        }else{
            checkNombreCon = 0;
        }
        if($('#checkCaracteriricas').is(':checked')){
            checkCaracteriricasCon = 1
        }else{
            checkCaracteriricasCon = 0;
        }
        if($('#checkPrecio').is(':checked')){
            checPrecioCon = 1
        }else{
            checPrecioCon = 0;
        }
        if($('#checkCodigo').is(':checked')){
            checCodigoCon = 1
        }else{
            checCodigoCon = 0;
        }

   //alert(checCodigoCon); 
   //alert(checCodigoCon);
/*var pathname = window.location.pathname;
//labelPrints = 'http://'+document.location.host+pathname+'?c=inventario&f=labelPrintFile';
labelPrints = 'http://'+document.location.host+pathname+'?c=inventario&f=labelPrintFile';
//alert(labelPrints);
//var popup = window.open(labelPrints, "_blank");
 //popup.document.write('<div id="contenidoLabels"><h1>okodkeodoedkoekdokeooooooo</h1></div>');

//$("#titulo",popup).append("<h1>Si se pudo</h1>"); 

var w = window.open();
var html = $('#colorEtique').html();

$(w.document.head).html(html); */
$('#divLabels').html('');
var contador = 0;
$('input[name="prods"]', allPages).each(function () {
    if( $(this, allPages).prop('checked') ) {
        idProducto = $(this, allPages).val();
        if(idProducto!=''){
          nombrePro = $('#nom_'+idProducto, allPages).val();
          precio = $('#price_'+idProducto, allPages).val();
          codigo = $('#cod_'+idProducto, allPages).val();
          desc = $('#des_'+idProducto, allPages).val();
          carac = $('#carac_'+idProducto, allPages).val();
          cant = $('#cant_'+idProducto, allPages).val();

          if(desc!=''){
            nombre = desc;
        }else{
            nombre = nombrePro;
        }

        cantTmp = (cant) ? cant : cantidad;
        for (var i = 0; i < cantTmp; i++) {
            
            if(contador==18){
                contenido +='<div class="col-sm-12 saltopagina"></div>'
                contador = 0;
            }contador++;
                //contenido +='<div style="width:33.3%;float:left;">'
                contenido +='<div class="col-sm-4">'
                contenido +=                            '<div class="row">'
                contenido +=                                '<div class="col-sm-12">'
                contenido +=                                   '<div class="colorEtiqueClass" id="colorEtique" style="background:'+cLabel+';border: 2px solid;border-radius: 25px;">'
                contenido +=                                        '<div class="row nombreClass">'
                contenido +=                                            '<div class="col-sm-12">'
                contenido +=                                                '<h4 class="letra" align="center" style="color:'+cLetter+';">'+nombre+'</h4>'
                contenido +=                                           '</div>'
                contenido +=                                        '</div>'
                contenido +=                                        '<div class="row caractClass">'
                contenido +=                                            '<div class="col-sm-12">'
                contenido +=                                               '<h6 class="letra" align="center" style="color:'+cLetter+'">'+( (carac) ? carac : "" )+'</h6>' 
                contenido +=                                            '</div>'
                contenido +=                                       '</div>'
                contenido +=                                        '<div class="row precioClass">'
                contenido +=                                            '<div class="col-sm-12">'
                contenido +=                                               '<h3 class="letra" align="center" style="color:'+cLetter+'">$'+precio+'</h3>' 
                contenido +=                                            '</div>'
                contenido +=                                       '</div>'
                contenido +=                                   '</div>'
                contenido +=                                   '<div class="row codigoClass">'
                contenido +=                                        '<div class="col-sm-12">'
                contenido +=                                            '<div align="center"><svg id="barcode_'+idProducto+'" aling="center"></svg></div>'
                contenido +=                                        '</div>'
                contenido +=                                    '</div>'
                contenido +=                                '</div>'                                                   
                contenido +=                           '</div>'
                contenido +=                       '</div>';
                //contenido +='</div>'
            }//fin del for
            $('#divLabels').append(contenido);
            JsBarcode("#barcode_"+idProducto, codigo,{width: 1,height:70});
            contenido='';

        }
    }
}); 
$('#divLabels2').html('');
var contador = 0;
$('input[name="prods"]', allPages).each(function () {
    if( $(this, allPages).prop('checked') ) {
        idProducto = $(this, allPages).val();
        if(idProducto!=''){
          nombrePro = $('#nom_'+idProducto, allPages).val();
          precio = $('#price_'+idProducto, allPages).val();
          codigo = $('#cod_'+idProducto, allPages).val();
          desc = $('#des_'+idProducto, allPages).val();
          carac = $('#carac_'+idProducto, allPages).val();
          cant = $('#cant_'+idProducto, allPages).val();

          if(desc!=''){
            nombre = desc;
        }else{
            nombre = nombrePro;
        }

        cantTmp = (cant) ? cant : cantidad;
        for (var i = 0; i < cantTmp; i++) {
            
            console.log(contador)
            if(contador==18){
                contenido +='<div class="saltopagina"><br></div>'
                console.log(contenido)
                contador = 0;
            }contador++;
            contenido +='<div style="width:32%;float:left;padding-left:1%;font-family:arial,helvetica;font-size:12px;">'
            contenido +='<div class="col-sm-4">'
            contenido +=                            '<div class="row">'
            contenido +=                                '<div class="col-sm-12">'
            contenido +=                                   '<div class="colorEtiqueClass" id="colorEtique" style="background:'+cLabel+';border: 2px solid;border-radius: 25px;">'
            contenido +=                                        '<div class="row nombreClass">'
            contenido +=                                            '<div class="col-sm-12">'
            contenido +=                                                '<h4 class="letra" align="center" style="color:'+cLetter+';">'+nombre+'</h4>'
            contenido +=                                           '</div>'
            contenido +=                                        '</div>'

            contenido +=                                        '<div class="row caractClass">'
            contenido +=                                            '<div class="col-sm-12">'
            contenido +=                                               '<h6 class="letra" align="center" style="color:'+cLetter+'">'+( (carac) ? carac : "" )+'</h6>' 
            contenido +=                                            '</div>'
            contenido +=                                       '</div>'

            contenido +=                                        '<div class="row precioClass">'
            contenido +=                                            '<div class="col-sm-12">'
            contenido +=                                               '<h3 class="letra" align="center" style="color:'+cLetter+'">$'+precio+'</h3>' 
            contenido +=                                            '</div>'
            contenido +=                                       '</div>'

            contenido +=                                   '</div>'
            contenido +=                                   '<div class="row codigoClass">'
            contenido +=                                        '<div class="col-sm-12">'
            contenido +=                                            '<div align="center"><svg id="barcode_'+idProducto+'" aling="center"></svg></div>'
            contenido +=                                        '</div>'
            contenido +=                                    '</div>'
            contenido +=                                '</div>'                                                   
            contenido +=                           '</div>'
            contenido +=                       '</div>';
            contenido +='</div>'

            

            }// fin del for
            $('#divLabels2').append(contenido);
            JsBarcode("#barcode_"+idProducto, codigo,{width: 1, height:65});
            contenido='';
            
        }
    }
});        



if(checkEtiquetaCon==0){
    $('.colorEtiqueClass').hide();
}
if(checkNombreCon==0){
    $('.nombreClass').hide();
}
if(checkCaracteriricasCon==0){
    $('.caractClass').hide();
} 
if(checPrecioCon==0){
    $('.precioClass').hide();
}
if(checCodigoCon==0){
    $('.codigoClass').hide();
}  



eliminaMensaje();
setTimeout(function(){
 $('#modalCodigos').modal({
    show:true,
}); 
}, 500);

   //console.log(proString);
   //alert(proString);


}, 1000);
//alert('entro15');


}
function allsi(){
    //alert('uijuy69');

    var oTable = $('#tableGrid').dataTable();
    var allPages = oTable.fnGetNodes();


    if($("#todos").is(':checked'))
    {
        //$(".checkPro", allPages).click();
        //$('#check_20').prop('checked', true);
        $('input[type="checkbox"]', allPages).prop('checked', true);
    }
    else
    {
        $('input[type="checkbox"]', allPages).prop('checked', false);
    } 
}

    function imprimeLabels() {
    //alert('aver que pedo4');
      var ficha = $('#divLabels2').html();
      //alert(ficha);
      //var ventimp = window.open(' ', 'popimpr');
      //ventimp.document.write('<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"><script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script><script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>');
      //ventimp.document.write('<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">')
      //ventimp.document.write('<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">');
      //ventimp.document.write('<link rel="stylesheet" href="imprimir_bootstrap.css">');
      //ventimp.document.write(' <div class="row"><div class="col-sm-12"><h1 align="center">Etiquetas</h1></div></div>');
      //ventimp.document.write(' <div class="row"><div class="col-sm-12"><div align="center"><button class="btn btn-default" onclick="javascript:window.print();">Imprimir</button></div></div></div>');
      //ventimp.document.write('<style>@media print{.saltopagina{display:block !important;page-break-after:always !important; width:100% !important;}</style>');

      //ventimp.document.write(ficha);
      //ventimp.document.close();
      //ventimp.print( );


      var win = window.open('','printwindow');
            win.document.write('<html><head><title>Netwarmonitor</title><link rel="stylesheet" type="text/css" href="styles.css">');
            win.document.write('<style>@media print{.saltopagina{display:block !important;page-break-after:always !important; width:100% !important;}</style>');
            win.document.write('</head><body>');
            win.document.write(ficha);
            win.document.write('</body></html>');
        setTimeout(
          function() 
          {
            win.print();
          }, 999);

        
      //ventimp.close();
    }

    function buscaLotes(){
        mensaje('Procesando...');
    }

    function buscaCaracteristicas(){
        mensaje('Procesando...');
        var id= $('#producto').val(); 
        var idProv= $('#proveedor').val();   
        var lote = $("#producto option:selected").attr('lote');
        var text = $("#producto option:selected").text();

        

            $.ajax({
                url: 'ajax.php?c=inventario&f=obtenCaracteristicas',
                type: 'POST',
                dataType: 'json',
                data: {id: id,
                    cantidad: 0,idProv:idProv},
            })
            .done(function(result) {
                console.log(result);                
                $("#tipoproducto").val('');
                $("#tipoproducto").val(result.tipo);

                var cantidad = $("#cantidad").val();

                $('#uventa').val(result.uventa).attr('readonly', 'true');

                $('#costo').val(result.costo).attr('readonly', 'true');
                $('#factor').val(result.factor).attr('readonly', 'true');
                $('#costoT').val(result.factor*result.costo*cantidad).attr('readonly', 'true');
                $('#precio').val(result.precio).attr('readonly', 'true');

                /// CARACTERISTICAS
                        if(result.tieneCar > 0 && lote != 1){                            

                                $('#prodCarcDiv').empty();
                                var contenido = '';
                                $.each(result.cararc, function(index, val) {
                                     //alert(index);
                                     contenido += '<div class="row">';
                                     //contenido += '<div class="col-sm-6">';
                                     //contenido +='</div>';
                                     contenido += '<div class="col-sm-12">';
                                     contenido +' <label>'+index+'</label>';
                                     //contenido += '<select class="form-control recr" onchange="getExisCara();">';
                                     contenido += '<select class="form-control recr">';
                                     $.each(val, function(index2, val2) {
                                          contenido +='<option value="'+val2.id_caracteristica_padre+'=>'+val2.id+'">'+val2.nombre+'</option>';
                                     });
                                     contenido +='</select>';
                                     contenido +='</div></div>';
                                    
                                });
                                /*contenido += '<div class="row"><div class="col-sm-6">';
                                contenido +='<label>Existencia:</label></div>';
                                contenido +='<div class="col-sm-6">';
                                contenido +='<label id="exiCaracText"></label>';
                                contenido +='<input type="hidden" id="exiCaracInput">';
                                contenido +='</div></div>'; */


                                $('#carIdProddiv').val(id);
                                $('#prodCarcDiv').append(contenido);
                                $('#modalCarac').modal({
                                    show:true,
                                }); 

                                $('#divImagenPro').attr("src", '../pos/'+result.imagen);
                                $('#modal-labelCr').text(result.nombreProd);

                                //getExisCara();
                                eliminaMensaje();
                                //alert('prueba');

                                salir = 1;
                                //alert('salir1='+salir);
                                ////lotes
                               /*     var contenido2 = '';
                                    var options='';
                                    $.each(result.lotes, function( k, v ) {
                                        alert(v.idLote);
                                        options+='<option value="'+v.idLote+'">'+v.numero+' ('+v.cantidad+')</option>';
                                    });

                                    contenido2 += '<div class="row"><div class="col-sm-6">';
                                    contenido2 +='<label>Prosucto lote</label></div>';
                                    contenido2 += '<div class="col-sm-6">';
                                    contenido2 +='<select id="lotes" multiple="" class="selectpicker">';
                                    contenido2 +=options+'</select>';
                                    contenido2 +='</div></div>';
                                $('#prodCarcDiv').append(contenido2);
                                $('#modalCarac').modal({
                                    show:true,
                                });
                                $('#lotes').select2({width : '100%'}); 
                            */
                        }
                /// LOTES
                        if(lote == 1){                            
                            $('#prodLoteDiv').empty();
                            var contenido = '';
                            console.log(result.lotes);
                            contenido += '<div class="row">';
                            contenido += '<div class="col-sm-12">';
                            contenido +=' <label>Lotes</label><br>';                                     
                            contenido += '<div ><select id ="selectlote" onchange="chagelote('+id+')">';
                            contenido +='<option value="0">Selecciona un Lote</option>';
                                     
                             $.each(result.lotes, function(index, val) {
                                  contenido +='<option value="'+val.id+'">'+val.no_lote+'</option>';
                             });
                             
                             contenido +='</select></div>';
                             contenido +='</div></div>'; 
                             contenido +='<br><br><br><br><div class="row"><label id="lbexislote"></label> <input id="inpexisLote" type="hidden" value=""/></div>'; 

                                                        
                            $('#lotIdProddiv').val(id);
                            $('#prodLoteDiv').append(contenido);
                            $('#modalLote').modal({show:true}); 

                            $('#divImagenProLot').attr("src", '../pos/'+result.imagen);
                            $('#modal-labelLt').text(text);
                            $("#selectlote").select2();

                        }
                        else{                            
                            //caja.agregaProducto(id,'');
                           
                            $('#hiddenCarac_input').val('');
                            $('#hiddenLote_input').val('');
                        }
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
    }   
    function chagelote(idProducto){
        var idlote = $("#selectlote").val();
        $.ajax({
                url: 'ajax.php?c=inventario&f=exisLote',
                type: 'POST',                
                data: {idProducto:idProducto,idlote: idlote},
            })
            .done(function(existencia) {
                $('#lbexislote').text('Existencia:'+existencia);
                $('#inpexisLote').val(existencia);                
            });
        
    }    
    function getExisCara(){
        var a = '';
        var idProducto = $('#carIdProddiv').val();
        $(".recr").each(function() {
            a += $(this).val()+',';
        });
     
        $.ajax({
            url: 'ajax.php?c=caja&f=getExisCara',
            type: 'post',
            dataType: 'json',
            data: { a : a,
                    producto : idProducto},
        })
        .done(function(respExisCar) {
           $('#exiCaracInput').val(respExisCar.cantidadExis);
           $('#exiCaracText').text(respExisCar.cantidadExis);
        })
        .fail(function() {
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });
    }
    function agregaCarac(){
        var a = '';
        var idProducto = $('#carIdProddiv').val();
        $(".recr").each(function() {
            a += $(this).val()+'*';
        });
       /* var can1 = $('#cantidad-producto').val();
        var can = $('#exiCaracInput').val();
        if(parseFloat(can1)>parseFloat(can)){
            alert('No tienes la existencia suficiente.');
            return false;
        } */
        $('#modalCarac').modal('hide');
        $('#hiddenCarac_input').val(a);
    }
    function agregaLote(){
        var selectlote = $("#selectlote").val();
        if(selectlote == 0){
            alert('Selecione un Lote');
            return 0;
        }        
        var exisLote = $("#inpexisLote").val();        
        $('#modalLote').modal('hide');
        $('#hiddenLote_input').val(selectlote);
        $('#hiddenExis_input').val(exisLote);    
    }    

    function guardartipo(){       
        var tipo = $("#tipoMerma").val();
        if(tipo != ''){
            $.ajax({
                url: 'ajax.php?c=inventario&f=guardartipo',
                type: 'post',
                dataType: 'json',
                data: {tipo:tipo},
            })
            .done(function(resp) {
                console.log(resp);
                if(resp.exist == 1){
                    alert('Ya exsite');
                    return 0;
                }else{
                    $("#tipo").html('');            
                    $("#tipo").append('<option value="0">-Selecciona un Tipo-</option>');  
                    $.each(resp.tipos, function(index, val) {
                        if(resp.idTipo == val.id){
                            $("#tipo").append('<option value="'+val.id+'" selected>'+val.tipo_merma+'</option>');  
                        }else{
                            $("#tipo").append('<option value="'+val.id+'">'+val.tipo_merma+'</option>');
                        }
                        
                    });
                    $("#tipo").select2();
                    $("#modaltipo").modal('hide');
                    return 0;
                }
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        }else{
            alert('Debe ingresar un tipo');
        }
    }






