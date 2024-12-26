<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Caja 3.0</title>
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../libraries/font-awesome-4.7.0/css/font-awesome-4.7.min.css">
    <link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="../../libraries/typeahead/typeahead.css">
    <script src="../../libraries/jquery.min.js"></script>
    <script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../../libraries/numeric.js"></script>
    <script src="js/ticket.js"></script>
    <script src="js/caja2.js"></script>
    <script src="../../libraries/numeric/jquery.numeric.js"></script>
    <script src="../../libraries/typeahead/typeahead.js"></script>
    <!--Select 2 -->
    <script src="../../libraries/select2/dist/js/select2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

    <link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
    <!-- <script src="../../libraries/dataTable/js/datatables.min.js"></script> -->
    <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>

    <!-- Modificaciones RCA -->
    <script src="../../libraries/export_print/jquery.dataTables.min.js"></script>
    <script src="../../libraries/export_print/dataTables.buttons.min.js"></script>
    <script src="../../libraries/export_print/buttons.html5.min.js"></script>
    <script src="../../libraries/export_print/jszip.min.js"></script>

	<!-- Notify  -->
	<script src="../../libraries/notify.js"></script>
    <script>
    $(document).ready(function() {
        
        $.fn.modal.Constructor.prototype.enforceFocus = function () {};
        //alert($('#pb').height());
        totales = $('#pb').height() * 100;
        xer = $('#pb').height() * 70;
        xer = xer / 100;
        divCliTota = totales / 70;
        productos = totales / 70;
        $('#productosDivList').height(xer-50);
        $('#totalesCliente').height(productos);
        altura = $('#pb').height() - 50;
        $('#containerTouch').height(altura)
        //$('#leftSide').height(altura)
        $('#monedaVenta').select2({width:'100%'});
        $('.arre').select2({width:'100%'});
        $('#cargosAbono').select2({width:'100%'});
         $('#clienteAbono').select2({width:'100%'});
         $('#lotes').select2({ width: '100%' });
         $('#prodPronti').select2({ width: '100%' });

        $('#usoCfdi').select2({ width: '100%' });
        $('#mpCat').select2({ width: '100%' });
        $('#tipoRelacionCfdi').select2({ width: '100%' });

         //$("#series").select2({width:'100%'});


    $("#tpin").keypress(function(e) {
       if(e.which == 13) { 

          var tipoPago = $('#cboMetodoPago').val();
          var numTarjeta = $('#tpin').val();

          //if(tipoPago == 3){
                $.ajax({
                    url: 'ajax.php?c=caja&f=getPointsCard',
                    type: 'POST',
                    dataType: 'json',
                    data: {numTarjeta: numTarjeta},
                })
                .done(function(data) {
                    console.log(data);
                    if(data.estatus==true){
                       // alert(data.puntos);
                    //if(data.usada!=1){
                    if(data.puntos>=0){
                        $('#puntosCheck').show('slow');
                        $('#pointsCardT').text(data.puntos)
                        $('#pointsCardIn').val(data.puntos)
                        $('#pointsCardInActiv').val(data.usada)
                        $('#cliente-caja').val(data.nombreCliente);
                        $('#clienteName').text(data.nombreCliente);
                        $('#hidencliente-caja').val(data.idCliente);
                        caja.checatimbres(data.idCliente);
                    }else{
                        alert('Esta tarjeta no tiene credito.');
                        $('#tpin').val('');
                        return false;
                    }

                    }else{
                        alert('La tarjeta no existe.');
                        $('#tpin').val('');
                        $('#txtReferencia').val('');
                    }
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
                   /* $.ajax({
                        url: 'ajax.php?c=caja&f=getPointsCard',
                        type: 'POST',
                        dataType: 'json',
                        data : { numTarjeta: numTarjeta }
                        success: function(data)
                        {
                            if(data.estatus==true){
                                $('#puntosCheck').show('slow');
                            }else{
                                alert('La tarjeta no existe.');
                                $('#txtReferencia').val('');
                            }

                        }
                    }); */
         // }
       }
    });
        //$('#cliente-caja').select2({width:'100%'});
        $( document ).keydown(function(e) {
        keyCode = e.which;
        if (keyCode == 13 && (!$('#search-producto').is( ":focus" ) && $('.typeahead').is( ":focus" )))
        {
            //alert("El campo de busqueda no tenia el foco intentelo nuevamente.");
            $('#search-producto').focus();
        }
        if(keyCode == 27){
          
            $('#search-producto').focus();
        }
        //alert($("#modalComprobante").is(":visible"));
        //var xyz = $("#modalComprobante").is(":visible");
        //alert(xyz)
        $('#modalComprobante').on('hidden.bs.modal', function () {
                javascript:window.location.reload();
            })
    });


    $('#usarPuntos').change(function() {
        var usada = $('#pointsCardInActiv').val();
        if(usada == 1){
            alert('Tarjeta Invalida');
            return 0;
        }
        if($(this).is(":checked")) {
            //alert(<?php echo $_SESSION['caja']['cargos']['total']; ?>);
            //var total = <?php echo $_SESSION['caja']['cargos']['total']; ?>;
            if($('#tpin').val()==''){
                alert('Necesitas agregar una tarjeta valida.');
                return false;
            }
            var total = $('#lblTotalxPagar').text();

            total = total * 1;
            var puntosTar = $('#pointsCardIn').val();

            var cant = 0;
            if(total > puntosTar){

                cant = puntosTar;
                //$('#pointsCardIn').val('0');
                //$('#pointsCardT').text('0.00');
            }else{

                cant = total * 1;
                //$('#pointsCardIn').val(puntosTar - (total * 1));
                //$('#pointsCardT').text(puntosTar - (total * 1));
            }

            $('#modTarRegPuntos').val(cant);
            $('#modalTarjetaRegalo').modal({backdrop: 'static', keyboard: false});
          //caja.agregarPago(3,'(99) Tarjeta de regalo',cant,$('#tpin').val());

        }else{
            caja.eliminarPago(3);

            var tipoPago = $('#cboMetodoPago').val();
            var numTarjeta = $('#tpin').val();

          //if(tipoPago == 3){
                $.ajax({
                    url: 'ajax.php?c=caja&f=getPointsCard',
                    type: 'POST',
                    dataType: 'json',
                    data: {numTarjeta: numTarjeta},
                })
                .done(function(data) {
                    console.log(data);
                    if(data.estatus==true){
                        //alert('4343');
                       // alert(data.puntos);
                        $('#puntosCheck').show('slow');
                        $('#pointsCardT').text(data.puntos)
                        $('#pointsCardIn').val(data.puntos)
                    }else{
                        alert('La tarjeta no existe.');
                        $('#txtReferencia').val('');
                    }
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
        }
    });


            caja.init();
            $('.numeros').numeric();
            $('#tableSales').DataTable({
                            dom: 'Bfrtip',
                            buttons: [ 'excel' ],
                            language: {
                            search: "Buscar:",
                            lengthMenu:"",
                            zeroRecords: "No hay datos.",
                            infoEmpty: "No hay datos que mostrar.",
                            info:"Mostrando del _START_ al _END_ de _TOTAL_ elementos",
                            paginate: {
                                first:      "Primero",
                                previous:   "Anterior",
                                next:       "Siguiente",
                                last:       "Último"
                            },
                         },
                          aaSorting : [[0,'desc' ]]
            });
            if($('#saldoDisponibleHide').val()) {
                alert("Has sobrepasado el limite de monto disponible en caja");
                caja.formRetiro();
            }
 
    });
    function cambiaVista(){

        $.ajax({
            url: 'ajax.php?c=caja&f=updatevista',
            type: 'POST',
            dataType: 'json',
            success: function(data)
            {
                if(data.vista==1){
                    $('#viewTouch').show('slow');
                    $('#normalView').hide('slow');

                    $('#leftSide').addClass('col-sm-5');
                    $('#leftSide').removeClass('col-sm-12');
                    $('#containerTouch').show('slow');

                }else{

                    $('#leftSide').removeClass('col-sm-5');
                    $('#leftSide').addClass('col-sm-12');
                    $('#containerTouch').hide('slow');

                }

            }
        });

	}
	function muestraComandas(){
		$('#divComandas').toggle('slow');
		$('#containerTouch').toggle('slow');
	}
    function modalDesc(){
        $('#modalDesgeneralBot').modal();
    }
    function cierraModDes(){
        $('#modalDesgeneralBot').modal('hide');
        caja.aplicaDescuento();
    }
    function prontipagos(){
         $('#modalProntipagos').modal();
    }


    function preticket(){
        $.ajax({
            url: 'ajax.php?c=caja&f=escomanda',
            dataType: 'html',
            async: false,
            success: function(resp){

                if(resp == 0){
                    alert('No es Comanda');
                    return false;
                }else{
                    // Ejecuta los scripts de la comanda
                        $("#div_ejecutar_scripts").html(resp);
                    //abrimos una ventana vacÃ­a nueva
                        var ventana = window.open('', '_blank', 'width=207.874015748,height=10,leftmargin=8px,rightmargin=8px');

                    $(ventana).ready(function() {

                        // Cargamos la vista ala nueva ventana
                        ventana.document.write(resp);
                        // Cerramos el documento
                        ventana.document.close();

                        setTimeout(closew, 3000);

                        function closew() {
                            ventana.print();

                            ventana.close();

                            caja.cancelarCaja();

                        };
                    });
                }

            }
        });
    }



    function cambiolote(){

        lotes = $('#lotes').val();
        console.log(lotes);
        if(lotes!='' && lotes!=null){
            $('#newlotes').html('');
            $.each(lotes, function( k, v ) {
                console.log(v);
                separa=v.split('-#*-');
                separa2=v.split('-');

                $('#newlotes').append('<div class="divlotes row" style="padding-top:10px;">\
                            <div class="col-sm-6"><label class="control-label text-left">Cant: '+separa[1]+':</label></div>\
                            <div class ="col-sm-6">\
                              <input class="quantity form-control" data="'+separa2[0]+'-'+separa2[1]+'" type="number" min="1" max="'+separa2[2]+'" value="1"/>\
                            </div>\
                        </div>');
                $('.quantity').numeric();
            });

        }else{
            $('#newlotes').html('');
        }

    }
    function enterTpin(){
        // Creamos el evento
        e = jQuery.Event("keypress")
        // Indicamos la tecla que deseamos que se pulse de manera automatica
        e.which = 13
        $("#tpin").keypress().trigger(e);
    }

    </script>
 <?php
    session_start();
// Valida si la instancia tiene Foodware, para buscar las comandas
    if (in_array(2156, $_SESSION['accelog_menus'])) { ?>
        <script>
            function buscar_comandas() {
                var $objeto = {};
                $objeto['individual'] = 3;
                $objeto['status'] = 2;
                $objeto['json'] = 1;
                $objeto['div'] = 'listar_comandas_pendientes';
                caja.listar_comandas($objeto);
            }

        // Dispara la funcion al iniciar
            buscar_comandas();

        // Busca las comandas cada 20 segundos
            setInterval(buscar_comandas, 20000);
        </script><?php
    } ?>


<style>
/*	thead {
       background-image: linear-gradient(to bottom, #A5AD6D 0px, #A5AD6D 100%);
    	background-repeat: repeat-x;
    	border-color: #A5AD6D;
    }
	.footer {
	  position: absolute;
	  right: 0;
	  bottom: 0;
	  left: 0;
	  padding: 1rem;
	  background-color: #efefef;
	  text-align: center;
	}
.panel-default > .panel-heading {
  color: #333 !important;
  background-color: #f5f5f5 !important;
  border-color: #ddd !important;
}*/
#cliente-caja, #search-producto{
    font-size: 80%;
}
html,body {
    height:100%;
}

.container,.panel {
    height:100%;
}
.divPuntos{
    display: none;
}
.itemsProds{
    position: relative;
    top: 0px;
    right: 0px;
    padding:4px;
    width: 110px;
    height: 110px;
   /* background-image: url('noimage.jpeg'); */
    background-size: 110px 110px;
    background-repeat: no-repeat;


    margin: 4px;
    /*margin: 1px 1px 1px 1px solid #000000;    */

    -webkit-box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    border-radius: 3%;
}.itemsProds2{
    position: relative;
    top: 0px;
    right: 0px;
    padding:4px;
    width: 200px;
    height: 200;
   /* background-image: url('noimage.jpeg'); */
    background-size: 200px 200px;
    background-repeat: no-repeat;


    margin: 4px;
    /*margin: 1px 1px 1px 1px solid #000000;    */

    -webkit-box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.75);
    border-radius: 3%;
}
.labelItemPrice{
    z-index: 10;
    position: absolute;
    top: 0px;
    right: 0px;
    background: rgba(0, 0, 0, 0.60);
    /*left: calc(40%); */
    color:white;
    font-size: 90%;
    padding: 3px;
    border-radius: 3%;
}
.labelItemName{
    z-index: 10;
    position: absolute;
    bottom:  0px;
    left: 0px;
    border-left: 1px;
    border-right: 1px;
    background-color: rgba(0, 0, 0, 0.50);
    width: 100%;
    color:white;
    font-size: 90%;
    padding: 3px;
    border-radius: 3%;
    margin-bottom: 0px;
    text-transform: capitalize;
}

.inpClass{
    width :60%;
   border-radius: 4px 4px 4px 4px;
-moz-border-radius: 4px 4px 4px 4px;
-webkit-border-radius: 4px 4px 4px 4px;
border: 1px solid #cccccc;
}
.inpClass2{
    width :80%;
   border-radius: 4px 4px 4px 4px;
-moz-border-radius: 4px 4px 4px 4px;
-webkit-border-radius: 4px 4px 4px 4px;
border: 1px solid #cccccc;
}
.modal-header-success {
    color:#fff;
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #5cb85c;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
}
.modal-header-warning {
    color:#fff;
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #f0ad4e;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
}
.modal-header-danger {
    color:#fff;
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #d9534f;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
}
.modal-header-info {
    color:#fff;
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #5bc0de;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
}
/*.modal-header-primary {
    color:red;
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #428bca;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
} */

.select2-selection{
    height: 34px !important;
}
.select2-selection__placeholder{
    position: relative;
    bottom: -3px;
    font-size: 80%;
}
#modalCarac .select2-selection--multiple{
    height: 100% !important;
} 
#scrollable-dropdown-menu .tt-dropdown-menu {
  max-height: 500px;
  overflow-y: auto;
}

#cliente-caja, #vendedor-caja{
    margin-bottom: -3%;
    width: 100%;
    font-size: 80%;
}
#search-producto{
    margin-bottom: -3%;
    width: 100%;
}


            .contenedor{
              width:75px;
              height:240px;
              position:absolute;
              right:0px;
              top: 0px;
            }
            .botonF1{
              width:45px;
              height:45px;
              border-radius:100%;
              background: transparent;
              right:14;
              top:-6;
              /*position:absolute; */
              margin-right:16px;
              margin-bottom:16px;
              border:none;
              outline:none;
              color:#808080;
              font-size:25px;
              /*box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);*/
              transition:.3s;
            }
            span{
              transition:.5s;
            }
            .botonF1:hover span{
              transform:rotate(360deg);
            }
            .botonF1:active{
              transform:scale(1.1);
            }
            .btnF{
              width:51px;
              height:51px;
              border-radius:100%;
              border:none;
              color:#FFF;
              box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
              font-size:32px;
              outline:none;
              position:absolute;
              z-index: 99;
              right:0;
              top:0;
              margin-right:26px;
              transform:scale(0);
            }
            .animacionVer{
              transform:scale(1);
            }
            .mainPanel {
                position: fixed;
                width: 100%;
                top: 35px;
                background-color: white;

            }
            @media only screen and (max-width: 768px) {
                .mainPanel {
                    position: fixed;
                    width: 100%;
                    top: 140px;
                    background-color: white;

                }
            }


        </style>
        <script>
            function clickFlotante(){
                if($(".botonF1").attr("menu-add") == 1){
                    $(".botonF1").attr("menu-add", "2");
                    $('.btnF').addClass('animacionVer');
                    $('.botonF1').css("background-color", "rgba(0,0,0,0.1)");
                } else {
                    $(".botonF1").attr("menu-add", "1");
                    $('.btnF').removeClass('animacionVer');
                    $('.botonF1').css("background-color", "transparent");
                }
            }
        </script>
