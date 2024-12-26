<link rel="stylesheet" type="text/css" href="dhtmlxgantt.css"   title="estilo"  />
<script src="dhtmlxgantt.js" type="text/javascript"></script>
<script src="gant.espanol.js" type="text/javascript"></script>
<script src="dhtmlxgantt_tooltip.js"></script>

<style type="text/css">
	.grid_h0{ background-color: #c3c3c3 !important; border: 1px solid #c3c3c3;}
	.grid_h1{ background-color: #d3d3d3 !important;}
	.grid_h2{ background-color: #e3e3e3 !important;}
	.grid_h3{ background-color: #f3f3f3 !important;}

	.colores{ background-color: #f3f3f3 !important; color:#ff0000;}


#lc_chat_layout{
	display:none;
}



</style>

<body>

    <div id="divcuandohayobra" class="row">
      <div class="col-sm-12">
        <div class="panel panel-default" >
			<!-- Panel Heading -->
			<div class="panel-heading">
			<div class="panel-title">Importar subcontratistas</div>
			</div><!-- End panel heading -->

			<!-- Panel body -->
			<div class="panel-body" >
				<div class="col-md-12" style="padding:0px;">
				 	<b>Descarga plantilla de importacion de subcontratistas</b>
				 	<div class="row">
					 <div class="col-md-2">
					 	<button class="btn btn-primary btn-sm" onclick="programaObra(1)"><span class="glyphicon glyphicon-download"></span> Descargar xls</button>
					 </div>
					</div>
				 </div>
				<div class="row">
				&nbsp;
				</div>
				<div class="row">
				&nbsp;
				</div>

				<div class="col-md-12" style="padding:0px;">
				 	<b>Importar subcontratistas</b>
				 	<div class="row">
					 <div class="col-sm-10">
						<input type="file" name="file_upload" id="file_upload_2" />
					</div>
					</div>
				 </div>

			</div>
		</div>
      </div>
    </div>

</body>





<!--
<div class="row">
<button class="btn btn-primary btn-xs" onclick="guardarGantt();"> Guardar</button>
<div class="col-md-12" style="padding:0px;height:200px;">
</div>
</div>
-->


 <script>
$(function() {

$("#file_upload_2").uploadify({
	height        : 28,
	swf           : 'uploadify/uploadify.swf',
	uploader      : 'uploadify/uploadify.php',
	width         : 105,
	'onUploadSuccess' : function(file, data, response) {
	  selpobra = $('#selpobra').val();

	    $.ajax({
	        url:"importacion_subc.php",
	        type: 'POST',
	        async: false,
	        data:{file:file.name},
	        success: function(r){
	          if(r==1){
	            alert('Importacion creada exitosamente');
	            window.location='index.php?modulo='+modulo;
	          }else{
	            alert('Error durante el proceso de importacion');
	          }
	            
	        }
	    });

	} 
});
});

	 function programaObra(option){
	 	window.open('plantilla_subc.xlsx');
	 }


</script>

