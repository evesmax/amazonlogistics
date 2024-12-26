<!DOCTYPE html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/xmlnominas.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 

</head>
<body><br><br>
	<div class="container well" style="width: 50%">
		<h3 align="center"> Emision de recibos Electronicos <br>
		
			<div align="center"  style="width: 50%">
			<select id="periodonom" class="selectpicker" data-width="100%" data-live-search="true" onchange="cambiaperiodo(this.value)">
			<?php
			while($p = $periodos->fetch_object()){
				if($nominaacti['idtipop'] == $p->idtipop){ $se = "selected";}else{ $se="";}?>
				<option value="<?php echo $p->idtipop;?>" <?php echo $se;?>><?php echo $p->nombre; ?></option>
	<?php	}
			?>
		</select>
		</div>
		</h3><br>
		<div class="alert alert-danger col-md-12">
	        <button type="button" class="close" data-dismiss="alert">
	            <span aria-hidden="true">×</span>
	            <span class="sr-only">Cerrar</span>
	        </button>
	         <i class="fa fa-info-circle fa-lg"></i> 
	         INFORMACION.<br>
	         Si desea emitir recibos de otro periodo diferente al actual debe cambiarlo en <a href="" title="Ir a Configuracion" onclick="irConfiguracion()"><b>Configuracion</b></a>
	        <input type="hidden" name="fechainicio" id="fechainicio" /> 
	   </div>
		<div class="alert alert-info col-md-12">
	       <div class="col-md-4" align="center">
	       	Nomina
	       	<select id="idnomina" name="idnomina" class="selectpicker" data-width="100%" data-live-search="true" onchange="iniciocalendario(this.value)">
	       		<option value="0">-Seleccione-</option>
	       		<?php 
	       		while($n = $listaDeNominasSinTimbrar->fetch_object() ){ ?>
	       			<option value="<?php echo $n->idnomp."/".$n->fechainicio;?>"><?php echo $n->numnomina." - ( ".$n->fechainicio." al ".$n->fechafin." )"."Ejer.".$n->ejercicio ?></option>
	       		
	       		<?php 
	       		} ?>
	       	</select>
	       </div>
	       <div class="col-md-4" align="center">
	       	Fecha de Pago
	       		<input type="text" name="fechapago" id="fechapago">
	       </div>
	        <div class="col-md-4" align="center" style="display: none" id="divori">
	       	Origen
	       		<select id="origen" name="origen" class="selectpicker" data-width="100%" data-live-search="true" onchange="cambiofi(this.value)" >
	       		<option value="0">-Seleccione-</option>
	       		
	       		
	       	</select>
	       	 
	       </div>
	       <div class="col-md-4" align="center" style="display: none" id="divfini">
	       	Finiquitos pendientes
	       		<select id="fini" name="fini" class="selectpicker" data-width="100%" data-live-search="true" onchange="finibaja(this.value)" >
	       		<option value="0">-Seleccione-</option>
	       		
	       		
	       		</select>
	       </div>
	       <div class="col-md-4" align="center">
	       	Fecha de emision:
	       		<label> <?php echo date('Y-m-d'); ?></label>
	       </div>
	   </div>
	   	<div align="right">
			<button title="Timbrar nomina" type="button" class="btn btn-primary" id="timbra" data-loading-text="<i class='fa fa-cog fa-spin fa-3x fa-fw margin-bottom'></i>"><i class="fa fa-cogs" aria-hidden="true"></i> Emitir Recibos</button>
		</div>
	</div>
</body>
<!-- <div id="loading" class="alert alert-success" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;overflow-x: scroll;">
            <button type="button" class="close" data-dismiss="alert">
	            <span aria-hidden="true">×</span>
	            <span class="sr-only">Cerrar</span>
	        </button>
          <div id="divmsg">
        </div>
      </div> -->
	      
<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
          <div 
          id="divmsg"
          style="
          opacity:0.8;
          position:relative;
          background-color:#000;
          color:white;
          padding: 20px;
          -webkit-border-radius: 20px;
          border-radius: 10px;
          left:-50%;
          top:-30%
          ">
        </div>
      </div>
      <script>
        function cerrarloading(){
          $("#loading").fadeOut(0);
          var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
          $("#divmsg").html(divloading);
        }
      </script>	        
        
</html>