			$(document).ready( function() {
				$('#accordion_acciones2').hide();
				$('#accordion_acciones').hide();
				$('.accionesagregadas').hide();
			// $("#btn_guardar_receta_prd").hide();

			});

			function agregarnuevo(){

				$('#accordion_acciones').show();
				$('.accionesagregadas').show();
				$('#accordion_acciones2').show();

				limpiartablas();

			}

			function limpiartablas(){
				$.ajax({
					url: 'ajax.php?c=recetas&f=nuevo',
					type: 'POST',
					async: false,
					success: function(r){
						$('#div_insumos_agregados').html('<br /><br />\
							<blockquote style="font-size: 16px">\
							<p>\
							Registre un <strong>"ciclo"</strong>\
							y asígnele <strong>"procesos de producción"</strong>.\
							</p>\
							</blockquote>');

						$('#input_paso_produccion').val('');

						var tabla = $('#tabla_acciones').dataTable();
						var tabla = tabla.fnGetNodes();


						tablep = $('#tabla_pasos').DataTable(
							{"language": {
								"url": "../../libraries/Spanish.json"},
								destroy:true});
						tablep.clear().draw();



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
			$("#selectciclo").val('');

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
				},

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
					order: [[0, 'asc']]
				});

			//table.rowReordering();   
			}
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

			return rid+'#.,#'+rstatus+'#.,#'+rtipo+'#.,#'+ralias+'#.,#'+ract+'#.,#'+eti+'#.,#'+agru; //id_ps,id_esti,imp_sem,proviene 
			}).get().join('#.#.#');

			// Loader
			$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');

			idp=$('#sel_productos').val();
			$.ajax({
				data : {objeto:$objeto,alias:alias,idp:idp},
				url : 'ajax.php?c=recetas&f=agregar_accioncatalogo',
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

			// $('.alias_hrs').inputmask("hh:mm");

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
				// alert(paso);
				// alert(num);

			// 	paso_ori=paso;
			// 	console.log('objeto agregar paso');

			// 	var accionesDelPaso = recetas.pasosCargados.filter(el=>el.nombrePaso== paso);
			// 	console.log( JSON.stringify(accionesDelPaso) );
			// // Loader
			// //$("#"+$objeto['div']).html('<div align="center"><i class="fa fa-refresh fa-5x fa-spin"></i></div>');
			// // idp=$('#sel_productos').val();
			// $.ajax({
			// 	data : {paso:paso,num:num, accionesDelPaso: JSON.stringify(accionesDelPaso) } ,
			// 	url : 'ajax.php?c=recetas&f=ver_pasos',
			// 	type : 'POST',
			// 	dataType : 'html',
			// 	async:false
			// }).done(function(resp) {

			// 	ir = paso.replace(/_/g, " ");
			// 	ir = ir.replace(/"/g, " ");
			// 	$('#titpaso').html(ir);
			// 	$('#bodymodal').html(resp);

			// 	$('#modalpaso').show();
			// // $('#modalpaso').modal('show');

			// // 	console.log('----> Done agregar proceso '+$objeto['id']);
			// console.log(resp);

			// $("#tabla_insumos_agregados2 tbody").sortable({
			// //items: "> tr:not(:first)",
			// appendTo: "parent",
			// helper: "clone",
			// start: function(event, ui) {
			// 	var start_pos = ui.item.index();
			// 	ui.item.data('start_pos', start_pos);
			// },
			// update:function(event, ui){
			// 	validarorden2();
			// 	var start_pos = ui.item.data('start_pos');
			// 	var end_pos = ui.item.index(); 
			// 	console.log("start: " + start_pos , ", end: " + end_pos);

			// 	if ($('#validarAcomodoGuardar').val()==1) {
			// 		var temp  = recetas.pasosCargados[end_pos];
			// 		recetas.pasosCargados[end_pos] = recetas.pasosCargados[start_pos];
			// 		recetas.pasosCargados[start_pos] = temp;
			// 		console.log(JSON.stringify(recetas.pasosCargados ) )	;
			// 		console.log( ui.item.attr('id') );

			// 		pasosAcciones = $('#tabla_insumos_agregados2 tbody tr').map(function() {
			// 			id_accion=$(this).attr('id');
			// 			id_accion = id_accion.replace('acc_','');
			// return id_accion; //id_ps,id_esti,imp_sem,proviene 
			// }).get().join('#..#');

			// // validarorden2();
			// $.ajax({
			// 	url: 'ajax.php?c=recetas&f=morden_accion',
			// 	data:{pasosAcciones: JSON.stringify(recetas.pasosCargados) },
			// 	type: 'POST'
			// //dataType : 'json',
			// }).done(function(resp){

			// }).fail(function(resp) {

			// });
			// }


			// }
			// }).disableSelection();


			// }).fail(function(resp) {
			// 	console.log('----> Fail agregar insumo');
			// 	console.log(resp);

			// 	$mensaje = 'Error, no se puede cargar los datos';
			// 	$.notify($mensaje, {
			// 		position : "top center",
			// 		autoHide : true,
			// 		autoHideDelay : 5000,
			// 		className : 'error',
			// 		arrowSize : 15
			// 	});
			// });
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
			console.log("Areglo de aaciones ya registradas"+disabledaccexist);


			//el prueba toma el valor de  1 si los datos estan mal acomodados y 0 si estab bien acomodados antes de guardar el paso AM
			if ($('#filascorrectas').val()==1) { }else{ 
				console.log('agregar paso producción ');
				console.log($objeto);

				paso=$('#input_paso_produccion').val();
				if(paso==''){
					alert('Tienes que escribir el nombre del paso');
					return false;
				}

			// erroreti=0;
			// erroragru=0;
			alias = $('#tabla_insumos_agregados tbody tr').map(function() {
				id_accion = $(this).attr('id');
				txt_alias = $(this).find('#alias').val();
			
			return id_accion+'_#_'+txt_alias; 
			}).get().join('_##_');


			


			$.ajax({
				data: $objeto,
				url: 'ajax.php?c=recetas&f=agregar_pasociclo',
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
					table = $('#tabla_pasos').DataTable({
						"destroy":true,
						"info": false,
						"language": { "url": "../../libraries/Spanish.json"}
					});
					$("#btn_guardar_receta_prd").show();

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
				Registre un <strong>"Ciclo"</strong>\
				y asígnele <strong>"procesos de producción"</strong>.\
				</p>\
				</blockquote>');

			$('#input_paso_produccion').val('');

			var tabla = $('#tabla_acciones').dataTable();
			var tabla = tabla.fnGetNodes();

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


		

			// funcional para guardar los pasos en el 
			guardar_procesos_produccionciclo : function() {


				var cicloSeleccionado=$('#selectciclo').val();

				if(cicloSeleccionado==''){
					alert('Escriba el nombre del ciclo.');
					return false;
				}

				var accionesDelPaso = recetas.pasosCargados.filter(el=>el.nombrePaso);

				console.log("accionesdelpasoxx"+JSON.stringify(accionesDelPaso));


			// OBTENER LOS REGISTROS QUE SE GUARDARON EN LA TABLA DE PASOS
			var data = []; 
			$('#tabla_pasos tbody tr').map(function() { 
				data.push({
					idpaso     : $(this).find('.idpaso').text(),
					nombrepaso : $(this).find('.nombrepaso').text()
				})
			});

			var edicion   = $('#btn_guardar_receta_prd').val();
			var tipociclo = $('#tipociclo').val();
			$.ajax({
				data:{cicloSeleccionado:cicloSeleccionado, accionesDelPaso: accionesDelPaso,data:data,edicion:edicion,tipociclo:tipociclo},
				url : 'ajax.php?c=recetas&f=guardar_catalogo_ciclos',
				type : 'POST',
				dataType : 'html'
			}).done(function(resp) {

				if(resp==1){
					table = $('#tabla_pasos').DataTable();
					table.clear().draw();
					$('.accionpicker').removeAttr('disabled');
					alert('Proceso Guardado con exito.');
					$('#selectciclo').val('');
					$('#tipociclo').val('');
				}
			// else if(resp=='ini'){
			// 	alert('Este producto no se puede editar ya que tiene procesos de produccion iniciados');
			// }
			else{
				alert('Error al guardar proceso.');
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
		
},


			// funcional para editar los ciclos abre el modal
			vista_editar2 : function() {

				limpiartablas();

				$('#btn_guardar_receta_prd').val(1);
				$.ajax({
					url:'ajax.php?c=recetas&f=editarCatalogociclos',
					type: 'POST',
					dataType:'json',
					success: function(r){
						if(r.success==1 ){

							var table = $('#tabla_ciclos_editar').DataTable({
								"language": {
									"url": "../../libraries/Spanish.json"
								},
								"data": r.data,
								"destroy": true,
								"autoWidth": false,
								"columns": [
								{ "data": "id_tipociclo","width": "35%"},
								{ "data": "descripcion","width": "30%"},
								{ "data": "id_tipociclo",
								"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
									$(nTd).html("<button type='button' class='btn btn-block btn-primary' style='width:220px;' onclick=editar_ciclo('"+oData.id_tipociclo+"')>Editar</a>");}}
									],'order': [[0, 'asc']]
								});
						}else{

							table = $('#tabla_ciclos_editar').DataTable({"language": {
									"url": "../../libraries/Spanish.json"
								},"destroy": true
							});
							table.clear().draw();
						}
					}
				});
			},


			editar_productos_proceso2 : function(idProd) {

			setTimeout(function(){ 
				$.ajax({
					data : {idProd:idProd},
					url : 'ajax.php?c=recetas&f=cargaEdicioncatalogo',
					type : 'POST',
					dataType : 'json'

				}).done(function(resp) {

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

	
			vista_eliminar : function() {
				limpiartablas();

				$.ajax({
					url:'ajax.php?c=recetas&f=editarCatalogociclos',
					type: 'POST',
					dataType:'json',
					success: function(r){
						if(r.success==1 ){

							var table = $('#tabla_ciclos_eliminar').DataTable({
								"language": {
									"url": "../../libraries/Spanish.json"
								},
								"data": r.data,
								"destroy": true,
								"autoWidth": false,
								"columns": [
								{ "data": "id_tipociclo","width": "35%"},
								{ "data": "descripcion","width": "30%"},
								{ "data": "id_tipociclo",
								"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
									$(nTd).html("<button type='button' class='btn btn-block btn-danger' style='width:220px;' onclick=eliminar_ciclo('"+oData.id_tipociclo+"')>Eliminar</a>");}}
									],'order': [[0, 'asc']]
								});
						}else{
							

							table = $('#tabla_ciclos_eliminar').DataTable({"language": {
									"url": "../../libraries/Spanish.json"
								},
								"destroy": true});
							table.clear().draw();

						}
					}
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
			$('#filascorrectas').val('1'); //campo para llamarlo al crear el paso si es 1, no guardo, porque esto sig que tengo acciones mal ordenadas. 

			alert("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas xx");

			console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " NO estan correctamente acomodadas");
			console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
			console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );

			}else{

				$('#filascorrectas').val('0');
				console.log("Las filas con valores " + valores[i].valorArriba + " y "  + valores[i].valorAbajo + " SI estan correctamente acomodadas");
				console.log( "indice de la fila que tiene el " +  valores[i].valorArriba + ":" + indiceTrArriba);
				console.log( "indice de la fila que tiene el " +  valores[i].valorAbajo  + ":" + indiceTrAbajo );
			}
			}
			}
			}

			// Editar ciclo seleccionado de tabla tabla_ciclos_editar
			function editar_ciclo(ciclo) {

				$('#accordion_acciones').show();
				$('.accionesagregadas').show();
				$('#accordion_acciones2').show();

				var table = $('#tabla_acciones').DataTable({
					destroy:true,
					scrollY: "250px",
					scrollCollapse: true,
					paging: false,
					info: false,
					"language": { "url": "../../libraries/Spanish.json" }
				});

				setTimeout(function(){ 
					$.ajax({
						data     : { ciclo:ciclo },
						url      : 'ajax.php?c=recetas&f=cargaEdicioncatalogo',
						type     : 'POST',
						dataType : 'json'

					}).done(function(resp) {

						var nombreciclo = JSON.stringify(resp['data'][0]['descripcionciclo']);
						var tipociclo   = JSON.stringify(resp['data'][0]['id_tipociclo']);
						nombreciclo     = nombreciclo.replace(/["']/g, "");
						tipociclo       = tipociclo.replace(/["']/g, "");
						$('#selectciclo').val(nombreciclo);
						$('#tipociclo').val(tipociclo);


			// console.log('arreglo pasos');
			// console.log(resp);

			if(resp.success==0){
				if(resp.error=='NOACCIONES'){
					alert('Tienes que agregar acciones.');
					return false;
				}
				if(resp.error=='PASOREP'){
					alert('Elige otro nombre para este paso.');
					return false;
				}
				if(resp.error=='NOHAYPASOS'){
					alert('No hay pasos registrados para este producto.');
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


			// $mensaje = 'Error al modificar';
			// $('#notificaciones').notify($mensaje, {
			// 	position : "top right",
			// 	autoHide : true,
			// 	autoHideDelay : 5000,
			// 	className : 'error',
			// 	arrowSize : 15
			// });
			});

			}, 1000); 

				$('#btn_cerrar_editar').click();


			}

			function eliminar_ciclo (cicloEliminar) {

				if(confirm("¿Estas seguro que quieres eliminar el ciclo?")){
					$.ajax({
						data : {cicloEliminar:cicloEliminar},
						url : 'ajax.php?c=recetas&f=eliminarciclo',
						type : 'POST'
					}).done(function(resp) {
						console.log('----> Done eliminar');
						console.log(resp);

						if(resp=='ini'){
							alert('Este ciclo no se puede eliminar ya que tiene procesos de produccion iniciados.');
						}

						// Todo bien :D
						if(resp==1){
				
						alert('Eliminado con exito.');
						$('#btn_cerrar_eliminar').click();

						}

						// Error
						if(resp==0){
							alert("Error al eliminar");

							return 0;
						}

						}).fail(function(resp) {
							console.log('----> Fail eliminar');
							console.log(resp);
						});
						}
						}







