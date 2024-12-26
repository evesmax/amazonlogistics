/**
 * @author Fer De La Cruz
 */

function cambiaActividad(id){
	act= $('#actividad_'+id).val();
	if(act==1){
		$('#actinput_'+id).html('<input id="alias_hr" class="alias_hrs" style="width: 100%;" type="text" value="" />');
		//$('#thact').html('<strong>Tiempo horas</strong>');
		$('.alias_hrs').inputmask("hh:mm");
	}
	if(act==2){
		$('#actinput_'+id).html('<input id="alias_piezas" class="alias_piezas" style="width: 100%;" type="text" value="" />');
		$('.alias_piezas').numeric();
	}
	
}

function checamultiplo(){
	factor=$('#factor').val();
	cant=$('#cant_minima').val();
	if(factor==0 || factor==''){
		return false;
	}
	
	if (cant % factor == 0){

	}else{
		alert('La cantidad minima solo pueden ser multiples del factor minimo');
		$('#cant_minima').val(factor);
	}
}

function checamultiplof(){
factor=$('#factor').val();
		$('#cant_minima').val(factor);
	
}
//AM
function getDistinct(arrayInput){
    return arrayInput.filter( (value, index, self)=> self.indexOf(value)===index);
}


var $total=0;
var $total_preparados=0;
var $costo=0;