</head>
<body style="height:100%;">




	<!-- <div id="appModal"> -->
		<!-- <pre>
			{{ $data }}
		</pre> -->
		<div id="appModalVUE" 
            class="modal fade"  
            role="dialog" 
            aria-labelledby="appModalVUE" 
            aria-hidden="true"
            tabindex="-1" 
            data-backdrop="static" 
            data-keyboard="false"
            >
			<div class="modal-dialog modal-lg" role="document">
				<div v-if="view=='main'" class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" style="text-align: center;font-size: 150%;">Kits</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="(() => {
                                setTimeout( () => { mv.view = 'main' }, 500)
                            })()">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<button type="button" class="btn btn-success" @click.prevent="addKit"> + AGREGAR KIT</button>

						<table class="table">
							<thead>
								<tr>
									<th>Estatus</th>
									<th>Kit</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="kit in kits">
									<td v-for="(field, index) in kit">


										<div v-if="index=='done'">
											<div v-if="field==true" style="background-color: green;">.</div>
											<div v-else style="background-color: red;">.</div>
										</div>
										<div v-else-if="index=='items'">
											<!-- {{index}} - {{field}} -->
											<button  type="button" class="btn btn-light"  @click.prevent="editKit(field)">Editar</button>
										</div>

									</td>

								</tr>
							</tbody>
						</table>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" @click.prevent="cancel">Cancelar</button>
						<button v-if="done==true" type="button" class="btn btn-success" @click.prevent="addToSale">ACEPTAR</button>
					</div>
				</div>
			<!-- </div> -->

			<!-- <div v-if="view=='kit'" class="modal-dialog" role="document"> -->
				<div v-if="view=='kit'" class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" style="text-align: center;font-size: 150%;">Kit</h5>
                        <button type="button" onclick="(() => {
                            mv.view = 'main'
                        })()" class="btn btn-light"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar </button>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="(() => {
                                setTimeout( () => { mv.view = 'main' }, 500)
                            })()">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<table class="table">
							<thead>
								<tr>
									<th>Estatus</th>
									<th>Código</th>
									<th>Nombre</th>
									<th>Cantidad</th>
									<th>Existencia</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="item in kit.items">

									<td v-if="item.done!=false || item.done!='0'" style="background-color: green;">.</td>
									<td v-else style="background-color: red;">.</td>
									<td>{{ item.code }}</td>
									<td>{{ item.name }}</td>
									<td>{{ item.quantity }}</td>
									<td>{{ item.stock }}</td>
									<td>
										<button v-if="item.ser=='1' && Number(item.stock)>=Number(item.quantity)" type="button" class="btn btn-light"  @click.prevent="editSeries(item)">Editar</button>
										<button v-else-if="item.lot=='1' && Number(item.stock)>=Number(item.quantity)" type="button" class="btn btn-light"  @click.prevent="editBatches(item)">Editar</button>
										<button v-else-if="item.car=='1' && Number(item.stock)>=Number(item.quantity)" type="button" class="btn btn-light"  @click.prevent="editCharacteristics(item)">Editar</button>
										<!-- <button v-else type="button" class="btn btn-light" >Sin Existencias</button> -->
									</td>

								</tr>
							</tbody>
						</table>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" @click.prevent="cancel">Cancelar</button>
						<button v-if="kit.done" type="button" class="btn btn-success" @click.prevent="aceptKit">ACEPTAR</button>
					</div>
				</div>
			<!-- </div> -->

			<!-- <div v-if="view=='series'" class="modal-dialog" role="document"> -->
				<div v-if="view=='series'" class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" style="text-align: center;font-size: 150%;">Series</h5>
                        <button type="button" onclick="(() => {
                            mv.view = 'kit'
                        })()" class="btn btn-light"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar </button>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="(() => {
                                setTimeout( () => { mv.view = 'main' }, 500)
                            })()">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<select2-multiple :options="optionsSeries" v-model="productSerie.series">

						</select2-multiple>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" @click.prevent="cancel">Cancelar</button>
						<button v-if="productSerie.quantity==productSerie.series.length" type="button" class="btn btn-success" @click.prevent="aceptSeries(productSerie)">ACEPTAR</button>
					</div>
				</div>
			<!-- </div> -->

			<!-- <div v-if="view=='batches'" class="modal-dialog" role="document"> -->
				<div v-if="view=='batches'" class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" style="text-align: center;font-size: 150%;">Lotes</h5>
                        <button type="button" onclick="(() => {
                            mv.view = 'kit'
                        })()" class="btn btn-light"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar </button>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="(() => {
                                setTimeout( () => { mv.view = 'main' }, 500)
                            })()">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="container">
							<div clas="row" v-for="batch in productBatches.batches">
								<div class="form-group">
							    <label >{{batch.name}} ({{batch.stock}})</label>
							    <input type="number" v-model="batch.quantity" min=0 :max="batch.stock" class="form-control" style="width: 250px;">
							  </div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" @click.prevent="cancel">Cancelar</button>
						<button v-if="productBatches.quantity==productBatches.batches.map( (e) => Number(e.quantity) ).reduce( (a,b) => (Number(a)+Number(b)), 0 )"
							type="button" class="btn btn-success" @click.prevent="aceptBatches(productBatches)">ACEPTAR</button>
					</div>
				</div>
			<!-- </div> -->

			<!-- <div v-if="view=='characteristics'" class="modal-dialog" role="document"> -->
				<div v-if="view=='characteristics'" class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" style="text-align: center;font-size: 150%;">Características</h5>
                        <button type="button" onclick="(() => {
                            mv.view = 'kit'
                        })()" class="btn btn-light"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Regresar </button>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="(() => {
                                setTimeout( () => { mv.view = 'main' }, 500)
                            })()">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<div class="container">
							<div clas="row" v-for="characteristic in productCharacteristics.characteristics">
								<div class="form-group">
							    <label >{{characteristic.name}} ({{characteristic.stock}})</label>
							    <input type="number" v-model="characteristic.quantity" min=0 :max="characteristic.stock" class="form-control" style="width: 250px;">
							  </div>
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" @click.prevent="cancel">Cancelar</button>
						<button v-if="productCharacteristics.quantity==productCharacteristics.characteristics.map( (e) => Number(e.quantity) ).reduce( (a,b) => (Number(a)+Number(b)), 0 )"
							type="button" class="btn btn-success" @click.prevent="aceptCharacteristics(productCharacteristics)">ACEPTAR</button>
					</div>
				</div>
			<!-- </div> -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	<!-- </div> -->
	<script src="https://cdn.jsdelivr.net/npm/vue"></script>
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
	<script>
		Vue.component('select2', {
			props: ['options', 'value'],
			template: `<select class="form-control">
									<slot></slot>
								</select>`,
			mounted: function () {
				var vm = this
				$(this.$el)
					// init select2
					.select2({ data: this.options })
					.val(this.value)
					.trigger('change')
					// emit event on change.
					.on('change', function () {
						vm.$emit('input', this.value)
					})
			},
			watch: {
				value: function (value) {
					// update value
					$(this.$el)
						.val(value)
						.trigger('change')
				},
				options: function (options) {
					// update options
					$(this.$el).empty().select2({ data: options })
				}
			},
			destroyed: function () {
				$(this.$el).off().select2('destroy')
			}
		})
		Vue.component('select2Multiple', {
			props: ['options', 'value'],
			template: `<select multiple class="form-control">
									<slot></slot>
								</select>`,
			mounted: function () {
				var vm = this
				$(this.$el)
					// init select2
					.select2({ data: this.options })
					.val(this.value)
					.trigger('change')
					// emit event on change.
					.on('change', function () {
						vm.$emit('input', $(this).val())
					})
			},
			watch: {
				value: function (value) {
					 if ([...value].sort().join(",") !== [...$(this.$el).val()].sort().join(","))
						$(this.$el).val(value).trigger('change');
				},
				options: function (options) {
					// update options
					$(this.$el).select2({ data: options })
				}
			},
			destroyed: function () {
				$(this.$el).off().select2('destroy')
			}
		})

		const mv = new Vue({
			el: '#appModalVUE',
			data: {
				view: "main", //kit, series, batches, characteristics
				kit: { items: [
					{
						code: "PROD",
						name: "PRODUCTO NORMAL",
						quantity: 5,
						stock: 20,
						empty: {},

						done: false
					},
					{
						code: "SERIE",
						name: "PROD CON SERIE",
						quantity: 2,
						stock: 20,
						series: [],

						done: false
					},
					{
						code: "LOTE",
						name: "PROD CON LOTE",
						quantity: 2,
						stock: 20,
						batches: [
							{ batch: 0, quantity: 0 },
						],

						done: false
					},
					{
						code: "CARACT",
						name: "PROD CON CARACTERÍSTICAS",
						quantity: 2,
						stock: 20,
						characteristics: [],


						done: false
					},
				], done: false },

				productoSerie: {
					code: "SERIE",
					name: "PROD CON SERIE",
					quantity: 2,
					stock: 20,
					series: [],

					done: false
				},
				// optionsSeries: [
				// 	{ id: 1, text: 'SERIE001' },
				// 	{ id: 2, text: 'SERIE002' },
				// 	{ id: 3, text: 'SERIE003' }
				// ],

				productBatches: {
					code: "LOTE",
					name: "PROD CON LOTE",
					quantity: 2,
					stock: 20,
					batches: [],
					batchesAux: [],

					done: false
				},
				// optionsBatches: [
				// 	{ id: 1, text: 'LOTE001', quantity: 10 },
				// 	{ id: 2, text: 'LOTE002', quantity: 9 },
				// 	{ id: 3, text: 'LOTE003', quantity: 8 }
				// ],

				productCharacteristics: {
					code: "CARACT",
					name: "PROD CON CARACTERÍSTICAS",
					quantity: 5,
					stock: 20,
					characteristics: {1: 0, 2: 0 },
					stockCharacteristics: 0,

					done: false
				},
				// optionsCharacteristics: [
				// 	{
				// 		id: 1,
				// 		text: 'color',
				// 		list: [
				// 			{
				// 				id: 1,
				// 				text: 'azul',
				// 			}
				// 		]
				// 	},
				// 	{
				// 		id: 2,
				// 		text: 'tamaño',
				// 		list: [
				// 			{
				// 				id: 1,
				// 				text: 'chico',
				// 			},
				// 			{
				// 				id: 2,
				// 				text: 'mediano',
				// 			}
				// 		]
				// 	},
				//
				// ],





				kits: [
					{ items: [
						{
							code: "PROD",
							name: "PRODUCTO NORMAL",
							quantity: 5,
							stock: 20,
							empty: {},

							done: true
						},
						{
							code: "SERIE",
							name: "PROD CON SERIE",
							quantity: 2,
							stock: 20,
							series: [],

							done: false
						},
						{
							code: "LOTE",
							name: "PROD CON LOTE",
							quantity: 25,
							stock: 30,
							batches: [],

							done: false
						},
						{
							code: "CARACT",
							name: "PROD CON CARACTERÍSTICAS",
							quantity: 5,
							stock: 20,
							characteristics: {1: 0, 2: 0 },
							stockCharacteristics: 0,
							// characteristicsAux: {1: {}, 2: {} },

							done: false
						},
					], done: false },

					{ items: [
						{
							code: "PROD",
							name: "PRODUCTO NORMAL",
							quantity: 5,
							stock: 20,
							empty: {},

							done: true
						},
						{
							code: "SERIE",
							name: "PROD CON SERIE",
							quantity: 2,
							stock: 20,
							series: [],

							done: false
						},
						{
							code: "LOTE",
							name: "PROD CON LOTE",
							quantity: 25,
							stock: 30,
							batches: [],

							done: false
						},
						{
							code: "CARACT",
							name: "PROD CON CARACTERÍSTICAS",
							quantity: 5,
							stock: 20,
							characteristics: {1: 0, 2: 0 },
							stockCharacteristics: 0,
							// characteristicsAux: {1: {}, 2: {} },

							done: false
						},
					], done: false },

				],
				done: false

			},
			methods: {
				updateKitsStatus() {
					this.kits.forEach( (items) => {
						items['items'] = items['items']
																.map( (e) => {
																	if(  e.car == 0 && e.ser == 0 && e.lot == 0 )
																		e.done = (Number(e.stock) >= Number(e.quantity) )
																	return e
																} )
						items['done'] = items['items']
																.map( (e) => e.done == '0' ? false:  true )
																.reduce(
																	(a, b) => a && b
																)
					})
					this.done = this.kits
													.map( (e) => e.done )
													.reduce(
														(a, b) => a && b
													)
				},
				updateKitStatus() {
					this.kit['items'] = this.kit['items']
															.map( (e) => {
																if(  e.car == 0 && e.ser == 0 && e.lot == 0  )
																	e.done = (Number(e.stock) >= Number(e.quantity) )
																return e
															} )
					this.kit['done'] = this.kit['items']
															.map( (e) => e.done == '0' ? false : true )
															.reduce(
																(a, b) => a && b
															)
				},
				cancel() {
					$('#appModalVUE').modal('hide')
					this.view = 'main'
				},
				addToSale() {
					let idProducto = mv.kits[0].items[0].idKit
					let kitsAux = this.kits
					$.ajax({
	            url: 'ajax.php?c=caja&f=eliminaProducto',
	            type: 'POST',
	            dataType: 'json',
	            data: {
								'id': idProducto,
								'idpedidoComanda': 0
							},
	            success: function(data) {
								idProducto = mv.kits[0].items[0].idCode
								caja.agregaProducto(idProducto,'','','',0,kitsAux);
								$('#appModalVUE').modal('hide')
	            }
	        });


					// caja.eliminarProducto(idProducto,'0','0');
					// setTimeout( () => {
					// 	idProducto = mv.kits[0].items[0].idCode
					// 	caja.agregaProducto(idProducto,'','','',0,this.kits);
					// 	$('#appModalVUE').modal('hide')
					// }, 1500 )
				},
				addKit() {
					$.ajax({
			        url: 'ajax.php?c=caja&f=getKitTemplate',
			        type: 'POST',
			        dataType: 'json',
			        data: {id: this.idKit}
						})
			    .done(function(result) {
						mv.kits.push( { done: false, items: result, } )
						mv.updateKitsStatus()
					})
				},
				editKit(items) {
					this.view = 'kit'
					this.kit.items = items
					this.updateKitStatus()
				},
				aceptKit() {
					this.view = 'main'
					this.updateKitsStatus()
				},
				editSeries(item) {
					this.optionsSeries = item.optionsSeries
					this.productSerie = item
					this.view = 'series'
				},
				aceptSeries(item) {
					item.done = true
					this.view = 'kit'
					this.updateKitStatus()
				},
				editBatches(item) {
					this.productBatches = item
					this.view = 'batches'
				},
				aceptBatches(item) {
					item.done = true
					this.view = 'kit'
					this.updateKitStatus()
				},
				editCharacteristics(item) {
					this.productCharacteristics = item
					this.view = 'characteristics'
				},
				aceptCharacteristics(item) {
					item.done = true
					this.view = 'kit'
					this.updateKitStatus()
				},
			},
			created() {
				this.updateKitsStatus()
			},
		})
	</script>









<div class="row">

    <input type="hidden" id="rango" value="100">

</div>
<?php

if($configDatos[0]['vista']==1){
    $vista1 = 'col-sm-5';
    $vista2 = 'display: block;';

}else{
    /*$vista1 = 'col-sm-12';
    $vista2 = 'display: none;';*/
    $vista1 = 'col-sm-5';
    $vista2 = 'display: block;';
}
?>
<!-- inicio Container -->

