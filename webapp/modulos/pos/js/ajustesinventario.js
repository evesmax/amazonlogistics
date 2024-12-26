$(document).ready(function() {
    $('.verMovimientos')
        .click(function(event) {
            fecha = $(this).parent().parent().find(':nth-child(1)').html();
            responsable = $(this).parent().parent().find(':nth-child(2)').html();
            $.ajax({
                url: 'ajax.php?c=ajustesInventario&f=movimientos',
                type: 'GET',
                dataType: 'json',
                data: {fecha: fecha},
            })
            .done(function(movimientos) {
                $('#tablaMovimientos').empty();
                $('#fechaAjuste').empty().append('Fecha: '+fecha + '<br>Responsable: '+responsable);
                $(movimientos).each(function(index, el) {
                    if(el.serie) {
                        //for (var i = 0; i < el.cantidad; i++) {
                            $('#tablaMovimientos')
                            .append(`
                            <tr>
                                <td>1</td>
                                <td>${el.nombre}</td>
                                <td>${el.serie ? el.serie : ""}</td>
                                <td>${el.lote ? el.lote : ""}</td>
                                <td>${el.id_almacen_origen ? el.id_almacen_origen : "" }</td>
                                <td>${el.id_almacen_destino ? el.id_almacen_destino : ""}</td>
                            </tr>
                            `);
                        //}
                    } else {
                        $('#tablaMovimientos')
                        .append(`
                        <tr>
                            <td style="text-align:center">${el.cantidad}</td>
                            <td style="text-align:center">${el.nombre}</td>
                            <td style="text-align:center">${el.serie ? el.serie : ""}</td>
                            <td style="text-align:center">${el.lote ? el.lote : ""}</td>
                            <td style="text-align:center">${el.id_almacen_origen ? el.id_almacen_origen : ""}</td>
                            <td style="text-align:center">${el.id_almacen_destino ? el.id_almacen_destino : ""}</td>
                        </tr>
                        `);
                    }
                    
                });
                $('#modalMovimientos').modal();
                $('#tableSale').DataTable({
                    destroy: true,
                            dom: 'Bfrtip',
                            buttons: [  
                                {extend: 'print',
                                    title: `Ajuste de inventario - ${fecha} <br>Responsable: ${responsable}`,
                                    customize: function(doc) {
                                         console.log(doc)
                                       }

                                } ,
                                {extend: 'pdf',
                                    title: `Ajuste de inventario - ${fecha} \nResponsable: ${responsable}`,
                                    customize: function(doc) {
                                         console.log(doc)
                                       }

                                } 
                            ],
                            language: {
                                
                                    buttons: {
                                        print: 'Imprimir'
                                    },
                                
                                search: "Buscar",
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
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
            
        });
});