var recetas = {
    pasosCargados : [],

	selCiclo : function() {
		
		ciclo = $('#sel_ciclo').val();
		if(ciclo==0){
			$('#accordion_acciones').css('display','block');
			$('#accordion_acciones2').css('display','block');
			$('#ppasos').css('display','block');
			$('#phead').css('display','block');
			$('#pboton').css('display','block');

		}else{
			$('#accordion_acciones').css('display','none');
			$('#accordion_acciones2').css('display','none');
			$('#ppasos').css('display','none');
			$('#phead').css('display','none');
			$('#pboton').css('display','none');

		}
	},
///////////////// ******** ---- 			vista_nueva				------ ************ //////////////////
//////// Consulta los productos, las recetas y las agrega a un div
	// Como parametros recibe:
		// div -> div donde se cargara el contenido html
		// btn -> boton del loader
		// panel -> Clase que se le aplicara al panel de la receta

	vista_nueva : function($objeto) {
		console.log('----> Objeto nueva');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=vista_nueva',
			type : 'POST',
			dataType : 'html'
		}).done(function(resp) {
			console.log('----> Done nueva');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);
			$('#factor').numeric();
			$('#cant_minima').numeric();


			$('.selectpicker').selectpicker('refresh');
		}).fail(function(resp) {
			console.log('----> Fail vista nueva');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN vista_nueva				------ ************ //////////////////

///////////////// ******** ---- 		convertir_dataTable			------ ************ //////////////////
//////// Conviertela tabla en dataTable
	// Como parametros recibe:
		// id -> ID de la tabla a convertir

	convertir_dataTable : function($objeto) {
		console.log('objeto convertir dataTable');
		console.log($objeto);

	// Validacion para evitar error al crear el dataTable
		if (!$.fn.dataTable.isDataTable('#' + $objeto['id'])) {
			var table = $('#' + $objeto['id']).DataTable({
				"language": {
  				"url": "../../libraries/Spanish.json",
  				"search" : "<i class=\"fa fa-search\"></i>"
    			},
				// language : {
				    
				    // autoWidth: false,
					
					// lengthMenu: [[15, 25, 50, -1], [10, 25, 50, "All"]],
					// scrollY:        "200px",
     //                scrollCollapse: true,
     //                 fixedHeader: true,
					// lengthMenu : "_MENU_ por pagina",
					// zeroRecords : "No hay datos.",
					// infoEmpty : "No hay datos que mostrar.",
					// info : " ",
					// infoFiltered : " -> <strong> _TOTAL_ </strong> resultados encontrados",
					// paginate : {
					// 	first : "Primero",
					// 	previous : "<<",
					// 	next : ">>",
					// 	last : "Último"
					// }
				// },
				order: [[0, 'asc']]
			});

			//table.rowReordering();   
		}
	},

///////////////// ******** ---- 	FIN convertir_dataTable		------ ************ //////////////////

///////////////// ******** ---- 		agregar_insumo			------ ************ //////////////////
//////// Agrega un insumo al array de los insumos agregados
	// Como parametros recibe:
		// id -> ID del insumo
		// div -> ID de la div donde se cargara la vista
		// id_unidad -> ID de la unidad
		// unidad_compra -> ID de la unidad de compra
		// nombre -> nombre del insumo
		// unidad -> nombre de la unidad
		// check -> valor del check(true o false)

	agregar_insumo : function($objeto) {
		console.log('objeto agregar insumo');
		console.log($objeto);

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=agregar_insumo',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {
			console.log('----> Done agregar insumo '+$objeto['id']);
			console.log(resp);

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);

			$('.selectpicker').selectpicker('refresh');

			console.log('----> check');
			console.log($objeto['check']);


			var tabla = $('#tabla_insumos').dataTable();
    		var tabla = tabla.fnGetNodes();

			$(tabla).each(function (index){
				id = $(this,tabla).attr('id');

				if(id == 'tr_'+$objeto['id']){
					checkbox = $(this,tabla).find('input');

					if($objeto['check'] === false){
						checkbox.prop("checked", true);
						$(this,tabla).addClass('success');
					}else{
						checkbox.prop("checked", false);
						$(this,tabla).addClass('success');
						$(this,tabla).removeClass('success');
					}
				}
    		});
		}).fail(function(resp) {
			console.log('----> Fail agregar insumo');
			console.log(resp);

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN agregar_insumo			------ ************ //////////////////

///////////////// ******** ---- 		agregar_proceso			------ ************ //////////////////
//////// Agrega un insumo al array de los insumos agregados
	// Como parametros recibe:
		// id -> ID del insumo
		// div -> ID de la div donde se cargara la vista
		// id_unidad -> ID de la unidad
		// unidad_compra -> ID de la unidad de compra
		// nombre -> nombre del insumo
		// unidad -> nombre de la unidad
		// check -> valor del check(true o false)

	agregar_proceso : function($objeto) {
		console.log('objeto agregar proceso');
		console.log($objeto);

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=agregar_proceso',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {
			console.log('----> Done agregar proceso '+$objeto['id']);
			console.log(resp);

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);

			$('.selectpicker').selectpicker('refresh');

			console.log('----> check');
			console.log($objeto['check']);


			var tabla = $('#tabla_acciones').dataTable();
    		var tabla = tabla.fnGetNodes();

			$(tabla).each(function (index){
				id = $(this,tabla).attr('id');

				if(id == 'tr_'+$objeto['id']){
					checkbox = $(this,tabla).find('input');

					if($objeto['check'] === false){
						checkbox.prop("checked", true);
						$(this,tabla).addClass('success');
					}else{
						checkbox.prop("checked", false);
						$(this,tabla).addClass('success');
						$(this,tabla).removeClass('success');
					}
				}
    		});
		}).fail(function(resp) {
			console.log('----> Fail agregar insumo');
			console.log(resp);

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN agregar_insumo			------ ************ //////////////////

///////////////// ******** ---- 		agregar_accion			------ ************ //////////////////
//////// Agrega un insumo al array de los insumos agregados
	// Como parametros recibe:
		// id -> ID del insumo
		// div -> ID de la div donde se cargara la vista
		// id_unidad -> ID de la unidad
		// unidad_compra -> ID de la unidad de compra
		// nombre -> nombre del insumo
		// unidad -> nombre de la unidad
		// check -> valor del check(true o false)


	addmultiple : function($objeto) {
			alias = $('#tabla_insumos_agregados tbody tr').map(function() {

		
        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }

        	rid = $(this).attr('id');
        	rstatus = $(this).find('#sta').val();

			if ( $(this).find('#sta').is(':checked') ) {
				rstatus=1;
			}else{
				rstatus='';
			}

        	rtipo = $(this).find('#tipo').val();
        	ralias = $(this).find('#alias').val();
        	ract = $(this).find('#actividad_'+rid).val();

        	if(ract==1){
        		rhr = $(this).find('#alias_hr').val();
        	}

        	if(ract==2){
        		rhr = $(this).find('#alias_piezas').val();
        	}

        	if(rid==16){
        		eti = $(this).find('#eti select').val();
        	}else{
        		eti='';
        	}

        	ola=rid.split('_');
        	if(ola[0]=='11'){
				agru = $(this).find('#agru select').val();
        	}else{
        		agru = '';
        	}
        

        	return rid+'#.,#'+rstatus+'#.,#'+rtipo+'#.,#'+ralias+'#.,#'+ract+'#.,#'+rhr+'#.,#'+eti+'#.,#'+agru; //id_ps,id_esti,imp_sem,proviene 
      	}).get().join('#.#.#');


		idp=$('#sel_productos').val();
		$.ajax({
			data : {id:$objeto, alias:alias,idp:idp},
			url : 'ajax.php?c=recetas&f=agregar_accion_m',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {

			$('#div_insumos_agregados').html(resp);

			$("#tabla_insumos_agregados tbody").sortable({
			    //items: "> tr:not(:first)",
			    appendTo: "parent",
			    helper: "clone"
			}).disableSelection();

		});

	},

	agregar_accion : function($objeto) {
	
		console.log('objeto agregar paso');
		console.log($objeto);








		alias = $('#tabla_insumos_agregados tbody tr').map(function() {
        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }

        	rid = $(this).attr('id');
        	rstatus = $(this).find('#sta').val();

			if ( $(this).find('#sta').is(':checked') ) {
				rstatus=1;
			}else{
				rstatus='';
			}

        	rtipo = $(this).find('#tipo').val();
        	ralias = $(this).find('#alias').val();
        	ract = $(this).find('#actividad_'+rid).val();

        	if(ract==1){
        		rhr = $(this).find('#alias_hr').val();
        	}

        	if(ract==2){
        		rhr = $(this).find('#alias_piezas').val();
        	}

        	if(rid==16){
        		eti = $(this).find('#eti select').val();
        	}else{
        		eti='';
        	}

        	ola=rid.split('_');
        	if(ola[0]=='11'){
				agru = $(this).find('#agru select').val();
        	}else{
        		agru = '';
        	}

        	return rid+'#.,#'+rstatus+'#.,#'+rtipo+'#.,#'+ralias+'#.,#'+ract+'#.,#'+rhr+'#.,#'+eti+'#.,#'+agru; //id_ps,id_esti,imp_sem,proviene 
      	}).get().join('#.#.#');

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		idp=$('#sel_productos').val();
		$.ajax({
			data : {objeto:$objeto,alias:alias,idp:idp},
			url : 'ajax.php?c=recetas&f=agregar_accion',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {
			console.log('----> Done agregar proceso '+$objeto['id']);
			console.log(resp);

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);



			// if($objeto['id']==16){

			// 	$('#'+$objeto['id']+' td:last').after('<td><select class="form-control"><option value="1">Tipo de etiqueta</option></select></td>')
			// }

			$('.alias_hrs').inputmask("hh:mm");

			$('.selectpicker').selectpicker('refresh');

			console.log('----> check');
			console.log($objeto['check']);


			var tabla = $('#tabla_acciones').dataTable();
    		var tabla = tabla.fnGetNodes();

			$(tabla).each(function (index){
				id = $(this,tabla).attr('id');


				if(id == 'tr_'+$objeto['id']){
					checkbox = $(this,tabla).find('input');
					cbxbtn = $(this,tabla).find('button span');

					if($objeto['check'] === false){
						checkbox.prop("checked", true);
						$(this,tabla).addClass('success');
						cbxbtn.addClass('glyphicon-remove');
						console.log("check"+$objeto['id']);
						

						// <!---- Agregado para agregar la dependicia de cada accion  AM---->
						if($objeto['id']==9 && $("#tr_17").hasClass("success")==false){
							alert(" Se agregará la dependencia 17. ");
							onclick=(recetas.agregar_accion({id:17,nombre:'Registro de producto a inventario', tiempo_hrs: 1, div:'div_insumos_agregados',check:$('#check_a_17').prop('checked')}));
						
						}if ($objeto['id']==11 && $("#tr_4").hasClass("success")==false) {
							alert(" Se agregará la dependencia 4. ");
							onclick=(recetas.agregar_accion({id:4,nombre:'Registro de personal', tiempo_hrs: 4, div:'div_insumos_agregados',check:$('#check_a_4').prop('checked')}));
						
						}if ($objeto['id']==18 && $("#tr_4").hasClass("success")==false) {
							alert(" Se agregará la dependencia 4. ");
							onclick=(recetas.agregar_accion({id:4,nombre:'Registro de personal', tiempo_hrs: 4, div:'div_insumos_agregados',check:$('#check_a_4').prop('checked')}));
						
						}

						
						if($objeto['id']==11){
							text = 'Envio de material a proceso';
							$(this,tabla).find('td:eq(1)').html(text+' <button onclick="recetas.addmultiple('+$objeto["id"]+');" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> Agregar actividad adicional</button>');
						}
						if($objeto['id']==19){
							text = 'Registro de actividad';
							$(this,tabla).find('td:eq(1)').html(text+' <button onclick="recetas.addmultiple('+$objeto["id"]+');" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> Agregar actividad adicional</button>');
						}


						
					}else{
						if($objeto['id']==11){
							$(this,tabla).find('td:eq(1)').html('Envio de material a proceso');
						}
						if($objeto['id']==19){
							$(this,tabla).find('td:eq(1)').html('Registro de actividad');
						}
						checkbox.prop("checked", false);
						cbxbtn.removeClass('glyphicon-remove');
						$(this,tabla).addClass('success');
						$(this,tabla).removeClass('success');
					}
				}
    		});


   //  		$("#tabla_insumos_agregados tbody").sortable({
   //  			placeholder: 'sombrita',
			//     //items: "> tr:not(:first)",
			//     appendTo: "parent",
			//     helper: "clone"
			// }).disableSelection();

			$("#tabla_insumos_agregados tbody").sortable({
			    //items: "> tr:not(:first)",
			    appendTo: "parent",
			    helper: "clone",
			    update:function(event, ui){
			    	pasoslist = $('#tabla_insumos_agregados tbody tr').map(function() {
						id_paso=$(this).attr('id');
				        return id_paso; //id_ps,id_esti,imp_sem,proviene 
				    }).get().join('#..#');

				    $.ajax({
						url: 'ajax.php?c=recetas&f=morden_insumos',
						data:{pasoslist:pasoslist},
						type: 'POST'
						//dataType : 'json',
					}).done(function(resp){
						
					}).fail(function(resp) {

					});
			  	}
			}).disableSelection();



		}).fail(function(resp) {
			console.log('----> Fail agregar insumo');
			console.log(resp);

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},


ver_pasos : function(paso,num) {
	
		paso_ori=paso;
		console.log('objeto agregar paso');

 		var accionesDelPaso = recetas.pasosCargados.filter(el=>el.nombrePaso== paso);
 	    console.log( JSON.stringify(accionesDelPaso) );
	// Loader
		//$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');
		idp=$('#sel_productos').val();
		$.ajax({
			data : {paso:paso,num:num,idp:idp, accionesDelPaso: JSON.stringify(accionesDelPaso) } ,
			url : 'ajax.php?c=recetas&f=ver_pasos',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {

			ir = paso.replace(/_/g, " ");
			ir = ir.replace(/"/g, " ");
			$('#titpaso').html(ir);
			$('#bodymodal').html(resp);
			$('#modalpaso').modal('show');

		// 	console.log('----> Done agregar proceso '+$objeto['id']);
		 	console.log(resp);




  			$("#tabla_insumos_agregados2 tbody").sortable({
			    //items: "> tr:not(:first)",
			    appendTo: "parent",
			    helper: "clone",
			    start: function(event, ui) {
			        var start_pos = ui.item.index();
			        ui.item.data('start_pos', start_pos);
			    },
			    update:function(event, ui){
			    	validarorden2();
			    	var start_pos = ui.item.data('start_pos');
        			var end_pos = ui.item.index(); 
        			console.log("start: " + start_pos , ", end: " + end_pos);

if ($('#validarAcomodoGuardar').val()==1) {
	var temp  = recetas.pasosCargados[end_pos];
        			recetas.pasosCargados[end_pos] = recetas.pasosCargados[start_pos];
        			recetas.pasosCargados[start_pos] = temp;
        			console.log(JSON.stringify(recetas.pasosCargados ) )	;
			    	console.log( ui.item.attr('id') );
 
			    	pasosAcciones = $('#tabla_insumos_agregados2 tbody tr').map(function() {
						id_accion=$(this).attr('id');
						id_accion = id_accion.replace('acc_','');
				        return id_accion; //id_ps,id_esti,imp_sem,proviene 
				    }).get().join('#..#');
					
					// validarorden2();
				    $.ajax({
						url: 'ajax.php?c=recetas&f=morden_accion',
						data:{pasosAcciones: JSON.stringify(recetas.pasosCargados) },
						type: 'POST'
						//dataType : 'json',
					}).done(function(resp){
						
					}).fail(function(resp) {

					});
				}
        			
					
			  	}
			}).disableSelection();


		}).fail(function(resp) {
			console.log('----> Fail agregar insumo');
			console.log(resp);

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
		//validarorden2();
	},

///////////////// ******** ---- 		FIN agregar_accion			------ ************ //////////////////

removerPaso: function(paso){
	recetas.pasosCargados  = recetas.pasosCargados.filter(el=>el.nombrePaso != paso);
	recetas.deshabilitaAcciones();

	$.ajax({
		url: 'ajax.php?c=recetas&f=remover_paso',
		data:{ pasos: JSON.stringify(recetas.pasosCargados )},
		type: 'POST',
		dataType : 'html',
		async: false
	}).done(function(resp){
		console.log('----> Done remover paso ' + resp);

		table = $('#tabla_pasos').DataTable();
		table.row('#tr_paso_'+paso).remove().draw();

	}).fail(function(resp) {
		console.log('----> Fail remover paso ');
		console.log(resp);
		$mensaje = 'Error, no se pueden remover los pasos';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});

},

removerAccion: function(paso,accion){
	//dejamos solo las acciones diferentes a la accion que queremos eliminar AM
    recetas.pasosCargados  = recetas.pasosCargados.filter(el=> el.idAccion !=accion ); 
    //checamos si despues de filtrar las acciones, existen otras acciones  dentro del paso AM
    var existenAunAcciones = recetas.pasosCargados.some(el=>el.nombrePaso== paso);

	recetas.deshabilitaAcciones();

	$.ajax({
		url: 'ajax.php?c=recetas&f=remover_paso',
		data:{pasos: JSON.stringify(recetas.pasosCargados)},
		type: 'POST',
		dataType : 'html',
		async: false
	}).done(function(resp){

		$('#acc_'+accion).remove(); 
        if(! existenAunAcciones){ 				//si no existen acciones dentro del paso, cerramos el modal AM
			$('#modalpaso').modal('hide');
			recetas.removerPaso(paso);
		} 

	}).fail(function(resp) {
		console.log('----> Fail remover paso ');
		console.log(resp);
		$mensaje = 'Error, no se pueden remover los pasos';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});

},


///////////////// ******** ---- 		agregar_paso			     ------ ************ //////////////////

agregar_paso : function($objeto){
	
	// valida que no se repita el paso AM
   var array=recetas.pasosCargados.map(el=>el.nombrePaso); 
   var valordepaso = $('#input_paso_produccion').val();

   if (jQuery.inArray(valordepaso, array) !== -1) {
		alert('Elige otro nombre para este paso');
	}else{

    validarorden(); //valido el orden antes de agregar el paso AM
    // verifico si tiene alguna de las acciones que dependen de otra. AM
	var arraydepende = new Array();
	$('.leerdato').each(function(i, selected) { 
		arraydepende.push($(this).html()); 
	});
	 

	 if(jQuery.inArray("11", arraydepende) !== -1 && jQuery.inArray("4", arraydepende) == -1 ){ 
	 	alert(" Se agregará la dependencia 4. ");
	 	onclick=(recetas.agregar_accion({id:4,nombre:'Registro de personal', tiempo_hrs: 4, div:'div_insumos_agregados',check:$('#check_a_4').prop('checked')}));

	 }else if (jQuery.inArray("9", arraydepende) !== -1 && jQuery.inArray("17", arraydepende) == -1 ){
	 	alert(" Se agregará la dependencia 17. ");
	 	onclick=(recetas.agregar_accion({id:17,nombre:'Registro de producto a inventario', tiempo_hrs: 1, div:'div_insumos_agregados',check:$('#check_a_17').prop('checked')}));

	 }else if (jQuery.inArray("18", arraydepende) !== -1 && jQuery.inArray("4", arraydepende) == -1 ){
	 	alert(" Se agregará la dependencia 4. ");
	 	onclick=(recetas.agregar_accion({id:4,nombre:'Registro de personal', tiempo_hrs: 4, div:'div_insumos_agregados',check:$('#check_a_4').prop('checked')}));
	 }


	 //agregar disabled a las acciones que ya existan en algunos pasos.  
	 var disabledaccexist =recetas.pasosCargados.map(el=>el.idAccion); 
			console.log("Areglo de aaciones ya rehistradas"+disabledaccexist);


//el prueba toma el valor de  1 si los datos estan mal acomodados y 0 si estab bien acomodados antes de guardar el paso AM
	if ($('#prueba').val()==1) { }else{ 
	console.log('agregar paso producción ');
	console.log($objeto);

	paso=$('#input_paso_produccion').val();
	if(paso==''){
		alert('Tienes que escribir el nombre del paso');
		return false;
	}

	erroreti=0;
	erroragru=0;
	alias = $('#tabla_insumos_agregados tbody tr').map(function() {
		id_accion=$(this).attr('id');
		txt_alias=$(this).find('#alias').val();
		txt_tipo=$(this).find('#tdtipo select').val();
		txt_sta=$(this).find("#sta").is(':checked') ? 1 : 0;
		txt_actividad=$(this).find('#tdactividad select').val();
		if(txt_actividad==1){
			txt_alias_hr=$(this).find('#alias_hr').val();
		}
		if(txt_actividad==2){
			txt_alias_hr=$(this).find('#alias_piezas').val();
		}

		if(id_accion==16){
    		eti = $(this).find('#eti select').val();
    	}else{
    		eti='';
    	}

    	if(eti=='0'){
    		erroreti++;
    	}

    	ola=id_accion.split('_');
    	if(ola[0]=='11'){
			agru = $(this).find('#agru select').val();
    	}else{
    		agru = '';
    	}

    	if(agru=='0'){
    		erroragru++;
    	}

        	

		//alert(id_accion+' - '+txt_alias+' - '+txt_alias_hr);
        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }
        return id_accion+'_#_'+txt_alias+'_#_'+txt_alias_hr+'_#_'+txt_actividad+'_#_'+txt_tipo+'_#_'+txt_sta+'_#_'+eti+'_#_'+agru; //id_ps,id_esti,imp_sem,proviene 
    }).get().join('_##_');


    if(erroreti>0){
		alert('Tienes que seleccionar una etiqueta');
		return false;
	}

	if(erroragru>0){
		alert('Tienes que seleccionar una agrupacion');
		return false;
	}


	$.ajax({
		data: $objeto,
		url: 'ajax.php?c=recetas&f=agregar_paso',
		data:{paso:paso,alias:alias},
		type: 'POST',
		dataType : 'json',
		async: false
	}).done(function(resp){
		console.log('arreglo pasos');
		console.log(resp);
		if(resp.success==0){
			if(resp.error=='NOACCIONES'){
				alert('Tienes que agregar acciones');
				return false;
			}
		}else{
			table = $('#tabla_pasos').DataTable();

			table.clear().draw();
			$("#tabla_pasos tbody").sortable({
			    //items: "> tr:not(:first)",
			    appendTo: "parent",
			    helper: "clone",
			    update:function(event, ui){
			    	console.log( ui.item.attr('id') );

			    	pasoslist = $('#tabla_pasos tbody tr').map(function() {
						id_paso=$(this).attr('id');
						// ids=id_paso.split('_')
						// id_paso=ids[2];
						id_paso = id_paso.replace('tr_paso_','');
						//alert(id_accion+' - '+txt_alias+' - '+txt_alias_hr);
				        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }
				        return id_paso; //id_ps,id_esti,imp_sem,proviene 
				    }).get().join('#..#');

				    $.ajax({
						url: 'ajax.php?c=recetas&f=morden_paso',
						data:{pasoslist:pasoslist},
						type: 'POST'
						//dataType : 'json',
					}).done(function(resp){
						
					}).fail(function(resp) {

					});
			  	}
			}).disableSelection();

			
			bodycad='';
			c=1;

			var pasosExistentes = 
				getDistinct( resp.data.map(dat => dat.nombrePaso) ); //obtener un array de diferentes pasos

 			recetas.pasosCargados = resp.data;
 			recetas.deshabilitaAcciones();

			$.each(pasosExistentes, function( idx, val ) { //val: nombrePaso
				valSinUnderscore = val.replace(/_/g, " ").replace(/"/g, ""); //reemplaza el '_' por ' '  en el nombre del paso

	            bodycad='<tr id="tr_paso_'+val+'"  >\
	            <td class="idpaso">'+c+'</td>\
	            <td class="nombrepaso" style="cursor:pointer;" onclick="recetas.ver_pasos(\''+val+'\','+c+')" >'+valSinUnderscore+'</td>\
	            <td><button onclick="recetas.removerPaso(\''+val+'\');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span> Remover</button></td>\
	            </tr>';
	            c++;
	            table.row.add($(bodycad)).draw();
	        });

	        $('#div_insumos_agregados').html('<br /><br />\
					<blockquote style="font-size: 16px">\
				    	<p>\
				      		Seleccione un <strong>"producto"</strong>\
							y asígnele <strong>"procesos de producción"</strong>.\
				    	</p>\
				    </blockquote>');

	        $('#input_paso_produccion').val('');

	        var tabla = $('#tabla_acciones').dataTable();
    		var tabla = tabla.fnGetNodes();


			$('#tr_11').find('td:eq(1)').html('Envio de material a proceso');

 


			$(tabla).each(function (index){
				cbxbtn = $(this,tabla).find('button span');
				checkbox = $(this,tabla).find('input');
				if($objeto['check'] === false){
					checkbox.prop("checked", true);
					$(this,tabla).addClass('success');
					cbxbtn.addClass('glyphicon-remove');
				}else{
					checkbox.prop("checked", false);
					$(this,tabla).addClass('success');
					$(this,tabla).removeClass('success');
					cbxbtn.removeClass('glyphicon-remove');
				}
    		});
	        // $.each(resp.data, function( i, v ) {
	        //     bodycad+='<tr>\
	        //     <td>'+v.idAccion+'</td>\
	        //     <td>'+v.nombreAccion+'</td>\
	        //     <input style="cursor: pointer" disabled="1" type="checkbox" id="check_a_'+v.idAccion+'" />\
	        //     </tr>';
	        // });
	        

	        
		}
		console.log('----> Done agregar paso ' + $objeto['id']);
		console.log(resp);
	}).fail(function(resp) {
		console.log('----> Fail agregar paso ');
		console.log(resp);
		$mensaje = 'Error, no se pueden cargar los datos';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});


	}
	}


	
	
},
 

cambiap : function(idp){
	if(idp==0 && $('#PasoEditar').val()==''){
		$('#pacciones').hide();
		alert('Seleccione un producto');
		return false
	}

	if(idp>0 && $('#PasoEditar').val()==''){
		$.ajax({
			url: 'ajax.php?c=recetas&f=quitatodo',
			type: 'POST',
			async: false,
			success: function(r){
				$('#div_insumos_agregados').html('<br /><br />\
					<blockquote style="font-size: 16px">\
				    	<p>\
				      		Seleccione un <strong>"producto"</strong>\
							y asígnele <strong>"procesos de producción"</strong>.\
				    	</p>\
				    </blockquote>');

		        $('#input_paso_produccion').val('');

		        var tabla = $('#tabla_acciones').dataTable();
	    		var tabla = tabla.fnGetNodes();


	    		tablep = $('#tabla_pasos').DataTable();
				tablep.clear().draw();


				$('#tr_11').find('td:eq(1)').html('Envio de material a proceso');
				$(tabla).each(function (index){
					cbxbtn = $(this,tabla).find('button span');
					checkbox = $(this,tabla).find('input');
					if(checkbox === false){
						checkbox.prop("checked", true);
						$(this,tabla).addClass('success');
						cbxbtn.addClass('glyphicon-remove');
					}else{
						checkbox.prop("checked", false);
						$(this,tabla).addClass('success');
						$(this,tabla).removeClass('success');
						cbxbtn.removeClass('glyphicon-remove');
					}
	    		});

            }
		});


		$('#pacciones').show();
		// Reordenar las columnas  AM
		var table = $('#tabla_acciones').DataTable({
			
				destroy:true,
				lengthMenu: [[15, 25, 50, -1], [10, 25, 50, "All"]],
				scrollY:    "250px",
            	scrollCollapse: true,
            	paging: false,
                info: false,
                "language": { search: "Buscar:"}
        });
		 table.columns.adjust().draw();
	}

},

///////////////// ******** ---- 		FIN agregar_paso			------ ************ //////////////////

///////////////// ******** ---- 		agregar_parametro			------ ************ //////////////////

agregar_parametro : function($objeto) {
	console.log('objeto agregar parametro');
	console.log($objeto);

// Loader
	$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=agregar_parametro',
		type : 'POST',
		dataType : 'html',
		async:false
	}).done(function(resp) {
		console.log('----> Done agregar parámetro '+$objeto['id']);
		console.log(resp);

	// Carga la vista a la div
		$('#' + $objeto['div']).html(resp);

		$('.selectpicker').selectpicker('refresh');

		console.log('----> check');
		console.log($objeto['check']);


		var tabla = $('#tabla_insumos').dataTable();
			var tabla = tabla.fnGetNodes();

		$(tabla).each(function (index){
			id = $(this,tabla).attr('id');
			if(id == 'tr_'+$objeto['id']){
				checkbox = $(this,tabla).find('input');
				if($objeto['check'] === false){
					checkbox.prop("checked", true);
					$(this,tabla).addClass('success');
				}else{
					checkbox.prop("checked", false);
					$(this,tabla).addClass('success');
					$(this,tabla).removeClass('success');
				}
			}
			});
	}).fail(function(resp) {
		console.log('----> Fail agregar insumo');
		console.log(resp);

		$mensaje = 'Error, no se puede cargar los datos';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});
},

///////////////// ******** ---- 		FIN agregar_parametro			------ ************ //////////////////

///////////////// ******** ---- 		agregar_insumo_producto			------ ************ //////////////////

agregar_insumos_producto : function($objeto) {
	console.log('objeto agregar insumos por producto');
	console.log($objeto);

// Loader
	$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=agregar_insumos_producto',
		type : 'POST',
		dataType : 'html',
		async:false
	}).done(function(resp) {
		console.log('----> Done agregar insumo '+$objeto['id']);
		console.log(resp);

	// Carga la vista a la div
		$('#' + $objeto['div']).html(resp);

		$('.selectpicker').selectpicker('refresh');

		console.log('----> check');
		console.log($objeto['check']);


		var tabla = $('#tabla_insumos').dataTable();
			var tabla = tabla.fnGetNodes();

		$(tabla).each(function (index){
			id = $(this,tabla).attr('id');
			if(id == 'tr_'+$objeto['id']){
				checkbox = $(this,tabla).find('input');
				if($objeto['check'] === false){
					checkbox.prop("checked", true);
					$(this,tabla).addClass('success');
				}else{
					checkbox.prop("checked", false);
					$(this,tabla).addClass('success');
					$(this,tabla).removeClass('success');
				}
			}
			});
	}).fail(function(resp) {
		console.log('----> Fail agregar insumos por producto');
		console.log(resp);

		$mensaje = 'Error. No se pueden cargar los insumos por producto.';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});
},

///////////////// ******** ---- 		FIN agregar_insumo_producto			------ ************ //////////////////


///////////////// ******** ---- 		cargar_formulario_lab			------ ************ //////////////////

cargar_formulario_lab : function($objeto) {
	console.log('objeto cargar formulario');
	console.log($objeto);

	// alert("Change: " + $objeto['producto']);

	$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=cargar_formulario_lab',
		type : 'POST',
		dataType : 'html',
		async:false
	}).done(function(resp) {
		console.log('----> Done cargar formulario '+$objeto['producto']);
		console.log('SQL: '+resp);

	// Carga la vista a la div
		$('#' + $objeto['div']).html(resp);

		$('.selectpicker').selectpicker('refresh');

		console.log('----> check');
		console.log($objeto['check']);


		var tabla = $('#tabla_insumos').dataTable();
			var tabla = tabla.fnGetNodes();

		$(tabla).each(function (index){
			id = $(this,tabla).attr('id');

			if(id == 'tr_'+$objeto['id']){
				checkbox = $(this,tabla).find('input');

				if($objeto['check'] === false){
					checkbox.prop("checked", true);
					$(this,tabla).addClass('success');
				}else{
					checkbox.prop("checked", false);
					$(this,tabla).addClass('success');
					$(this,tabla).removeClass('success');
				}
			}
			});
	}).fail(function(resp) {
		console.log('----> Fail agregar insumo');
		console.log(resp);

		$mensaje = 'Error, no se puede cargar los datos';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});

},