<input type="hidden" id="saldoDisponibleHide" value="<?php echo $retiroObligado; ?>">
<input type="hidden" id="versionFacturacionHide" value="<?php echo $versionFac; ?>">
<input type="hidden" id="modificaPrecios" value="<?php echo $configDatos[0]['modifica_precios']; ?>">
<input type="hidden" id="nombreDeInstancia" value="<?php echo $_SESSION['accelog_nombre_instancia']; ?>">
<input type="hidden" id="disponible_nota">
<input type="hidden" id="buscaCar" value="<?php echo $buscaCar; ?>"> 
    <div class="container col-sm-12 well" style="height:100%;">
        <div class="row" style="height:100%;margin-top:-1%">
        <!-- inicio del panel -->
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="row">
                                <div class="col-sm-4" id="option-cliente" >
                                    <div class="input-group " id="clienteSelect" style="margin-left:-7%">
                                        <span class="input-group-addon" onclick="caja.clienteAddButton();"><i class="fa fa-user-plus" aria-hidden="true"></i></span>

                                    <!--    <select name="" id="cliente-caja" class="form-control" onchange="caja.selCliente();">
                                        <option value="">-Publico en General-</option>
                                            <?php
                                                foreach ($clientes['clientes'] as $key => $value) {
                                                    echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
                                                }
                                            ?>
                                        </select> -->
                                        <input type="text" class="typeahead form-control " id="cliente-caja" placeholder="Publico General" data-provide="typeahead">

                                        <input type="hidden" id="hidencliente-caja" value="">
                                        <input type="hidden" id="idPedido" value="<?php echo $_SESSION['caja']['pedido']; ?>">
                                        <input type="hidden" id="idOrden" value="<?php echo $_SESSION['caja']['idorden']; ?>">
                                        <input type="hidden" id="listaDePreciosClient">
                                        <div style="display: none;">
                                            <audio id="audio" controls>
                                                <source type="audio/wav" src="error.wav">
                                            </audio>
                                        </div>
                                    </div>
                                </div>


                              <!--  <div class="col-sm-4">
                                     <input type="text" class="typeahead form-control " id="cliente-caja" placeholder="Publico en General" data-provide="typeahead">
                                    <input type="hidden" id="hidencliente-caja" value="">
                                    <input type="hidden" id="idPedido">
                                    <input type="hidden" id="listaDePreciosClient">
                                </div> -->
                                <div class="col-sm-3">

                                    <div class="input-group">
                                        <span class="input-group-addon" onclick="caja.basculaPeso();"><label>#</label></span>
                                        <input type="text" class="typeahead form-control numero" onkeypress="return caja.isNumberKey(event)" id="cantidad-producto" data-provide="typeahead" value="1" style="width:100%;">
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><span class="glyphicon glyphicon-barcode"></span></span>
                                        <div id="scrollable-dropdown-menu">
                                            <input type="text" class="typeahead form-control " id="search-producto" placeholder="C&oacute;digo o descripción" data-provide="typeahead" onkeypress="caja.busquedaXcodigo(event)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-1" >
                            <span class="input-group-addon" onclick="caja.medicoAddButton();" <?php echo ( ( isset( $configDatos[0]['activar_antibioticos'] ) && $configDatos[0]['activar_antibioticos'] == '1' ) ? '' : 'style="display: none;"' ) ?> ><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">

                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <select class="erre form-control " id="selectDepartamento" >

                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="erre form-control" id="selectFamilia" >

                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <select class="erre form-control" id="selectLinea" >

                                            </select>
                                        </div>
                                    <div class="col-sm-1">

                                        <i class="fa fa-undo fa-2x" aria-hidden="true" onclick="caja.resetFilters();" style="color: white; background-color: rgba(0, 0, 0, 0.1); border-radius: 100%; margin: 4px;"></i>

                                    </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <!--<div id="divSuspendidas" style="display:none;"> -->
                                        <?php 
                                        if(in_array('35', $_SESSION['accelog_acciones'])){
                                            echo '<i class="fa fa-share-square-o fa-2x" aria-hidden="true" onclick="$(\'#modalPreticketsList\').modal();" style="color: white; background-color: rgba(0, 0, 0, 0.1); border-radius: 100%; margin: 4px;"></i>';
                                        }
                                        ?>

                                   <!-- </div> -->
                                </div>

                                <div class="col-sm-1">
                                    <div style="text-align: center;margin-top:1%;">
                                        <div class="contenedor">

                                            <?php //    ACCIONES 10 - Funciones Caja
                                                $margintop = 0;
                                                if(in_array('11', $_SESSION['accelog_acciones'])
                                                 ||in_array('12', $_SESSION['accelog_acciones'])
                                                 ||in_array('13', $_SESSION['accelog_acciones'])
                                                 ||in_array('14', $_SESSION['accelog_acciones'])
                                                 ||in_array('15', $_SESSION['accelog_acciones'])
                                                 ||in_array('16', $_SESSION['accelog_acciones'])
                                                 ||in_array('17', $_SESSION['accelog_acciones'])
                                                 ||in_array('18', $_SESSION['accelog_acciones'])
                                                 ){
                                            ?>

                                                    <button class="botonF1" menu-add="1" onclick="clickFlotante()" style="color:white;margin-top:-9%">
                                                        <i class="fa fa-wrench"></i>
                                                    </button>
                                           <?php } ?>


                                            <div id="buttons-normal">

                                            <?php //    ACCIONES 11 - Corte de Caja
                                                if(in_array('11', $_SESSION['accelog_acciones'])){
                                                    $margintop += 50;
                                                ?>  
                                                    
                                                    <?php 
                                                         if($configDatos[0]['cortesP'] == 1){
                                                            $onclick = 'modal_corte();'; // modal para ingresar pass
                                                         }else{
                                                            $onclick = 'caja.corteButtonAccion();'; // directo al corte

                                                         }
                                                     ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.5s;"
                                                        data-tooltip="tooltip"
                                                        title="Corte"
                                                        data-placement="left"
                                                        onclick="<?php echo $onclick; ?>"
                                                        class="btnF btn-warning">
                                                        <i class="fa fa-scissors" aria-hidden="true"></i>
                                                    </button>';
                                                    
                                            <?php } ?>

                                            <?php //    ACCIONES 11 - Corte de Caja parcial
                                                if(in_array('11', $_SESSION['accelog_acciones']) and $configDatos[0]['cortesP'] == 1){
                                                    $margintop += 60;
                                                ?>
                                                     <button style="margin-top:<?php echo $margintop;?>px; transition:0.5s;"
                                                        data-tooltip="tooltip"
                                                        title="Corte"
                                                        data-placement="left"
                                                        onclick="caja.corteParcial();"
                                                        class="btnF btn-danger">
                                                        <i class="fa fa-scissors" aria-hidden="true"></i>
                                                    </button>';
                                                    
                                            <?php } ?>
                                           

                                            <?php //    ACCIONES 12 - Ventas
                                                if(in_array('12', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                                ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.7s;"
                                                        data-tooltip="tooltip"
                                                        title="Venta"
                                                        data-placement="left"
                                                        onclick="caja.ventasButtonAccion();"
                                                        class="btnF btn-success">
                                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                    </button>';
                                           <?php } ?>

                                            <?php //    ACCIONES 13 - Facturas
                                                if(in_array('13', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                            ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.9s;"
                                                        data-tooltip="tooltip"
                                                        title="Factura"
                                                        data-placement="left"
                                                        onclick="caja.facturarButton();"
                                                        class="btnF btn-info">
                                                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                    </button>';
                                            <?php } ?>


                                               <!-- <button style="margin-top:230px; transition:0.12s;"
                                                        data-tooltip="tooltip"
                                                        title="Garantias"

                                                        data-placement="left"
                                                        onclick="caja.garantiaButtonAction();"
                                                        class="btnF btn-primary">
                                                            <i class="fa fa-list" aria-hidden="true"></i>
                                                </button> -->

                                                <?php //    ACCIONES 14 - Cambio de Vista
                                                if(in_array('14', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                                ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.12s;"
                                                        data-tooltip="tooltip"
                                                        title="Vista"
                                                        data-placement="left"
                                                        onclick="cambiaVista();"
                                                        class="btnF btn-success">
                                                        <i class="fa fa-window-restore" aria-hidden="true"></i>
                                                    </button>
                                                <?php } ?>

                                                <?php //    ACCIONES 15 - Retiros de Caja
                                                if(in_array('15', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                                ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.15s;"
                                                        data-tooltip="tooltip"
                                                        title="Retiros"
                                                        data-placement="left"
                                                        onclick="caja.formRetiro();"
                                                        class="btnF btn-warning">
                                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                                    </button>
                                                <?php } ?>

                                                <?php //    ACCIONES 16 - Abonos de Caja
                                                if(in_array('16', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                                ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.18s;"
                                                            data-tooltip="tooltip"
                                                            title="Abonos"
                                                            data-placement="left"
                                                            onclick="caja.formAbono();"
                                                            class="btnF btn-primary">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                <?php } ?>

                                                <?php //    ACCIONES 17 - Venta de Servicios
                                                if(in_array('17', $_SESSION['accelog_acciones'])){
                                                    $margintop += 60;
                                                ?>
                                                        <button style="margin-top:<?php echo $margintop;?>px; transition:0.20s;"
                                                            data-tooltip="tooltip"
                                                            title="Pago de Servicios"
                                                            data-placement="left"
                                                            onclick="prontipagos();"
                                                            class="btnF btn-warning">
                                                            <i class="fa fa-usd" aria-hidden="true"></i>
                                                        </button>
                                                <?php } ?>

                                                <?php
                                                //if (in_array(2156, $_SESSION['accelog_menus'])) {
                                                if(in_array('18', $_SESSION['accelog_acciones'])){ // Comandas
                                                    $margintop += 60;

                                                    $misproductosname = $_SESSION['misproductos'];
                                                    $app = explode('|', $misproductosname);

                                                    if(in_array('1002', $app)){ /// solo foodware
                                                ?>
                                                    <button style="margin-top:<?php echo $margintop;?>px; transition:0.22s;"
                                                            data-tooltip="tooltip"
                                                            title="Comandas"
                                                            data-placement="left"
                                                            onclick="muestraComandas();"
                                                            class="btnF btn-info">
                                                                <i class="fa fa-cutlery" aria-hidden="true"></i>
                                                    </button>
                                                <?php

                                                    } /// solo foodware fin

                                                } ?>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body" style="height: 100%;" id="pb">
                        <div class="container <?php echo $vista1; ?>" style="height:100%;" id="leftSide">
                                <div class="row table-responsive" style="height:80%;overflow:auto;" id="productosDivList">
                                            <table class="table table-condensed" id="productsTable1" style="font-size:11px; border:0.5px solid #c8c8c8;">
                                                <thead>
                                                  <tr>
                                                    <th class="col-sm-1">Cant.</th>
                                                    <th class="col-sm-7">Producto</th>
                                                    <th class="col-sm-2">Precio</th>
                                                    <th class="col-sm-2">Subtotal</th>
                                                    <th class="col-sm-1"></th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                </div>
                                <div class="row well" style="position: absolute; bottom: 25px; width: 100%;">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <label id="clienteName">Publico en General</label>
                                        </div>
                                        <div class="row">
                                            <div id="selectrfc" style="display:none;">
                                                <select class="form-control input-sm" id="rfc">
                                                    <option value="0">XAXX010101000</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div id="articulos" class="col-xs-6">
                                                Articulos:
                                                <label id="totalDeProductos">0</label>
                                                <input type="hidden" id="totalDeProductosInput">
                                            </div>
                                            <?php
                                                if($configDatos[0]['puntos']==0){
                                                    $oculto = 'style= "display:none;"';
                                                }else{
                                                    $oculto = '';
                                                }
                                            ?>
                                            <div class="col-xs-6" <?php echo $oculto; ?> >
                                                Puntos:
                                                <label id="totalPuntos">0</label>
                                                <input type="hidden" id="totalPuntosInput">
                                            </div>
                                        </div>
                                        <div class="row">
                                        <?php
                                            $tiposdocs=array();
                                            if($configDatos[0]['tipo_documento']!='0'){
                                                $explodeDoc=explode(',',$configDatos[0]['tipo_documento']);
                                                $tiposdocs[2]=$explodeDoc[0];
                                                $tiposdocs[5]=$explodeDoc[1];
                                                $tiposdocs[4]=$explodeDoc[2];
                                                //$tiposdocs[15]=$explodeDoc[3];
                                            }else{
                                                $tiposdocs[2]=0;
                                                $tiposdocs[5]=0;
                                                $tiposdocs[4]=0;
                                                //$tiposdocs[15]=0;
                                            }

                                        ?>
                                            <div class="col-sm-7 pull-left" id="documentoDiv">
                                                <select class="form-control input-sm" id="documento" onchange="caja.notaCreditoM();">

                                                    <option selected value="1">Ticket</option>
                                                    <?php if($tiposdocs[2]!=0){ ?> <option  value="2">Factura</option> <?php } ?>
                                                    <?php if($tiposdocs[5]!=0){ ?> <option  value="5">Recibo de Honorarios</option> <?php } ?>
                                                    <?php if($tiposdocs[4]!=0){ ?> <option  value="4">Recibo de Pago</option> <?php } ?>
                                                    <!--<?php if($tiposdocs[15]!=0){ ?> <option  value="15">Nota de Credito</option> <?php } ?> -->
                                            <?php //    ACCIONES 9 - Modificar Ticket
                                                if(in_array('25', $_SESSION['accelog_acciones'])){

                                                    echo '<option  value="15">Nota de Credito</option>';

                                                }
                                            ?>
                                                    <!--<option selected value="15">Nota de Credito</option> -->


                                                </select>
                                            </div>
                                             <?php
                                                if($versionFac!='3.3'){




                                             ?>
                                            <div class="col-sm-4">
                                                <button class="btn btn-info btn-sm" onclick="modalDesc();" style="font-size: 78%; width: 100%; height: 100%;" >Desc Global%</button>
                                            </div>

                                            <?php
                                                }
                                            ?>

                                            <?php //    ACCIONES 9 - Modificar Ticket
                                                if(in_array('9', $_SESSION['accelog_acciones'])){

                                                    $misproductosname = $_SESSION['misproductos'];
                                                    $app = explode('|', $misproductosname);

                                                    if(in_array('1002', $app)){ /// solo foodware

                                                        echo '<div class="col-sm-1" style="padding-right: 0; padding-left: 0; ">
                                                            <button class="btn btn-default btn-sm" onclick="preticket();" style="font-size: 78%; width: 100%; height: 100%;"><i class="fa fa-print" aria-hidden="true"></i></button>
                                                        </div>';

                                                    }

                                                }
                                            ?>
                                            <div class="col-sm-3" style="padding-right: 0; padding-left: 0; display:none;">
                                                            <button class="btn btn-default btn-sm" onclick="caja.complementoImp();" style="font-size: 78%; width: 100%; height: 100%;">Complemento</button>
                                            </div>
                                        </div>
                                        <div class="row">

                                                <div class="total col-sm-12">
                                                    <div id="pagar" style="padding-top: 2%;">
                                                         <button type="button" id="btn_pagar" class="btn btn-success btn-lg btn-block" onclick="caja.modalPagar();"> <i class="fa fa-money"></i> PAGAR <label id="totalLabel">$0.00</label></button>
                                                    </div>
                                                  <!--  <div class="col-sm-6">
                                                       <h1>Total</h1>
                                                    </div>
                                                     <div class="col-sm-6">
                                                        <label><h1 id="totalLabel">$0.00</h1></label>
                                                    </div> -->
                                                </div>

                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                        <div id="cargos">
                        <div style="font-size:12px;" id="desDiven">

                        </div>
                        <div style="font-size:12px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    Subtotal
                                </div>
                                <div class="col-sm-6">
                                    <label id="subtotalLabel">$0.00</label>
                                </div>
                            </div>
                        </div>
                      <!-- <div style="font-size:12px;">
                            <div class="row">
                                <div class="col-sm-6">
                                    Descuento
                                </div>
                                <div class="col-sm-6">
                                    <label id="descuentoLabel">$0.00</label>
                                </div>
                            </div>
                        </div> -->

                        <div class="immpuestos" id="impestosDiv" style="font-size:12px;">
                        <!--    <div class="row">
                                <div class="col-sm-3">
                                    IVA

                                </div>
                                <div class="col-sm-3">
                                    <label>$23454</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    IVA

                                </div>
                                <div class="col-sm-3">
                                    $23454
                                </div>
                            </div>  -->
                        </div>
                    </div>
                    <div class="coinDiv" style="display:none;">
                        <label>Moneda</label>
                        <select id="monedaVenta" class="form-control input-sm" onchange="caja.tipoCambio();">
                            <option value="1">Peso Mexicano(MXN)</option>
                            <option value="2">Dólar estadounidense(USD)</option>
                        </select>
                    </div>
                    <div class="coinDiv" style="display:none;">
                        <label>T.Cambio</label>
                        <input type="text"  id="tpc" class="form-control input-sm" value="1.00">
                    </div>
                                    </div>
                                </div>
                        </div>
                        <div class="col-sm-7" style="overflow-y: scroll;<?php echo $vista2; ?>" id="containerTouch" >
                            <!-- <div class="row" style="padding-left:5%"> -->

                                  <!--  <div class="pull-left itemsProds" style="background-image: url('noimage.jpeg');">
                                        <label class="labelItemPrice">$12344.34</label>
                                        <label class="labelItemName">Manzana Verde</label>
                                    </div>
                                    <div class="pull-left" style="padding:2px;width: 110px;height: 110px;background-image: url('noimage.jpeg'); background-size: 110px 110px; background-repeat: no-repeat;">
                                        <label style="z-index: 10; position: relative; background: red none repeat scroll 0% 0%; left: calc(40%);">$12344.34</label>
                                        <label style="z-index: 10; position: relative; top: 90%; background-color: rgba(0, 0, 0, 0.3); width: 100%;">Manzana Verde</label>
                                    </div>
                                    <div class="pull-left" style="padding:2px;width: 110px;height: 110px;background-image: url('noimage.jpeg'); background-size: 110px 110px; background-repeat: no-repeat;">
                                        <label style="z-index: 10; position: relative; background: red none repeat scroll 0% 0%; left: calc(40%);">$12344.34</label>
                                        <label style="z-index: 10; position: relative; top: 90%; background-color: rgba(0, 0, 0, 0.3); width: 100%;">Manzana Verde</label>
                                    </div>
                                    <div class="pull-left" style="padding:2px;width: 110px;height: 110px;background-image: url('noimage.jpeg'); background-size: 110px 110px; background-repeat: no-repeat;">
                                        <label style="z-index: 10; position: relative; background: red none repeat scroll 0% 0%; left: calc(40%);">$12344.34</label>
                                        <label style="z-index: 10; position: relative; top: 90%; background-color: rgba(0, 0, 0, 0.3); width: 100%;">Manzana Verde</label>
                                    </div>     -->

                                <?php
                                    $contador = 1;
                                    $nombre = '';
                                        foreach ($proTouchContainer as $key => $value) {
                                            if ($value['tipo_producto']!=3 && $value['tipo_producto']!=8) {
                                                if($value['descripcion_corta']!=''){
                                                    $nombre = $value['descripcion_corta'];
                                                }else{
                                                    $nombre = $value['nombre'];
                                                }

                                               /* echo '<div class="pull-left" style="padding:2px;">';
                                                echo '  <button class="btn btn-default" codigoProTouch="'.$value['codigo'].'" onclick="caja.agregaProTouch(this)">';
                                                echo '    <div class="row">';
                                                echo '       <div style="width:90px;" class="wrapPro">';
                                                echo '          <label>'.$nombre.'</label>';
                                                echo '       </div>';
                                                echo '    </div>';
                                                echo '    <div class="row">';
                                                echo '      <div style="height:70px; width:100px;">';
                                                echo '          <img src="'.$value['ruta_imagen'].'" alt="" style="height:70px; width:90px;">';
                                                echo '      </div>';
                                                echo '    </div>';
                                                echo '    <div class="row">';
                                                echo '      <label>$'.number_format($value['precio'],2).'</label>';
                                                echo '    </div>';
                                                echo '  </button>';
                                                echo '</div>'; */
                                                $value['ruta_imagen'] = str_replace(' ', '%20', $value['ruta_imagen']);
                                                $value['ruta_imagen'] = str_replace('(', '\(', $value['ruta_imagen']);
                                                $value['ruta_imagen'] = str_replace(')', '\)', $value['ruta_imagen']);


                                                echo '<div class="pull-left itemsProds" codigoProTouch="'.$value['codigo'].'" onclick="caja.agregaProTouch(this)" style="background-image: url('.$value['ruta_imagen'].');">';

//para precio por sucursal
                                                $resListPreTmp = $this->CajaModel->listaPreciosDe($value['id']);
                                                echo '<label class="labelItemPrice">$'.( count($resListPreTmp) == 0 ?number_format($value['precio'],2) : number_format($resListPreTmp[0]['precio']) ).'</label>';


                                                echo '<label class="labelItemName">'.strtolower( $nombre ).'</label>';
                                                echo '</div>';
                                                $contador++;
                                            }

                                        }
                                        echo '<div class="row" id="botonCarga"><div class="col-sm-12"><button class="btn btn-default" onclick="caja.cargarMas();">Cargar mas</button></div></div>';
                                ?>

                            <!-- </div> -->
                        </div>
                        <div class="col-sm-7" id="divComandas" style="display:none;">
                            <div class="col-sm-12" id="listar_comandas_pendientes">
                                <!-- En esta div se cargan las comandas pendientes -->
                            </div>
                        </div>
                        <div class="col-sm-7" id="divComandas" style="display:none;">
                            <div class="col-sm-12" id="serviciosEspeciales">
                          <!--  <div class="pull-left itemsProds2" codigoprotouch="w" onclick="caja.agregaProTouch(this)"><i class="fa fa-plane fa-5x" aria-hidden="true"></i>
<label class="labelItemPrice">$10.00</label><label class="labelItemName">ch agua ciel</label></div> -->
                            <div class="row">
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#" onclick="caja.avionForm();">
                                      <i class="fa fa-plane fa-5x pull-center"></i>
                                      <br>Avion
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#" onclick="caja.busForm();">
                                      <i class="fa fa-bus fa-5x pull-center"></i>
                                      <br>Autobus
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-building fa-5x pull-center"></i>
                                      <br>Hotel
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-globe fa-5x pull-center"></i>
                                      <br>Operadores
                                    </a>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-lock fa-5x pull-center"></i>
                                      <br>Seguros
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-truck fa-5x pull-center"></i>
                                      <br>Traslados
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-money fa-5x pull-center"></i>
                                      <br>Cuotas
                                    </a>
                                </div>
                                <div class="col-xs-3">
                                    <a class="btn btn-lg btn-default btn-block" href="#">
                                      <i class="fa fa-usd fa-5x pull-center"></i>
                                      <br>Comision
                                    </a>
                                </div>
                            </div>
                            </div>
                        </div>

                </div>
            </div>
            <!-- fin del panel -->
        </div>
    </div>
    <!-- Fin del Container-->
<div class="modal fade" id="modalMensajes" role="dialog" style="z-index:1051;" data-backdrop="static">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Espere un momento...</h4>
        </div>
        <div class="modal-body">
          <div class="alert alert-default">
            <div align="center"><label id="lblMensajeEstado"></label></div>
            <div align="center"><i class="fa fa-refresh fa-spin fa-5x fa-fw margin-bottom"></i>
                 <span class="sr-only">Loading...</span>
             </div>
        </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalDescParcial" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Descuento y cortesía por producto</h3>
        </div>
        <div class="modal-body" style="padding-top: 0px;">
            <div class="row" style="background-color: rgba(0,0,0,0.05)">
                <div class="col-sm-5">
                    <h4>Producto</h4>
                </div>
                <div class="col-sm-3">
                    <h4>Lista de precios</h4>
                </div>
                <div class="col-sm-2">
                    <h4>Precio</h4>
                </div>
                <div class="col-sm-2">
                    <h4>Subtotal</h4>
                </div>
            </div>
            <div class="row">
            <input type="hidden" id="xProParc">
                <div class="col-sm-5">
                    <h5 id="encabezadoNombre" style="margin-top: 20px;"></h5>
                </div>
                <div class="col-sm-3">
                    <h5><select id="selectListaPrecios" class="form-control" onclick="caja.changeListaPrecio();" onchange="caja.changeListaPrecio();">
                    </select></h5>
                </div>
                <div class="col-sm-2">
                    <h5 id="encabezadoPrecio" style="margin-top: 20px;"></h5>
                    <input type="hidden" id="encabezadoPrecioInput">
                    <input type="hidden" id="limite_porcentaje">
                    <input type="hidden" id="limite_cantidad">
                </div>
                <div class="col-sm-2">
                    <h5 id="encabezadoImporte" style="margin-top: 20px;"></h5>
                    <input type="hidden" id="encabezadoImporteInput">
                </div>
            </div>
            <br>
            <div class="row" id="edicionProd">
                <div class="col-xs-12">
                    <label>Descripcion:</label>
                    <input type="text" id="descProdUpdate" class="form-control">
                </div>
            </div>
            <br>
            <div class="row" id="edicionProd">

                <div class="col-xs-5">
                    <label>Impuesto Local:</label>
                    <select class="form-control" id="impuestoLocal">
                        <option value="0">-Selecciona-</option>
                        <?php
                            foreach ($impuestosLocales['impuestos'] as $key => $value) {
                                echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="col-xs-5">
                    <label>Importe</label>
                    <input type="text" class="form-control" id="importeIL">
                </div>
                <div class="col-xs-2">
                    <button class="btn btn-success" onclick="caja.aplicaImpuesLocal();">+</button>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-3">
                            <label style="margin: 10px;">Descuento:</label>
                        </div>
                        <div class="col-sm-5">
                            <select id="tipoDescu" class="form-control" onchange="caja.changeTipoDescuento();" >
                                <option value="N" selected="selected">Precio de lista</option>
                                <option value="%" >Porcentaje</option>
                                <option value="$">Monto</option>
                                <option value="C">Cortesía</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="desCantidad" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-primary btn-block" onclick="caja.validaPassDesPP();">Aplicar</button>
                <!--    <button class="btn btn-primary btn-block" onclick="caja.aplicaDesParcial();">Aplicar</button> -->
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger btn-block" data-dismiss="modal" onclick="caja.resetModalDescuento();">Cancelar</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

    <div class="modal fade" id="modalPagar" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header modal-header-success">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><i class="fa fa-money fa-lg"></i> Pagos</h4>
        </div>
        <div class="modal-body">
            <div class="row">
            <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-4">
                    <select id="cboMetodoPago" class="form-control" onchange="caja.changeMetodoPago();">
                    <?php
                        foreach ($formasDePago['formas'] as $key => $value) {
                            echo '<option value="'.$value['idFormapago'].'">('.$value['claveSat'].') '.$value['nombre'].'</option>';
                        }
                    ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="text" id="txtCantidadPago" class="form-control numeros" onchange="caja.changeCantidadPago()">
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-default btn-block" id="btnAgregarPago">Agrega Pago</button>
                </div>
                <div class="col-sm-2">
                    <button id="btnDenominacionesPago" class="form-control btn btn-primary" data-toggle="modal" data-target="#modalPagoDenominacion" onclick="caja.pagoDenominacionButtonAction();">
                                <i class="fa fa-money" aria-hidden="true"></i>
                    </button>
                </div>
               <!-- <div class="col-sm-3" id="option-vendedor">
                                    <div class="row">
                                        <div class="col-sm-12" >
                                            <div class="input-group " id="vendedorSelect" style="margin-left:-7%; ">
                                                <span class="input-group-addon" ><i class="fa fa-user-circle-o" aria-hidden="true"></i></span>
                                                <input type="text" class="typeahead form-control " id="vendedor-caja" placeholder="Vendedor comisionista" data-provide="typeahead" style="width: 105%;">

                                                <input type="hidden" id="hidenvendedor-caja" value="">
                                            </div>
                                        </div>
                                    </div>


                                </div> -->
            </div><br>
                <div class="row" id="divReferenciaPago" style="display:none;">
                    <div class="col-md-8">
                            <label id="lblReferencia">Referencia transferencia:</label>
                            <input type="text" id="txtReferencia" class="form-control pull-left" value="">
                    </div>
                    <div class="col-sm-4" id="tarjetasRadios" style="display:none;">
                        <label id="er">Tipo de Tarjeta</label>
                      <!--  <select class="form-control" name="" id="">
                            <option value="1">VISA/<i class="fa fa-cc-visa" aria-hidden="true"></i></option>
                            <option value="2">MASTER CARD/<i class="fa fa-cc-mastercard" aria-hidden="true"></i></option>
                            <option value="3">AMERICAN EXPRESS/<i class="fa fa-cc-amex" aria-hidden="true"></i></option>
                        </select>-->
                       <div style="margin-top:1%;">
                        <label class="radio-inline"><input type="radio" name="tarRadio" value="1"><i class="fa fa-cc-visa" aria-hidden="true"></i></label>
                        <label class="radio-inline"><input type="radio" name="tarRadio" value="2"><i class="fa fa-cc-mastercard" aria-hidden="true"></i></label>
                        <label class="radio-inline"><input type="radio" name="tarRadio" value="3"><i class="fa fa-cc-amex" aria-hidden="true"></i></label>
                        </div>
                    </div>
                    <div class="col-sm-4" id="puntosCheck" style="display:none;">
                     <!--  <div class="row">
                           <div class="col-xs-12">
                                <label>Puntos:</label>
                                <label id="pointsCardT">Puntos:</label>
                                <input type="hidden" id="pointsCardIn" class="form-control pull-left" value="">
                                <input type="checkbox" id="usarPuntos" name="point" value="1" class="form-control"> Usar Puntos
                           </div>
                       </div> -->
                    </div>
                </div>
        <?php
            if($configDatos[0]['puntos']==0){
                $oculto2 = 'style="display:none;"';
            }else{
                $oculto2 = '';
            }

        ?><br>
            <div class="row divPuntos" <?php echo $oculto2; ?>>
                <div class="col-xs-5">
                    <input type="text" id="tpin" placeholder="Monedero Electronico" class="form-control">
                </div>
                <div class="col-xs-1">
                    <button class="btn btn-primary" onclick="enterTpin();"><i class="fa fa-search" aria-hidden="true"></i></button>
                </div>
                <div class="col-xs-3 divPuntos">
                    <label>Puntos:</label>
                    <label id="pointsCardT">0</label>
                    <input type="hidden" id="pointsCardIn" class="form-control pull-left" value="">
                    <input type="hidden" id="pointsCardInActiv" class="form-control pull-left" value="">
                </div>
                <div class="col-xs-3 divPuntos">
                   <div class="form-group">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input type="checkbox" id="usarPuntos" name="point" value="1">Usar Puntos
                      </label>
                    </div>
                  </div>
                </div>
                <br>
            </div>
            <div class="row divPuntos">
               <!-- <div class="col-sm-3">
                    <label>Total a Pagar:</label>
                    <label id="lblTotalxPagar"></label> 
                </div>
                <div class="col-sm-3">
                    <label>Entregado:</label>
                    <label id="lblAbonoPago"></label> 
                </div> -->
        <?php
            if($configDatos[0]['puntos']==0){
                $oculto3 = 'style="display:none;"';
            }else{
                $oculto3 = '';
            }

        ?>
                 <div class="col-sm-6" <?php echo $oculto3; ?>>
                     <label>Puntos Venta:</label>
                     <label id="lblPuntosVenta"></label>
                 </div>
                 <div class="col-sm-6" <?php echo $oculto3; ?>>
                     <label>Puntos Generados:</label> <!---->
                     <label id="lblPuntosGene">0</label>
                 </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Forma de Pago</th>
                                <th>Cantidad</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody id="divDesglosePagoTablaCuerpo">

                        </tbody>
                    </table>
                </div>
            </div><?php

        // Valida si se debe de mostrar la propina
            if($ajustes_foodware['switch_propina'] == 1){
                $ajustes_json = json_encode($ajustes_foodware);
                $ajustes_json = str_replace('"', "'", $ajustes_json); ?>

                <script>caja.info_venta['ajustes'] = <?php echo $ajustes_json ?></script>
                <div class="row">
                    <div class="col-sm-4">
                        <select id="metodo_pago_propina" class="form-control" onchange="caja.changeMetProp();">
                            <option value="1">Efectivo</option>
                            <option value="2">Cheque</option>
                            <option value="3">Tarjeta de regalo</option>
                            <option value="4">Tarjeta de crédito</option>
                            <option value="5">Tarjeta de debito</option>
                            <option value="6">Crédito</option>
                            <option value="7">Transferencia</option>
                            <option value="8">Spei</option>
                            <option value="9">-No Identificado-</option>
                            <option value="21">Otros</option>
                            <option value="24">NA</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                            <input
                                id="porcentaje_propina"
                                onchange="caja.calcular_propina({porcentaje: $(this).val()})"
                                type="number"
                                min="0"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                            <input
                                id="monto_propina"
                                type="number"
                                min="0"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <button
                            class="btn btn-default"
                            onclick="caja.agregar_propina({
                                    metodo_pago: $('#metodo_pago_propina').val(),
                                    monto: $('#monto_propina').val()
                            })">
                            Propina
                        </button>

                    </div>
                    <!--<div class="col-sm-2" style="padding-top: 10px">
                        <label id="txt_total_propina">$ 0</label>
                    </div>-->
                </div><br />
                <div class="row" id="divReferenciaPagoPro" style="display:none;">
                    <div class="col-md-8">
                            <label id="lblReferenciaPro">Numero de tarjeta:</label>
                            <input type="text" id="txtReferenciaPro" class="form-control pull-left" value="">
                    </div>
                    <div class="col-sm-4" id="tarjetasRadiosPro">
                        <label id="erPro">Tipo de Tarjeta</label>
                      <!--  <select class="form-control" name="" id="">
                            <option value="1">VISA/<i class="fa fa-cc-visa" aria-hidden="true"></i></option>
                            <option value="2">MASTER CARD/<i class="fa fa-cc-mastercard" aria-hidden="true"></i></option>
                            <option value="3">AMERICAN EXPRESS/<i class="fa fa-cc-amex" aria-hidden="true"></i></option>
                        </select>-->
                       <div style="margin-top:1%;">
                        <label class="radio-inline"><input type="radio" name="tarRadioPro" value="1"><i class="fa fa-cc-visa" aria-hidden="true"></i></label>
                        <label class="radio-inline"><input type="radio" name="tarRadioPro" value="2"><i class="fa fa-cc-mastercard" aria-hidden="true"></i></label>
                        <label class="radio-inline"><input type="radio" name="tarRadioPro" value="3"><i class="fa fa-cc-amex" aria-hidden="true"></i></label>
                        </div>
                    </div>
                </div>
                                <br />
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Metodo</th>
                                    <th>Cantidad</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody id="divDesglosePagoTablaCuerpoPro">

                            </tbody>
                        </table>
                    </div>
                </div>



                <?php
            } ?>
                <div class="row">
                    <div class="col-sm-12" id="option-vendedor">
                                    <div class="row">
                                        <div class="col-sm-12" >
                                            <div class="input-group " id="vendedorSelect">
                                                <span class="input-group-addon" ><i class="fa fa-user-circle-o" aria-hidden="true"></i></span>
                                                <input type="text" class="typeahead form-control " id="vendedor-caja" placeholder="Vendedor comisionista" data-provide="typeahead" style="width: 105%;">

                                                <input type="hidden" id="hidenvendedor-caja" value="">
                                            </div>
                                        </div>
                                    </div>
                </div>
                </div>
                <br>    
           <!-- <div class="row">
                <div class="col-sm-5">
                    <label>Aun por Pagar:</label>
                    <label id="lblPorPagar"></label>
                </div>
                <div class="col-sm-2"></div>
                <div class="col-sm-5">
                    <label>Cambio:</label>
                    <label id="lblCambio"></label>
                </div>
            </div> -->
            </div>
            <div class="col-sm-4 well">
                <div class="row">
                    <div class="col-sm-4" align="right">
                        <h3 class="text-success">Total:</h3>
                    </div>
                    <div class="col-sm-8" align="left">
                        <h3 id="lblTotalxPagar"></h3>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-sm-4" align="right">
                        <label>Entregado:</label>
                    </div>
                    <div class="col-sm-8" align="left">
                        <label id="lblAbonoPago"></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4" align="right">
                        <label>Faltante:</label>
                    </div>
                    <div class="col-sm-8" align="left">
                        <label id="lblPorPagar"></label>
                    </div>
                </div>
                  <div class="row">
                    <div class="col-sm-4" align="right">
                        <h3 class="text-primary">Cambio:</h3>
                    </div>
                    <div class="col-sm-8" align="left">
                        <h3 id="lblCambio"></h3>
                    </div>
                </div>
                <?php 
                     if($ajustes_foodware['switch_propina'] == 1){

                    
                ?>
               
                <div class="row">
                     <div class="col-sm-4" align="right">
                        <h3 class="text-warning">Propina:</h3>
                    </div>
                    <div class="col-sm-8">
                        <h3 id="txt_total_propina"></h3>
                        
                    </div>
                </div>
                <?php 
                    }
                ?>
            </div>
            </div>
            <div class="row">

                <?php 
               
                    if(!in_array('36', $_SESSION['accelog_acciones'])){
                        echo '  <div class="col-sm-3">
                    <button id="pagarPagar" class="btn btn-success btn-block" onclick="javascript:caja.pagar();">Pagar    </button>
                </div>';
                    }
                ?>
               <!-- <div class="col-sm-3">
                    <button id="pagarPagar" class="btn btn-success btn-block" onclick="javascript:caja.pagar();">Pagar    </button>
                </div> -->
                <div class="col-sm-3">
                    <button class="btn btn-warning btn-block" onclick="javascript:$('#modalPagar').modal('toggle'); caja.suspender();">Preticket</button>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-primary btn-block" onclick="javascript:$('#modalPagar').modal('toggle');">Salir    </button>
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-danger btn-block" onclick="javascript:caja.cancelarCaja(true); $('#modalPagar').modal('toggle');">Cancelar  </button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

    <div id='modalComprobante' class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="labelTF">Ticket</h4>
                    <input type="hidden" id="idVentaTicket">
                </div>
                <div class="modal-body">
                    <div class="row rTouch">
                        <div class="col-md-12">
                            <iframe id="frameComprobante" src="" frameborder="0" style="float:left;height:300px;width:100%;"></iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Email:</label>
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="emailTicket">
                        </div>
                        <div class="col-sm-2 facSend">
                            <label>Asunto:</label>
                        </div>
                        <div class="col-sm-4 facSend">
                            <input type="text" class="form-control" id="asuntoTicket">
                        </div>
                    </div>
                   <!-- <div class="row facSend">
                        <div class="col-sm-2">
                            <label>Asunto:</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="asuntoTicket">
                        </div>
                    </div> -->
                    <br class="facSend">
                    <div class="row facSend">
                        <div class="col-sm-12">
                            <label>Mensaje:</label>
                        </div>
                    </div>
                     <div class="row facSend" align="center">
                        <div class="col-sm-12">
                            <textarea id="mensajeTicket" style="width:100%; resize:none" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="row">
                    <!--   <div class="col-md-6 col-md-offset-6">
                            <input type="text" id="emailTicket" class="form-control">
                            <button class="btn btn-primary" onclick="caja.enviarTicket();">Enviar</button>
                            <button class="btn btn-danger btnMenu" onclick="javascript:window.location.reload();">Salir</button>
                        </div> -->
                        <div id="emailTicketHide">
                        <div class="col-sm-2">
                          <!--   <label>Email</label> -->
                        </div>
                        <div class="col-sm-4">
                          <!--  <input type="text" class="form-control" id="emailTicket"> -->
                        </div>
                        <div class="col-sm-3">
                            <input type="hidden" id="inputRecibo">
                            <button onclick="caja.enviarTicket();" class="btn btn-primary btn-block"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar</button>
                        </div>
                        </div>
                        <div class="col-sm-3">
                            <button id="botonSalirModal" onclick="javascript:caja.reloadTodo();" class="btn btn-danger btnMenu btn-block">Salir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal de observaciones para facturar -->
    <div id='modal_Observaciones' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-warning">
                    <h4 class="modal-title">Observaciones</h4>
                </div>
                <div class="modal-body">
                <?php

                    if($versionFac=='3.3'){



                ?>
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Uso CFDI:</label>
                            <select id="usoCfdi" class="form-control">
                                <?php
                                    foreach ($usoCFDI['usos'] as $key => $value) {
                                        echo '<option value="'.$value['id'].'">('.$value['c_usocfdi'].') '.$value['descripcion'].'</option>';
                                    }

                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label>Metodo de Pago:</label>
                            <select id="mpCat" class="form-control">
                                <?php
                                    foreach ($usoCFDI['metodosdepago'] as $key => $value) {
                                        echo '<option value="'.$value['id'].'">('.$value['clave'].') '.$value['descripcion'].'</option>';
                                    }

                                ?>
                            </select>
                        </div>
                        <div class="col-xs-4"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Tipo de Relacion:</label>
                            <select id="tipoRelacionCfdi" class="form-control">
                                <option value="0">-Selecciona una Relacion-</option>
                                    <?php
                                    foreach ($usoCFDI['relaciones'] as $key => $value) {
                                        echo '<option value="'.$value['c_tiporelacion'].'">('.$value['c_tiporelacion'].') '.$value['descripcion'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-xs-6">
                            <label>UUID:</label>
                            <input type="text" class="form-control" id="cfdiUuidRelacion">
                        </div>
                    </div>
                    <?php
                }
                        if($configDatos[0]['seriesFactura']==1){
                            $xyz = '';
                        }else{
                            $xyz = 'style="display:none;"';
                        }
                    ?>
                    <div class="row" <?php echo $xyz; ?> >
                        <div class="col-xs-4">
                            <label>Serie:</label>
                            <select id="seriesCfdi" class="form-control">
                                <?php
                                    foreach ($seriesCfdi['series'] as $key => $value) {
                                        echo '<option value="'.$value['id'].'">'.$value['serie'].'</option>';
                                    }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label class="col-md-12 text-center" > Comentarios en </label>
                            <label class="col-md-12 text-center" id="lblComentarioE"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="txtareaObservaciones" style="width:100%; resize:none" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal de Facturacion -->
    <div id='modalFacturacion' class="modal fade facturarModales" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal" id="cierre">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o fa-lg"></i>  Facturar</h4>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Introduce El RFC</label>
                        </div>
                        <div class="col-sm-6">
                            <input type="text" id="rfcMoldal" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <button type="button" onclick="caja.revisaRfc();" class="btn btn-primary">Verifica RFC</button>
                        </div>
                    </div>
                    <br>
                    <div style="overlow:auto;overflow-y: hidden;">
                    <div class="row">

                        <div class="col-sm-12" style="display:none;" id="gridHidden">

                            <table class="table table-hover table-bordered" id="datosFactGrid" >
                                <thead>
                                    <tr>
                                        <th>RFC</th>
                                        <th>Razon Social</th>
                                        <th>Correo</th>
                                        <th>Pais</th>
                                        <th>Regimen F.</th>
                                        <th>Domicilio</th>
                                        <th>Numero</th>
                                        <th>Codigo Postal</th>
                                        <th>Colonia</th>
                                        <th>Estado</th>
                                        <th>Municipio</th>
                                        <th>Ciudad</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>

                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de cliente -->
    <div id='modalCliente' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-user-plus fa-lg" aria-hidden="true"></i> Agregar Cliente </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-sm-2">
                        <label class="control-label" for="email">ID</label>
                        <input id="idCliente" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['id'];}?>" readonly placeholder="(Autonumerico)">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Codigo</label>
                          <input id="codigo" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['codigo'];}?>">
                      </div>
                      <div class="col-sm-7">
                          <div class="alert alert-warning"><strong>NOTA:</strong>Si el cliente se registra con RFC y Razon Social, automaticamente se ingresaran a sus datos de facturacion.</div>
                      </div>

                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <label class="control-label">Nombre</label>
                        <input id="nombre" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombre'];}?>">
                      </div>
                      <div class="col-sm-6">
                          <label class="control-label">Razon Social</label>
                          <input id="razonSocial" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombre'];}?>">
                      </div>

                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">RFC</label>
                        <input id="rfc2" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['rfc'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">CURP</label>
                        <input id="curp" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['curp'];}?>">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Régimen Fiscal</label>
                          <input id="regimen" type="text" class="form-control">
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                          <label class="control-label">Tienda</label>
                          <input id="tienda" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['nombretienda'];}?>">
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <label class="control-label">Direccion</label>
                        <input id="direccion" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['direccion'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Exterior</label>
                        <input id="numext" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_ext'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Interior</label>
                        <input id="numint" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['num_int'];}?>">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Colonia</label>
                        <input id="colonia" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['colonia'];}?>">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Codio Postal</label>
                          <input id="cp" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['cp'];}?>">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Estado</label>
                          <select id="estado" class="form-control" onchange="caja.municipiosF();">
                            <option value="0">-Selecciona un estado</option>
                            <?php
                              foreach ($estados as $key => $value) {
                                echo '<option value="'.$value['idestado'].'">'.$value['estado'].'</option>';
                              }
                            ?>
                          </select>
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Municipio</label>
                          <select  id="municipios" class="form-control">
                            <option value='0'>-Selecciona un municipio--</option>

                          </select>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="control-label">Ciudad</label>
                            <input id="cdF" type="text" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Email</label>
                        <input id="email" class="form-control" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['email'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Celular</label>
                        <input id="celular" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['celular'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Telefono 1</label>
                        <input id="tel1" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono1'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Telefono 2</label>
                        <input id="tel2" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['telefono2'];}?>">
                      </div>
                    </div>



                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Dias de Credito</label>
                        <input id="diasCredito" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['dias_credito'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Limite de Credito</label>
                        <input id="limiteCredito" class="form-control numeros" type="text" value="<?php
                                  if(isset($datosCliente)){echo $datosCliente['basicos'][0]['limite_credito'];}?>">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Moneda</label>
                        <!--<input id="moneda" class="form-control" type="text" value=""> -->
                        <select id="moneda" class="form-control">
                          <?php

                            foreach ($moneda as $keyMon => $valueMon) {
                              echo '<option value="'.$valueMon['coin_id'].'">'.$valueMon['description'].'/'.$valueMon['codigo'].'</option>';
                            }

                          ?>
                        </select>
                      </div>
                      <div class="col-sm-3"></div>
                    </div>

                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Lista de Precio</label>
                        <select id="listaPrecio" class="form-control">
                          <?php
                          foreach ($listaPre as $key1 => $value1) {
                            echo '<option value="'.$value1['id'].'">'.$value1['nombre'].'</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-sm-3"></div>
                      <div class="col-sm-3"></div>
                      <div class="col-sm-3"></div>
                    </div>

                    <div class="row"><br>
                      <div class="col-sm-10"></div>
                      <div class="col-sm-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button type="button" class="btn btn-primary" onclick="caja.guardaCliente();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--- Modal Caracteristicass -->
    <div id="modalCarac" class="modal fade" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content panel-default">
                <div class="modal-header panel-heading">
                    <h4 id="modal-labelCr"></h4>
                    <input type="hidden" id="carIdProddiv">
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <img id="divImagenPro" height="250" width="250">
                        </div>
                        <div class="col-sm-6" >
                             <div class="row" id="seriesDiv" style="display:none;">
                                <div class="col-sm-3">
                                    <label>Series:</label>
                                </div>
                                <div class="col-sm-9">
                                    <select id="series" multiple="multiple" class="form-control"></select>
                                </div>
                             </div>
                               <div class="row" id="lotesDiv">
                                <div class="col-sm-3">
                                    <label>Lotes:</label>
                                </div>
                                <div class="col-sm-9">
                                    <select id="lotes" multiple="" class="form-control" onchange="cambiolote();"></select>
                                </div>
                             </div>
                             <div class="row" id="newlotes"></div>
                            <div class="row" id="prodCarcDiv"></div>

                            <div id="medicoReceta">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Médico:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <select id="medicoCedula" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label>Receta:</label>
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="text" id="receta" class="form-control">
                                    </div>
                                </div>
                                <di class="row">
                                    <div class="col-sm-12">
                                        <label><input type="checkbox" name="recetaRetenida" class="propiedades 7" id="recetaRetenida" > Receta retenida</label>
                                    </div>
                                </di>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnAgregarProductoCaracteristicas" class="btn btn-primary" onclick="caja.agregaCarac();">Agregar</button>
                    <button type="button" class="btn btn-danger"  data-dismiss="modal" onclick="caja.cancelaCarac();"> Cancelar</button>
                </div>
            </div>
        </div>
    </div>





    <!-- Modal cliente guardado con exito -->
  <div id="modalSuccess" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header panel-heading">
                <h4 id="modal-label">Exito!</h4>
            </div>
            <div class="modal-body">
                <p>Tu Cliente se guardo existosamente</p>
            </div>
            <div class="modal-footer">
                <button id="modal-btnconf2-uno" type="button" class="btn btn-default" onclick="caja.cierramodales();">Continuar</button>
            </div>
        </div>
    </div>
  </div>

      <!-- Modal cliente guardado con exito -->
  <div id="modal_autorizar_corteP" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content panel-success">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Autorizar Corte</h4>
                
            </div>
            <div class="modal-body">
                <h3><small>Introduce la contraseña:</small></h3>
                <div class="input-group input-group-lg">
                    <span class="input-group-addon"> <i class="fa fa-unlock-alt"></i> </span>
                    <input id="pass_corte" onkeypress="if(((document.all) ? event.keyCode : event.which)==13) autorizar_corte()" class="form-control" type="password">
                    <span class="input-group-btn">
                        <button onclick="autorizar_corte()" class="btn" style="background-color: #209775; color:white;" type="button">
                            <i class="fa fa-check"></i> Autorizar
                        </button> 
                    </span>
                </div>
            </div>
            <div class="modal-footer">                
            </div>
        </div>
    </div>
  </div>


<!-- Modal de Ventas -->
    <div id='modalVentasList' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-shopping-cart fa-lg"></i>  Ventas</h4>
                </div>
                <div class="modal-body">
               <!-- <div class="col-sm-12"> -->
                    <div class="row">
                        <div class="col-sm-1">
                            <label>ID Venta</label>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" id="inputidVenta" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info" onclick="caja.buscarVenta();"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div style="height:400px;overflow:auto;">
                            <table class="table table-bordered table-hover" id="tableSales">
                                <thead>
                                    <tr>
                                        <th>Folio</th>
                                        <th>Tipo Doc.</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Empleado</th>
                                        <th>Sucursal</th>
                                        <th>Estatus</th>
                                        <th>Impuestos</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
             <!--   </div> -->
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Modal modalVentasDetalle -->
<!-- Modal de Ventas -->
    <div id='modalVentasDetalle' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" >Venta <span id="idFacPanel"></span></h4>
                </div>
                <div class="modal-body">
                    <div style="height:400px;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                    <input id="idVentaHidden" type="hidden">
                                <table class="table table-bordered" id="tableSale">
                                    <thead>
                                        <tr>
                                            <th>Codigo</th>
                                            <th>Descripcion</th>
                                            <th>Cantidad</th>
                                            <th>Precio U.</th>
                                           <!-- <th>Descuento</th> -->
                                            <th>Impuestos</th>
                                            <th>Importe</th>
                                            <th>Devolver</th>
                                            <th>Movimientos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaVenta">
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    <div class="row">
                    <div class="col-sm-6">
                        <div id="pay">

                        </div>
                    </div>
                    <div class="col-sm-3" id="impuestosDiv"></div>
                    <div class="col-sm-3">
                        <div id="subtotalDiv" class="totalesDiv"></div>
                        <div id="ddiv" class="totalesDiv"></div>
                        <div id="totalDiv" class="totalesDiv"></div>
                        <!-- inputs donde se guarda el total y subtotal -->
                        <input type="hidden" id="inputSubTotal">
                        <input type="hidden" id="inputTotal">
                    </div>
                    </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-3">
                            <textarea id="idComentarioDevolucion" class="form-control" rows="2" placeholder="Escribe un comentario"></textarea>
                        </div>
                        <div class="col-sm-2">
                            <select id="idAlmacenDevolucion" class="form-control">

                                <?php foreach ($selectAlmacenes as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"> <?= $value['nombre'] ?> </option>
                                <?php } ?>
                                <option value="0"> Merma </option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <button id="devButton" class="btn btn-default" onclick="javascript:caja.devolverVenta();"><i class="fa fa-undo" aria-hidden="true"></i> Devolver</button>
                        </div>

                        <div class="col-md-6 ">
                         <!--   <button class="btn btn-default" onclick="javascript:caja.devolver();"><i class="fa fa-undo" aria-hidden="true"></i> Devolver</button> -->
                            <button id="cancelButton" class="btn btn-danger" onclick="javascript:caja.cancelaVenta();"> <i class="fa fa-ban" aria-hidden="true"></i> Cancelar</button>
                            <button class="btn btn-primary" onclick="javascript:caja.reImprimeticket();"><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>
                            <button class="btn btn-warning" onclick="javascript:$('#modalVentasDetalle').modal('hide');"><i class="fa fa-times" aria-hidden="true"></i> Salir</button>
                          <!--  <button class="btn btn-info" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal de printipagos -->
    <div id='modalProntipagos' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-default">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-shopping-cart fa-lg"></i>  Prontipagos</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Producto:</label>
                        </div>
                        <div class="col-sm-8">
                            <select id="prodPronti">
                                <option value="0">-Selecciona un servicio-</option>
                                <?php
                                foreach ($proPPagos as $key => $value) {
                                    echo '<option value="'.$value['codigo'].'" precio="'.$value['precio'].'" idProducto="'.$value['id'].'">'.$value['nombre'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                             <label>Referencia (Telefono, numero, otro...):</label>
                        </div>
                        <div class="col-sm-8">
                            <input id="pronti_referencia" type="text" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Confirmar referencia (Telefono, numero, otro...):</label>
                        </div>
                        <div class="col-sm-8">
                            <input id="pronti_confirmar_referencia" type="text" class="form-control">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Monto:</label>
                        </div>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input id="pronti_monto" type="text" class="form-control">
                                <span class="input-group-btn">
                                    <button id="pronti_editar" class="btn btn-default" type="button"><i class="fa fa-pencil"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                         <button class="btn btn-success btnMenu" onclick="javascript:caja.prontipagosAccion();">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modal prontipagos -->

    <!-- Modal de detalle de devoluciones  -->
    <div id='modalDetalleMovimientoDevolucion' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="idMovimientoDevolucionProducto">  </h4>
                </div>
                <div class="modal-body">
                    <div style="height:400px;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                    <input id="idVentaHidden" type="hidden">
                                <table class="table table-bordered" id="tableSale">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Cantidad</th>
                                            <th>ID Almacen</th>
                                            <th>Comentario</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaMovimientosDevolucion">
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="pay">

                                </div>
                            </div>
                            <div class="col-sm-3" id="impuestosDiv"></div>
                            <div class="col-sm-3">
                                <div id="subtotalDiv" class="totalesDiv"></div>
                                <div id="ddiv" class="totalesDiv"></div>
                                <div id="totalDiv" class="totalesDiv"></div>
                                <!-- inputs donde se guarda el total y subtotal -->
                                <input type="hidden" id="inputSubTotal">
                                <input type="hidden" id="inputTotal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button class="btn btn-warning" onclick="javascript:$('#modalDetalleMovimientoDevolucion').modal('hide');"><i class="fa fa-times" aria-hidden="true"></i> Salir</button>
                          <!--  <button class="btn btn-info" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal de Formulario Facturacion -->
    <div id='modalCuestion' class="modal fade facturarModales" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header modal-header-warning">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o fa-lg"></i>  Facturar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            No se encontraron coincidencias. ¿Quieres dar de alta tus datos para facturacion.?
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-4">
                            <button class="btn btn-success btn-block" onclick="caja.despliegaForm();">Dar de Alta los datos</button>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-danger btn-block">Salir</button>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de formulario de datos de Facturacion -->
    <div id='modalFormFact' class="modal fade facturarModales" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o fa-lg"></i>  Facturar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1">
                            <div id="newOrUpd"></div>
                        </div>
                        <div class="col-sm-1">
                            <input type="hidden" id="comFacId">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>RFC</label>
                            <input type="text" class="form-control formF" id="rfcFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Razon Social <span>*</span></label>
                            <input type="text" class="form-control formF" id="razonSFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Correo<span>*</span></label>
                            <input type="text" class="form-control formF" id="emailFormF">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Pais<span>*</span></label>
                            <input type="text" class="form-control formF" id="paisFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Regimen Fiscal<span>*</span></label>
                            <input type="text" class="form-control formF" id="regimenFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Domicilio<span>*</span></label>
                            <input type="text" class="form-control formF" id="domicilioFormF">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Numero Ext. int.<span>*</span></label>
                            <input type="text" class="form-control formF" id="numeroFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Codigo Postal<span>*</span></label>
                            <input type="text" class="form-control formF" id="cpFormF">
                        </div>
                        <div class="col-sm-4">
                            <label>Colonia<span>*</span></label>
                            <input type="text" class="form-control formF" id="coloniaFormF">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <label>Estado<span>*</span></label>
                            <select id="estadoFormF" class="form-control formF" onchange="caja.municipiosFact();">
                                <option value="0">-Selecciona un Estado-</option>
                                <?php
                                    foreach ($estados as $keyE => $valueE) {
                                        echo '<option value="'.$valueE['idestado'].'">'.$valueE['estado'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Municipio<span>*</span></label>
                            <select id="municipioFormF" class="form-control formF">
                                <option value="0">-Selecciona un Municipio-</option>
                                <?php
                                    foreach ($municipios as $keyE => $valueE) {
                                        echo '<option value="'.$valueE['idmunicipio'].'">'.$valueE['municipio'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label>Ciudad<span>*</span></label>
                            <input type="text" class="form-control formF" id="ciudadFormF">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <div id="butlo" style="display:none;"><i class="fa fa-refresh fa-spin fa-3x fa-fw margin-bottom"></i></div>
                            <div id="but">
                                <button class="btn btn-primary" onclick="caja.guardaFormF();"><i class="fa fa-floppy-o"></i> Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id='modalCodigoVenta' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal" >&times;</button>
                    <h4 class="modal-title"><i class="fa fa-file-text-o fa-lg"></i>  Venta a Facturar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="idComunFactu">
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Ingresa el Id de venta del Ticket</label>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" id="codigoTicket" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-default" onclick="caja.buscaTicket();"> Verifica Ticket</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div style="height:400px; display:none;" id="ticketHideDiv">
                                <iframe id="ticketDiv" src="" frameborder="0" style="float:left;height:100%;width:100%;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <div id="facB" style="display:none;">
                                <button class="btn btn-success" onclick="caja.factSale();"><i class="fa fa-floppy-o"></i> Facturar</button>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Div del inicio de Caja Parcial -->
    <div id='inicio_cajaP' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Inicializar caja Corte Parcial</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="divContSucursalP">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Saldo con el que se Inicio la caja:</label>
                            <label id="saldocajaP"></label>
                            <input type="hidden" id="saldocajaInputP">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label>Ingrese con cuanto inicia caja:</label>  
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" onkeypress="return caja.isNumberKey(event)" id="iniciocajaP" maxlength="8" value="">
                            </div>                                                    
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciarP();">Iniciar</button>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Div del inicio de Caja -->
    <div id='inicio_caja' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Inicializar caja</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="divContSucursal">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Saldo actual en caja:</label>
                            <label id="saldocaja"></label>
                            <input type="hidden" id="saldocajaInput">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Ingrese con cuanto inicia caja:</label>
                            <input type="text" class="form-control" onkeypress="return caja.isNumberKey(event)" id="iniciocaja" maxlength="8" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-danger btnMenu btn-block" onclick="javascript:caja.cajaCancelar();">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- Div modal Password General -->
    <div id='modalPassDes' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Password</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"><label>Password:</label></div>
                        <div class="col-sm-10"><input type="password" id="modPass" class="form-control"><input type="hidden" id="passhide"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                         <!--   <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button> -->
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btnMenu btn-block" onclick="javascript:caja.validaPassDes();">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalComplemento">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Complementos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4"><label>Complemento</label></div>
                    <div class="col-sm-8">
                        <select id="complementosSel" class="form-control" onchange="caja.cargaComple();">
                            <option value="0">-Selecciona-</option>
                            <?php
                                foreach ($complementos['complementos'] as $key => $value) {
                                    echo '<option value="'.$value['id'].'">'.$value['nombre'].'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                        <form id="fromExtra" method="post">


                        </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" onclick="caja.cancelaComple();">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="caja.guardarComple();">Guardar</button>
            </div>
        </div>
    </div>
</div>
    <!-- Div modal Password Por producto -->
    <div id='modalPassDesPP' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Password</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"><label>Password:</label></div>
                        <div class="col-sm-10">
                            <input type="password" id="modPass2" class="form-control">
                            <input type="hidden" id="contrasenaPP">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                         <!--   <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button> -->
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btnMenu btn-block" onclick="javascript:caja.hazUnTruco2();">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
    <div class="modal" id="parcial" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Corte Parcial</h5>
                    <input type="hidden" id="idempleado" value="<?php echo $_SESSION['accelog_idempleado']; ?>">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="row">
                        <label>Monto con que se Inicio la caja en corte normal:&nbsp </label><label id="lbinicio"></label>
                    </div>

                    <div class="row">
                        <i class="fa fa-money fa-2x" aria-hidden="true"></i>
                    </div>

                    
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 20</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso20C" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 50</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso50C" >
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 100</span>
                                <input type="number" class="form-control arqueo"  value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso100C" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 200</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso200C" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 500</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso500C" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 1000</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso1000C" >
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <i class="fa fa-usd fa-2x" aria-hidden="true"></i>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 1</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso1C">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 2</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso2C" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 5</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso5C" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 10</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="peso10C" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">5¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="centavo5C" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">10¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="centavo10C" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">20¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="centavo20C" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">50¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoC(this)" id="centavo50C" >
                            </div>
                        </div>
                    </div>
                    <br>                 
                    <div class="row">
                        <label>Reportado</label>
                        <input type="text" id="reportado">
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="caja.corteP();">Aceptar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Corte de Caja -->
    <div id='modalCorteDeCaja' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width:98%">
            <div class="modal-content">
                <div class="modal-header modal-header-warning">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-scissors"></i>  Corte de Caja</h4>
                </div>
                <div class="modal-body ">
                <!--    <div class="row">
                        <div class="col-sm-3">
                            <label>Desde</label>
                            <input type="text" id="desdeCut" class="form-control" readonly>
                        </div>
                        <div class="col-sm-3">
                            <label>Hasta</label>
                            <input type="text" id="hastaCut" class="form-control" readonly>
                        </div>
                    </div> -->
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">

                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <h3 class="panel-title">Saldos</h3>
                                               <!-- <label>Desde 2030-9494-90</label>
                                                <label>Hasta 4849-900-009</label> -->
                                                </div>
                                               <div class="col-sm-4">
                                                    <label>Desde: </label><label id="desdeCutText"></label>
                                                    <input type="hidden" id="desdeCut" class="form-control" readonly>
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>Hasta: </label><label id="hastaCutText"></label>
                                                    <input type="hidden" id="hastaCut" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <label>S.Inicial</label>
                                                        </span>
                                                        <input type="text" class="form-control" id="saldo_inicial" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <label>Ventas</label>
                                                        </span>
                                                        <input type="text" class="form-control" id="monto_ventas" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="input-group" id="divdisponible">
                                                        <span class="input-group-addon">
                                                            <label>Disponible</label>
                                                        </span>
                                                        <input type="text" class="form-control" id="saldo_disponible" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <label>Retiro</label>
                                                        </span>
                                                        <input type="text" class="form-control numeros" id="retiro_caja" style="background-color: #FFCCDD;">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <label>Deposito</label>
                                                        </span>
                                                        <input type="text" class="form-control numeros" id="deposito_caja" style="background-color: #A9F5A9;">
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                        
                                                        <?php 
                                                         if($configDatos[0]['cortesP'] == 1 ){                                                            
                                                            //$onclick2 = 'caja.corteParcialF();'; // modal arqueo
                                                            $onclick2 = 'caja.newCut();'; // directo al corte

                                                         }else{
                                                            $onclick2 = 'caja.newCut();'; // directo al corte
                                                         }
                                                     ?>



                                                        <button type="button" class="btn btn-primary btn-block" 
                                                        id="makeCut" 
                                                        onclick="<?php echo $onclick2; ?>">
                                                        Hacer Corte </button>

                                                </div>
                                            </div>
                                       <!--     <div class="row">
                                                <div class="col-sm-12">
                                                    <h4>Depositos/Retiros</h4>
                                                </div>
                                            </div> -->
                                        <!--    <div class="row">
                                                <div class="col-sm-4">
                                                    <label>Retiro de Caja $</label>
                                                    <input type="text" class="form-control numeros" id="retiro_caja" style="background-color: #FFCCDD;">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>Deposito de Caja $</label>
                                                    <input type="text" class="form-control numeros" id="deposito_caja" style="background-color: #A9F5A9;">
                                                </div>

                                                <div class="col-sm-3">
                                                    <div style="padding-top:8%;">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="caja.newCut();">Hacer Corte </button>
                                                    </div>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">
                            <button class="form-control btn btn-primary" data-toggle="modal" data-target="#modalArqueo" onclick="caja.arqueoButtonAction();">
                                Arqueo
                            </button>
                        </div>
                        <div class="col-sm-2" hidden>
                            <select name="tipoCorte" id="tipoCorte" class="form-control" placeholder="Tipo de corte" required>
                            <?php
                            if( !isset( $_SESSION['corteParcial']['inicial'] ) ) { ?>
                                <option value="1">Corte Normal</option>
                            <?php } ?>
                                <option value="2">Corte Parcial</option>
                                <option value="3">Corte Z</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div style="height:350px;overflow:auto;">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Pagos</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridPagosCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Venta</th>
                                                    <th>Cliente</th>
                                                    <th>Fecha</th>
                                                    <th>EF</th>
                                                    <th>TC</th>
                                                    <th>TD</th>
                                                    <th>CR</th>
                                                    <th>CH</th>
                                                    <th>TRA</th>
                                                    <th>SPEI</th>
                                                    <th>TR</th>
                                                    <th>NI</th>
                                                    <th>TVales</th>
                                                    <th>Cortesía</th>
                                                    <th>Otros</th>
                                                    <th>Cambio</th>
                                                    <th>Impuestos</th>
                                                    <th>Monto</th>
                                                    <th>Des.</th>
                                                    <th>Importe</th>
                                                    <th>Ingreso(EF-Cambio)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12"><h4>Tarjetas</h4></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridTarjetas">
                                            <thead>
                                                <tr>
                                                    <th>Tarjeta</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Productos Vendidos</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridProductosCut">
                                            <thead>
                                                <tr>
                                                    <th>Codigo</th>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Descuento</th>
                                                    <th>Impuestos</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Retiros de Caja</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridRetirosCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Retiro</th>
                                                    <th>Fecha</th>
                                                    <th>Concepto</th>
                                                    <th>Usuario</th>
                                                    <th>Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Abonos de Caja</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridAbonosCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Abono</th>
                                                    <th>Fecha</th>
                                                    <th>Concepto</th>
                                                    <th>Usuario</th>
                                                    <th>Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Propinas </h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridPropinasCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Venta</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Devoluciones </h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridDevolucionesCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Venta</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Cancelaciones</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridCancelacionesCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Venta</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h4>Facturas</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover" id="gridFacturasCut">
                                            <thead>
                                                <tr>
                                                    <th>ID Venta</th>
                                                    <th>Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal de Corte de Parcial Final -->
    <div class="modal" id="parcialF" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Corte Parcial</h5>
                    <input type="hidden" id="idempleadoF" value="<?php echo $_SESSION['accelog_idempleado']; ?>">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <label>Monto con que se Inicio la caja en corte normal:&nbsp </label><label id="lbinicioF"></label>
                    </div>

                    <div class="row">
                        <i class="fa fa-money fa-2x" aria-hidden="true"></i>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 20</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso20CF" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 50</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso50CF" >
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 100</span>
                                <input type="number" class="form-control arqueo"  value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso100CF" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 200</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso200CF" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 500</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso500CF" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-addon">$ 1000</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso1000CF" >
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <i class="fa fa-usd fa-2x" aria-hidden="true"></i>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 1</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso1CF">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 2</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso2CF" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 5</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso5CF" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">$ 10</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="peso10CF" >
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">5¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="centavo5CF" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">10¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="centavo10CF" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">20¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="centavo20CF" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group">
                                <span class="input-group-addon">50¢</span>
                                <input type="number" class="form-control arqueo" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueoCF(this)" id="centavo50CF" >
                            </div>
                        </div>
                    </div>
                    <br>

                    
                    <div class="row">
                        <label>Reportado</label>
                        <input type="text" id="reportadoF">                    
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="caja.cortePF(0);">Aceptar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de Ventas, tabla ver garantías  -->
    <div id='modalGarantiaVenta' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width:98%">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-shopping-cart fa-lg"></i>  Garantías</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-1">
                            <label>ID Venta</label>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" id="idGarantiaVenta" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info" onclick="caja.buscarGarantiaVenta();"><i class="fa fa-search" aria-hidden="true"></i> Buscar Venta</button>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-2 form-group">
                            <label for="iddGarantiaVenta">ID:</label>
                            <input type="text" id="iddGarantiaVenta" class="form-control" disabled />
                        </div>
                        <div class="col-sm-7 form-group">
                            <label for="clienteGarantiaVenta">Cliente:</label>
                            <input type="text" id="clienteGarantiaVenta" class="form-control" disabled />
                        </div>
                        <div class="col-sm-3 form-group">
                            <label for="fechaGarantiaVenta">Fecha:</label>
                            <input type="text" id="fechaGarantiaVenta" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12" style="overflow:auto;">
                            <table class="table table-hover table-fixed" style="background-color:#F9F9F9; border:1px solid #c8c8c8;" id="tableGrid">
                                <thead>
                                  <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>P. Unitario</th>
                                    <th>Impuesto</th>
                                    <th>Subtotal</th>
                                    <th>Garantía</th>
                                    <th>Vigencia</th>
                                    <th>Reclamar</th>
                                    <th>Movimientos</th>
                                  </tr>
                                </thead>
                                <tbody id="tablaGarantiaProducto">

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <textarea id="idComentarioGarantia" class="form-control" rows="2" placeholder="Escribe un comentario"></textarea>
                        </div>
                        <div class="col-sm-4">
                            <select id="idAlmacenGarantia" class="form-control">
                                <?php foreach ($selectAlmacenes as $key => $value) { ?>
                                <option value="<?= $value['id'] ?>"> <?= $value['nombre'] ?> </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-info" onclick="caja.reclamarGarantia();"> Reclamar Garantia </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

<!-- Modal de detalle de garantías  -->
    <div id='modalDetalleMovimientoGarantia' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="width:90%">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="idMovimientoGarantiaProducto">  </h4>
                </div>
                <div class="modal-body">
                    <div style="height:400px;overflow:auto;">
                        <div class="row">
                            <div class="col-sm-12">
                                    <input id="idVentaHidden" type="hidden">
                                <table class="table table-bordered" id="tableSale">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Cantidad</th>
                                            <th>ID Almacen</th>
                                            <th>Tipo movimiento</th>
                                            <th>Comentario</th>
                                            <th>Fecha</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaMovimientosGarantia">
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div id="pay">

                                </div>
                            </div>
                            <div class="col-sm-3" id="impuestosDiv"></div>
                            <div class="col-sm-3">
                                <div id="subtotalDiv" class="totalesDiv"></div>
                                <div id="ddiv" class="totalesDiv"></div>
                                <div id="totalDiv" class="totalesDiv"></div>
                                <!-- inputs donde se guarda el total y subtotal -->
                                <input type="hidden" id="inputSubTotal">
                                <input type="hidden" id="inputTotal">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button class="btn btn-warning" onclick="javascript:$('#modalDetalleMovimientoGarantia').modal('hide');"><i class="fa fa-times" aria-hidden="true"></i> Salir</button>
                          <!--  <button class="btn btn-info" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal arqueo de caja -->
<div id="modalArqueo" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Arqueo de caja</h4>
      </div>
      <div class="modal-body">

      <div class="row">
          <i class="fa fa-money fa-4x" aria-hidden="true"></i>
      </div>
      <div class="row">
        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">$ 20</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso20" >
            </div>
        </div>
        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">$ 50</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso50" >
            </div>
        </div>
        <div class="col-sm-4">
            <div class="input-group">
                <span class="input-group-addon">$ 100</span>
                <input type="number" class="form-control"  value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso100" >
            </div>
        </div>
      </div>
      <div class="row">
          <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-addon">$ 200</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso200" >
            </div>
          </div>
          <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-addon">$ 500</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso500" >
            </div>
          </div>
          <div class="col-sm-4">
              <div class="input-group">
                <span class="input-group-addon">$ 1000</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso1000" >
            </div>
          </div>
      </div>
      <br>
      <div class="row">
          <i class="fa fa-usd fa-4x" aria-hidden="true"></i>
      </div>
      <div class="row">
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">$ 1</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso1">
            </div>
        </div>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">$ 2</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso2" >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">$ 5</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso5" >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="input-group">
                <span class="input-group-addon">$ 10</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="peso10" >
            </div>
        </div>
      </div>
      <div class="row">
          <div class="col-sm-3">
              <div class="input-group">
                <span class="input-group-addon">5¢</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="centavo5" >
            </div>
          </div>
          <div class="col-sm-3">
              <div class="input-group">
                <span class="input-group-addon">10¢</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="centavo10" >
            </div>
          </div>
          <div class="col-sm-3">
              <div class="input-group">
                <span class="input-group-addon">20¢</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="centavo20" >
            </div>
          </div>
          <div class="col-sm-3">
              <div class="input-group">
                <span class="input-group-addon">50¢</span>
                <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarArqueo(this)" id="centavo50" >
            </div>
          </div>
      </div>

      <br><br>
        <div class="row">
            <div class="col-sm-12 ">
                <div class="input-group">
                    <span class="input-group-addon"> Disponible </span>
                    <input type="number" class="form-control" value="0" style="text-align: right;" id="disponibleArqueo" disabled >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="input-group">
                    <span class="input-group-addon"> Total </span>
                    <input type="number" class="form-control" value="0" style="text-align: right;" id="totalArqueo" disabled >
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="aceptarArqueo" onclick="caja.aceptarArqueo(this)" disabled >Aceptar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal pagar denomincaciones de caja -->
<div id="modalPagoDenominacion" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Pago de caja</h4>
      </div>
      <div class="modal-body">

      <div class="container" style="width: 100%;">
          <div class="row">
              <i class="fa fa-money fa-4x" aria-hidden="true"></i>
          </div>
          <div class="row">
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 20</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD20" >
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 50</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD50" >
                </div>
            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 100</span>
                    <input type="number" class="form-control"  value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD100" >
                </div>
            </div>
          </div>
          <div class="row">
              <div class="col-sm-4">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 200</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD200" >
                </div>
              </div>
              <div class="col-sm-4">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 500</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD500" >
                </div>
              </div>
              <div class="col-sm-4">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 45% !important;">$ 1000</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD1000" >
                </div>
              </div>
          </div>
          <br>
          <div class="row">
              <i class="fa fa-usd fa-4x" aria-hidden="true"></i>
          </div>
          <div class="row">
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">$ 1</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD1">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">$ 2</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD2" >
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">$ 5</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD5" >
                </div>
            </div>
            <div class="col-sm-3">
                <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">$ 10</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="pesoD10" >
                </div>
            </div>
          </div>
          <div class="row">
              <div class="col-sm-3">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">5¢</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="centavoD5" >
                </div>
              </div>
              <div class="col-sm-3">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">10¢</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="centavoD10" >
                </div>
              </div>
              <div class="col-sm-3">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">20¢</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="centavoD20" >
                </div>
              </div>
              <div class="col-sm-3">
                  <div class="input-group">
                    <span class="input-group-addon" style="width: 50% !important;">50¢</span>
                    <input type="number" class="form-control" value="0" min="0" style="text-align: right;" onchange="caja.validarPagoDenominacion(this)" id="centavoD50" >
                </div>
              </div>
          </div>

          <br><br>
            <div class="row">
                <div class="col-sm-12 ">
                    <div class="input-group">
                        <span class="input-group-addon" > Monto a pagar</span>
                        <input type="number" class="form-control" value="0" style="text-align: right;" id="aPagar" disabled >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-addon"> Monto pagado </span>
                        <input type="number" class="form-control" value="0" style="text-align: right;" id="totalPago" disabled >
                    </div>
                </div>
            </div>
      </div>



      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="aceptarPago" onclick="caja.aceptarPago(this)"  >Aceptar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!--<div class="modal fade" id="modalDesgeneralBot" role="dialog" style="z-index:1051;" >
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Descuento General</h4>
        </div>
        <div class="modal-body">

        </div>
      </div>
    </div>
  </div> -->
    <div class="modal fade" id="modalDesgeneralBot" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header modal-header-primary">
          <h4 class="modal-title">Descuento General</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-7">
                    <input id="descuentoGeneral" class="form-control numeros" placeholder="Desc %" type="text">
                </div>
                <div class="col-sm-3">
                    <button class="btn btn-default" onclick="cierraModDes();">Aplicar</button>
                </div>
                <div class="col-sm-1"></div>
            </div><br>
            <div class="row">
                <div class="col-sm-12">
                    <button class="btn btn-warning" onclick="caja.eliminaDescuento();" style="width:100%;">Elimina Desc.</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!--- Modal de Ventas suspendidas -->
    <div class="modal fade" id="modalVentasSuspendidas" role="dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header modal-header-primary">
          <h4 class="modal-title">Ventas Suspendidas</h4>
        </div>
        <div class="modal-body">
            <div class="row">
            <form class="form-horizontal" role="form">
                <div class="form-group col-xs-12">
                    <div class="col-xs-12">
                        <label class="col-xs-2 control-label">Ventas suspendidas:</label>
                        <select id="s_cliente" class="form-control">
                            <option value="0" selected>Selecciona</option>
                        </select>
                    </div>
                    <br>
                    <div id="divAccionesSuspender" class="form-group col-xs-12">
                        <input type="button" value="Cargar" class="btn btn-success btn btn-success col-xs-offset-1 col-xs-3" onclick="caja.cargarSuspendida();">
                        <input id="sselimina" type="button" class="btn btn-danger col-xs-offset-1 col-xs-3" value="Eliminar" onclick="caja.eliminarSuspendida();">
                        <input  id="ssnueva" class="btn btn-success col-xs-offset-1 col-xs-3"  type="button" value="Realizar nueva venta" onclick="caja.cancelarCaja();">

                    </div>
                </div>
            </form>
            </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Fin de modal de ventas suspendidas -->

  <!-- modal Retiro -->
  <!-- Modal del Form -->
  <div class="modal fade" id="modalformRetiro" <?php echo ($retiroObligado) ? 'role="dialog" data-backdrop="static" data-keyboard="false"' : '' ; ?> >
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header modal-header-primary">
          <h4 class="modal-title"><i class="fa fa-minus" aria-hidden="true"></i> Nuevo Retiro</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <label>Disponible $</label>
                    <input type="text" class="form-control numeros" id="saldo_disponibleR" readonly>
                </div>
                <div class="col-sm-12">
                    <label>Cantidad a Retirar</label>
                    <input type="text" class="form-control numeros" id="cantidadRetiro">
                </div>
                <div class="col-sm-12">
                    <label>Concepto</label>
                    <textarea  cols="30" rows="5" id="concepto" class="form-control"></textarea>
                </div>
            </div>
        </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                             <button id="bRetira" class="btn btn-primary btn-block" onclick="caja.retira();"> <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                        </div>
                    </div>
                </div>
      </div>
    </div>
  </div>
  <!-- Fin modal retiro -->
  <!-- Modal abono -->
  <!-- Modal de printipagos -->
    <div id='modalNotasCreditoE' class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-default">
            <div class="modal-content">
                <div class="modal-header modal-header-default">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Notas de Credito</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4"><label>Factura Relacionada(UUID):</label></div>
                        <div class="col-sm-8"><input type="text" class="form-control" id="uuidRelacNota"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button class="btn btn-danger btnMenu" onclick="javascript:caja.cierraNota();">Cancelar</button>
                            <button class="btn btn-success btnMenu" onclick="javascript:caja.cargaNotaCredito();">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modal prontipagos -->
    <!-- Modal de Preticket -->
    <!--<a class="btn btn-primary" data-toggle="modal" href='#modalPreticketsList'>Trigger dededee</a> -->
    <div class="modal fade" id="modalPreticketsList">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Pretickets</h4>
                </div>
                <div class="modal-body">
                   <div class="row">
                       <div class="col-sm-12">
                           <table class="table table-bordered table-hover" id="preticketTable">
                               <thead>
                                   <tr>
                                       <th>Folio</th>
                                       <th>Cliente</th>
                                       <th>Fecha</th>
                                       <th>Monto</th>
                                       <th>Empleado</th>
                                       <th>Sucursal</th>
                                       <th>Acciones</th>
                                   </tr>
                               </thead>
                               <tbody>
                                <?php 
                                    foreach ($pretickets['pretickets'] as $key => $value) {
                                        echo '<tr>';
                                        echo '<td>'.$value['id'].'</td>';
                                        echo '<td>'.$value['cliente'].'</td>';
                                        echo '<td>'.$value['fecha'].'</td>';
                                        echo '<td>'.number_format($value['s_monto'],2).'</td>';
                                        echo '<td>'.$value['usuario'].'</td>';
                                        echo '<td>'.$value['sucursal'].'</td>';
                                        echo '<td>';
                                        //echo "<button class='btn btn-warning' onclick='caja.cargarPreticket(".$value['id'].",".$value['idCliente'].")'><i class='fa fa-undo'></i></button>";
                                        echo '<button class="btn btn-warning" onclick="caja.cargarPreticket('.$value['id'].','.$value["idCliente"].',\''.$value["cliente"].'\');"><i class="fa fa-undo"></i></button>';
                                        echo '<button class="btn btn-primary" onclick="caja.impPreticket('.$value['id'].')"><i class="fa fa-print"></i></button>';
                                        echo '<button class="btn btn-danger" onclick="caja.eliminaPreticket('.$value['id'].')"><i class="fa fa-ban"></i></button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                ?>
                               </tbody>
                           </table>
                       </div>
                   </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                   <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </div>
            </div>
        </div>
    </div>
    <!-- Fin modal Preticket -->
    <!-- Modal del Form -->
  <div class="modal fade" id="modalformAbono" role="dialog">
    <div class="modal-dialog modal-md modal-primary">
      <div class="modal-content">
        <div class="modal-header modal-header-primary">
          <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Nuevo Abono</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <label>Cliente</label>
                    <select class="form-control" id="clienteAbono" onchange="caja.buscaCargos();">
                        <option value="0">-Selecciona Cliente-</option>
                        <?php
                            foreach ($clientes['clientes'] as $key => $value) {
                               echo '<option value="'.$value['id'].'">'.$value['codigo'].'/'.$value['nombre'].'</option>';
                            }
                        ?>
                    </select>
                </div>
              <!-- <div class="col-sm-12">
                    <label>Cargos</label>
                    <select id="cargosAbono"><option value="0">-Selecciona Cargo-</option></select>
                </div> -->
                <div class="col-sm-12">
                    <label>Importe</label>
                    <input type="text" class="form-control numeros" id="cantidadAbono">
                </div>
                <div class="col-sm-12">
                    <label>Concepto</label>
                    <input type="text" id="conceptoAbono" class="form-control">
                </div>
                <div class="col-sm-12">
                    <label>Forma de Pago</label>
                    <select class="form-control" id="formaPagoAbono">
                    <?php
                        foreach ($listaFormasPago['formas'] as $key => $value) {
                            echo '<option value="'.$value['idFormapago'].'">('.$value['claveSat'].') '.$value['nombre'].'</option>';
                        }
                    ?>
                    </select>
                </div>
                <div class="col-sm-12">
                    <label>Moneda</label>
                    <select class="form-control" id="monedaAbono">
                    <?php
                        foreach ($moneda as $key => $value) {
                            echo '<option value="'.$value['coin_id'].'">('.$value['codigo'].') '.$value['description'].'</option>';
                        }
                    ?>
                    </select>
                </div>
            </div>
        </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                             <button id="bAbona" class="btn btn-primary btn-block" onclick="caja.abona();"> <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                        </div>
                    </div>
                </div>


      </div>
    </div>
  </div>
  <!-- fin modal abono -->


<!-- Modal series y lotes -->
<div id="modalSeriesDevolucion" 
    class="modal fade" 
    role="dialog" 
    aria-labelledby="modalSeriesDevolucion" 
    aria-hidden="true"
    tabindex="-1" 
    data-backdrop="static" 
    data-keyboard="false"
>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Devolución de series</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <table class="table table-bordered" producto="">
            <thead>
                <tr>
                    <th></th>
                    <th>Series</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button> -->
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="aceptarDevolucionSeries">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<div id="modalLotesDevolucion" 
    class="modal fade" 
    role="dialog" 
    aria-labelledby="modalLotesDevolucion" 
    aria-hidden="true"
    tabindex="-1" 
    data-backdrop="static" 
    data-keyboard="false"
>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Devolución de lotes</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <table class="table table-bordered" producto="">
            <thead>
                <tr>
                    <th></th>
                    <th>Cantidad</th>
                    <th>Lote</th>

                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button> -->
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="aceptarDevolucionLotes">Aceptar</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="modalAvion">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><i class="fa fa-plane" aria-hidden="true"></i>Avion</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Proveedor Turistico</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Agregar</button>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modalAvion2">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><i class="fa fa-plane" aria-hidden="true"></i>Avion</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Proveedor Turistico</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">

                                <table>
                                    <tr>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="interjetL.png" style="width:100px;height: 20px;"></td>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="viva.png" style="width:100px;height: 50px;"></td>

                                    </tr>
                                    <tr>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="volaris.png" style="width:100px;height: 50px;"></td>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="aeromexico.png" style="width:100px;height: 50px;"></td>

                                    </tr>
                                </table>

                            <!--    <div class="radio">
                                 <label>
                                      <input type="radio" name="optradio">

                                 </label>
                                </div>
                                <div class="radio">
                                  <label><input type="radio" name="optradio">Option 2</label>
                                </div>
                                <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Option 3</label>
                                </div>  -->
                            </div>
                            <div class="col-sm-6">
                            <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Otro</label>
                            </div>
                                <selec id="" class="form-control">
                                    <option value="0">Proveedor</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xs-4">
                                <div class="radio">
                                  <label><input type="radio" name="optradio">Redondo</label>
                                </div>
                                <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Sencillo</label>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label>Origen:</label>
                                <select id="origenV" class="form-control">
                                    <option value="0">Seleccion</option>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <label>Destino:</label>
                                <select id="destinoV" class="form-control">
                                    <option value="0">Seleccion</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Adultos:</label>
                                <input type="text" id="adultosV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Ida</label>
                                <div id="datetimepicker1" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input id="idaV" class="form-control" type="text" placeholder="Ida">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label>Regreso</label>
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input id="regresoV" class="form-control" type="text" placeholder="Regreso">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Datos de Reservación</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Pasajero Titular:</label>
                                <input type="text" id="pasajeroTitularV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Teléfono:</label>
                                <input type="text" id="telPasajeroV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>E-mail:</label>
                                <input type="text" id="mailPasajeroV" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Clave de confirmación:</label>
                                <input type="text" id="claveInfoV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Precio Total:</label>
                                <input type="text" id="totalVi" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <label>Impuestos</label>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="radio">
                                          <label><input type="radio" name="optradio">Si</label>
                                        </div>
                                        <div class="radio disabled">
                                          <label><input type="radio" name="optradio" disabled>No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Termina el del 8 -->
                    <!-- empieza lo  de 4 -->
                    <div class="col-xs-4" style="border-left:1px solid black;">
                        <div class="row">
                            <div class="col-xs-12">
                                 <h4>Desgloce de Impuestos</h4>
                            </div>
                        </div><br><br><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Importe de IVA cobrado</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="ivaCobradoV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br><br><br><br><br><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Tarifa</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="tarifaV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>IVA y TUA</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="tuaV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Precio Total</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="precioTotalV" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--  termina el de 4-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Agregar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBus">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title"><i class="fa fa-bus" aria-hidden="true"></i> Autobus</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Proveedor Turistico</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">

                                <table>
                                    <tr>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="primera_plus.png" style="width:100px;height: 50px;"></td>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="etn.png" style="width:100px;height: 50px;"></td>

                                    </tr>
                                    <tr>
                                        <td><input type="radio" name="optradio"></td>
                                        <td><img src="omnibus.png" style="width:100px;height: 50px;"></td>


                                    </tr>
                                </table>

                            <!--    <div class="radio">
                                 <label>
                                      <input type="radio" name="optradio">

                                 </label>
                                </div>
                                <div class="radio">
                                  <label><input type="radio" name="optradio">Option 2</label>
                                </div>
                                <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Option 3</label>
                                </div>  -->
                            </div>
                            <div class="col-sm-6">
                            <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Otro</label>
                            </div>
                                <selec id="" class="form-control">
                                    <option value="0">Proveedor</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xs-4">
                                <div class="radio">
                                  <label><input type="radio" name="optradio">Redondo</label>
                                </div>
                                <div class="radio disabled">
                                  <label><input type="radio" name="optradio" disabled>Sencillo</label>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label>Origen:</label>
                                <select id="origenV" class="form-control">
                                    <option value="0">Seleccion</option>
                                </select>
                            </div>
                            <div class="col-xs-4">
                                <label>Destino:</label>
                                <select id="destinoV" class="form-control">
                                    <option value="0">Seleccion</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Adultos:</label>
                                <input type="text" id="adultosV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Ida</label>
                                <div id="datetimepicker1" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input id="idaV" class="form-control" type="text" placeholder="Ida">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label>Regreso</label>
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input id="regresoV" class="form-control" type="text" placeholder="Regreso">
                                </div>
                            </div>
                        </div>//////7
                        <div class="row">
                            <div class="col-xs-12">
                                <h4>Datos de Reservación</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Pasajero Titular:</label>
                                <input type="text" id="pasajeroTitularV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Teléfono:</label>
                                <input type="text" id="telPasajeroV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>E-mail:</label>
                                <input type="text" id="mailPasajeroV" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4">
                                <label>Clave de confirmación:</label>
                                <input type="text" id="claveInfoV" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <label>Precio Total:</label>
                                <input type="text" id="totalVi" class="form-control">
                            </div>
                            <div class="col-xs-4">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <label>Impuestos</label>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="radio">
                                          <label><input type="radio" name="optradio">Si</label>
                                        </div>
                                        <div class="radio disabled">
                                          <label><input type="radio" name="optradio" disabled>No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- termina vista 2 -->
                    </div>
                    <div class="col-xs-4" style="border-left:1px solid black;">
                        <div class="row">
                            <div class="col-xs-12">
                                 <h4>Desgloce de Impuestos</h4>
                            </div>
                        </div><br><br><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Importe de IVA cobrado</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="ivaCobradoV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br><br><br><br><br><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Tarifa</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="tarifaV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>IVA y TUA</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="tuaV" class="form-control" type="text">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-6">
                                <label>Precio Total</label>
                            </div>
                            <div class="col-xs-6">
                                <div id="datetimepicker2" class="input-group date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-usd"></span>
                                    </span>
                                    <input id="precioTotalV" class="form-control" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">Agregar</button>
            </div>
        </div>
    </div>
</div>

<div id='modalPassCan' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Password</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"><label>Password:</label></div>
                        <div class="col-sm-10"><input type="password" id="modPassCan" class="form-control"><input type="hidden" id="passhide"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                         <!--   <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button> -->
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btnMenu btn-block" onclick="javascript:caja.confirmaCancelacion();">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id='modalPassDev' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Password</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"><label>Password:</label></div>
                        <div class="col-sm-10"><input type="password" id="modPassDev" class="form-control"><input type="hidden" id="passhide"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                         <!--   <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button> -->
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btnMenu btn-block" onclick="javascript:caja.confirmaDevolucion();">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id='modalTarjetaRegalo' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" onclick="usarPuntos();">×</button>
                    <hdiv class="modal-title">Pagar con Tarjeta de regalo</h4>

                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-2"><label>Cantidad de puntos:</label></div>
                        <div class="col-sm-10"><input type="number" id="modTarRegPuntos" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6">
                         <!--   <button class="btn btn-success btnMenu btn-block" onclick="javascript:caja.cajaIniciar();">Iniciar</button> -->
                            <button class="btn btn-secondary btnMenu btn-block" data-dismiss="modal" onclick="usarPuntos();">Cerrar</button>
                        </div>
                        <div class="col-md-6">

                            <button class="btn btn-primary btnMenu btn-block" onclick="javascript:caja.confirmaPuntos();">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal Tarjetas de regalo -->

    <div class="modal fade" id="modalGiftCards">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Tarjetas de Regalo</h4>
                </div>
                <div class="modal-body col-md-12">
                    <div class="row">
                        <div class="col-md-1"><input type="hidden" id="giftProdId"></div>
                        <div class="col-md-10" align="center">
                            <h1 id="txtGcName">Tarjeta de Regalo</h1>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    <div class="row" id="inputs_nombres_container">
                        
                    </div>
                  <!--  
                    <div class="row">
                        <div class="col-md-3">
                            <label>Numero de Tarjeta</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="giftCarNum" placeholder="Agrega numero de Tarjeta">
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="caja.addGiftCard();">Agregar</button>
                </div>
            </div>
        </div>
    </div>
<!-- Fin Modal Tarjetas de regalo -->





        <!-- Modal de medico -->
    <div id='modalMedico' class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-info">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-user-plus fa-lg" aria-hidden="true"></i> Agregar Médico </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label" for="email">ID</label>
                        <input id="idmedico" class="form-control" type="text" value="" readonly placeholder="(Autonumerico)">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">* Código</label>
                          <input id="codigoMed" class="form-control" type="text" value="">
                      </div>

                      <div class="col-sm-6">
                          <label class="control-label">* Nombre</label>
                          <input id="nombreMed" class="form-control" type="text" value="">
                      </div>

                    </div>

                    <div class="row">
                      <div class="col-sm-6">
                        <label class="control-label">* Cédula</label>
                        <input id="cedulaMed" class="form-control" type="text" value="">
                      </div>
                      <div class="col-sm-6">
                          <label class="control-label">Dirección</label>
                          <input id="direccion" class="form-control" type="text" value="">
                      </div>

                    </div>
                    <div class="row">
                      <div class="col-sm-3">
                        <label class="control-label">Exterior</label>
                        <input id="numextMed" class="form-control" type="text" value="">
                      </div>
                      <div class="col-sm-3">
                        <label class="control-label">Interior</label>
                        <input id="numintMed" class="form-control" type="text" value="">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Colonia</label>
                          <input id="coloniaMed" type="text" class="form-control" value="">
                      </div>
                      <div class="col-sm-3">
                          <label class="control-label">Código Postal</label>
                          <input id="cpMed" type="text" class="form-control" value="">
                      </div>
                    </div>


                    <div class="row">
                                <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-8">
                        <label class="control-label"><span style="color:red;"></span> País</label>
                        <select id="selectPais" class="form-control" >

                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label class="control-label"></label>
                        <button type="button" data-toggle="modal" data-target="#nuevoPais" class="btn btn-success">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
<!-- Modal -->
<div class="modal fade" id="nuevoPais"  role="dialog" aria-labelledby="nuevoPais" aria-hidden="true">
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
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-8">
                        <label class="control-label">Estado</label>
                        <select id="selectEstado" class="form-control" >

                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label class="control-label"></label>
                        <button type="button" data-toggle="modal" data-target="#nuevoEstado" class="btn btn-success">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
<!-- Modal -->
<div class="modal fade" id="nuevoEstado"  role="dialog" aria-labelledby="nuevoEstado" aria-hidden="true">
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
            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-8">
                        <label class="control-label">Municipio</label>
                        <select id="selectMunicipio" class="form-control" >

                        </select>
                    </div>
                    <div class="col-sm-1">
                        <label class="control-label"></label>
                        <button type="button" data-toggle="modal" data-target="#nuevoMunicipio" class="btn btn-success">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
<!-- Modal -->
<div class="modal fade" id="nuevoMunicipio"  role="dialog" aria-labelledby="nuevoMunicipio" aria-hidden="true">
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
                      <div class="col-sm-3">
                        <label class="control-label">Ciudad</label>
                        <input id="ciudadMed" class="form-control" type="text" value="">
                      </div>
                    </div>

                    <div class="row">
                       <div class="col-sm-4">
                            <label class="control-label">Teléfono</label>
                            <input id="tel1Med" type="text" class="form-control" value="">
                        </div>
                    </div>

                    <div class="row" style="display:none;">
                        <div class="col-sm-4">
                            <label class="control-label"><span style="color:red;"></span>Comisión de Venta (%)</label>
                            <input id="comisionventaMed" class="form-control" type="number" value="">
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div class="col-sm-4">
                          <label class="control-label"><span style="color:red;"></span>Comisión de Cobranza (%)</label>
                          <input id="comisioncobranza" class="form-control" type="number" value="">
                        </div>
                    </div>
                    <div class="row" style="display:none;">
                        <div class="col-sm-4">
                            <label class="control-label">Vendedor</label>
                                    <select id="vendedorMed" class="form-control" >

                                    </select>


                          <!-- <label class="control-label"><span style="color:red;"></span>Vendedor [Lista de usuarios registrados]</label>
                          <input id="nombre" class="form-control" type="text" value="<?php
                                    if(isset($datosMedico)){echo $datosMedico['nombre'];}?>"> -->
                        </div>
                      </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-6">
                            <button type="button" class="btn btn-primary" onclick="caja.guardaMedico();"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar</button>
                            <!-- <button class="btn btn-success btnMenu" onclick="javascript:caja.observacionesEnviar();">Enviar</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div id="div_ejecutar_scripts" style="display: none">
    <!-- en esta div se ejecutan los scripts mediante la carga de contenido html -->
</div>

<div id="modalAux" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
			<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





</body>
</html>
<script>
    function modal_corte(){ 
    /// new       
        $.ajax({
            url: 'ajax.php?c=caja&f=totalVentasCP',
            async:false        
        })
        .done(function(total) {            
            if(total > 0){
                alert('Debes finalizar el corte parcial');
              return 0;  
            }else{
                console.log('Ventas:'+total);
                $("#modal_autorizar_corteP").modal('show');        
            }
        });
        /// new 
        //$("#modal_autorizar_corteP").modal('show');        
    }
    function autorizar_corte(){        
        var pass = $("#pass_corte").val();
        //alert(pass+' == '+'<?php echo $configDatos[0]['password'];  ?>');
        if(pass == '<?php echo $configDatos[0]['password'];  ?>'){
            $("#modal_autorizar_corteP").modal('hide');
            $("#pass_corte").val('');
            caja.corteButtonAccion(1);
        }else{
            alert('Contraseña incorrecta');
        }
    }
    function usarPuntos(){
        $("#usarPuntos").attr('checked', false);
    }
</script>
