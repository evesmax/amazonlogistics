function createnewP(){ 
   var pathname = window.location.pathname;
   window.location.href = "ajax.php?c=pedido&f=pedidoView";
    //$("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=pedidoView');  
}
function backbuttonP(){ 
  
    var pathname = window.location.pathname;
    window.location.href = "ajax.php?c=pedido&f=imprimeGridP";
    //$("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=imprimeGridP');
}
function mostrarmas(){
  var estatus = $("#estatus").val();
  var lim = $("#lim").val();
  var cliente = $('#cotiClienteP').val();
  var empleado = $('#cotiEmpleadoP').val();
  var desde = $('#desde').val();
  var hasta = $('#hasta').val();
  if(lim == null || lim == ''){
    return false;
  }  
    $.ajax({
      url: 'ajax.php?c=pedido&f=buscaP2',
      type: 'POST',
      dataType: 'json',
      data: {cliente: cliente, empleado:empleado,desde:desde,hasta:hasta,lim:lim},
    })
    .done(function(data) {

      var cotizacion='';
                var perfilBo = '';
                var total = 0;
                $.each(data.pedidos, function(index, val) {
                  total = val.total * 1;
                    if(val.idCotizacion==null){
                      cotizacion='';
                    }else{
                      cotizacion=val.idCotizacion;
                    }



                       switch(val.status) {
                        case '0':
                            estado = '<a class="btn btn-danger">Cancelado</a>';
                            break;
                        case '1':
                            estado = '<a onclick="aProceso('+val.id+');" class="btn btn-default">Activo</a>';
                            break;
                        case '2':
                            estado = '<a onclick="aTerminado('+val.id+');" class="btn btn-warning">Proceso</a>';
                            break;
                        case '3':
                            estado = '<a  class="btn btn-primary">Terminado</a>';
                            break;
                        case '4':
                            estado = '<a  class="btn btn-info">En Venta</a>';
                            break;  
                        case '5':
                            estado = '<a class="btn btn-success">Vendido</a>';
                            break;

                    }

                    //if(data.perfil=='(5)'){
                    if(data.perfil=='(2)'){
                      if(val.status==3 || val.status==4){
                         perfilBo = '<a onclick="pedido('+val.id+');" class="btn btn-default"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
                      }
                      if(val.status==1 || val.status==2){
                         perfilBo = '<a onclick="cancelarP('+val.id+');" class="btn btn-default"><i class="fa fa-remove" aria-hidden="true"></i></a>';
                      }
                      if(val.status==0){
                         perfilBo = '<a onclick="eliminarP('+val.id+');" class="btn btn-default"><i class="fa fa-remove" aria-hidden="true"></i></a>';
                      }

                    }

                    x ='<tr class="trtablitaGridP">'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.id+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.nombre+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+cotizacion+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">$'+total.toFixed(2)+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.usuario+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.fecha+'</a></td>'+
                        '<td><a onclick="FunPdf('+val.id+');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></td>'+
                        '<td>'+estado+perfilBo+
                        '</td>'+
                        '</tr>';
                        window.table.row.add($(x)).draw(); 
                        perfilBo = '';

                        $("#lim").val(val.lim*1);
                }); 

    })
}
function buscaP(){
  var cliente = $('#cotiClienteP').val();
  var empleado = $('#cotiEmpleadoP').val();
  var desde = $('#desde').val();
  var hasta = $('#hasta').val();
  
  $.ajax({
    url: 'ajax.php?c=pedido&f=buscaP',
    type: 'POST',
    dataType: 'json',
    data: {cliente: cliente, empleado:empleado,desde:desde,hasta:hasta},
  })
  .done(function(data) {
    console.log(data);

                window.table.clear().draw();
                var x ='';
                var estado = '';
                var total = 0;
                /*$('.trtablitaGridP').remove();*/

                var cotizacion='';
                var perfilBo = '';
                $.each(data.pedidos, function(index, val) {
                  total = val.total * 1;
                  /**/
                    if(val.idCotizacion==null){
                      cotizacion='';
                    }else{
                      cotizacion=val.idCotizacion;
                    }



                       switch(val.status) {
                        case '0':
                            estado = '<a class="btn btn-danger">Cancelado</a>';
                            break;
                        case '1':
                            estado = '<a onclick="aProceso('+val.id+');" class="btn btn-default">Activo</a>';
                            break;
                        case '2':
                            estado = '<a onclick="aTerminado('+val.id+');" class="btn btn-warning">Proceso</a>';
                            break;
                        case '3':
                            estado = '<a  class="btn btn-primary">Terminado</a>';
                            break;
                        case '4':
                            estado = '<a  class="btn btn-info">En Venta</a>';
                            break;  
                        case '5':
                            estado = '<a class="btn btn-success">Vendido</a>';
                            break;

                    }

                    //if(data.perfil=='(5)'){
                    if(data.perfil=='(2)'){
                      if(val.status==3 || val.status==4){
                         perfilBo = '<a onclick="pedido('+val.id+');" class="btn btn-default"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
                      }
                      if(val.status==1 || val.status==2){
                         perfilBo = '<a onclick="cancelarP('+val.id+');" class="btn btn-default"><i class="fa fa-remove" aria-hidden="true"></i></a>';
                      }
                      if(val.status==0){
                         perfilBo = '<a onclick="eliminarP('+val.id+');" class="btn btn-default"><i class="fa fa-remove" aria-hidden="true"></i></a>';
                      }

                    }

                    x ='<tr class="trtablitaGridP">'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.id+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.nombre+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+cotizacion+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">$'+total.toFixed(2)+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.usuario+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.fecha+'</a></td>'+
                        '<td><a onclick="FunPdf('+val.id+');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></td>'+
                        '<td>'+estado+perfilBo+
                        '</td>'+
                        '</tr>';
                        window.table.row.add($(x)).draw(); 
                        perfilBo = '';
                    /*$('#tabliGriP tr:last').after('<tr class="trtablitaGridP">'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.id+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.nombre+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+cotizacion+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">$'+val.total+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.usuario+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.fecha+'</a></td>'+
                        '<td><a onclick="FunPdf('+val.id+');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></td>'+
                        '<td>'+estado+perfilBo+
                        '</td>'+
                        '</tr>'); 
                        perfilBo = '';*/

                        $("#lim").val(val.lim*1);
                }); 
  })
  .fail(function() {
    console.log("error");
  })
  .always(function() {
    console.log("complete");
  });
  
}