///////////////// ******** ---- 		FIN cargar_formulario_lab			------ ************ //////////////////


///////////////// ******** ---- 		agregar_insumo_preparado	------ ************ //////////////////
//////// Agrega un insumo preparado al array de los insumos agregados
	// Como parametros recibe:
		// id -> ID del insumo
		// div -> ID de la div donde se cargara la vista
		// id_unidad -> ID de la unidad
		// unidad_compra -> ID de la unidad de compra
		// nombre -> nombre del insumo
		// unidad -> nombre de la unidad
		// check -> valor del check(true o false)
		// preparado -> 1 si es insumo preparado

	agregar_insumo_preparado : function($objeto) {
		console.log('objeto agregar insumo preparado');
		console.log($objeto);

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=agregar_insumo',
			type : 'POST',
			dataType : 'html',
			async:false
		}).done(function(resp) {
			console.log('----> Done agregar insumo preparado '+$objeto['id']);
			console.log(resp);

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);

			$('.selectpicker').selectpicker('refresh');

			console.log('----> check');
			console.log($objeto['check']);

			if($objeto['check']===false){
				$('#check_preparado_'+$objeto['id']).prop("checked", true);
				$('#tr_preparado_'+$objeto['id']).addClass('success');
			}else{
				$('#check_preparado_'+$objeto['id']).prop("checked", false);
				$('#tr_preparado_'+$objeto['id']).removeClass('success');
			}
		}).fail(function(resp) {
			console.log('----> Fail agregar insumo');
			console.log(resp);

			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN agregar_insumo			------ ************ //////////////////

///////////////// ******** ---- 		asignar_parametros				------ ************ //////////////////

asignar_referencias : function($objeto) {
	console.log('objeto asignar_referencias');
	console.log($objeto);

// Loader

	/*if($objeto['preparado'] == 1){
		$('#loader_preparado_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
	}else{
		$('#loader_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
	}*/

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=asignar_referencias',
		type : 'POST',
		dataType : 'json'
	}).done(function(resp) {
		console.log('----> Done asignar_referencias');
		console.log(resp);

	// Quita el loader
		/*if($objeto['preparado'] == 1){
			$('#loader_preparado_'+$objeto['id']).html('<i class="fa fa-slack"></i>');
		}else{
			$('#loader_'+$objeto['id']).html('<i class="fa fa-slack"></i>');
		}*/


	// Actualiza el precio de venta

	}).fail(function(resp) {
		console.log('----> Fail calcular precio');
		console.log(resp);

		$mensaje = 'Error, no se pueden hacer cambios';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});
},

