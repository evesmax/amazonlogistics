function reloadtable(){

	var desde = $("#desde").val();
	var hasta = $("#hasta").val();
	var sucursal = $("#idSucursal").val();
	var cliente = $("#cliente").val();
	var puntos = $("#puntos").val();
	var formaPago = $("#cboMetodoPago").val();

	$.ajax({
		url: 'ajax.php?c=reporte&f=datosMonedeto',
		type: 'POST',
		dataType: 'json',
		data: {desde: desde,
				hasta: hasta,
				sucursal: sucursal,				
				cliente: cliente,
				puntos: puntos,
				formaPago: formaPago
			},		
	})
	.done(function(data) {
		console.log(data);
		$('#tableDivCont').empty();

		$('#tableDivCont').append('<table class="table table-bordered table-hover "  id="tableProductos">'+
			'<thead><tr>'+
						'<th width="11%"># Monedero</th>'+
						'<th>ID Venta</th>'+
						'<th>Fecha</th>'+
						'<th>Cliente</th>'+
						'<th>Sucursal</th>'+
						'<th>Forma de Pago</th>'+
						'<th>Total Venta</th>'+
						'<th>Generados</th>'+
						'<th>Utilizados</th>'+
						'<th>Saldo</th>'+						
					'</tr>'+
				'</thead>'+
				'<tbody></tbody><tfoot></tfoot></table>');
		var x = '';
		var totalLabel = 0;
		var table =  $('#tableProductos').DataTable({
							fixedHeader: {
					            header: true,
					            footer: true
					        },
					        paging:   false,
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
                            "columnDefs": [
						      { className: "text-right", "targets": [6] },
						      { className: "text-center", "targets": [6,7,8,9] }						      
						    ],
							"bPaginate": false,
						    "bLengthChange": false,
						    "bFilter": true,
						    "bInfo": false,
						    "bAutoWidth": false,
						    "searching": false,
                            "bSort": false
        });
		var monedero = 0;
		$.each(data, function(index, val) {			
			

			if( monedero != val.monedero ) {
				x ='<tr class="filas header1">'+
                                '<td> <i class="btn btn-xs fa fa-bars" data-toggle="collapse" data-target=".collapsed'+val.monedero+'"></i> '+val.monederoN+'</td>'+
                                '<td></td>'+
                                '<td>'+val.fechaMonedero+'</td>'+
                                '<td>'+val.cliente+'</td>'+
                                '<td>'+val.sucursal+'</td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td>'+val.puntos+'</td>'+                                
                    '</tr>';                            
                table.row.add($(x)).draw();
			}
            
            if(val.idFormapago != 10){
            	var utilizados = 0
				var generados = val.generados
            }else{
            	var utilizados =val.utilizados;
            	var generados = 0
            }
            x ='<tr class="collapsed'+val.monedero+' collapse filas header2 monederoC_'+val.monedero+'" >'+
		                '<td> </td>'+
		                '<td>'+val.idVenta+'</td>'+
		                '<td>'+val.fecha+'</td>'+
		                '<td></td>'+
		                '<td>'+val.sucursal+'</td>'+
		                '<td>'+val.formaPago+'</td>'+
		                '<td>$'+val.totalVenta+'</td>'+
		                '<td class"monedero_'+val.monedero+'">'+generados+'</td>'+
		                '<td>'+utilizados+'</td>'+
		                '<td>'+val.saldo+'</td>'+                               
		        '</tr>';

            table.row.add($(x)).draw();

            monedero = val.monedero            
		});
		
		// x ='<tr class=" filas header2" >'+
  //                               '<th></th>'+
  //                               '<th>TOTAL</th>'+
  //                               '<th>>>>></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+
  //                               '<th></th>'+                                
  //                               '</tr>';

		// $('#tableDivCont tfoot').append(x);
  //       table.row.add($(x)).draw();
		// eliminaMensaje();
	});

}
// function checkMonedero(monedero){
// 	if($('monederoC_'+monedero+'').is(":hidden")){
// 		alert(monedero);
// 	}else{
// 		alert('no')
// 	}
// }