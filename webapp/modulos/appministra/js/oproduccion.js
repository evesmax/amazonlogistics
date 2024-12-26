/**
 * @author KRMN
 */

/*explosion masiva de materiales*/
    function explosionmasiva(){
    	//le digo a la view q estos explosionando una masiva
    		$("#explotandoinsumosmasivos").val(1);
    		
    		var idoparray=new Array;
    	  	$(".multiexplosion").each(function( index ) {
    	  		if( $("#"+$(this).attr('id') ).is(":checked") ){
            		//alert($(this).attr('id'));
            		idoparray.push($(this).attr('id'));
            
           }
        });
        console.log(idoparray);
        //return false;
        
        $('#div_ciclo').css('display','none');
        //$('#btn_savequit').text('Generar Pre-Requisicion');

         if(orden=='0'){
			$('#btn_savequit').text('Generar Requisicion');
 			$('#orden').val('0');
    		 }
         else{  
         	$('#btn_savequit').text('Generar Orden');
			$('#tit').text('Orden de Compra');          
			$('#orden').val('1');
     	}


        $('#btnlistorden').css('visibility','hidden');
        $("#btnexplosionmasiva").css('visibility','hidden');
        $('#btnback').css('visibility','visible');

        table = $('#tablaprods2').DataTable();
        table.destroy();

        $('#listareq').css('display','none');
        $('#modal-conf1').modal('hide');
        $('#nreq').css('display','none');
        $('#nreq_load').css('display','block');
        $('#panel_tabla').css('display','none');
        $('#panel_tabla2').css('display','block');
        $('#nreq').css('display','block');


        $('#addprodoexplo').css('display','none');
        $('#addprodoexplo2').css('display','block');


        //$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales - Prerequisiciones</span>');
		if(orden=='0'){
			$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales - Requisiciones</span>');}
		else{
			$('#ph').html('<span opt="3" class="label label-warning" style="cursor:pointer;">Explosion de materiales Masiva - Ordenes</span>');}

        $.ajax({
            url:"ajax.php?c=produccion&f=a_explosionMatMasiva",
            type: 'POST',
            dataType:'JSON',                                
            data:{idop:idoparray},
            success: function(r){

                if(r.success==1){

                    // $('#userlog').text(r.requisicion.username);
                        // $('#iduserlog').val(r.requisicion.idempleado);
                        // $('#txt_nreq').text(r.requisicion.id);
                        $('#nreq_load').css('display','none');
                        $(".simple").hide();
                        // $("#c_prioridad").val(r.requisicion.prioridad).trigger("change");
                        // $("#c_sucursal").val(r.requisicion.idsuc).trigger("change");
                        // $("#date_hoy").val(r.requisicion.fi);
                        // $("#date_entrega").val(r.requisicion.fe);
 						// $("#c_solicitante").val(r.requisicion.idsol).trigger("change");
                        $("#c_prioridad").prop('disabled',true);
                        $("#c_sucursal").prop('disabled',true);
                        $("#date_hoy").prop('disabled',true);
                        $("#date_entrega").prop('disabled',true);
                        $("#comment").prop('disabled',true);
   						$("#c_solicitante").prop('disabled',true);
                       
                        $('#panelexplosion').css('display','block');
// 
//                         
                        // var comment = r.requisicion.observaciones.replace(/<br\s?\/?>/g,"\n");
// 
                        // $("#comment").val(comment);

                      	$.each(r.productos, function( k, v ) {
                        //eliminProd="<button onclick='removeProdReq("+v.id+",0);' id='btn_addProd' class='btn btn-sm btn-danger' type='button' style='height:26px;'>Quitar</button>";

                        eliminProd='';

                        // Rowdata1="<tr ch='0' id='tr_"+v.id+"' eshead='1' style='background-color:#eee;'><td colspan='4'><b>Orden de produccion:</b> "+v.nomprod+"</td><td colspan='5' style='color:red;size:14px' id='leyendavariable'></td></tr>";
// 
                        // $('#filasprods2').append(Rowdata1);

                        if(v.insumos!=0){
                            usar=0;
                            $.each(v.insumos, function( k2, v2 ) {
                                cant_total=v2.canti;

                                cant_total=parseFloat(cant_total).toFixed(2);

                                if(v2.existencias<cant_total){
                                    usar++;
                                    ext='<font color="#ff0000">'+v2.existencias+'</font>';
                                }else{
                                    ext='<font color="#096">'+v2.existencias+'</font>';
                                }
                                
                                 var cantidadtotal = cant_total;
                                
                                 var listaprv="<td></td><td></td>";
                                if( $("#mostrarprv").val()==1){
                                		 listaprv = "<td id='valUnit'>"+v2.listprovs+"</td><td><input style='width:60%;' class='numeros' type='text' value='0' disabled /></td>";
                                }
                                
                                Rowdata="<tr ch='0' id='tr_"+v.id+"_"+v2.idProducto+"' eshead='0'><td>"+v2.codigo+"</td><td>"+v2.nombre+"</td><td>"+v2.unidad_clave+"</td>"+listaprv+"<td class='valCantidad valcanti"+v2.unidad_clave+"' id='valCantidad'>"+cantidadtotal+"</td><td class='exxxis'>"+ext+"</td><td class='text-right' id='ttt' implimpio='0'>0.00</td><td>"+eliminProd+"</td></tr>";
                                $('#filasprods2').append(Rowdata);

                            });
                        }else{
                            Rowdata="<tr ch='0' id='tr_"+v.id+"' eshead='2'><td colspan='8'>Este producto no tiene insumos registrados</td></tr>";
                            $('#filasprods2').append(Rowdata);
                        }
                        
                        if(usar==0){
                            $('#btn_savequit_usar').css('visibility','visible');
                        }else{
                            $('#btn_savequit_usar').css('visibility','hidden');
                        }
                        
                        
                        

                    });
                }

            }
        });
        
        
        
        
    }