///////////////// ******** ---- 		FIN asignar_parametros			------ ************ //////////////////

///////////////// ******** ---- 		asignar_cant_req				------ ************ //////////////////

asignar_cant_req : function($objeto) {
	console.log('objeto asignar_referencias');
	console.log($objeto);

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=asignar_cant_req',
		type : 'POST',
		dataType : 'json'
	}).done(function(resp) {
		console.log('----> Done asignar_cant_req');
		console.log(resp);
}).fail(function(resp) {
		console.log('----> Fail calcular precio');
		console.log(resp);

		$mensaje = 'Error, no se pueden hacer cambios';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});
},

///////////////// ******** ---- 		FIN asignar_cant_req			------ ************ //////////////////

///////////////// ******** ---- 		asignar_valor_lab				------ ************ //////////////////

asignar_valor_lab : function($objeto) {
	console.log('objeto asignar_valor_lab');
	console.log($objeto);

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=asignar_valor_lab',
		type : 'POST',
		dataType : 'json'
	}).done(function(resp) {
		console.log('----> Done asignar_valor_lab');
		console.log(resp);
}).fail(function(resp) {
		console.log('----> Fail asignar_valor_lab');
		console.log(resp);
		$mensaje = 'Error, no se pueden hacer cambios';
		$.notify($mensaje, {
			position : "top center",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});
},

///////////////// ******** ---- 		FIN asignar_valor_lab			------ ************ //////////////////

///////////////// ******** ---- 		calcular_precio				------ ************ //////////////////
//////// Calcula el sub total del insumo, el total de la receta y carga los valores
	// Como parametros recibe:
		// id -> ID del insumo
		// cantidad -> cantidad del insumo
		// preparado -> 1 si es insumo preparado

	calcular_precio : function($objeto) {
		console.log('objeto calcular_precio');
		console.log($objeto);

	// Loader
		if($objeto['preparado'] == 1){
			$('#loader_preparado_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
		}else{
			$('#loader_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
		}

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=calcular_precio',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done calcular precio');
			console.log(resp);

		// Quita el loader
			if($objeto['preparado'] == 1){
				$('#loader_preparado_'+$objeto['id']).html('<i class="fa fa-slack"></i>');
			}else{
				$('#loader_'+$objeto['id']).html('<i class="fa fa-slack"></i>');
			}

			$total=0;
			$total_preparados=0;
			$costo=0;

			if(resp["insumos"]){
			// Agrega los subtotales a los insumos
				$.each(resp["insumos"], function(index, val) {
				// Calculamos el sub-total de cada insumo, el total y los cargamos
					if(val['sub_total']){
						$total+=parseFloat(val['sub_total']);
						$costo+=parseFloat(val['sub_total']);
						$('#sub_total_'+val['id']).html('$ '+val['sub_total']);
					}else{
						$('#sub_total_'+val['id']).html('$ 0');
					}
				});

			// Actualiza el total
				$('#total').html($total);
			}

			if(resp["insumos_preparados"]){
			// Agrega los subtotales a los insumos
				$.each(resp["insumos_preparados"], function(index, val) {
				// Calculamos el sub-total de cada insumo, el total y los cargamos
					if(val['sub_total']){
						$total_preparados+=parseFloat(val['sub_total']);
						$costo+=parseFloat(val['sub_total']);
						$('#sub_total_preparado_'+val['id']).html('$ '+val['sub_total']);
					}else{
						$('#sub_total_preparado_'+val['id']).html('$ 0');
					}
				});

			// Actualiza el total
				$('#total_preparados').html($total_preparados);
			}
		// Actualiza el precio de venta
			$total = $('#total').html();
			$total = (!$total) ? 0 : parseFloat($total) ;

			$total_preparados = $('#total_preparados').html();
			$total_preparados = (!$total_preparados) ? 0 : parseFloat($total_preparados) ;

			$('#precio_venta').val($total + $total_preparados);

		// calcula la ganancia
			recetas.calcular_ganancia({porcentaje:$('#margen_ganancia').val()});
		}).fail(function(resp) {
			console.log('----> Fail calcular precio');
			console.log(resp);

			$mensaje = 'Error, no se pueden hacer cambios';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN calcular_precio			------ ************ //////////////////

///////////////// ******** ---- 		calcular_ganancia			------ ************ //////////////////
//////// Calcula la ganancia segun el precio de venta
	// Como parametros recibe:
		// porcentaje -> porcentaje que se quiere ganar

	calcular_ganancia : function($objeto) {
		console.log('objeto calcular_ganancia');
		console.log($objeto);

	// Calculamos el nuevo precio
		$total = $('#total').html();
		$total = (!$total) ? 0 : parseFloat($total) ;

		$total_preparados = $('#total_preparados').html();
		$total_preparados = (!$total_preparados) ? 0 : parseFloat($total_preparados) ;

		$precio = ($total + $total_preparados);

		var $porcentaje=(parseFloat($precio)*$objeto['porcentaje'])/100;
		var $nuevo_precio=parseFloat($porcentaje)+parseFloat($precio);

	// Cambiamos el valor del precio de venta
		$('#precio_venta').val($nuevo_precio);
	},

///////////////// ******** ---- 		FIN calcular_ganancia			------ ************ //////////////////

///////////////// ******** ---- 		guardar							------ ************ //////////////////
//////// Guarda la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	guardar : function($objeto) {
		$objeto['costo']=$costo;
		console.log('objeto guardar');
		console.log($objeto);

	// ** Validaciones


	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=guardar',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done guardar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Guardado';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga de nuevo la vista
				recetas.vista_nueva({div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}

		// El producto ya existe
			if(resp['status']==3){
				var $mensaje = 'Cambia el nombre y/o codigo';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail guardar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al guardar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN guardar			------ ************ //////////////////

///////////////// ******** ---- 		guardar_procesos_produccion							------ ************ //////////////////
//////// Guarda la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

		guardar_procesos_produccion3 : function() {

			sel_ciclo=$('#sel_ciclo').val();
			idFamilia=$('#sel_familias').val();
			if(idFamilia==''){
				alert('Seleccione una familia');
				return false;
			}
			$.ajax({
				data:{idFamilia:idFamilia,sel_ciclo:sel_ciclo},
				url : 'ajax.php?c=recetas&f=guardar_procesos_produccion3',
				type : 'POST',
				dataType : 'html'
			}).done(function(resp) {
				if(resp==1){
					recetas.vista_nueva({vista: 'prc_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});
					alert('Proceso Guardado con exito');
				}else{
					alert('Error al guerkjberkjerkbrekrebkjnbekjbrkbardr proceso');
				}

				console.log('----> Done guardar');
				alert('Proceso guardado con exito');


			}).fail(function(resp) {
				console.log('----> Fail guardar');
				console.log(resp);

			// Quita el loader
	

				$mensaje = 'Error al guardar';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'error',
					arrowSize : 15
				});
			});

		},



		guardar_procesos_produccion2 : function(modi) {
		    
			var sel_ciclo=$('#sel_ciclo').val();
			idProducto=$('#sel_productos').val();
			

			if(idProducto=='' || idProducto==0){
				alert('Seleccione un producto porfavor');
				return false;
			}

		var accionesDelPaso = recetas.pasosCargados.filter(el=>el.nombrePaso);
 	    console.log( JSON.stringify(accionesDelPaso) );
	
			

			// OBTENER LOS REGISTROS QUE SE GUARDARON EN LA TABLA DE PASOS
			var data = []; 
			$('#tabla_pasos tbody tr').map(function() { 
			data.push({
				idpaso     : $(this).find('.idpaso').text(),
				nombrepaso : $(this).find('.nombrepaso').text() 
				})
			});

			
			//Agregado para validar que tenga las acciones requeridas al guardar AM
			var arregloAccionesrequeridas=recetas.pasosCargados.map(el=>el.idAccion); 
			console.log("arregloAccionesrequeridas"+arregloAccionesrequeridas);

			if(jQuery.inArray("2", arregloAccionesrequeridas) == -1 || jQuery.inArray("9", arregloAccionesrequeridas) == -1 || jQuery.inArray("17", arregloAccionesrequeridas) == -1 && modi==0){
       
				alert("Imposible guardar, Acciones requeridas --- 2,9,17 --- ");
                
}else{
	
	$.ajax({
				data:{idProducto:idProducto,modi:modi,sel_ciclo:sel_ciclo, accionesDelPaso: accionesDelPaso,data:data},
				url : 'ajax.php?c=recetas&f=guardar_procesos_produccion2',
				type : 'POST',
				dataType : 'html'
			}).done(function(resp) {
				

				if(resp==1){
					recetas.vista_nueva({vista: 'prc_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});
					alert('Proceso Guardado con exito');
				}else if(resp=='ini'){
					alert('Este producto no se puede editar ya que tiene procesos de produccion iniciados');
				}else{
					alert('Error al guardr proceso223');
				}


			}).fail(function(resp) {
				console.log('----> Fail guardar');
				console.log(resp);

			// Quita el loader
	

				$mensaje = 'Error al guardar';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'error',
					arrowSize : 15
				});
			});
		}
            
			
			
			
		},


	guardar_procesos_produccion : function($objeto) {
		
		$objeto['costo']=$costo;
		console.log('objeto guardar proceso produccion');
		console.log($objeto);

	// ** Validaciones


	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=guardar_procesos_produccion',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done guardar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Guardado';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga de nuevo la vista
				recetas.vista_nueva({vista:'prc_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}

		// El producto ya existe
			if(resp['status']==3){
				var $mensaje = 'Cambia el nombre y/o codigo';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail guardar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al guardar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN guardar			------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_varias							------ ************ //////////////////
//////// Guarda la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	guardar_lab_varias : function($objeto) {
		console.log('Guardar lab varias');
		console.log($objeto);

	// ** Validaciones



	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=guardar_lab_varias',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done guardar varias');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Guardado';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga de nuevo la vista
				recetas.vista_nueva({vista:'lab_cpts', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}

		// El producto ya existe
			if(resp['status']==3){
				var $mensaje = 'Cambia el nombre y/o codigo';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail guardar varias');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al guardar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN guardar_lab_varias			------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_conceptos_productos			------ ************ //////////////////

	guardar_lab_conceptos_productos : function($objeto) {
		console.log('----> Objeto lab_conceptos_productos');
		console.log($objeto);

		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=guardar_lab_conceptos_productos',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done lab_conceptos_productos');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Guardado';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga de nuevo la vista
				recetas.vista_nueva({vista:'lab_cs_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}

		// El producto ya existe
			if(resp['status']==3){
				var $mensaje = 'Cambia el nombre y/o codigo';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail lab conceptos productos');

			console.log(resp.responseText);
			console.log(resp);

		// Quita el loader
			//$btn.button('reset');

			$mensaje = 'Error al guardar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});


	},

///////////////// ******** ---- 		FIN guardar_lab_conceptos_productos	------ ************ //////////////////

///////////////// ******** ---- 		guardar_lab_registro	------ ************ //////////////////

guardar_lab_registro : function($objeto) {
	console.log('----> Objeto lab_registro');
	console.log($objeto);

	var $btn = $('#' + $objeto['btn']);
	$btn.button('loading');


	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=guardar_lab_registro',
		type : 'POST',
		dataType : 'json'
	}).done(function(resp) {
		console.log('----> Done lab_conceptos_registro');
		console.log(resp);

	// Quita el loader
	$btn.button('reset');

	// Todo bien :D
		if(resp['status']==1){
			var $mensaje = 'Guardado';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'success',
			});

		// Carga de nuevo la vista
			recetas.vista_nueva({vista:'lab_rgtr', div:'div_recetas', btn:'btn_nueva', panel:'success'});

			return 0;
		}

	// Sin insumos :p
		if(resp['status']==2){
			var $mensaje = 'Agrega unos cuantos insumos';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// El producto ya existe
		if(resp['status']==3){
			var $mensaje = 'Cambia el nombre y/o codigo';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}
	}).fail(function(resp) {
		console.log('----> Fail guardar varias');
		console.log(resp);

	// Quita el loader
		//$btn.button('reset');

		$mensaje = 'Error al guardar';
		$('#notificaciones').notify($mensaje, {
			position : "top right",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});


},

///////////////// ******** ---- 		FIN guardar_lab_registro	------ ************ //////////////////

///////////////// ******** ---- 		guardar_insumos_producto	------ ************ //////////////////

guardar_insumos_producto : function($objeto) {
	console.log('----> Objeto insumos_producto');
	console.log($objeto);



	var $btn = $('#' + $objeto['btn']);
	$btn.button('loading');

	$.ajax({
		data : $objeto,
		url : 'ajax.php?c=recetas&f=guardar_insumos_producto',
		type : 'POST',
		dataType : 'json'
	}).done(function(resp) {
		console.log('----> Done guardar_insumos_producto');
		console.log(resp);

	// Quita el loader
	$btn.button('reset');

	// Todo bien :D
		if(resp['status']==1){
			var $mensaje = 'Guardado';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'success',
			});

		// Carga de nuevo la vista
			recetas.vista_nueva({vista:'frm_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

			return 0;
		}

	// Sin insumos :p
		if(resp['status']==2){
			var $mensaje = 'Agrega unos cuantos insumos';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// El producto ya existe
		if(resp['status']==3){
			var $mensaje = 'Intente con un nombre o código de producto distinto.';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}
	}).fail(function(resp) {
		console.log('----> Fail guardar insumos producto');
		console.log(resp);

	// Quita el loader
		//$btn.button('reset');

		$mensaje = 'Error al guardar';
		$('#notificaciones').notify($mensaje, {
			position : "top right",
			autoHide : true,
			autoHideDelay : 5000,
			className : 'error',
			arrowSize : 15
		});
	});


},

///////////////// ******** ---- 		FIN guardar_insumos_producto	------ ************ //////////////////



///////////////// ******** ---- 			validar_codigo			------ ************ //////////////////
//////// Valida el codigo introducido y lo cambia de ser incorrecto
	// Como parametros recibe:
		// id -> ID del campo a validar

	validar_codigo : function($objeto) {
		console.log('-----> Obejto validar_codigo');
		console.log($objeto);

	// Formatea el codigo
		var $codigo=$('#'+$objeto['id']).val();
		$codigo = $codigo.replace(/\s/g, '');

	// Actualiza el codigo
		$('#'+$objeto['id']).val($codigo);
	},

///////////////// ******** ---- 		FIN validar_codigo			------ ************ //////////////////

///////////////// ******** ---- 		guardar_opcionales			------ ************ //////////////////
//////// Guarda los opcionales del insumo
	// Como parametros recibe:
		// id -> ID del insumo
		// opcionales -> cadena con los IDS de los opcionales
		// preparado -> 1 si el insumo es preparado

	guardar_opcionales : function($objeto) {
		console.log('objeto guardar_opcionales');
		console.log($objeto);

	// Loader
		if($objeto['preparado']==1){
			$('#loader_select_preparado_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
		}else{
			$('#loader_select_'+$objeto['id']).html("<i class='fa fa-refresh fa-spin'></i>");
		}

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=guardar_opcionales',
			type : 'POST',
			dataType : 'json',
			async:false
		}).done(function(resp) {
			console.log('----> Done guardar_opcionales');
			console.log(resp);

		// Quita el loader
			if($objeto['preparado']==1){
				$('#loader_select_preparado_'+$objeto['id']).html('<i class="fa fa-list-ul"></i>');
			}else{
				$('#loader_select_'+$objeto['id']).html('<i class="fa fa-list-ul"></i>');
			}
		}).fail(function() {
		// Quita el loader
			if($objeto['preparado']==1){
				$('#loader_select_preparado_'+$objeto['id']).html('<i class="fa fa-list-ul"></i>');
			}else{
				$('#loader_select_'+$objeto['id']).html('<i class="fa fa-list-ul"></i>');
			}

			$mensaje = 'Error, no se pueden hacer cambios';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN guardar_opcionales		------ ************ //////////////////

///////////////// ******** ---- 			vista_copiar			------ ************ //////////////////
//////// Consulta las recetas, los insumos preparados y los carga en la div
	// Como parametros recibe:
		// div -> div donde se cargara el contenido html
		// btn -> boton del loader
		// panel -> Clase que se le aplicara al panel de la receta

	vista_copiar : function($objeto) {
		
		console.log('----> Objeto vista_copiar');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=vista_copiar',
			type : 'POST',
			dataType : 'html'
		}).done(function(resp) {
			console.log('----> Done vista_copiar');
			console.log(resp);

		// Carga la vista de de nueva receta
			recetas.vista_nueva({vista: $objeto['vista'], div:'div_recetas', btn:'btn_nueva', panel:'warning'});

		// Quita el loader
			$btn.button('reset');

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);

	
			// AM se agrego para guardar la copia 
			$('.selectpicker').selectpicker('refresh');
				$('#guardarcopia').on('click', function(){ 
 			
    				var btnguardar = $(this);
    				btnguardar.button("loading");

    				 if ($('#inicial').val()==null && $('#final').val()==null) { 
    				 	alert("Llene los campos.");
    				    btnguardar.button('reset');

    				     }else{
			    				     		
			 			 $.ajax({
			    			url:"ajax.php?c=recetas&f=guardarcopia",
			    			type: 'POST',
			    			// dataType:'json',
			    			data:{
			      			inicial: $('#inicial').val(),
			      			final  : $('#final').val()
			    			},
			             success: function(resp){
			             	if (resp==1) {
			             		alert("Se copio correctamente");
			             		btnguardar.button('reset');
			             	}else{
			             		alert("Error al guardar.");
			             	}
			    	            
			    }
			  });
    	}
			    
  			});
		}).fail(function(resp) {
			console.log('----> Fail vista_copiar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');
			$("#"+$objeto['div']).html('Error al cargar los datos');

		// Mensaje error
			$mensaje = 'Error, no se puede cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN vista_copiar			------ ************ //////////////////

///////////////// ******** ---- 				copiar				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de la receta o insumo preparado
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	copiar : function($objeto) {

		console.log('----> Objeto copiar');
		console.log($objeto);

	// Cierra la ventana modal
		$('#btn_cerrar_copiar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["insumos"], function(index, val) {
		// Formateamos el array
			val['id']=val['idProducto'];
			val['id_unidad']=val['idunidad'];
			val['unidad_compra']=val['idunidadCompra'];
			val['check']=false;
			val['div']=$objeto['div'];
			val['select']=val['opcionales'].split(",");

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumo(val);

		// Calcula el precio y el subtotal del insumo
			recetas.calcular_precio(val);
		});

	// Agrega los insumos y los subtotales de los insumos preparados
		$.each($objeto["insumos_preparados"], function(i, v) {
		// Formateamos el array
			v['id']=v['idProducto'];
			v['id_unidad']=v['idunidad'];
			v['unidad_compra']=v['idunidadCompra'];
			v['check']=false;
			v['div']=$objeto['div'];
			v['select']=v['opcionales'].split(",");
			v['preparado']=1;

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumo_preparado(v);

		// Calcula el precio y el subtotal del insumo
			recetas.calcular_precio(v);
		});

	// LLena los campos
		$("#nombre").val($objeto['nombre']);
		$("#margen_ganancia").val($objeto['ganancia']);
		$("#codigo").val($objeto['codigo']);
		$("#precio_venta").val($objeto['precio']);
		$("#preparacion").val($objeto['preparacion']);
		$("#precio_venta").val($objeto['precio']);
		$("#unidad_compra").val($objeto['idunidadCompra']);
		$("#unidad_venta").val($objeto['idunidad']);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');
	},

///////////////// ******** ---- 			FIN copiar				------ ************ //////////////////

///////////////// ******** ---- 			vista_editar			------ ************ //////////////////
//////// Consulta las recetas, los insumos preparados y los carga en la div
	// Como parametros recibe:
		// div -> div donde se cargara el contenido html
		// btn -> boton del loader
		// panel -> Clase que se le aplicara al panel de la receta


		vista_editar2 : function($objeto) {
			
		console.log('----> Objeto vista_editar2');
		console.log($objeto);



		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=vista_editar2',
			type : 'POST',
			dataType : 'html'
		}).done(function(resp) {
			//alert("RESPOMDER"+resp);
			console.log('----> Done vista_editar');
			console.log(resp);

		// Carga la vista de de nueva receta
			//recetas.vista_nueva({vista: $objeto['vista'], div:'div_recetas', btn:'btn_nueva', panel:'primary'});

		// Quita el loader
			//$btn.button('reset');

		// Carga la vista a la div
			$('#div_editar').html(resp);

			$('.selectpicker').selectpicker('refresh');
		}).fail(function(resp) {
			console.log('----> Fail vista_editar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');
			$("#"+$objeto['div']).html('Error al cargar los datos');

		// Mensaje error
			$mensaje = 'Error, no se pueden cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

	vista_editar : function($objeto) {
		
		console.log('----> Objeto vista_editar');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		// Ventana para editar
		//alert("Vista: " + $objeto['vista']);
		//$objeto['vista'] = "frm_prd";

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=vista_editar',
			type : 'POST',
			dataType : 'html'
		}).done(function(resp) {
			console.log('----> Done vista_editar');
			console.log(resp);

		// Carga la vista de de nueva receta
			recetas.vista_nueva({vista: $objeto['vista'], div:'div_recetas', btn:'btn_nueva', panel:'primary'});

		// Quita el loader
			$btn.button('reset');

		// Carga la vista a la div
	
			$('#' + $objeto['div']).html(resp);

			$('.selectpicker').selectpicker('refresh');
		}).fail(function(resp) {
			console.log('----> Fail vista_editar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');
			$("#"+$objeto['div']).html('Error al cargar los datos');

		// Mensaje error
			$mensaje = 'Error, no se pueden cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN vista_editar			------ ************ //////////////////

///////////////// ******** ---- 				editar				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de la receta o insumo preparado
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	editar : function($objeto) {
		console.log('----> Objeto editar');
		console.log($objeto);

	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_copiar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["insumos"], function(index, val) {
		// Formateamos el array
			val['id'] = val['idProducto'];
			val['id_unidad'] = val['idunidad'];
			val['unidad_compra'] = val['idunidadCompra'];
			val['check'] = false;
			val['div'] = $objeto['div'];
			val['select'] = val['opcionales'].split(",");

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumo(val);

		// Calcula el precio y el subtotal del insumo
			recetas.calcular_precio(val);
		});

	// Agrega los insumos y los subtotales de los insumos preparados
		$.each($objeto["insumos_preparados"], function(i, v) {
		// Formateamos el array
			v['id']=v['idProducto'];
			v['id_unidad']=v['idunidad'];
			v['unidad_compra']=v['idunidadCompra'];
			v['check']=false;
			v['div']=$objeto['div'];
			v['select']=v['opcionales'].split(",");
			v['preparado']=1;

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumo_preparado(v);

		// Calcula el precio y el subtotal del insumo
			recetas.calcular_precio(v);
		});

	// LLena los campos
		$("#nombre").val($objeto['nombre']);
		$("#margen_ganancia").val($objeto['ganancia']);
		$("#codigo").val($objeto['codigo']);
		$("#precio_venta").val($objeto['precio']);
		$("#preparacion").val($objeto['preparacion']);
		$("#unidad_compra").val($objeto['idunidadCompra']);
		$("#unidad_venta").val($objeto['idunidad']);

	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');

	},

///////////////// ******** ---- 			FIN editar				------ ************ //////////////////

///////////////// ******** ---- 				editar_form_producto				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de la receta o insumo preparado
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	editar_form_producto : function($objeto) {
		console.log('----> Objeto editar formulación producto');
		console.log($objeto);



	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_editar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["insumos"], function(index, val) {
		// Formateamos el array
			val['id'] = val['idProducto'];
			val['id_unidad'] = val['idunidad'];
			val['unidad_compra'] = val['idunidadCompra'];
			val['check'] = false;
			val['div'] = $objeto['div'];
			val['unidad_clave'] = val['unidad_clave'];
			//val['select'] = val['opcionales'].split(",");

		// Agrega el insumo al array de insumos agregados
			//alert("Agregando: ID: " + val['id'] + ", codigo: " + val['codigo'] + ", nombre: " + val['nombre'] + ", cantidad: " + val['cantidad'] + ", " + val['div']);
			recetas.agregar_insumos_producto(val);


		// Calcula el precio y el subtotal del insumo
			//recetas.calcular_precio(val);
		});

	// Agrega los insumos y los subtotales de los insumos preparados
		$.each($objeto["insumos_preparados"], function(i, v) {
		// Formateamos el array
			v['id']=v['idProducto'];
			v['id_unidad']=v['idunidad'];
			v['unidad_compra']=v['idunidadCompra'];
			v['check']=false;
			v['div']=$objeto['div'];
			v['select']=v['opcionales'].split(",");
			v['preparado']=1;

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumo_preparado(v);

		// Calcula el precio y el subtotal del insumo
			recetas.calcular_precio(v);
		});

	// LLena los campos
		$("#nombre").val($objeto['nombre']);
		$("#margen_ganancia").val($objeto['ganancia']);
		$("#codigo").val($objeto['codigo']);
		$("#precio_venta").val($objeto['precio']);
		$("#preparacion").val($objeto['preparacion']);
		$("#unidad_compra").val($objeto['idunidadCompra']);
		$("#unidad_venta").val($objeto['idunidad']);
		$("#cant_minima").val($objeto['minimos']);
		$("#factor").val($objeto['multiplo'])


	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');

	// Muestra el boton de actualiza y le agrega el ID de la receta o insumo
		$('#btn_actualizar').show();
		$('#btn_actualizar').attr('id_receta', $objeto['idProducto']);
		$('#btn_precio_venta').attr('id_receta', $objeto['idProducto']);

	// Oculta el boton de guardar
		$("#btn_guardar_receta").hide();


	},

///////////////// ******** ---- 			FIN editar_form_producto				------ ************ //////////////////

///////////////// ******** ---- 				copiar_form_producto				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de la receta o insumo preparado
	// Como parametros recibe:


	copiar_form_producto : function($objeto) {
		console.log('----> Objeto copiar formulación de producto');
		console.log($objeto);



	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_copiar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["insumos"], function(index, val) {
		// Formateamos el array
			val['id'] = val['idProducto'];
			val['id_unidad'] = val['idunidad'];
			val['unidad_compra'] = val['idunidadCompra'];
			val['check'] = false;
			val['div'] = $objeto['div'];
			val['unidad_clave'] = val['unidad_clave'];
			val['unidad_codigo'] = val['idunidadCompra'];




			//val['select'] = val['opcionales'].split(",");

		// Agrega el insumo al array de insumos agregados
			recetas.agregar_insumos_producto(val);


		});

	// Agrega los insumos y los subtotales de los insumos preparados


	// LLena los campos
		$("#nombre").val($objeto['nombre']);
		$("#codigo").val($objeto['codigo']);
		$("#unidad_compra_venta").val($objeto['idunidad']);
		$("#cant_minima").val($objeto['minimos'])


	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');



	},

///////////////// ******** ---- 			FIN copiar_form_producto				------ ************ //////////////////


///////////////// ******** ---- 				editar_productos_proceso				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de los procesos asignados a un producto
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	editar_productos_proceso2 : function(idProd) {
		
		recetas.vista_nueva({vista: 'prc_prd', div:'div_recetas', btn:'btn_nueva', panel:'success', modi:'modi'});

		setTimeout(function(){ 
		$.ajax({
			data : {idProd:idProd},
			url : 'ajax.php?c=recetas&f=cargaEdicion',
			type : 'POST',
			dataType : 'json'

		}).done(function(resp) {
			
			
			$('#PasoEditar').val(JSON.stringify(resp['data'][0]['nombre']));
			
			
			$("#sel_productos").val(idProd).trigger('change.select2');
		    $("#sel_productos").prop("disabled", true);
			console.log('arreglo pasos');
			console.log(resp);
			if(resp.success==0){
				if(resp.error=='NOACCIONES'){
					alert('Tienes que agregar acciones');
					return false;
				}
				if(resp.error=='PASOREP'){
					alert('Elige otro nombre para este paso');
					return false;
				}
				if(resp.error=='NOHAYPASOS'){
					alert('No hay pasos registrados para este producto');
					return false;
				}
			}else{
				table = $('#tabla_pasos').DataTable();
				table.clear().draw();

				
			
			bodycad='';
			c=1;

			var pasosExistentes = getDistinct( resp.data.map(dat => dat.pasoR) ); //obtener un array de diferentes pasos

 			recetas.pasosCargados = resp.data;
 			recetas.deshabilitaAcciones();

			$.each(pasosExistentes, function( idx, val ) { //val: nombrePaso
				valSinUnderscore = val.replace(/_/g, " ").replace(/"/g, ""); //reemplaza el '_' por ' '  en el nombre del paso

	            bodycad='<tr id="tr_paso_'+val+'"  >\
	            <td class="idpaso">'+c+'</td>\
	            <td class="nombrepaso" style="cursor:pointer;" onclick="recetas.ver_pasos(\''+val+'\','+c+')" >'+valSinUnderscore+'</td>\
	            <td><button onclick="recetas.removerPaso(\''+val+'\');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span> Remover</button></td>\
	            </tr>';
	            c++;
	            table.row.add($(bodycad)).draw();
	        });
				$('#div_insumos_agregados').html('<br /><br />\
				<blockquote style="font-size: 16px">\
				   	<p>\
				     	Seleccione un <strong>"producto"</strong>\
				y asígnele <strong>"procesos de producción"</strong>.\
				   	</p>\
				   </blockquote>');

				// $.each(resp.data, function( i, v ) {
				// 	ir = i.replace(/_/g, " ");
				// 	ir = ir.replace(/"/g, "");
				// 	alert("ir"+ir);
		  //           bodycad='<tr id="tr_paso_'+i+'"  >\
		  //           <td>'+c+'</td>\
		  //           <td style="cursor:pointer;" onclick="recetas.ver_pasos(\''+i+'\','+c+')" >'+ir+'</td>\
		  //           <td><button onclick="recetas.removerPaso(\''+i+'\');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span> Remover</button></td>\
		  //           </tr>';
		  //           c++;
		  //           table.row.add($(bodycad)).draw();
		  //       });

		    //     $('#div_insumos_agregados').html('<br /><br />\
						// <blockquote style="font-size: 16px">\
					 //    	<p>\
					 //      		Seleccione un <strong>"producto"</strong>\
						// 		y asígnele <strong>"procesos de producción"</strong>.\
					 //    	</p>\
					 //    </blockquote>');

		        $('#input_paso_produccion').val('');

		        var tabla = $('#tabla_acciones').dataTable();
	    		var tabla = tabla.fnGetNodes();

				$(tabla).each(function (index){
					checkbox = $(this,tabla).find('input');
					if($objeto['check'] === false){
						checkbox.prop("checked", true);
						$(this,tabla).addClass('success');
					}else{
						checkbox.prop("checked", false);
						$(this,tabla).addClass('success');
						$(this,tabla).removeClass('success');
					}
	    		});
		   
		        

		        
			}
			console.log('----> Done agregar paso ' + $objeto['id']);
			console.log(resp);
			
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			//$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});

		 }, 1000); 

		$('#btn_cerrar_editar').click();


	},
	

	editar_productos_proceso : function($objeto) {
		console.log('----> Objeto editar productos proceso');
		console.log($objeto);

	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_editar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["procesos"], function(index, val) {
			// Formateamos el array
			val['div'] = 'div_insumos_agregados';
			val['check'] = false;
			/*val['id'] = val['idProducto'];
			val['id_unidad'] = val['idunidad'];
			val['unidad_compra'] = val['idunidadCompra'];
			val['check'] = false;
			val['div'] = $objeto['div'];
			val['unidad_clave'] = val['unidad_clave'];*/
			//val['select'] = val['opcionales'].split(",");



		// Agrega el insumo al array de insumos agregados
			recetas.agregar_proceso(val);


		// Calcula el precio y el subtotal del insumo
			//recetas.calcular_precio(val);
		});

		// alert
		$("#sel_productos").val($objeto['id']).trigger('change.select2');
		//$("#sel_productos").val($objeto['id']);
		$("#sel_productos").prop("disabled", true);

	// LLena los campos
		$("#nombre").val($objeto['nombre']);
		$("#margen_ganancia").val($objeto['ganancia']);
		$("#codigo").val($objeto['codigo']);
		$("#precio_venta").val($objeto['precio']);
		$("#preparacion").val($objeto['preparacion']);
		$("#unidad_compra").val($objeto['idunidadCompra']);
		$("#unidad_venta").val($objeto['idunidad']);
		$("#cant_minima").val($objeto['minimos']);
		//$("#factor").val($objeto['multiplo']);


	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');

	// Muestra el boton de actualiza y le agrega el ID de la receta o insumo
		$('#btn_actualizar').show();
		$('#btn_actualizar').attr('id_receta', $objeto['idProducto']);
		$('#btn_precio_venta').attr('id_receta', $objeto['idProducto']);

	// Oculta el boton de guardar
		$("#btn_guardar_receta_prd").hide();
		$("#familias").hide();


	},

///////////////// ******** ---- 			FIN editar_productos_proceso				------ ************ //////////////////

///////////////// ******** ---- 				editar_productos_conceptos_lab				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de los procesos asignados a un producto
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	editar_productos_conceptos_lab : function($objeto) {
		console.log('----> Objeto editar productos proceso lab');
		console.log($objeto);

	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_editar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["conceptos_lab"], function(index, val) {
			// Formateamos el array
			val['div'] = 'div_insumos_agregados';
			val['check'] = false;
			// Agrega el insumo al array de insumos agregados
			recetas.agregar_parametro(val);
		});


		$("#sel_productos").val($objeto['id']).trigger('change.select2');
		$("#sel_productos").prop("disabled", true);

	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');

	// Muestra el boton de actualiza y le agrega el ID de la receta o insumo
		$('#btn_actualizar_conceptos_lab_productos').show();
		$('btn_actualizar_conceptos_lab_productos').attr('id_receta', $objeto['idProducto']);
		$('#btn_precio_venta').attr('id_receta', $objeto['idProducto']);

	// Oculta el boton de guardar
		$("#btn_guardar_conceptos_lab_producto").hide();


	},

///////////////// ******** ---- 			FIN editar_productos_conceptos_lab				------ ************ //////////////////


///////////////// ******** ---- 				editar_lab_conceptos				------ ************ //////////////////
//////// Carga la vista y la llena con los datos de los procesos asignados a un producto
	// Como parametros recibe:
		// idProducto -> ID del insumo
		// nombre -> nombre del insumo
		// idUnidad -> ID de la unidad de venta
		// idunidadCompra -> ID de la unidad de compra
		// costo -> costo del insumo
		// tipo_producto -> 7 receta, 8 insumo preparado
		// unidad -> texto que se muestra de la unidad(unidad, kilo, litro, gramo, etc.)
		// insumos_preparados -> array con los insumos preparados que componen la receta
		// insumos -> array con los insumos que componen la receta
		// preparacion -> preparacion de la receta

	editar_lab_conceptos : function($objeto) {
		console.log('----> Objeto editar lab conceptos');
		console.log($objeto);

	// Loader
		$("#div_editar").html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

	// Cierra la ventana modal
		$('#btn_cerrar_editar').click();

	// Agrega los insumos y los subtotales de los insumos
		$.each($objeto["procesos"], function(index, val) {
			// Formateamos el array
			val['div'] = 'div_insumos_agregados';
			val['check'] = false;
		});

		// alert
		$("#tipo_concepto").val($objeto['tipo_concepto']);
		$("#tipo_concepto").prop("disabled", true);

	// LLena los campos
		$("#parametro").val($objeto['parametro']);
		$("#is_numeric").val($objeto['is_numeric']);
		$("#unidad").val($objeto['id_unidad']);


	// Actualiza el precio despues de 1 segundo para dar tiempo a la funcion que agrega los insumos
		setTimeout ("$(\"#precio_venta\").val("+$objeto['precio']+");", 1000);

	// Cambia el selec si es insumo preparado
		if($objeto['tipo_producto'] == 4){
			$("#tipo").val(2);
		}

		$('.selectpicker').selectpicker('refresh');

	// Muestra el boton de actualiza y le agrega el ID de la receta o insumo
		$('#btn_actualizar_conceptos_lab').show();
		$('#btn_actualizar_conceptos_lab').attr('id_concepto', $objeto['id']);

	// Oculta el boton de guardar
		$("#btn_guardar_conceptos_lab").hide();


	},

///////////////// ******** ---- 			FIN editar_lab_conceptos				------ ************ //////////////////

///////////////// ******** ---- 				actualizar_conceptos_lab			------ ************ //////////////////
//////// Actualizar la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	actualizar_conceptos_lab : function($objeto) {
		console.log('objeto actualizar formulacion de producto');
		console.log($objeto);

	// ** Validaciones
		if (!$objeto['parametro']) {
			var $mensaje = 'Escriba un nombre de parámetro';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=actualizar_conceptos_lab',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Modificado con exito';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga la vista para editar las recetas
				recetas.vista_nueva({vista:'lab_cpts', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN actualizar_conceptos_lab					------ ************ //////////////////


///////////////// ******** ---- 				actualizar_form_producto			------ ************ //////////////////
//////// Actualizar la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	actualizar_form_producto : function($objeto) {
		console.log('objeto actualizar formulacion de producto');
		console.log($objeto);

	// ** Validaciones
		if (!$objeto['nombre']) {
			var $mensaje = 'Escribe un nombre';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

		if (!$objeto['codigo']) {
			var $mensaje = 'Escribe un un codigo';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=actualizar_form_producto',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Modificado con exito';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga la vista para editar las recetas
				recetas.vista_nueva({vista:'frm_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN actualizar					------ ************ //////////////////

///////////////// ******** ---- 				actualizar_conceptos_lab_productos			------ ************ //////////////////
//////// Actualizar la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	actualizar_conceptos_lab_productos : function($objeto) {
		console.log('objeto actualizar lab_conceptos_productos');
		console.log($objeto);

	// ** Validaciones
		// if (!$objeto['nombre']) {
		// 	var $mensaje = 'Escribe un nombre';
		// 	$('#notificaciones').notify($mensaje, {
		// 		position : "top right",
		// 		autoHide : true,
		// 		autoHideDelay : 5000,
		// 		className : 'warn',
		// 	});
		//
		// 	return 0;
		// }

		// if (!$objeto['codigo']) {
		// 	var $mensaje = 'Escribe un un codigo';
		// 	$('#notificaciones').notify($mensaje, {
		// 		position : "top right",
		// 		autoHide : true,
		// 		autoHideDelay : 5000,
		// 		className : 'warn',
		// 	});
		//
		// 	return 0;
		// }

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=actualizar_conceptos_lab_productos',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Modificado con exito';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga la vista para editar las recetas
				recetas.vista_nueva({vista:'lab_cs_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN actualizar_conceptos_lab_productos					------ ************ //////////////////


///////////////// ******** ---- 				actualizar_procesos_produccion_producto			------ ************ //////////////////
//////// Actualizar la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	actualizar_procesos_produccion_producto : function($objeto) {
		console.log('objeto actualizar proceso producción');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=actualizar_procesos_produccion_producto',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Modificado con exito';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

				recetas.vista_nueva({vista:'prc_prd', div:'div_recetas', btn:'btn_nueva', panel:'success'});

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN actualizar_procesos_produccion_producto					------ ************ //////////////////


///////////////// ******** ---- 				actualizar			------ ************ //////////////////
//////// Actualizar la receta o insumo preparado
	// Como parametros recibe:
		// nombre -> nombre de la receta o insumo preparado
		// codigo -> codigo de la receta o insumo preparado
		// tipo -> 1(receta), 2(insumo preparado)
		// des -> comentarios sobre la receta o insumo preparado
		// precio_venta -> precio de venta
		// margen_ganancia -> margen de ganancia

	actualizar : function($objeto) {
		$objeto['costo']=$costo;
		console.log('objeto actualizar');
		console.log($objeto);

	// ** Validaciones
		if (!$objeto['nombre']) {
			var $mensaje = 'Escribe un nombre';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

		if (!$objeto['codigo']) {
			var $mensaje = 'Escribe un un codigo';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=actualizar',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');
			alert("Status de respuesta: " + resp['status']);
		// Todo bien :D
			if(resp['status']==1){
				var $mensaje = 'Modificado con exito';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
				});

			// Carga la vista para editar las recetas
				$('#btn_editar').click();

				return 0;
			}

		// Sin insumos :p
			if(resp['status']==2){
				var $mensaje = 'Agrega unos cuantos insumos';
				$('#notificaciones').notify($mensaje, {
					position : "top right",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'warn',
				});

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail actualizar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

			$mensaje = 'Error al modificar';
			$('#notificaciones').notify($mensaje, {
				position : "top right",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN actualizar					------ ************ //////////////////

///////////////// ******** ---- 			vista_eliminar				------ ************ //////////////////
//////// Consulta las recetas, los insumos preparados y los carga en la div
	// Como parametros recibe:
		// div -> div donde se cargara el contenido html
		// btn -> boton del loader
		// panel -> Clase que se le aplicara al panel de la receta

	vista_eliminar : function($objeto) {
		console.log('----> Objeto vista_eliminar');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

	// Loader
		$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=vista_eliminar',
			type : 'POST',
			dataType : 'html'
		}).done(function(resp) {
			console.log('----> Done vista_eliminar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Carga la vista a la div
			$('#' + $objeto['div']).html(resp);

			$('.selectpicker').selectpicker('refresh');
		}).fail(function(resp) {
			console.log('----> Fail vista_eliminar');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');
			$("#"+$objeto['div']).html('Error al cargar los datos');

		// Mensaje error
			$mensaje = 'Error, no se pueden cargar los datos';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN vista_eliminar			------ ************ //////////////////

///////////////// ******** ---- 				eliminar			------ ************ //////////////////
//////// Elimina una receta o insumo preparado
	// Como parametros recibe:
		// id -> ID de la receta o insumo preparado

	eliminar : function($objeto) {
		console.log('----> Objeto eliminar');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		if(confirm("¿Estas seguro que quieres eliminar?")){
			$.ajax({
				data : $objeto,
				url : 'ajax.php?c=recetas&f=eliminar',
				type : 'POST'
				//dataType : 'json'
			}).done(function(resp) {
				console.log('----> Done eliminar');
				console.log(resp);
				//alert(JSON.stringify(resp));

			if(resp=='ini'){
				alert('Este producto no se puede eliminar ya que tiene procesos de produccion iniciados.');
			}

			// Quita el loader
				$btn.button('reset');

			// Todo bien :D
				if(resp==1){
				// Indica que se elimino la receta y oculta el boton de eliminar
					$('#tr_eliminar_' + $objeto['id']).removeClass().addClass("danger");
					$btn.hide();

					var $mensaje = 'Eliminado con exito';
					$.notify($mensaje, {
						position : "top center",
						autoHide : true,
						autoHideDelay : 5000,
						className : 'success',
					});

					return 0;
				}

			// Error
				if(resp==0){
					var $mensaje = 'Error al eliminar';
					$('#' + $objeto['btn']).notify($mensaje, {
						position : "top right",
						autoHide : true,
						autoHideDelay : 5000,
						className : 'warn',
					});

					return 0;
				}

			}).fail(function(resp) {
				console.log('----> Fail eliminar');
				console.log(resp);

			// Quita el loader
				$btn.button('reset');

			// Mensaje error
				$mensaje = 'Error, no se puede eliminar';
				$.notify($mensaje, {
					position : "top center",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'error',
					arrowSize : 15
				});
			});
		}
	},

///////////////// ******** ---- 			FIN eliminar				------ ************ //////////////////

///////////////// ******** ---- 			restaurar_precio			------ ************ //////////////////
//////// Busca el precio actual del producto y lo agrega al campo precio_venta
	// Como parametros recibe:
		// id -> ID de la receta o insumo preparado
		// btn -> boton del loader

	restaurar_precio : function($objeto) {
		console.log('----> Objeto restaurar_precio');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=restaurar_precio',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done restaurar_precio');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D, actualiza el precio
			if(resp['status'] == 1){
				$('#precio_venta').val(resp['result']);

				return 0;
			}
		}).fail(function(resp) {
			console.log('----> Fail restaurar_precio');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Mensaje error
			$mensaje = 'Error, no se puede obtener el precio';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 		FIN restaurar_precio			------ ************ //////////////////

///////////////// ******** ---- 			preparar_insumo				------ ************ //////////////////
//////// Descuenta del inventario los insumos y prepara un insumo preparado
	// Como parametros recibe:
		// btn -> Boton del loader
		// id_producto -> ID del insumo preparado
		// cantidad -> Cantidad que se debe preparar del insumo

	preparar_insumo : function($objeto) {
		console.log('----> Objeto preparar_insumo');
		console.log($objeto);

	// ** Validaciones
		if (!$objeto['cantidad']) {
			var $mensaje = 'Escribe la cantidad';
			$('#' + $objeto['btn']).notify($mensaje, {
				position : "left",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'warn',
			});

			return 0;
		}

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=preparar_insumo',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done preparar_insumo');
			console.log(resp);

		// Todo bien :D
			if(resp['status'] == 1){
			// Mensaje error
				$mensaje = 'Preparando insumo...';
				$('#' + $objeto['btn']).notify($mensaje, {
					position : "left",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
					arrowSize : 15
				});

			// Asigna el ID de la preparacion y muestra el boton de terminado
				$("#btn_terminar_" + $objeto['id_producto']).attr("id_preparacion", resp['id_preparacion']);
				$("#btn_terminar_" + $objeto['id_producto']).show();

			// Bloquea la cantidad
				$("#cantidad_" + $objeto['id_producto']).prop( "disabled", true );
			}
		}).fail(function(resp) {
			console.log('----> Fail preparar_insumo');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Mensaje error
			$mensaje = 'Error al preparar el insumo';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},

///////////////// ******** ---- 			FIN preparar_insumo				------ ************ //////////////////

///////////////// ******** ---- 				terminar_insumo				------ ************ //////////////////
//////// Actualiza el inventario y el insumo preparado
	// Como parametros recibe:
		// btn -> Boton del loader
		// id -> ID del insumo preparado
		// id_preparacion -> ID de la preparacion
		// cantidad -> Cantidad que se debe preparar del insumo

	terminar_insumo : function($objeto) {
		console.log('----> Objeto terminar_insumo');
		console.log($objeto);

	// Loader en el boton
		var $btn = $('#' + $objeto['btn']);
		$btn.button('loading');

		$.ajax({
			data : $objeto,
			url : 'ajax.php?c=recetas&f=terminar_insumo',
			type : 'POST',
			dataType : 'json'
		}).done(function(resp) {
			console.log('----> Done terminar_insumo');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Todo bien :D
			if(resp['status'] == 1){
			// Mensaje error
				$mensaje = 'Insumo preparado';
				$("#cantidad_" + $objeto['id']).notify($mensaje, {
					position : "top",
					autoHide : true,
					autoHideDelay : 5000,
					className : 'success',
					arrowSize : 15
				});

				var $btn_preparar = $('#btn_preparar_' + $objeto['id']);
				$btn_preparar.button('reset');

				$("#cantidad_" + $objeto['id']).prop("disabled", false);
				$("#cantidad_" + $objeto['id']).val('');
				$("#btn_terminar_" + $objeto['id']).hide();
			}
		}).fail(function(resp) {
			console.log('----> Fail terminar_insumo');
			console.log(resp);

		// Quita el loader
			$btn.button('reset');

		// Mensaje error
			$mensaje = 'Error al terminar';
			$.notify($mensaje, {
				position : "top center",
				autoHide : true,
				autoHideDelay : 5000,
				className : 'error',
				arrowSize : 15
			});
		});
	},
	//Actualiza la tabla de las acciones que estàn habilitadas o no
	deshabilitaAcciones : function (){

		var arrayAcciones = recetas.pasosCargados.map(elem=>elem.idAccion);
		console.log("accionesActuales:" + arrayAcciones);

		$(".accionpicker").each(function(){
			var idAccion = $(this).attr("idAccion");
			console.log("idElemento: " + $(this).attr('leerdato'));
			console.log("idAccion: " + idAccion);
			if (   (arrayAcciones.indexOf(idAccion) != -1 && idAccion==1)  || (arrayAcciones.indexOf(idAccion) != -1 && idAccion==6)
			 	|| (arrayAcciones.indexOf(idAccion) != -1 && idAccion==9   || idAccion==10) 
			 	|| (arrayAcciones.indexOf(idAccion) != -1 && idAccion==17) || (arrayAcciones.indexOf(idAccion) != -1 && idAccion==14))
			 { $(this).attr("disabled","disabled");
				
			}else{
					$(this).removeAttr('disabled');

			}
	


		});

	}
///////////////// ******** ---- 			FIN terminar_insumo				------ ************ //////////////////
};



function regresaFilaParent1(value) {      //funciòn que recibe un valor y regresa la <tr> que contiene la celda con ese valor
  var tdRegresar =   
    $("#tabla_insumos_agregados2").find("td").filter(function(){    //buscamos los <td> que cumple la condiciòn
      return ( $(this).text()== value) ;	 //condiciòn a revisar (td tiene como texto el valor recibido)
    });
  if(tdRegresar.length>=1)	 //si existen <td> con ese valor, regresamos la fila que la contiene
return tdRegresar.parent();
  
}

// // <!-- Se manda a llamar la funcion valida orden para cada vez que guarde, verifico si estan acomodados correctamente los pasos. -->

function validarorden2(){

var valoresNoModificables =                      //arreglo que contiene los valores de celda que no se pueden mover
[{  valorArriba : 17, valorAbajo : 9 },     
{  valorArriba : 4, valorAbajo : 11 },
{  valorArriba : 4, valorAbajo : 18 },
{  valorArriba : 14, valorAbajo : 17}
] 
revisaIndicesDeFilas1(valoresNoModificables); 
}

function revisaIndicesDeFilas1(valores){
  for(var i =0; i<valores.length; i++){
    var trArriba = regresaFilaParent1( valores[i].valorArriba) ;
    var trAbajo  = regresaFilaParent1( valores[i].valorAbajo ) ;
  
    var indiceTrArriba = $(trArriba).index();
    var indiceTrAbajo  = $(trAbajo).index();
if( (indiceTrArriba !=-1 && indiceTrAbajo != -1) ){   //si los valores que hay en el arreglo existen en la tabla
if (indiceTrArriba +1 != indiceTrAbajo ){         //en caso de que existan los valores, comprobamos los indices de ambos, para saber si
 //está una arriba de la otra
 $('#validarAcomodoGuardar').val('0');

	alert("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas,No se guardara el cambio.");


 	console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas.");
 	console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
 	console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );
}
else{
	$('#validarAcomodoGuardar').val('1');
 	console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " SI estan correctamente acomodadas");
 	console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
 	console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );
}
}
  }
}

	

	function regresaFilaParent(value) {      //función que recibe un valor y regresa la <tr> que contiene la celda con ese valor
		var tdRegresar =   
		$("#tabla_insumos_agregados").find("td").filter(function(){    //buscamos los <td> que cumple la condiciòn
		return ( $(this).text()== value) ;	 //condiciòn a revisar (td tiene como texto el valor recibido)
	});

	if(tdRegresar.length>=1)	 //si existen <td> con ese valor, regresamos la fila que la contiene
		return tdRegresar.parent();

	}

// <!-- Se manda a llamar la funcion valida orden para cada vez que guarde, verifico si estan acomodados correctamente los pasos. -->

	function validarorden(){
			
		var valoresNoModificables =                      //arreglo que contiene los valores de celda que no se pueden mover
	[{  valorArriba : 17, valorAbajo : 9 },     
	{  valorArriba : 4, valorAbajo : 11 },
	{  valorArriba : 4, valorAbajo : 18 },
	{  valorArriba : 14, valorAbajo : 17}
	] 
	revisaIndicesDeFilas(valoresNoModificables); 
	}

	function revisaIndicesDeFilas(valores){
		for(var i =0; i<valores.length; i++){
			var trArriba = regresaFilaParent( valores[i].valorArriba) ;
			var trAbajo  = regresaFilaParent( valores[i].valorAbajo ) ;

			var indiceTrArriba = $(trArriba).index();
			var indiceTrAbajo  = $(trAbajo).index();
		
		if( (indiceTrArriba !=-1 && indiceTrAbajo != -1) ){   //si los valores que hay en el arreglo existen en la tabla
		if (indiceTrArriba +1 != indiceTrAbajo ){         //en caso de que existan los valores, comprobamos los indices de ambos, para saber si
		
		//está una arriba de la otra
		$('#prueba').val('1'); //campo para llamarlo al crear el paso si es 1, no guardo, porque esto sig que tengo acciones mal ordenadas. 

		alert("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas");

		console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas");
		console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
		console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );
		
		}else{
		
		$('#prueba').val('0');
		console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " SI estan correctamente acomodadas");
		console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
		console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );
	}
}
}
}


$(function() {
  
 
});



