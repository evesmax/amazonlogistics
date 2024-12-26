<?php
 $SQL = "SELECT autorizaciones, tiempo, puaut FROM constru_config WHERE id_obra='$idses_obra' LIMIT 1;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    $row = $result->fetch_array();
    $autsel=$row['autorizaciones'];
    $auttime=$row['tiempo'];
    $puaut=$row['puaut'];
  }else{	
  	$autsel=1;
  	$auttime=0;
  	$puaut=1;
  }

 ?>




 <body>

    <div id="divcuandohayobra" class="row">
      <div class="col-sm-12">
        <!--<div class="navbar navbar-default"  style="margin-top:10px;">
          <div class="navbar-header">
              <div class="navbar-brand" style="color:#333;">Configuracion inicial</div>
          </div>
        </div>-->


		<div class="panel panel-default" >
			<!-- Panel Heading -->
			<div class="panel-heading">
			<div class="panel-title">Configuracion inicial</div>
			</div><!-- End panel heading -->

			<!-- Panel body -->
			<div class="panel-body" >
			  
				<div class="row" style="padding: 10px;">
	<div class="col-md-2">
	 	¿Requiere autorizacion para requisiciones estimaciones y nominas?
	 </div>
	 <div class="col-md-2">
	 	<input type="radio" name="ra" value="1" <?php if($autsel==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;"> Si &nbsp;&nbsp;&nbsp;
	 	<input type="radio" name="ra" value="0" <?php if($autsel==0){ echo 'checked="checked"'; } ?>  style="cursor:pointer;"> No
	 </div>
	 <div class="col-md-2">
	 	Seleccione el intervalo de tiempo para la emision de alertas
	 </div>
	 <div class="col-md-6">
	 	<input type="radio" name="ti" value="1" <?php if($auttime==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;"> 10 Min &nbsp;&nbsp;&nbsp;
	 	<input type="radio" name="ti" value="2" <?php if($auttime==2){ echo 'checked="checked"'; } ?>  style="cursor:pointer;"> 15 Min
	 	&nbsp;&nbsp;&nbsp;
	 	<input type="radio" name="ti" value="3" <?php if($auttime==3){ echo 'checked="checked"'; } ?>  style="cursor:pointer;"> 30 Min
	 	&nbsp;&nbsp;&nbsp;
		<input type="radio" name="ti" value="4" <?php if($auttime==4){ echo 'checked="checked"'; } ?>  style="cursor:pointer;"> 60 Min

	 </div>
	 <div class="row">&nbsp;</div>
	 <div class="row">&nbsp;</div>
	 <div class="col-md-2">
	 	¿Desea asignar PU de destajo y subcontrato automaticamente?
	 </div>
	 <div class="col-md-2">
	 	<input type="radio" name="pu" value="1" <?php if($puaut==1){ echo 'checked="checked"'; } ?>   style="cursor:pointer;"> Si &nbsp;&nbsp;&nbsp;
	 	<input type="radio" name="pu" value="0" <?php if($puaut==0){ echo 'checked="checked"'; } ?>  style="cursor:pointer;"> No
	 </div>
</div>
<div class="row" style="padding: 10px;">
	 <div class="col-md-12">
	 	<button class="btn btn-primary btn-sm" onclick="saveConfig()"> Guardar cambios</button>
	 </div>
</div>




			    
			</div><!-- ENd panel body -->
		</div>

        
      </div>
    </div>

</body>