function gridPedidos(){

            $.ajax({
                url: 'ajax.php?c=pedido&f=printGridP',
                type: 'GET',
                dataType: 'json',
            })
            .done(function(data) {
               // var status='';
               var cotizacion='';
               var total = 0;

                //var table = $('#tabliGriP').DataTable();
               var table =     $('#tabliGriP').DataTable({
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
                //$('.filas').empty();
                table.clear().draw();
                var x ='';
                var estado = '';
                var perfilBo = '';
                $.each(data.pedidos, function(index, val) {
                  total = val.total * 1;
                    if(val.idCotizacion==null){
                      cotizacion='';
                    }else{
                      cotizacion=val.idCotizacion;
                    }

                    switch(val.status) {
                        case '0':
                            estado = '<a class="btn btn-danger">Cancelado</a>';
                            break;
                        case '1':
                            estado = '<a onclick="aProceso('+val.id+');" class="btn btn-default">Activo</a>';
                            break;
                        case '2':
                            estado = '<a onclick="aTerminado('+val.id+');" class="btn btn-warning">Proceso</a>';
                            break;
                        case '3':
                            estado = '<a  class="btn btn-primary">Terminado</a>';
                            break;
                        case '4':
                            estado = '<a  class="btn btn-info">En Venta</a>';
                            break;  
                        case '5':
                            estado = '<a class="btn btn-success">Vendido</a>';
                            break;

                    }
                    if(data.perfil=='(2)'){
                      if(val.status==3 || val.status==4){
                         perfilBo = '<a onclick="pedido('+val.id+');" class="btn btn-default"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>';
                      }

                    }
                    x ='<tr class="trtablitaGridP">'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.id+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.nombre+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+cotizacion+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">$'+total.toFixed(2)+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.usuario+'</a></td>'+
                        '<td><a href="ajax.php?c=pedido&f=pedidoView&pe='+val.id+'">'+val.fecha+'</a></td>'+
                        '<td><a onclick="FunPdf('+val.id+');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a></td>'+
                        '<td>'+estado+perfilBo+
                        '</td>'+
                        '</tr>';
                        table.row.add($(x)).draw(); 
                        perfilBo = '';
                }); 
            })
            .fail(function() {
                console.log("error");
            });

}
function filtrosPedidos(){
            $.ajax({
              url: 'ajax.php?c=pedido&f=printFiltrosP',
              type: 'GET',
              dataType: 'json',
            })
            .done(function(data) {
            
                  $.each(data.cliente, function(index, val) {
                    $('#cotiClienteP').append('<option value="'+val.id+'">'+val.nombre+'</option>');
                  });

                  $.each(data.empleado, function(index, val) {
                    $('#cotiEmpleadoP').append('<option value="'+val.idempleado+'">'+val.usuario+'</option>');
                  });
            })
            .fail(function() {
              console.log("error");
            })
            .always(function() {
              console.log("complete");
            }); 
}
function loadPedido(idPedido){
        $.ajax({
          url: 'ajax.php?c=pedido&f=pedidoView',
          type: 'POST',
          dataType: 'json',
          data: {idPedido: idPedido},
        })
        .done(function(data) {
        /*  var pathname = window.location.pathname;
          $("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=pedidoView1');
           */
           $('#cantidad').val(12);
          ////Select Productos
              $.each(data , function(index, value) {
                var optionProduct = $(document.createElement('option')).attr({'value': value.idProducto}).html(value.nombre).appendTo($('#selectProductP'));
              });

        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
    
}
function agregaP(){
      var idProducto =  $('#selectProductP').val();
      var cantidad = $('#cantidad').val();  
       
       if(idProducto==0 || idProducto ==''){
            alert('Tienes que agregar un producto');
            return;
       }
       if(cantidad <= 0 || cantidad==''){
            alert('la canidad no puede ser negativa o estar vacia');
            return;
       }

      $.ajax({
          url: 'ajax.php?c=pedido&f=addProductP',
          type: 'POST',
          dataType: 'json',
          data: {idProducto: idProducto,cantidad:cantidad},
      })
      .done(function(data) {
        
        $('#tableContainer').show();
        $(".xxx").remove();
        var precio = 0;
        var importe = 0;
          $.each(data.rows, function(index, val) {
            if(index!='charges'){
              var cantidad = val.cantidad;
                    cantidad = cantidad * 1;
                    precio = val.precio * 1;
                    importe = val.importe * 1;
                    $('#cotTable tr:last').after('<tr class="cotTable xxx" id="prodFila_'+val.idProducto+'">'+
                        '<td>'+cantidad+'</td>'+
                        '<td>'+val.nombre+'</td>'+
                        '<td>'+val.unidad+'</td>'+
                        '<td>'+precio.toFixed(2)+'</td>'+
                        '<td>'+importe.toFixed(2)+'</td>'+
                        '<td><span class="glyphicon glyphicon-minus-sign" onclick="deleteProP('+val.idProducto+');"></span></td>'+
                        '</tr>');
            }       
          });
          $(".tax").remove();
          $.each(data.rows["charges"]["taxes"], function(index, val) {
              if(val!=0 || val!='0'){
                 $('#divTaxes').append('<div class="tax"><label>'+index+':$</label>'+val.toFixed(2)+'</div>');
              }
            
          });
          data.rows["charges"]["sbtot"] = data.rows["charges"]["sbtot"].toFixed(2);
          data.rows["charges"]["Tot"] = data.rows["charges"]["Tot"].toFixed(2);
          $('#divTaxes').append('<div class="tax"><label>Subtotal:</label>'+data.rows["charges"]["sbtot"]+'</div>');
          $('#totalLab').html(data.rows["charges"]["Tot"]);
        /*  $.each(data.rows["charges"], function(index, val) {
             if(index='sbtot'){
                $('#divTaxes').append('<div class="tax"><label>Subtotal:</label>'+val+'</div>');
             }
             if(index='Tot'){
                $('#totalLab').html(val);
             }  
          }); */
          
          $('#cantidad').val(''); 
          $('#selectProductP > option[value="0"]').prop('selected',true);
          $("#selectProductP").select2({
              width : "150px"
          });  


      })
      .fail(function() {
          console.log("error");
      })
      .always(function() {
          console.log("complete");
      });
      
}
function sendP(){
    var idPedido = $('#idPedidoHide').val();
    var idCliente = $('#selectcliente').val();
    var observacion = $('#observ').val();
    if(idCliente==0 || idCliente==''){
        alert('Debes seleccionar un Cliente para enviar la cotzacion');
        return;
    }
      $('#sendBotton').hide();
      $('#modalMensajes').modal({
                        show:true,
                        keyboard: false,
                    });
      $.ajax({
        url: 'ajax.php?c=pedido&f=sendP',
        type: 'POST',
        dataType: 'json',
        data: {idCliente: idCliente,observacion:observacion,idPedido:idPedido},
      })
      .done(function(data) {

        if(data.status==true || data.status==1){
          alert('Se registro tu Pedido');
          var pathname = window.location.pathname;
          //$("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=imprimeGridP');
          window.location.href = "ajax.php?c=pedido&f=imprimeGridP";
        }else{
          alert('El cliente no tiene correo electronico registrado');
          var pathname = window.location.pathname;
          //$("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=imprimeGridP');
          window.location.href = "ajax.php?c=pedido&f=imprimeGridP";
        }
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
}
function deleteProP(idProducto){
  var idPedido = $('#idPedidoHide').val();
  var id = idProducto;
        $.ajax({
          url: 'ajax.php?c=pedido&f=deleteProP',
          type: 'POST',
          dataType: 'json',
          data: {id: id,idPedido:idPedido},
        })
        .done(function(data) {
         $('#prodFila_'+idProducto).remove();
         pintaPedi(data);
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
  
}
function pintaPedi(data){

        $('#tableContainer').show();
        $(".xxx").remove();
        var precio = 0;
        var importe = 0;
          $.each(data.rows, function(index, val) {
            if(index!='charges'){
              var cantidad = val.cantidad;
                    cantidad = cantidad * 1;
                    precio = val.precio*1;
                    importe = val.importe*1;
                    $('#cotTable tr:last').after('<tr class="cotTable xxx" id="prodFila_'+val.idProducto+'">'+
                        '<td>'+cantidad+'</td>'+
                        '<td>'+val.nombre+'</td>'+
                        '<td>'+val.unidad+'</td>'+
                        '<td>'+precio.toFixed(2)+'</td>'+
                        '<td>'+importe.toFixed(2)+'</td>'+
                        '<td><span class="glyphicon glyphicon-minus-sign" onclick="deleteProP('+val.idProducto+');"></span></td>'+
                        '</tr>');
            }       
          });
          $(".tax").remove();
          $.each(data.rows["charges"]["taxes"], function(index, val) {
              if(val!=0 || val!='0'){
                 $('#divTaxes').append('<div class="tax"><label>'+index+':$</label>'+val.toFixed(2)+'</div>');
              }
            
          });
          data.rows["charges"]["sbtot"] = data.rows["charges"]["sbtot"].toFixed(2);
          data.rows["charges"]["Tot"] = data.rows["charges"]["Tot"].toFixed(2);
          $('#divTaxes').append('<div class="tax"><label>Subtotal:</label>'+data.rows["charges"]["sbtot"]+'</div>');
          $('#totalLab').html(data.rows["charges"]["Tot"]);
        /*  $.each(data.rows["charges"], function(index, val) {
             if(index='sbtot'){
                $('#divTaxes').append('<div class="tax"><label>Subtotal:</label>'+val+'</div>');
             }
             if(index='Tot'){
                $('#totalLab').html(val);
             }  
          }); */
          
          $('#cantidad').val(''); 
}
function pedido(idPedido){  
  
        $.ajax({
          url: 'ajax.php?c=pedido&f=sendCajaPedido',
          type: 'POST',
          dataType: 'json',
          data: {idPedido: idPedido},
        })
        .done(function(data) {

        if(data.venta==true){
          alert('Este pedido ya se hizo venta');
          return false;
        }
          $.each(data.codigo, function(index, val) {
                var outElement=$("#tb1898-u",window.parent.document).parent();
                var caja=outElement.find("#tb1238-u");
                var pestana=$("body",window.parent.document).find("#tb1238-1");
                var openCaja=$("body",window.parent.document).find("#mnu_1238");
                var pathname = window.location.pathname;
                var url=document.location.host+pathname;
                
                openCaja.trigger('click');
                
                pestana.trigger('click');
                //if(caja.length>0){
                  var campoBuscar=$(".frurl",caja).contents().find("#search-producto");
                  var campoCantidad=$(".frurl",caja).contents().find("#cantidad-producto");
                  var campoPedido=$(".frurl",caja).contents().find("#idPedido");
                  
                  ///PAra el pedido
                
                  campoPedido.trigger('focus');
                  campoPedido.val(idPedido);
                  
                  campoCantidad.trigger('focus');
                  campoCantidad.val(val.cantidad);
                  
                  campoBuscar.trigger("focus");
                  //campoBuscar.trigger("click");
                  campoBuscar.val(val.codigo);
                  campoBuscar.trigger({type: "keypress", which: 13});


          });




        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
  
}
function cancelarP(id){
  var r = confirm("Deseas cancelar este pedido?");
    if (r == true) {
      console.log('Cancelado.. '+id);
      $.ajax({
              url: 'ajax.php?c=pedido&f=cancelarP',
              type: 'POST',
              dataType: 'json',
              data: {id: id },
            })
      .done(function(resp) {
        console.log(resp);
        buscaP();
      });
    }else{
      return false;
    } 
}

function eliminarP(id){
  var r = confirm("Deseas eliminar este pedido?");
    if (r == true) {
      console.log('eliminado.. '+id);
      $.ajax({
              url: 'ajax.php?c=pedido&f=eliminarP',
              type: 'POST',
              dataType: 'json',
              data: {id: id },
            })
      .done(function(resp) {
        console.log(resp);
        buscaP();
      });
    }else{
      return false;
    }
}

function FunPdf(id){
	$.ajax({
		url: '../../modulos/cotizaciones/cotizacionesPdf/pedido_'+id+'.pdf',
	})
	.done(function(){
		window.open("../../modulos/cotizaciones/cotizacionesPdf/pedido_"+id+".pdf");
	})
	.fail(function() {
		alert("El archivo ha sido eliminado o no se ha creado correctamente");
	})
    .always(function() {
      console.log("complete");
    });
}

function aProceso(id){
    var txt;
    var r = confirm("Deseas cambiar este pedido a proceso?");
    if (r == true) {

      $('#modalMensajes').modal({
          show:true,
          keyboard: false,
      });
        $.ajax({
          url: 'ajax.php?c=pedido&f=aProceso',
          type: 'POST',
          dataType: 'json',
          data: {id: id },
        })
        .done(function(resp) {
          console.log(resp);
          $('#modalMensajes').modal('hide');
          alert('Se Cambio de estado');
          var pathname = window.location.pathname;
          $("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=imprimeGridP');
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
        
    } else {
        
    }

}
function aTerminado(id){
    var txt;
    var r = confirm("Deseas cambiar este pedido a Terminado?");
    if (r == true) {

      $('#modalMensajes').modal({
          show:true,
          keyboard: false,
      });
        $.ajax({
          url: 'ajax.php?c=pedido&f=aTerminado',
          type: 'POST',
          dataType: 'json',
          data: {id: id },
        })
        .done(function(resp) {
          console.log(resp);
          $('#modalMensajes').modal('hide');
          alert('Se Cambio de estado');
          var pathname = window.location.pathname;
          $("#tb1898-u .frurl",window.parent.document).attr('src','http://'+document.location.host+pathname+'?c=pedido&f=imprimeGridP');
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
        
    } else {
        
    }
}




