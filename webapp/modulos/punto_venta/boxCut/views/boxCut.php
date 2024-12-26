<?php 
	@session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<LINK href="../../../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
		<LINK href="../../../../netwarelog/catalog/css/view.css"   title="estilo" rel="stylesheet" type="text/css" />
		<!--<LINK href="../../../../netwarelog/design/default/netwarlog.css"   title="estilo" rel="stylesheet" type="text/css" / -->
		<?php include('../../../../netwarelog/design/css.php');?>
	    <LINK href="../../../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

		<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<link href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"/> 
		
		<script type="text/javascript" src="../../../punto_venta/js/jquery.numeric.js"></script>
		<style>
			[id=pagos_div] tbody tr td:nth-child(2)
			{
				max-width: 150px;
			}
			[id=pagos_div] tbody tr td:last-child, 
			[id=productos_div] tbody tr td:last-child, 
			[id=cxc_div] tbody tr td:last-child
			{
				text-align: right;
				padding-right: 5px;
			}
		</style>
		<script src="../../../punto_venta/js/ui.datepicker-es-MX.js"></script>
		<script src="../../js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
		<link rel="stylesheet" type="text/css" href="../../../../libraries/bootstrap/dist/css/bootstrap.min.css" />
		<script src="../../../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>

		<!-- <script type="text/javascript" src="../../../punto_venta/js/corte_caja.js"></script> -->
		<!-- <script type="text/javasctipt" src="../../../punto_venta/js/paginaciongrid.js"></script> -->
		<!--Script del autocompletado del campo de productos !-->
		<script>
			$.fn.disable = function() {
				return this.each(function() {
					if (typeof this.disabled !== "undefined")
					{
						$(this).data('jquery.disabled', this.disabled);
						this.disabled = true;
					}
				});
			};

			$.fn.enable = function() {
				return this.each(function() {
					if (typeof this.disabled !== "undefined")
					{
						this.disabled = $(this).data('jquery.disabled');
					}
				});
			};

			$(document).ready(function() {
				$(".numeric").numeric({ precision: 12, scale: 2 });

				$(".throwback").click(function(event) {
					location.href = "index.php";
				});

				$("#retiro_caja").blur(function(event) {
					var val = ( $(this).val() === null || $(this).val().trim().length === 0 ) ? 0 : $(this).val();
					var availabeValue = parseFloat( $("#saldo_disponible").val(), 10);

					if ( val < 0 )
					{
						promotedValue = ( val * -1 );
						finalValue = ( confirm("El valor no puede ser negativo. Tal vez quizo decir " + promotedValue ) === true ) ? promotedValue : 0;
						$(this).val( finalValue );
						$(this).focus().blur();
					}
					else if( val > availabeValue )
					{
						alert("El retiro no puede ser mayor a las existencias. Intente Otra vez.");
						$(this).val("").focus();
					}
				});

				$("#deposito_caja").blur(function(event) {
					var val = ( $(this).val() === null || $(this).val().trim().length === 0 ) ? 0 : $(this).val();
					if ( val < 0 )
					{
						promotedValue = ( val * -1 );
						finalValue = ( confirm("El valor no puede ser negativo. Tal vez quizo decir " + promotedValue ) === true ) ? promotedValue : 0;
						$(this).val( finalValue );
						$(this).focus().blur();
					}
				});

				$("#send").click(function(event){// Evento para guardar Corte de Caja
					
						var retiros = [];

						var $this, input, text, obj;
						$('.idret').each(function() { 
						   $this = $(this);
						   $input = $this.find("td");
						   text = $this.text();
						   obj = {};
						   obj['idre'] = text;
						   retiros.push(obj);
						});
					
						
					$(this).prop('disabled', true);
					$("#alerta_guardar").html("<div style='color: #01a05f; text-align: right;'><b>Se esta&#769; generando su corte. Por favor sea paciente. <img src='../../img/preloader.gif'></b></div>");

					// Inicia seccion de validaciones - Aqui contiene las alertas
						v1  = validation(["fecha_inicio","fecha_fin"],"date");
						v2  = validation(["saldo_inicial","monto_ventas","saldo_disponible","retiro_caja","deposito_caja"],"number");
						tot = v1 + v2 ;
					// Termina seccion de validaciones

					if ( tot === 2 )
					{
						$.post('../controllers/boxCut.php',
							{ method: 'sales', date: $("#fecha_fin").val() }, 
							function(data, textStatus, xhr) {
								try
								{
									data = $.parseJSON( data );		
									
									if ( parseFloat( data[0].ventas, 10 ) > 0 )
									{
										alert("Existen " + data[0].ventas + " Ventas fuera de fecha. Recargando..." );
										location.href = "boxCut.php";
									}
									else
									{
										retiro_cajaVar   = ( $("#retiro_caja").val().trim().length   === 0 ) ? 0 : $("#retiro_caja").val();
										deposito_cajaVar = ( $("#deposito_caja").val().trim().length === 0 ) ? 0 : $("#deposito_caja").val();
										
										$.post('../controllers/boxCut.php',
										{
											method           : 'newCut',
											fecha_inicio     : $("#fecha_inicio").val(),
											fecha_fin        : $("#fecha_fin").val(),
											saldo_inicial    : unCurrency( $("#saldo_inicial").val() ),
											monto_ventas     : unCurrency( $("#monto_ventas").val() ),
											saldo_disponible : unCurrency( $("#saldo_disponible").val() ),
											retiro_caja      : retiro_cajaVar,
											deposito_caja    : deposito_cajaVar,
											retiros          : retiros

										}, 
										function(req, textStatus, xhr)
										{
											if(req)
											{
												$("#alerta_guardar div").fadeOut();
												alert("Corte realizado con exito.");
												setTimeout( function(){
													location.href = "index.php";
												}, 3000);

												//////////////////////////////////////////////////
												//Al guardar el corte tomamos la consulta de la venta y la llevamos al sistema contable
													// Queda pendiente la confirmacion de Ivan
													// $.post("../../../cont/ajax.php?c=CaptPolizas&f=InsertPolMovPDV",
													//	{
													//	Fecha : $('#fecha_fin').val()
													//	},
													//	function(req)
													//	{
													//		$("#alerta_guardar").empty();
													//		alert("El corte de caja se generó con éxito");
													//		//alert(data)
													//		window.location="index.php";
													//	}
													// );
												//Termina cambios para el sistema contable
												//////////////////////////////////////////////////
											}
											else
											{
												$("#alerta_guardar div").fadeOut();
												$("#send").enable();
												alert("Hubo un error al generar el corte.");
											}
										});
									}
								}
								catch (e)
								{
									location.href = "boxCut.php";
								}
								
							}
						);				
					}
					else
					{
						$("#alerta_guardar div").fadeOut();
						setTimeout(function(){ location.href = "boxCut.php"; }, 1000);
					}
					
				});
				data = "";
				/* jshint ignore:start */
				<?php
					$_POST['method'] =  "getSales";
					require '../controllers/boxCut.php';
				?>
				/* jshint ignore:end */
				if ( data !== "" )
				{	//console.log(data);
					for (var i = data.length - 1; i >= 0; i--)
					{	
						switch(data[i].Flag)
						{
							case "Ventas":
								line = ( (i%2) === 1 ) ? "<tr class='busqueda_fila'>" : "<tr class='busqueda_fila2'>";
								line += "	<td>";
								line += data[i].idVenta;
								line += "	</td>";
								line += "	<td>";
								line += ( data[i].nombre !== null ) ? data[i].nombre : "Publico en General";
								line += "	</td>";
								line += "	<td>";
								line += data[i].fecha;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Efectivo;
								line += "	</td>";
								line += "	<td>";
								line += data[i].TCredito;
								line += "	</td>";
								line += "	<td>";
								line += data[i].TDebito;
								line += "	</td>";
								line += "	<td>";
								line += data[i].CxC;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Cheque;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Trans;
								line += "	</td>";
								line += "	<td>";
								line += data[i].SPEI;
								line += "	</td>";
								line += "	<td>";
								line += data[i].TRegalo;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Ni;
								line += "	</td>";
								line += "<td>";
								line += data[i].cambio;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Impuestos;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Monto;
								line += "	</td>";
								line += "	<td>";
								line += data[i].Importe;
								line += "	</td>";
								line += "	<td>";
								line += parseFloat(data[i].Efectivo, 10) - parseFloat(data[i].cambio,10);
								line += "	</td>";
								line += "</tr>";

								$("#pagos_div tbody").append( line );
								break;
							case "Productos":
								line = ( (i%2) === 1 ) ? "<tr class='busqueda_fila'>" : "<tr class='busqueda_fila2'>";
								line += "	<td>";
								line += data[i].idVenta;// Codigo
								line += "	</td>";
								line += "	<td>";
								line += data[i].fecha;// Nombre
								line += "	</td>";
								line += "	<td>";
								line += data[i].nombre;// Cantidad
								line += "	</td>";
								line += "	<td>";
								line += data[i].Efectivo;// Precio Unitario
								line += "	</td>";
								line += "	<td>";
								line += data[i].TCredito;// Descuento
								line += "	</td>";
								line += "	<td>";
								line += data[i].TDebito;// Impuestos
								line += "	</td>";
								line += "	<td>";
								line += data[i].CxC;// Subtotal
								line += "	</td>";
								line += "</tr>";
								$("#productos_div tbody").append(line);
								break;
							case "CxC":
								line = ( (i%2) === 1 ) ? "<tr class='busqueda_fila'>" : "<tr class='busqueda_fila2'>";
								line += "	<td>";
								line += data[i].idVenta;// ID de pago
								line += "	</td>";
								line += "	<td>";
								line += data[i].fecha;// Fecha de registro de cuenta por cobrar
								line += "	</td>";
								line += "	<td>";
								line += data[i].nombre;// Fecha de Vencimiento de Cuenta por cobrar
								line += "	</td>";
								line += "	<td>";
								line += data[i].TCredito;// Fecha del abono
								line += "	</td>";
								line += "	<td>";
								line += data[i].Efectivo;// Cliente
								line += "	</td>";
								line += "	<td>";
								line += data[i].Cheque;// Recibio
								line += "	</td>";
								line += "	<td>";
								line += data[i].CxC;// Forma de Pago
								line += "	</td>";
								line += "	<td>";
								line += data[i].TDebito;// Monto abonado
								line += "	</td>";
								line += "	<td>";
								line += ( parseInt(data[i].Trans, 10) === 1 ) ? data[i].TDebito : "0.00";// Suma De Ingresos
								line += "	</td>";
								line += "</tr>";
								$("#cxc_div tbody").append(line);
								break;
							case "LastData":
								$("#fecha_inicio").val( data[i].idVenta );
								$("#fecha_fin").val( data[i].fecha );
								$('label[for=fecha_inicio]').text( data[i].idVenta );
								$('label[for=fecha_fin]').text( data[i].fecha );
								break;
							case "SaldoIni":
								if ( data[i].idVenta === null  || data[i].idVenta === "")
								{
									alert("No ha iniciado caja. Inicie caja por favor.");
									location.href = 'index.php';
								}
								else
								{
									$("#saldo_inicial").val(data[i].idVenta);
									$("label[for=saldo_inicial]").text( data[i].idVenta );
								}
								
								break;

						}
					}
					/*$.ajax({
						url: '../controllers/boxCut.php',
						type: 'POST',
						dataType: 'json',
						data: {method: 'getRetiros'},
					})
					.done(function(resp) {
						var table;
						var total=0;
						$.each(resp, function(index, val) {
							 alert(val.concepto);
							 var cantidad = val.cantidad * 1;
							 //console.log(cantidad);
							 table +="<tr class='busqueda_fila'>";
							 table +="<td>"+val.id+"</td>";
							 table +="<td>"+cantidad.toFixed(2)+"</td>";
							 table +="<td>"+val.concepto+"</td>";
							 table +="<td>"+val.fecha+"</td>";
							 
							total +=cantidad; 
						});
						table +="<tr><td></td><td></td><td></td><td id='totalretiros' style='background-color:red;text-align:right;padding-right: 5px;'>$"+total+"</td></tr>"
						$("#retiro_div tbody").append(table);

					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					}); */

					var element = {};
					
					if( $("#pagos_div tbody tr").length )
					{
						element = {
							Efectivo  : 0,
							TCredito  : 0,
							TDebito   : 0,
							CxC       : 0,
							Cheque    : 0,
							Trans     : 0,
							SPEI      : 0,
							TRegalo   : 0,
							cambio    : 0,
							Impuestos : 0,
							Monto     : 0,
							Importe   : 0,
							Ni        : 0,
							Ingresos  : 0
						};
						$("#pagos_div tbody tr").each(function(index, el){
							element.Efectivo  += parseFloat($('td:nth-child(4)', this).text(), 10);
							element.TCredito  += parseFloat($('td:nth-child(5)', this).text(), 10);
							element.TDebito   += parseFloat($('td:nth-child(6)', this).text(), 10);
							element.CxC       += parseFloat($('td:nth-child(7)', this).text(), 10);
							element.Cheque    += parseFloat($('td:nth-child(8)', this).text(), 10);
							element.Trans     += parseFloat($('td:nth-child(9)', this).text(), 10);
							element.SPEI      += parseFloat($('td:nth-child(10)', this).text(), 10);
							element.TRegalo   += parseFloat($('td:nth-child(11)', this).text(), 10);
							element.Ni        += parseFloat($('td:nth-child(12)', this).text(), 10);
							element.cambio    += parseFloat($('td:nth-child(13)', this).text(), 10);
							element.Impuestos += parseFloat($('td:nth-child(14)', this).text(), 10);
							element.Monto     += parseFloat($('td:nth-child(15)', this).text(), 10);
							element.Importe   += parseFloat($('td:nth-child(16)', this).text(), 10);
							element.Ingresos  += parseFloat($('td:nth-child(17)', this).text(), 10);
						});
						line  = "<tr class='nmsubtitle'>";
							line += "	<td colspan='3'>";
							line += "Totales";
							line += "	</td>";
							line += "	<td>";
							line += element.Efectivo;
							line += "	</td>";
							line += "	<td>";
							line += element.TCredito;
							line += "	</td>";
							line += "	<td>";
							line += element.TDebito;
							line += "	</td>";
							line += "	<td>";
							line += element.CxC;
							line += "	</td>";
							line += "	<td>";
							line += element.Cheque;
							line += "	</td>";
							line += "	<td>";
							line += element.Trans;
							line += "	</td>";
							line += "	<td>";
							line += element.SPEI;
							line += "	</td>";
							line += "	<td>";
							line += element.TRegalo;
							line += "	</td>";
							line += "	<td>";
							line += element.Ni;
							line += "	</td>";
							line += "	<td>";
							line += element.cambio;
							line += "	</td>";
							line += "	<td>";
							line += element.Impuestos;
							line += "	</td>";
							line += "	<td>";
							line += element.Monto;
							line += "	</td>";
							line += "	<td style='background-color:red' id='tot_vta'>";
							line += element.Importe;
							line += "	</td>";
							line += "	<td style='background-color:green;text-align:right;padding-right: 5px;' id='tot_ingreso'>";
							line += element.Ingresos;
							line += "	</td>";
						line += "</tr>";
						$("#pagos_div tfoot").append( line );
						element = null;
						setCurrency( '#pagos_div tbody tr', 3, 16 );
						setCurrency( '#pagos_div tfoot tr', 1, 14 );
					}
					else
					{
						line  = "<tr class='nmsubtitle'>";
						line += "	<td colspan='17' style='text-align:center;color:red;text-align:center;font-weight:bold;'>Sin pagos durante el periodo.";
						line += "	</td>";
						line += "</tr>";
						$("#pagos_div tbody").append( line );
					}

					if ( $("#productos_div tbody tr").length )
					{
						element = {
							Descuento : 0,
							Impuestos : 0,
							Subtotal  : 0
						};
						$("#productos_div tbody tr").each(function(index, el){
							element.Descuento += parseFloat( $("td:nth-child(5)", this).text(), 10);
							element.Impuestos += parseFloat( $("td:nth-child(6)", this).text(), 10);
							element.Subtotal  += parseFloat( $("td:nth-child(7)", this).text(), 10);
						});

						total = ( element.Subtotal - element.Descuento ) + element.Impuestos;
						//console.log( total );
						line  = "<tr class='nmsubtitle'>";
						line += "	<td colspan='4'>";
						line += "Totales";
						line += "	</td>";
						line += "	<td>";
						line += element.Descuento;
						line += "	</td>";
						line += "	<td>";
						line += element.Impuestos;
						line += "	</td>";
						line += "	<td style='background-color:red;text-align:right;padding-right: 5px;' id='tot_prod'>";
						line += element.Subtotal;
						line += "	</td>";
						line += "</tr>";
						// line += "<tr>";
						// line += "	<td colspan='6'>";
						// line += "Total";
						// line += "	</td>";
						// line += "	<td>";
						// line += " " + total + " ";
						// line += "	</td>";
						// line += "</tr>";

						setCurrency('#productos_div tbody tr', 3, 6);

						$("#productos_div tfoot").append( line );
						setCurrency('#productos_div tfoot tr', 1, 3);
						element = null;
						line = null;
					}
					else
					{
						line  = "<tr>";
						line += "	<td colspan='7' style='text-align:center;color:red; font-weight:bold;'>Sin Productos vendidos durante el periodo.";
						line += "	</td>";
						line += "</tr>";
						$("#productos_div tbody").append( line );
					}

					if ( $("#cxc_div tbody tr").length )
					{
						element = {
							monto : 0,
							total : 0
						};
						
						$("#cxc_div tbody tr").each(function(index, el) {
							element.monto += parseFloat( $('td:nth-child(8)', this).text(), 10 );
							element.total += parseFloat( $('td:nth-child(9)', this).text(), 10 );
							$("td:nth-child(8)", this).text( Currency( "$", parseFloat($("td:nth-child(8)", this).text(), 10) ) );
							$("td:nth-child(9)", this).text( Currency( "$", parseFloat($("td:nth-child(9)", this).text(), 10) ) );
						});

						line  = "<tr>";
						line += '	<td colspan="7" style="text-align:right; padding-right:15px;">';
						line += "		Total";
						line += "	</td>";
						line += '	<td style="background-color:red;text-align:right;">';
						line +=  Currency("$", element.monto );
						line += "	</td>";
						line += '	<td id="tot_cxc" style="background-color:green;text-align:right;padding-right: 5px;">';
						line += Currency( "$", element.total ) ;
						line += "	</td>";
						line += "</tr>";

						$("#cxc_div tfoot").append( line );
					}
					else
					{
						line  = "<tr class='nmsubtitle'>";
						line += "	<td colspan='8' style='text-align:center;color:red; font-weight:bold;'>Sin pagos a cuentas por cobrar durante el periodo.";
						line += "	</td>";
						line += "</tr>";
						$("#cxc_div tbody").append( line );
					}

					// DEBIDO A QUE NO PUEDEN HABER PAGOS SIN PRODUCTOS ENTONCES
					tot_ingreso = ( $("#tot_ingreso").length !== 0 ) ? unCurrency( $("#tot_ingreso").html() ) : 0;
					tot_cxc     = ( $("#tot_cxc").length !== 0 ) ? unCurrency( $("#tot_cxc").html() ) : 0;


					if( $("#tot_vta").text() === $("#tot_prod").text())
					{
						monto_ventas = tot_cxc + tot_ingreso;
						monto_ventas = monto_ventas.toFixed(2);
						$("#monto_ventas").val( monto_ventas );
						s_i = parseFloat( unCurrency( $("#saldo_inicial").val() ), 10 );
						s_i += tot_ingreso + tot_cxc;
						$("#saldo_disponible").val( s_i.toFixed(2) );
					}
					else
					{
						alert("Alerta. Existe una diferencia entre el total de productos vendidos y el total de las ventas.");
						monto_ventas = tot_cxc + tot_ingreso;
						monto_ventas = monto_ventas.toFixed(2);
						$("#monto_ventas").val( monto_ventas );
						s_i = parseFloat( unCurrency( $("#saldo_inicial").val() ), 10 );
						s_i += tot_ingreso + tot_cxc;
						$("#saldo_disponible").val( s_i.toFixed(2) );
						// $("#send").prop( 'disabled', true );
					}
					var desde = $('#fecha_inicio').val();
					var hasta = $('#fecha_fin').val();
					
					$.ajax({
						url: '../controllers/boxCut.php',
						type: 'POST',
						dataType: 'json',
						data: {method : 'getRetiros',
								desde : desde,
								hasta : hasta,
								idcorte :'A'
									},
					})
					.done(function(resp) {
						
						var table;
						var total=0;
						$.each(resp, function(index, val) {
							 
							 var cantidad = val.cantidad * 1;
							 console.log(cantidad);
							 table +="<tr class='busqueda_fila'>";
							 table +="<td class='idret'>"+val.id+"</td>";
							 table +="<td>"+cantidad.toFixed(2)+"</td>";
							 table +="<td>"+val.concepto+"</td>";
							 table +="<td>"+val.fecha+"</td>";
							 table +="<td>"+val.usuario+"</td>";
							 
							total +=cantidad; 
						});
						table +="<tr><td></td><td></td><td></td><td></td><td id='totalretiros' style='background-color:red;text-align:right;padding-right: 5px;'>$"+total.toFixed(2)+"</td></tr>"
						$("#retiro_div tbody").append(table);
						var saldoDisponible = $('#saldo_disponible').val();
						var saldoDispoFinal = saldoDisponible - total;
						$('#saldo_disponible').val(saldoDispoFinal);
					})
					.fail(function() {
						console.log("error");
					})
					.always(function() {
						console.log("complete");
					}); 
				}
			});
			
			function pdf(user)
			{
				//Plugg in que envia gran cantidad de datos en un post o get
				//Nota---Se modifico el plugg in (Ivan Cuenca) para que se habra en una pagina emergente _blank
				//Documentacion: http://www.avramovic.info/razno/jquery/redirect/
				$().redirect('pdf.php', {'cont': $('#topdf').html(), 'name': user});				
			}
			
			function validation(selectors, type)
			{
				var i = selectors.length - 1;
				switch(type)
				{
					case "date":
						for (i ; i >= 0; i--)
						{
							var pattern = /^(19|2\d)\d\d-(0[1-9]|1[012])-([123]0|[012][1-9]|31)[ t]([01]\d|2[0-3]):([0-5]\d)(?::([0-5]\d))?$/i;
    						var x = pattern.test($( "#" + selectors[i] ).val());
							
							if(!x)
							{
								alert("Existe un error sobre las Fechas. Revise las fechas ubicadas en el .");
								return 0;
							}
							else
								return 1;
						}
						break;
					case "number":
						for ( i ; i >= 0; i-- )
						{
							switch( selectors[i] )
							{
								case "deposito_caja":
								case "retiro_caja":
									value = ( $("#" + selectors[i] ).val().trim().length === 0 ) ? 0 : $( "#" + selectors[i] ).val();
									break;
								default:
									if ( $( "#" + selectors[i] ).val().trim().length === 0 )
									{
										alert("Los saldos de la caja no son validos. Revise los calculos por el corte.");
										return 0;
									}
									else
										value = $( "#" + selectors[i] ).val();
									break;
							}

							if( isNaN( unCurrency( value ) ) )
							{
								alert("Existen valores no numericos en los saldos, retiros o depositos del corte.");
								return 0;
							}
							else
								return 1;
						}
						break;
					case "sales":
						if( validation(['fecha_fin'],'date') === 1 )
						{
							$.post('../controllers/boxCut.php',
								{ method: 'sales', date: $("#fecha_fin").val() }, 
								function(data, textStatus, xhr) {
									try
									{
										data = $.parseJSON( data );		
										if ( parseFloat( data[0].ventas, 10 ) > 0 )
										{
											alert("Existen " + data[0].ventas + " Ventas fuera de fecha. Recargando..." );
											location.href = "boxCut.php";
											response = 0;
											$("#validation").html( response );
										}
										else
											response = 1;
											$("#validation").html( response );
									}
									catch (e)
									{
										response = 0;
										$("#validation").html( response );
									}
									
								}
							);

							return $("#validation").html();
						}
						else
						{
							return 0;
						}
						break;						
				}
			}

			function Currency(sSymbol, vValue) 
			{
				aDigits = vValue.toFixed(2).split(".");
				aDigits[0] = aDigits[0].split("").reverse().join("").replace(/(\d{3})(?=\d)/g, "$1,").split("").reverse().join("");
				return sSymbol + aDigits.join(".");
			}
			
			function setCurrency(selector ,init, end)
			{
				$(selector).each(function() {
					$('td', this).each(function(index, el) {
						if( index >= init && index <= end )
						{

							str = Currency("$", parseFloat($(this).text(), 10) );
							$(this).text( str );
						}
					});
				});
			}
			
			function unCurrency(val)
			{
				val = val.toString();
				newStr = "";
				for (var i = 0; i < val.length; i++)
				{
					if( val[i] != "," && val[i] != "$" )
						newStr += val[i];
				}
				val = parseFloat( newStr, 10 );
				return val;
			}
			function arc(){
	
				//var tot_ingreso = $('#tot_ingreso').text();
				var tot_ingreso = $('#saldo_disponible').val();
				// tot_ingreso = tot_ingreso.substring(1)*1;

				//$("#arqueo").dialog({width: 330});
				$('#arqueo').modal('show');
				$('#totefect').text('$'+tot_ingreso);
				/*$("#dialog").dialog({
				
				buttons: {
					"Cerrar": function () {
					$(this).dialog("close");
						}
					}
				}); */
			}
			function calarque(){
				var mil = $('#1000').val() * 1000;
				var quini = $('#500').val() * 500;
				var dosie = $('#200').val() * 200;
				var cien = $('#100').val() * 100;
				var cincue = $('#50').val() * 50;
				var veinte = $('#20').val() * 20;
				var diez = $('#10').val() * 10;
				var cinco = $('#05').val() * 5;
				var dos = $('#02').val() * 2;
				var uno = $('#01').val() * 1;
				var cincen = $('#050').val() * .50;
				var veintecen = $('#020').val() * .20;
				var diezcen = $('#010').val() * .10;
				var total = mil+quini+dosie+cien+cincue+veinte+diez+cinco+dos+uno+cincen+veintecen+diezcen;
				$('#totalArc').val(total);

			}
			function comparqu(){
				// var tot_ingreso = $('#tot_ingreso').text();
				var tot_ingreso = $('#tot_ingreso').val();
				//tot_ingreso = tot_ingreso.substring(1)*1;
				var total_arq = $('#totalArc').val()*1;

				if(tot_ingreso==total_arq){
					alert('Las cantidades coinciden');
				}else{
					var	dif = tot_ingreso - total_arq;
					dif = dif+'';
					dif2 = dif.substring(1);
					alert('Hay una diferencia de $'+dif2);
				}
			}
		</script>
		<link href="../../css/imprimir_bootstrap.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
			.btnMenu{
	            border-radius: 0; 
	            width: 100%;
	            margin-bottom: 1em;
	            margin-top: 1em;
	        }
	        .row
	        {
	            margin-top: 1em !important;
	        }
	        .nmwatitles, [id="title"] {
				padding: 8px 0 3px !important;
				background-color: unset !important;
			}
			.select2-container{
				width: 100% !important;
			}
			.select2-container .select2-choice{
				background-image: unset !important;
				height: 31px !important;
			}
	        .tablaResponsiva{
		        max-width: 100vw !important; 
		        display: inline-block;
		    }
		    .nmsubtitle{
		    	color: black !important;
		    }
		    @media print{
				#imprimir,#filtros,#excel, .botones, input[type="button"], button, button[type="button"], .btnMenu{
					display:none;
				}
				.table-responsive{
					overflow-x: unset;
				}
				#imp_cont{
					width: 100% !important;
				}
			}
		</style>
	</head>
	<body>

		<div id='arqueo' class="modal fade" tabindex="-1" role="dialog">
	        <div class="modal-dialog">
	            <div class="modal-content">
	                <div class="modal-header">
	                	<h4 class="modal-title">Arqueo</h4>
	                </div>
	                <div class="modal-body">
	                    <div class="row">
	                        <div class="col-md-12 col-sm-12 col-xs-12 tablaResponsiva">
	                    		<div class="table-responsive">
	                    			<table class="table">
										<tr>
											<td><label>$1000</label></td>
											<td><input type="text" id="1000" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$500 </label></td>
											<td><input type="text" id="500" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$200 </label></td>
											<td><input type="text" id="200" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$100 </label></td>
											<td><input type="text" id="100" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$50  </label></td>
											<td><input type="text" id="50" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$20  </label></td>
											<td><input type="text" id="20" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$10  </label></td>
											<td><input type="text" id="10" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$5   </label></td>
											<td><input type="text" id="05" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$2   </label></td>
											<td><input type="text" id="02" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$1   </label></td>
											<td><input type="text" id="01" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$.50 </label></td>
											<td><input type="text" id="050" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$.20 </label></td>
											<td><input type="text" id="020" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>$.10 </label></td>
											<td><input type="text" id="010" onkeyup="calarque();" value="0" class="form-control"></td>
										</tr>
										<tr>
											<td><label>Total EF: $</label></td>
											<td><input type="text" id="totalArc" value="0" class="form-control" readonly></td>	
										</tr>
										<tr>
											<td><label>Total : $</label></td>
											<td><label id="totefect"></label></td>	
										</tr>
									</table>
	                    		</div>
	                        </div>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                	<button id="compara" class="btn btn-primary btnMenu col-md-6" onclick="comparqu();">Comparar</button>
	                </div>
	            </div>
	        </div>
	    </div>
		
		<div class="container" id="imp_cont">
			<div id="validation"></div>
	        <div class="row">
	            <div class="col-md-12">
	                <h3 class="nmwatitles text-center">
	                	Corte de Caja<br>
	                	<a href='javascript:window.print();'>
					    	<img class="nmwaicons" border='0' src='../../../../netwarelog/design/default/impresora.png'>
					    </a>
					    <!--<a href='javascript:pdf("Corte de Caja")' id='pdflink'>
					    	<img src='../../../../netwarelog/repolog/img/pdf.gif' title='Generar PDF' id='dopdf'>
					    </a>-->
	                </h3>
	            </div>
	        </div>
	        <input id='id_corte' type='hidden'>
			<input type="hidden" name="init" id="init">
			<input type="hidden" name="end" id="end">
			<input type="hidden" name="index" value="0" id="index">
			<section>
	        	<div class="row">
	        		<div class="col-sm-9">
	        			
	        		</div>
	        		<div class="col-sm-3">
	        			<button class="btn btn-success btnMenu col-md-6 throwback" id="newCut">Regresar al listado</button>
	        		</div>
	        	</div>
	        </section>
	        <div id='notifica_fecha_div' class="row"></div>
	        <h3>Filtro de b&uacute;squeda por fecha de corte</h3>
	        <section>
	            <div class="row">
	                <div class="col-sm-6">
	                    <label>Desde:</label>
	                    <input id='fecha_inicio' type='text' readonly class="form-control">
	                </div>
	                <div class="col-sm-6">
	                    <label>Hasta:</label>
	                    <input id='fecha_fin' type='text' readonly class="form-control">
	                </div>
	            </div>
	        </section>
	        <section id='topdf'>
	        	<section>
	        		<div class="row" id='print_pdf'>
		                <div class="col-sm-6">
		                    <label>Desde:</label>
		                    <label for='fecha_inicio'></label>
		                </div>
		                <div class="col-sm-6">
		                    <label>Hasta:</label>
		                    <label for='fecha_fin'></label>
		                </div>
		            </div>
	        	</section>
	        	<div id='aviso_canceladas'></div>
	        	<h4>Pagos</h4>
	        	<section>
	        		<div class="row">
	        			<div class="col-md-12 col-sm-12 col-xs-12 tablaResponsiva">
	        				<div class="table-responsive">
	        					<table class="busqueda table" id='pagos_div' cellpadding='0' cellspacing='0' width='100%' style='font-size: 12px;'>
									<thead>
										<tr class="tit_tabla_buscar">
											<th class="nmcatalogbusquedatit">Folio de venta</th>
											<th class="nmcatalogbusquedatit">Cliente</th>
											<th class="fechahora nmcatalogbusquedatit">Fecha y hora</th>
											<th class="nmcatalogbusquedatit">EF</th>
											<th class="nmcatalogbusquedatit">TC</th>
											<th class="nmcatalogbusquedatit">TD</th>
											<th class="nmcatalogbusquedatit">CR</th>
											<th class="nmcatalogbusquedatit">CH</th>
											<th class="nmcatalogbusquedatit">TRA</th>
											<th class="nmcatalogbusquedatit">SPEI</th>
											<th class="nmcatalogbusquedatit">TR</th>
											<th class="nmcatalogbusquedatit">Ni</th>
											<th class="nmcatalogbusquedatit">Cambio</th>
											<th class="nmcatalogbusquedatit">Impuestos</th>
											<th class="nmcatalogbusquedatit">Monto</th>
											<th class="nmcatalogbusquedatit">Importe</th>
											<th class="nmcatalogbusquedatit">Ingresos <br>(EF - Cambio)</th>
										</tr>
									</thead>
									<tbody></tbody>
									<tfoot></tfoot>
								</table>
	        				</div>
	        			</div>
	        		</div>
	        	</section>
	        	<h4>Productos Vendidos</h4>
	        	<section>
	        		<div class="row">
	        			<div class="col-md-12 col-sm-12 col-xs-12">
	        				<div class="table-responsive">
		        				<table id='productos_div' class="busqueda table">
									<thead>
										<tr class="tit_tabla_buscar">
											<th class="nmcatalogbusquedatit">Codigo</th>
											<th class="nmcatalogbusquedatit">Producto</th>
											<th class="nmcatalogbusquedatit">Cantidad</th>
											<th class="nmcatalogbusquedatit">Precio unitario</th>
											<th class="nmcatalogbusquedatit">Descuento</th>
											<th class="nmcatalogbusquedatit">Impuestos</th>
											<th class="nmcatalogbusquedatit">Subtotal</th>
										</tr>
									</thead>
									<tbody></tbody>
									<tfoot></tfoot>
								</table>
	        				</div>
	        			</div>
	        		</div>
	        	</section>
	        	<h4>Pagos a Cuentas por Cobrar</h4>
	        	<section>
	        		<div class="row">
	        			<div class="col-md-12 col-sm-12 col-xs-12">
	        				<div class="table-responsive">
	        					<table class="busqueda table" id="cxc_div">
									<thead>
										<tr class="tit_tabla_buscar">
											<th class="nmcatalogbusquedatit">ID de Pago</th>
											<th class="nmcatalogbusquedatit">Fecha de Registro (CxC)</th>
											<th class="nmcatalogbusquedatit">Fecha de Vencimiento (CxC)</th>
											<th class="nmcatalogbusquedatit">Fecha del Abono</th>
											<th class="nmcatalogbusquedatit">Cliente</th>
											<th class="nmcatalogbusquedatit">Recibio</th>
											<th class="nmcatalogbusquedatit">Forma de Pago</th>
											<th class="nmcatalogbusquedatit">Monto</th>
											<th class="nmcatalogbusquedatit">Ingresos a caja</th>
										</tr>
									</thead>
									<tbody></tbody>
									<tfoot></tfoot>
								</table>
	        				</div>
	        			</div>
	        		</div>
	        	</section>
	        	<h4>Retiros de Caja</h4>
	        	<section>
	        		<div class="row">
	        			<div class="col-md-12 col-sm-12 col-xs-12">
	        				<div class="table-responsive">
	        					<table class="busqueda table" id="retiro_div">
									<thead>
										<tr class="tit_tabla_buscar">
											<th class="nmcatalogbusquedatit">ID de Retiro</th>
											<th class="nmcatalogbusquedatit">Cantidad</th>
											<th class="nmcatalogbusquedatit">Concepto</th>
											<th class="nmcatalogbusquedatit">Fecha</th>
											<th class="nmcatalogbusquedatit">Usuario</th>
										</tr>
									</thead>
									<tbody></tbody>
									<tfoot></tfoot>
								</table>
	        				</div>
	        			</div>
	        		</div>
	        	</section>
	        </section>
	        <h3>Saldos</h3>
	        <section>
	        	<div class="row">
	        		<div class="col-sm-4">
	        			<label>Saldo inicial de caja:</label>
	        			$<input id='saldo_inicial' type='text' class='numeric form-control' maxlength='10' readonly>
	        		</div>
	        		<div class="col-sm-4">
	        			<label>Monto de ventas en el periodo:</label>
	        			$<input id='monto_ventas' type='text' class='numeric form-control' maxlength='10' readonly>
	        		</div>
	        		<div class="col-sm-4">
	        			<label>Saldo disponible en caja:</label>
	        			$<input id='saldo_disponible' type='text' class='numeric form-control' maxlength='10' readonly>
	        		</div>
	        	</div>
	        	<div class="row">
	        		<div class="col-sm-4">
	        			<button onclick="arc();" class="btn btn-primary btnMenu col-md-4">Arqueo</button>
	        		</div>
	        		<div class="col-sm-4">
	        		</div>
	        		<div class="col-sm-4">
	        		</div>
	        	</div>
	        </section>
	        <h3>Depositos / Retiros</h3>
	        <section>
	        	<div class="row">
	        		<div class="col-sm-6">
	        			<label>Retiro de caja:</label>
	        			$<input id='retiro_caja' type='text' class='numeric form-control' style='background-color: #FFCCDD;' maxlength='10'>
	        		</div>
	        		<div class="col-sm-6">
	        			<label>Depo&#769;sito de caja:</label>
	        			$<input id='deposito_caja'	type='text' class='numeric form-control' style='background-color: #A9F5A9;' maxlength='10'>
	        		</div>
	        	</div>
	        	<div class="row">
	        		<div class="col-sm-6">
	        			<button id='send' class="btn btn-primary btnMenu col-md-4">Guardar</button>
	        		</div>
	        		<div class="col-sm-6">
	        		</div>
	        	</div>
	        </section>
	    </div>

	</body>
</html>