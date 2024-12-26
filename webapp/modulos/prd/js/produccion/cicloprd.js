function ciclo(idop){
	$("#contenido").load("views/produccion/cicloprd.php");
	$('#izqpasos').html('<div class="col-sm-12 p0"  style="margin-top: 2px;">\
                        <div class="form-group" id="panelprod">\
                            <div class="col-sm-12" style="margin-bottom: 5px;">\
                                Cargando...\
                            </div>\
                        </div>\
                        </div>');
        
    $('#div_ciclo').css('display','block');
    $('#div_ciclo').attr('oprod',idop);

    $('#btnlistorden').css('visibility','hidden');
    $("#btnexplosionmasiva").css('visibility','hidden');
    $('#btnback').css('visibility','visible');

    table = $('#tablaprods').DataTable();
    table.clear().draw();
    
    $('#listareq').css('display','none');
    $('#modal-conf1').modal('hide');
    $('#nreq').css('display','none');
    $('#nreq_load').css('display','block');

    $('#panel_tabla2').css('display','none');
    $('#panel_tabla').css('display','block');
    $('#addprodoexplo').css('display','block');
    $('#addprodoexplo2').css('display','none');
    $.ajax({
        url:"ajax.php?c=Cicloprd&f=a_explosionMatCiclo",
        type: 'POST',
        dataType:'JSON',                                
        data:{idop:idop},
        success: function(r){
        	if(r.success==1){
        		o=new Object();
                d=new Object();
                cont='';
                subcont='';
                x=0;
                margtop="-2";
                realizados=0;
                disabled='disabled';

                tot = Object.keys(r.data).length;
                $('#ppp').html("<a id='printer' style='color:white;display:none;' >.</a><b>Producto: </b> "+r.ddd.nombre+" <b>Cantidad: <input id='lacant' type='hidden' value='"+r.ddd.cantidad+"'><input id='pesodim' type='hidden' value='"+r.ddd.peso_dimension+"'></b> "+r.ddd.cantidad);
                $.each(r.data, function( k, v ) {
                       if(v.pasorealizado==1){
                            d[k]='ok';
                            if((k+1)<tot){
                                d[k+1]='act';
                            }
                        }else{
                            if(v.tipo==2){
                                d[k]='act';
                            }
                        }
                        if(k==0 && v.pasorealizado==0){
                            d[k]='act';
                        }
						if(x>0){
                            margtop=12;
                        }
                        imp='';
                        if(v.id_paso in o){
                            subcont='<div class="col-sm-12"  style="margin-top: 5px;">\
                                    <button acc="'+v.id_accion+'" style="width:100%;" id="k_'+k+'" '+disabled+' onclick="clipaso('+v.id_paso+','+v.id_accion+','+idop+','+v.id_accion_producto+',\''+v.nombre_accion+'\','+v.id_producto+');" id="ciclo_p'+v.id_paso+'_a'+v.id_accion+'" class="btn btn-sm btn-default" style="width:316px;" type="button" style="height:28px;">'+v.nombre_accion+' '+imp+'</button>\
                                </div>';
                            o[v.id_paso]+=subcont;
                        }else{
                            o[v.id_paso]='';
                            subcont='<div class="col-sm-12" style="margin-top: 5px;">\
                                    <button acc="'+v.id_accion+'" style="width:100%;" id="k_'+k+'" '+disabled+' onclick="clipaso('+v.id_paso+','+v.id_accion+','+idop+','+v.id_accion_producto+',\''+v.nombre_accion+'\','+v.id_producto+');" id="ciclo_p'+v.id_paso+'_a'+v.id_accion+'" class="btn btn-sm btn-default" style="width:316px;" type="button" style="height:28px;">'+v.nombre_accion+' '+imp+'</button>\
                                </div>';
                            cont+='<div class="col-sm-12 p0"  style="margin-top: '+margtop+'px;">\
                            <div class="form-group" id="panelprod">\
                                <div class="col-sm-12" style="margin-bottom: 5px;">\
                                    '+v.nombre_paso+'\
                                </div>\
                                <div id="subcont_'+v.id_paso+'">\
                                </div>\
                            </div>\
                            </div>';
                            o[v.id_paso]+=subcont;
                        }
                        x++;
                    });
                    $('#izqpasos').html(cont);
                    $.each(o, function( k, v ) {
                        $('#subcont_'+k).html(v);
                    });

                    $.each(d, function( k, v ) {
                        if(v=='ok'){
                            $('#k_'+k).css("background-color", '#e0efdc');
                            acc = $('#k_'+k).attr('acc');
                            if(acc==16){
                                $('#k_'+k).css('width','83%');
                                $('#k_'+k).parent().append('<button onclick="btnprinter('+idop+');" style="width:15%;" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-print"></span></button>');
                            }
                        }
                        if(v=='act'){
                            $('#k_'+k).prop("disabled", false);
                        }
                    });

					$('#nreq_load').css('display','none');

                    if(r.agrupes!=0){
						cada='';
                        i=0;
                        $.each(r.agrupes, function( k, v ) {
                            if(idop==v.id){
                                sel=' selected="selected" ';
                            }else{
                                sel='';
                            }
                            if(i==0){
                                tx='Orden padre';
                            }else{
                                tx='Sub orden';
                            }
                            cada+='<option '+sel+' value="'+v.id+'">'+tx+'-'+v.id+'</option>';
                            i++;
                        });
                        $('#comboop').html('<b>Ordenes:</b> <select id="selagrupes" onchange="cambiaciclo();">'+cada+'</select>');
                    }else{
                        $('#comboop').html('');
                    }
        	}else{
        		$('#nreq_load').css('display','none');
        		$('#izqpasos').html('<div class="col-sm-12 p0"  style="margin-top: 2px;">\
                            <div class="form-group" id="panelprod">\
                                <div class="col-sm-12" style="margin-bottom: 5px;">\
                                    No hay procesos de produccion para esta orden.\
                                </div>\
                            </div>\
                            </div>');
        	}
        }
        });
            
}
 function clipaso(paso,accion,idop, idap, nac, idp){ 
    
 	 if(accion==1){
 	 	$.ajax({
	        url:"ajax.php?c=Accion1&f=viewAccion1",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
	        	//inicioaccion(idop,paso,accion,idap);
				$("#contenidociclo").html(r);
				
	        }
	   	});
 	 }if(accion==3){
       
        $.ajax({
             
             url:"ajax.php?c=Accion3&f=viewAccion3",
             type: 'post',
             data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
             success: function(r){
               
                $("#contenidociclo").html(r);
                
            }
        });
 }
     if(accion==2){//Registro de insumos utilizados

        $.ajax({
	        url:"ajax.php?c=Accion2&f=viewAccion2",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==4){//Registro de personal

        $.ajax({
	        url:"ajax.php?c=Accion4&f=viewAccion4",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==9){//fin prd

        $.ajax({
	        url:"ajax.php?c=Accion9&f=viewAccion9",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==14){//merma

        $.ajax({
	        url:"ajax.php?c=Accion14&f=viewAccion14",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==17){//reg inventario

        $.ajax({
	        url:"ajax.php?c=Accion17&f=viewAccion17",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==11){//envio material

        $.ajax({
	        url:"ajax.php?c=Accion11&f=viewAccion11",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==18){//envio material variable

        $.ajax({
	        url:"ajax.php?c=Accion18&f=viewAccion18",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==19){//registro de actividad

        $.ajax({
	        url:"ajax.php?c=Accion19&f=viewAccion19",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==6){//generacion lote

        $.ajax({
	        url:"ajax.php?c=Accion6&f=viewAccion6",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==16){//generacion etiqueta

        $.ajax({
	        url:"ajax.php?c=Accion16&f=viewAccion16",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==20){//empaque

        $.ajax({
	        url:"ajax.php?c=Accion20&f=viewAccion20",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==10){//caja master

        $.ajax({
	        url:"ajax.php?c=Accion10&f=viewAccion10",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     if(accion==21){//etiqueta master

        $.ajax({
	        url:"ajax.php?c=Accion21&f=viewAccion21",
	        type: 'post',
	        data:{idop:idop,paso:paso,accion:accion,idap:idap,idp:idp,nac:nac},
	        success: function(r){
				$("#contenidociclo").html(r);
				
	        }
	   	});
     }
     
 	 
 }