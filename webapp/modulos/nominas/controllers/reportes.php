    <?php
    require('controllers/nominalibre.php');
    require("models/reportes.php");

    class Reportes extends Nominalibre
    {
     public $ReportesModel;
     public $NominalibreModel;
     function __construct()
     {
      $this->ReportesModel = new ReportesModel();
      $this->NominalibreModel = $this->ReportesModel;
      $this->ReportesModel->connect();
    }

    function __destruct()
    {
      $this->ReportesModel->close();
    }


//R E P O R T E   D E   N O M I N A

    function reporteNominas(){


    

   	 	$nominascompleto = $this->NominalibreModel->validaNominas();
		$periodo = 0;
		if($nominascompleto == 1){
			if($_REQUEST['nominas']){
				$periodo =  $_REQUEST['tipoperiodo'];
				// $sepa = explode("/", $_REQUEST['nominas']);
				// $_REQUEST['fechainicial']	=  $sepa[0];
				// $_REQUEST['fechafinal'] 		=  $sepa[1];
				
			}
		}
	
		$reporteNomi = $this->ReportesModel->listadoNominas($_REQUEST['fechainicial'],$_REQUEST['fechafinal'],$_REQUEST['empleados'],$_REQUEST['tipoperiodo'],$_REQUEST['nominas'],$_REQUEST['origen']);
      	$empleados       = $this->ReportesModel->empleadosReporteNominas();
    		$tipoperiodo     = $this->ReportesModel->tipoperiodo();
	  if(!$reporteNomi->num_rows>0){
       $reporteNomi=0;
     }

      $checkbox             = $_REQUEST['checkbox'];
      $periodoseleccionado  = $_REQUEST['tipoperiodo'];
      $nominaseleccionada   = $_REQUEST['nominas'];
      $nombreEmpleado       = $_REQUEST['empleados'];
      $periodoselec         = $_REQUEST['periodoselec']; 

      require ("views/reportes/reportenomina.php");
   }

// F I N -->R E P O R T E   D E   N O M I N A



//R E P O R T E   D E   E N T R A D A S   D E   E M P L E A D O S 

   function reporteEntradas(){
   
    $logo1           = $this->ReportesModel->logo();
    $infoEmpresa     = $this->ReportesModel->infoEmpresa();
    $empleados       = $this->ReportesModel->empleados();
    $empleadosdos    = $this->ReportesModel->empleados();
    $nominas         = $this->ReportesModel->cargaPeriodo();
    $tipoperiodo     = $this->ReportesModel->tipoperiodo();
	  $nominaActual    = $this->ReportesModel->fechasNominaActivaxperiodo($_REQUEST['idtipop']);


    //Agregado para autorización
     $perfilactivo=preg_replace('/[()]/', '', $_SESSION["accelog_idperfil"]);
  
	   $Mostrarautorizar = $this->ReportesModel->Mostrarautorizar($perfilactivo);
     if($Mostrarautorizar!=0){
        $autori = $Mostrarautorizar->autorizado;
        $editar = $Mostrarautorizar->editar;
        $registrahors = $Mostrarautorizar->registrahors;
     }

	  
    $empleadosactivosperiodo = $this->ReportesModel->listaEmpleadosactivo();
    if(!$reporteEntradas->num_rows>0){
     $reporteEntradas=0;
   }
   if($nominaActual!=0){
     $fi = $nominaActual->fechainicio;
     $ff = $nominaActual->fechafin;
     $idnomp= $nominaActual ->idnomp;
     $periodotipo= $nominaActual ->periodotipo;
     $nombre= $nominaActual ->nombre;

   }else{
     $fi ='';
     $ff='';
     $idnomp ='';
   }

   $reporteEntradas = $this->ReportesModel->entradaSalidasEmple($_REQUEST['fechainicio'],$_REQUEST['fechafin'],$_REQUEST['empleados'],$_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['empleadosdos'],$fi,$ff,$idnomp);
   $idtipop  = $_REQUEST['idtipop'];
   $nomina   = $_REQUEST['idnomp'];
   $empleado = $_REQUEST['empleadosdos'];

   require ("views/reportes/reporte_entr_sali_empleado.php");

 }
 
 function CargarAutorizacionEntradas(){
    $diacompletoentradas = $this->ReportesModel->diacompletoentradas(); 
    $entradasoriginales = $this->ReportesModel->entradastemporales();
    require ("views/reportes/reporte_autorizacion_entradas.php");
}

 function eliminarTodoAutorizacionEntradas(){
   echo $ElimiAutorizarEntradasEmple = $this->ReportesModel->eliminarTodoAutorizacionEntradas($_REQUEST['idEmpleado'],$_REQUEST['idnomp'],$_REQUEST['diacompleto']);
}



 function AutorizarEntradasEmple(){
   echo $AutorizarEntradasEmple = $this->ReportesModel->AutorizarEntradasEmple($_REQUEST['idEmpleado'],$_REQUEST['idnomp'],$_REQUEST['diacompleto']);
 }



 function actHoras(){
  $reporte = $this->ReportesModel->listadoHoras($_POST['vali'],$_POST['input'], $_POST['idempleado'],$_POST['idtipop'], $_POST['idnomp']);

}

function periodo(){
  $tipo=$_POST['idtipop'];
  $numnomina = $_POST['numnomina'];
  $nominasD = $this->ReportesModel->cargaPeriodoD($tipo, $numnomina);
}

function reporteIncidencias(){


  $infoEmpresa = $this->ReportesModel->infoEmpresa();
  $logo1       = $this->ReportesModel->logo();

  $empleados            = $this->ReportesModel->empleados();
  $empleadosdos         = $this->ReportesModel->empleados();
  $nominas              = $this->ReportesModel->cargaPeriodo();
  $tipoperiodo          = $this->ReportesModel->tipoperiodo();
  $incidencias          = $this->ReportesModel->incidenciasfiltro();
  $incidenciasfiltrodos = $this->ReportesModel->incidenciasfiltro();
  if(!$reporteIncidencias->num_rows>0){
   $reporteIncidencias=0;
 }

 $nominaActual  = $this->ReportesModel->nominasActivas();
 if($nominaActual['total']>0){
   $fi = $nominaActual["rows"][0]["fechainicio"];
   $ff = $nominaActual["rows"][0]["fechafin"];

 }else{
   $fi ='';
   $ff='';
 }

 require ("views/reportes/reporte_incidencias.php");
}



function llenartablaIncidencias(){
  
  $reporteIncidencias = $this->ReportesModel->incidencias($_REQUEST['fechainicio'],$_REQUEST['fechafin'],$_REQUEST['empleados'],$_REQUEST['incidencias'],$_REQUEST['nombre'],$_REQUEST['nominas']);
}


/*R E P O RT E  D E   P R E N O M I N A *//*L A D O  V I S U A L */
function tablaReporteSobrerecibo(){

 $infoEmpresa = $this->ReportesModel->infoEmpresa();
 $logo1       = $this->ReportesModel->logo();

 $encabezadosReporteSobrerecibo   = $this->ReportesModel->cargaEncabezadosPercepcionesFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado']);
 $reporteSobrerecibo              = $this->ReportesModel->cargaPercepcionesFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado']);

 echo "<div class='table-responsive alert alert-info'>";
 echo "<div align='left'>";
 $url = explode('/modulos',$_SERVER['REQUEST_URI']);
 if($logo1 == 'logo.png') $logo1= 'x.png';
 $logo1 = str_replace(' ', '%20', $logo1);  
 echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo1 style='width: 180px;height: 35px;padding-right:30px'>";
 echo "<b style='font-weight:17.5'>".$infoEmpresa['nombreorganizacion']."</b>";
 echo "<b style='font-weight:17.5'>".$infoEmpresa['RFC']."</b>";
 echo "</div>";

 if ($_REQUEST['idtipop'] !="*" && $_REQUEST['idnomp']!="*"){

  echo "<div style='font-weight:bold;text-align:center'>
  "."Periodo"." "."del ".$_REQUEST['nomi']." al ".$_REQUEST['nomidos']."
  </div>";
  echo "<div style='font-weight:bold;text-align:center'>
  "."Nomina"." ".$_REQUEST['idtipop']."
  </div>";
}

else if ($_REQUEST['idtipop'] =="*" && $_REQUEST['idnomp']=="*") {

  echo "<div style='font-weight:bold;text-align:center'>"."Todos los periodos existentes"."</div>";
  echo "<div style='font-weight:bold;text-align:center'>"."Todas las nominas  existentes"."</div>";
}

echo "<br>";
echo"<table id='divVisualx' cellpadding='2' class=\"taman table table-striped table-bordered table-responsive \" width='100%'; style='font-size:9.6px;' border='.1px' bordercolor='#0000FF'>";
echo "<thead>";
echo "<tr style='background-color:rgb(180,191,193);font-weight:bold;color:black;'>";
$arrayCols  = array();
$arrayTipos = array();
$suma =0;
echo "<th class='colemple'>EMPLEADO</th>";
while($e = $encabezadosReporteSobrerecibo->fetch_object()) {  

  echo "<th class='coluno'>".$e->Descripcion."</th>";
  array_push($arrayCols, $e->idConcepto);
  array_push($arrayTipos, $e->idtipop);
}; 
echo "<th class='coluno'>TOTAL</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";
while($e = $reporteSobrerecibo->fetch_object()) {

  $suma = 0;                       
  echo "<tr>";
  echo "<td class='colemple'>".$e->empleado."</td>";
  for ($f=0; $f < count($arrayCols); $f++){
    $fieldname =  "CONCEPTO_".(string)$arrayCols[$f];
    echo "<td class='coluno taman'>".$e->$fieldname."</td>";

        //$suma = $suma + ($arrayTipos[$f] == 1 ? ($e->$fieldname)  : ($e->$fieldname  * -1) );
    $suma = $suma + ($arrayTipos[$f] == 1 ? ($e->$fieldname)  : ($e->$fieldname  * 1) );
      // echo $arrayCols;
  }
  echo "<td class='coluno'>".$suma."</td>";
  echo "</tr>"; 
}
echo "</tbody>";
echo "</table>";
echo "<div>";
}



function reporteSobrerecibo(){
  
  $codigo         = $this->ReportesModel->empleados();
  $codigodos      = $this->ReportesModel->empleados();
  $empleadosdos   = $this->ReportesModel->empleados();
  $nominas        = $this->ReportesModel->cargaPeriodo();
  $tipoperiodo    = $this->ReportesModel->tipoperiodo();
  
  if ($_REQUEST['idtipop']=='' || $_REQUEST['idnomp']=='' ) {
   
  }else{
    $reporteSobrerecibo   = $this->ReportesModel->cargaPerceFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado'],$_REQUEST['codigouno'],$_REQUEST['codigodos'],$_REQUEST['origen']);
  }
  require ("views/reportes/reporte_prenomina.php");

}

function cargaPerceFiltros(){
  $infoEmpresa   = $this->ReportesModel->infoEmpresa();
  $regPatronal   = $this->ReportesModel->infoRegPatronalRecibo(); 
  $logo1         = $this->ReportesModel->logo();

  $cargaPerceFiltros   = $this->ReportesModel->cargaPerceFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado'],$_REQUEST['codigouno'],$_REQUEST['codigodos'],$_REQUEST['origen'], false);
  $cargaEmpleadosPerceFiltros = $this->ReportesModel->cargaPerceFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado'],$_REQUEST['codigouno'],$_REQUEST['codigodos'],$_REQUEST['origen'], true);


  $idtipop = $_REQUEST['idtipop'];
  $idnomp =$_REQUEST['idnomp'];
  $codigouno = $_REQUEST['codigouno'];
  $codigodos = $_REQUEST['codigodos'];
  $origen = $_REQUEST['origen'];
  // $cargaDeduccion   = $this->ReportesModel->cargaDeduccionFiltros($_REQUEST['idtipop'],$_REQUEST['idnomp'],$_REQUEST['idEmpleado'],$_REQUEST['codigouno'],$_REQUEST['codigodos'],$_REQUEST['origen']);

  require ("views/reportes/PrenominaReport.php");
}

function cargarcodigo(){

  $codigouno = $this->ReportesModel->cargarcodigo($_REQUEST['codigouno']);
  
}

/*T E R M I N A   R E P O R T E   D E P R E N O M I N A*/


/* R E P O R T E   D E  A C U M U L A D O*/
function reporteAcumulado(){

  $reporteAcumulado = $this->ReportesModel->reporteAcumulado($_REQUEST['empleadosdos'],$_REQUEST['tipoperiodo'],$_REQUEST['nominas'],$_REQUEST['nominasdos'],$_REQUEST['origen'],$_REQUEST['idEmpleado']);

  $logo1           = $this->ReportesModel->logo();
  $infoEmpresa     = $this->ReportesModel->infoEmpresa();
  $empleados       = $this->ReportesModel->empleados();
  $empleadosdos    = $this->ReportesModel->empleados();
  $nominas         = $this->ReportesModel->cargaPeriodo();
  $tipoperiodo     = $this->ReportesModel->tipoperiodo();
  if(!$reporteAcumulado->num_rows>0){
   $reporteAcumulado=0;
 }
 require ("views/reportes/reporte_acumulado.php");

}
/*TERMINA REPORTE DE ACUMULADO*/




// R E S U M E N  A N A L I T I C O  P O R   D E P A R T A M E N T O
function resumenAnaliticoDepa(){

  $nominas         = $this->ReportesModel->cargaPeriodo();
  $tipoperiodo     = $this->ReportesModel->tipoperiodo();
  $departamentos   = $this->ReportesModel->departamentos();

  require ("views/reportes/resumenAnaliticoDep.php");
}


function resumenAnaliticoDep(){

  $resumenAnaliticoDep = $this->ReportesModel->resumenAnaliticoDep($_REQUEST['idtipop'],
    $_REQUEST['nomi'], $_REQUEST['nomidos'],$_REQUEST['depa'],$_REQUEST['idnomi']);


      if($resumenAnaliticoDep!=0){

       echo $resumenAnaliticoDep;
        while($in = $resumenAnaliticoDep->fetch_object()){

          echo"<tr>
          <td>".($in->codigo)."</td>
          <td align='right'>".(number_format($in->salarioAnterior,2,'.',','))."</td>
          <td align='right'>".(number_format($in->salarioNuevo,2,'.',','))."</td>
          <td align='right'>".(number_format($in->sbcfija,2,'.',','))."</td>
          <td align='right'>".(number_format($in->sdi,2,'.',','))."</td>
          <td>".($in->nombreEmpleado)." ".($in->apellidoPaterno)." ".($in->apellidoMaterno)."</</td> 
        </tr>";
      } 
    }


}


//R E P O R T E   D E   P R E N O M I N A   D E T A L L A D O
function reportePrenominaDetallado(){

$reportePrenominaDetallado = $this->ReportesModel->reportePrenominaDetallado($_REQUEST['empleados'],$_REQUEST['nombre'],$_REQUEST['nominas']);

$sumasconceptos = $this->ReportesModel->sumasConceptos($_REQUEST['empleados'],$_REQUEST['nombre'],$_REQUEST['nominas']);

$configuracionTE = $this->NominalibreModel->configuracionNominas();
if($_REQUEST['nominas']){
	$empleadosTE = $this->ReportesModel->tiempoextradeta($_REQUEST['nominas'], $_REQUEST['nombre'], $configuracionTE->minacumulaTE,$configuracionTE-> mincuentaTE,$configuracionTE->acumuladosemanal);
	$empleadosTErelacion = $this->ReportesModel->listadoDempleadoparaTEdeta($_REQUEST['nominas'], $_REQUEST['nombre']);

}	
 $tipoperiodo     = $this->ReportesModel->tipoperiodo();
 $nominasDeri     = $this->ReportesModel->cargaPeriodo();
 $empleados       = $this->ReportesModel->empleados();
 $logo1           = $this->ReportesModel->logo();
 $infoEmpresa     = $this->ReportesModel->infoEmpresa();

 if(!$reportePrenominaDetallado->num_rows>0){
   $reportePrenominaDetallado=0;
 }

   $idtipop  = $_REQUEST['nombre'];
   $nomina   = $_REQUEST['nominas'];
   $empleado = $_REQUEST['empleados'];

if ($_REQUEST['nombre']!='') {
 $nomActiperioSelecc = $this->ReportesModel->nomActiperioSelecc($_REQUEST['nombre']);

}
   

 require ("views/reportes/reporte_prenomina_detallado.php");


}

function almacenaHorario(){
	$fechas = explode(",", $_REQUEST['fechas']);
	$dias = array('Dom','Lun','Mar','Mie','Jue','Vie','Sab');
	
	$entrada = (!$_REQUEST['entrada']) ? 'null' : "'".$_REQUEST['entrada']."'" ;
	$comidaini = (!$_REQUEST['comidaini']) ? 'null' : "'".$_REQUEST['comidaini']."'" ;
	$comidafin = (!$_REQUEST['comidafin']) ? 'null' : "'".$_REQUEST['comidafin']."'" ;
	$salida = (!$_REQUEST['salida']) ? 'null' : "'".$_REQUEST['salida']."'" ;
	
	foreach($_REQUEST['idempleado'] as $key =>$e){
		foreach($fechas as $f){
			$dia = $dias[date('N', strtotime($f))];
			
			$sql.="INSERT INTO temp_registroentradas (horaentrada, iniciocomida, fincomida, horasalida, idEmpleado, fecha, dia, idnomp,diacompleto)
				VALUES
			(".$entrada.", ".$comidaini.",".$comidafin.", ".$salida.", $e, '$f', '$dia', ".$_REQUEST['idnomp'].",1);
			";
		}	
	}
	echo $this->ReportesModel->insertPeriodo($sql);
	
}
function eliminarHorario(){
	echo $this->ReportesModel->eliminarHorario($_REQUEST['idregistro']);
}
function vertiempoextra(){
		$te = $this->ReportesModel->verTE($_REQUEST['ide'],$_REQUEST['idnomp']);
		if(!$te->num_rows>0){
			$te = 0;
		}
		require('views/prenomina/tiempoextra/vistate.php');
	}
/*REPORTE DE TIEMPO EXTRA */
function tiempoextra(){
	$logo1           = $this->ReportesModel->logo();
    $infoEmpresa     = $this->ReportesModel->infoEmpresa();
    $empleados       = $this->ReportesModel->empleados();
    $nominas         = $this->ReportesModel->cargaPeriodo();
    $tipoperiodo     = $this->ReportesModel->tipoperiodo();
	
	require('views/prenomina/tiempoextra/reportetiempoextra.php');
}
function contenidote(){
	$cont = $this->ReportesModel->reporteTE($_REQUEST['empleado'], $_REQUEST['idnomp']);
	if($cont->num_rows>0){
		while($co = $cont->fetch_object()){
			echo "<tr>
			<td align='center'>".$co->numdia."</td>
			<td align='center'>".$co->tipohora."</td>
			<td align='center'>".$co->numhrs."</td>
			<td align='right'>".$co->importepagado."</td>
			<td align='center'> <a onclick=javascript:recibo(".$co->idEmpleado.",".$co->idnomp.",".$co->idtipop.") > Recibo de pago</a></td>
			<td align='left'>".$co->empleado."</td>
			<td align='center'>".$co->minutos."</td>";
			if($co->automatico == 1){ $a="SI"; }else{ $a="NO";}
			echo "<td align='center'>".$a."</td>
			<td align='center'>".$co->usuario."</td>
			</tr>";
		}
	}else{
		echo "<tr><td colspan='9' align='center'>Sin tiempo extra</td></tr>";
	}
}	
/*FIN REPORTE DE TIEMPO EXTRA */	

/*reporte DE RESUMEN DE NOMINA*/	
function nominaglobal(){
	$logo1           = $this->ReportesModel->logo();
    $infoEmpresa     = $this->ReportesModel->infoEmpresa();
    $nominas         = $this->ReportesModel->cargaPeriodo();
    $tipoperiodo     = $this->ReportesModel->tipoperiodo();
	$anosperiodo		 = $this->ReportesModel->anosNominasPeriodo();
	
	require('views/reportes/nominaglobal.php');
}
function contenidoResumeGlobal(){
	 
	 $array = array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Nomivembre','12'=>'Diciembre');
     if(is_array($_REQUEST['mes']) ){
     	$cd="";
     	foreach($_REQUEST['mes'] as $va){
     		$cd.= $array["$va"].", ";
     	}
	 	$cd = substr($cd, 0, -2);
		echo $cd;
	  }else{
	  	$m = $_REQUEST['mes'];
	  	echo $array["$m"];
	  }
	echo "-_-";	
	if(is_array($_REQUEST['periodo']) ){		
       $cd="";
	 	foreach($_REQUEST['periodo'] as $va){
	 		$p = $this->NominalibreModel->editarTipoperidoo($va);
	 		
	 		$cd.= $p->nombre.", ";
	 	}
		$cd = substr($cd, 0, -2);
		echo $cd;
	 }else{
	 	$p = $this->NominalibreModel->editarTipoperidoo($_REQUEST['periodo']);
	  	echo  $p->nombre;
	 }
	echo "-_-";	
	echo $_REQUEST['ano'];
	echo "-_-";	
	  
	if(is_array($_REQUEST['mes']) ){
		$_REQUEST['mes']  = implode(",", $_REQUEST['mes']);
	}
	if(is_array($_REQUEST['periodo']) ){
		$_REQUEST['periodo']  = implode(",", $_REQUEST['periodo']);
	}if(is_array($_REQUEST['ano']) ){
		$_REQUEST['ano']  = implode(",", $_REQUEST['ano']);
	}
	$deduc = $percep = $basep = $based = 0;
	$sumasconceptos = $this->ReportesModel->sumasConceptosGlobal($_REQUEST['ano'],$_REQUEST['periodo'],$_REQUEST['mes'],1);
	if($sumasconceptos){
		if($sumasconceptos->num_rows>0){
         	while($con = $sumasconceptos->fetch_assoc()){
         		if($con['idtipo'] == 1 || $con['idtipo'] == 4){ $percep +=$con['importe'];}
				if($con['idtipo'] == 2){ $deduc +=$con['importe'];}  
            echo" <tr>
                     <td>".$con['concepto']."</td>
                     <td>". $con['descripcion']."</td>
                     <td style='text-align: right;'>$".' '.(number_format($con['importe'],2,'.',','))."</td>
                  </tr>";
                  
    			} 
    		}
    	}else{
    		echo "<tr><td colspan='3'>Sin conceptos extras</td></tr>";
    	}
    echo '-_-';  
	$sumasconceptosGlobal = $this->ReportesModel->sumasConceptosGlobal($_REQUEST['ano'],$_REQUEST['periodo'],$_REQUEST['mes'],2);
	
                
	 if($sumasconceptosGlobal->num_rows>0){
	 	echo "<tr>";
	 	while($co = $sumasconceptosGlobal->fetch_object()){
	 		 $baseini =  0 ;
	 		//percepciones
	 		$array[$co->idtipo][$co->idAgrupador] = $co->importe;
			 if($co->idAgrupador == 1 && $co->idtipo == 1){//percepcion normal
	 			$busca2 = strripos($co->descripcion, "vacacion");
				if($busca2 !== false){
					$array[$co->idtipo]["vacacion"] = $co->importe;
				}
	 		}
		}
		
		 // if($co->idAgrupador == 16 && $co->idtipo == 1){//percepcion normal
		 			// $busca2 = strripos($c->descripcion, "vacacion");
					// if($busca2 !== false){
						echo "<td align='center'>".number_format($array[1][1],2,'.',',')."</td>";
						$basep+=$array[1][1];
						$baseini+=$array[1][1];
					// }
		 		// }
				// if($co->idAgrupador == 42 && $co->idtipo == 1){
		 			echo "<td align='center'>".number_format($array[1][42],2,'.',',')."</td>";
		 			$basep+=$array[1][42];$baseini+=$array[1][42];
		 		// }
				// if($co->idAgrupador == 8 && $co->idtipo == 1){
		 			echo "<td align='center'>".number_format($array[1][8],2,'.',',')."</td>";
					$basep+=$array[1][8];$baseini+=$array[1][8];
		 		//}
			echo  "<td align='center'>".number_format($baseini,2,'.',',')."</td>";
			
				// if($co->idAgrupador == 2 && $co->idtipo == 2){//isr
		 			echo "<td align='center'>".number_format($array[2][2],2,'.',',')."</td>";
		 			$based+=$array[2][2];
		 		// }
				// if($co->idAgrupador == 2 && $co->idtipo == 4){//subsidio
		 			echo "<td align='center'>".number_format($array[4][2],2,'.',',')."</td>";
		 			$basep+=$array[4][2];
		 		// }
				// if($co->idAgrupador == 1 && $co->idtipo == 2){//imss
		 			echo "<td align='center'>".number_format($array[2][1],2,'.',',')."</td>";
		 			$based+=$array[2][1];
		 		// }
				// if($co->idAgrupador == 18 && $co->idtipo == 1){//prima
		 			echo "<td align='center'>".number_format($array[1][18],2,'.',',')."</td>";
					$basep+=$array[1][18];
				// }
				// if($co->idAgrupador == 16 && $co->idtipo == 1){//vacaciones
		 			// $busca2 = strripos($c->descripcion, "vacacion");
					// if($busca2 !== false){
				echo "<td align='center'>".number_format($array[1]['vacacion'],2,'.',',')."</td>";
				$basep+=$array[1]['vacacion'];
					// }
		 		// }
			
			
		
			echo  "<td align='center'>".number_format($basep - $based + $percep - $deduc,2,'.',',')."</td>";
			echo "</tr>";
	 }else{
		 echo "<tr><td colspan='10' align='center'>Sin importes</td></tr>";
	 }
	 
	
}	
/*FIN reportex DE RESUMEN DE NOMINA*/	

  /* R E P O R T E   D E   V A C A C I O N E S */

  function reportevacaciones(){

    $empleados       = $this->ReportesModel->empleados();
    $nominas         = $this->ReportesModel->cargaPeriodo();
    $tipoperiodo     = $this->ReportesModel->tipoperiodo();
    
    require ("views/reportes/reporteVacaciones.php");
}

function llenarReporteVacaciones(){
    $logo1           = $this->ReportesModel->logo();
    $infoEmpresa     = $this->ReportesModel->infoEmpresa(); 
  
    $cargarvacaciones  = $this->ReportesModel->cargarvacaciones($_REQUEST['idtipop'],$_REQUEST['emple'],$_REQUEST['anioselec']);

    require ("views/reportes/llenarReporteVacaciones.php");  
}



	
	function cargarEmpleados(){
  $tipo=$_POST['idtipop'];
  $Empleado = $this->ReportesModel->cargarEmpleados($tipo);
}

function vacacionexport(){

  require_once 'importar/Excel/reader.php';
  $cadena="";

  if (isset($_FILES['archivo'])) { 

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], "importar/vacaciones.xls" )) {

      $data = new Spreadsheet_Excel_Reader();
      $data->setOutputEncoding('CP1251');
      $data->read('importar/vacaciones.xls');

      for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {

      $dato[2] = trim($data->sheets[0]["cells"][$i][1]);// codigoempleado
      $dato[3] = trim($data->sheets[0]["cells"][$i][2]);// claveincidencia
      $dato[4] = trim($data->sheets[0]["cells"][$i][3]);// fechainicial
      $dato[5] = trim($data->sheets[0]["cells"][$i][4]);// fechafinal
      $dato[6] = trim($data->sheets[0]["cells"][$i][5]);// fechapago
      $dato[7] = trim($data->sheets[0]["cells"][$i][6]);// diasvacaciones

      $cadena = "2".",'".$dato[3]."','".$dato[4]."','".$dato[5]."','".$dato[6]."','".$dato[2]."','".$dato[7]."',(";
      $sql = substr($cadena, 0, -2);
      $insert = $this->ReportesModel->importinsertVaca($sql);

      }
    }
      if($insert==1){
        unlink("importar/vacaciones.xls");
        
        echo "<script>alert('Datos Importados.');window.location='index.php?c=Reportes&f=reportevacaciones';</script>";   
    } else {
        echo "<script>alert('No se subio el archivo, intente nuevamente.'); window.location='index.php?c=Reportes&f=reportevacaciones'; </script>";
    
  }
}
}

   /*F I N   D E   R E P O R T E   D E   V A C A C I O N E S*/

}
?>