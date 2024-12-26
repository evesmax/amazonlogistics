<?php
//ini_set("display_errors", 1); error_reporting(E_ALL);
class ReportesModel extends NominalibreModel
{



//R E P O R T E   D E   N O M I N A

	function listadoNominas($fechaini,$fechafin,$nomEmple,$tipop,$nomina,$origen){

       //Se le suma un dia porque la hora de la fecha no me trae la del dia por ese tiempo...
		$fechafin = strtotime ('+1 day' , strtotime ($fechafin) );
		$fechafin = date ('Y-m-j', $fechafin );
		$filtroEmpleado = $filtroperi = $filtronomina= "";

		if($nomEmple != "" && $nomEmple != "0" ){
			$filtroEmpleado = "AND em.idEmpleado = $nomEmple ";
		}
		if($tipop!='' && $tipop!='0' && $tipop!='3'){
			$filtroperi = "where em.idtipop=$tipop";
		}
		if($nomina!=''){
			$filtronomina = "and nt.idnomp=$nomina";
		}
		if ($origen!='') {
			$filtrorigen = "where cp.origen=$origen";
		}

	
		$consultar="SELECT em.email,nt.cancelado,nt.idNominatimbre,nt.UUID,nt.idEmpleado,nt.nombreXML,nt.fechainicial,nt.fechafinal,nt.diaspago,nt.subtotal,nt.descuento,nt.total,em.apellidoPaterno,em.apellidoMaterno,em.nombreEmpleado,nt.idnomp,cp.origen
			from nomi_nominas_timbradas as nt
			inner join nomi_empleados as em
			on nt.idEmpleado=em.idEmpleado
			left join nomi_calculo_prenomina cp
			on nt.idNominatimbre=cp.idNominatimbre";
		

		if($fechaini!='' && $fechafin!=''){
			
				$sql = $this->query("$consultar where fechainicial >= '$fechaini' AND fechafinal <= '$fechafin' $filtroEmpleado group by idNominatimbre order by em.nombreEmpleado asc;");
				
		}if($tipop!=''){
				
				$sql = $this->query("$consultar $filtroperi $filtronomina $filtroEmpleado $filtrorigen group by idNominatimbre order by em.nombreEmpleado asc;");
			
		} 
		return $sql;

	}

	function empleadosReporteNominas($empleados)
	{
		$sql = $this->query("SELECT * from nomi_empleados where salario > 0;");
		return $sql;
	}

// F I N -->R E P O R T E   D E   N O M I N A




	/* I N C I D E N C I A S      I N T E R F A Z */
	// function rangoConceptos(){
// 
		// $sql=$this->query("select cp.idconfpre,cp.idtipo,cp.idconcepto,cp.valor,cp.importe,c.idconcepto,c.concepto,c.descripcion,p.idEmpleado,p.idnomp,p.idconfpre,p.valordias,p.importe,p.idNominatimbre,p.idcal
			// from nomi_conf_prenomina as cp
			// inner join nomi_calculo_prenomina as p on cp.idconfpre=p.idconfpre
			// inner join nomi_conceptos as c on cp.idconcepto=c.idconcepto where idnomp=27 
			// GROUP BY descripcion;");
// 
		// return $sql;
// 
	// }





	function empleados($empleados)
		{
			$sql = $this->query("SELECT * from nomi_empleados where salario > 0;");
			return $sql;
		}



//R E P O R T E   D E   E N T R A D A S   D E   E M P L E A D O S 

	function entradaSalidasEmple($fechaini,$fechafin,$nomEmple,$periodos,$nominas,$empleados,$fi,$ff,$idnomactiva){


		$filtroEmpleado    = "";
		$filtroperiodo     = "";
		$filtronomina      = "";
		$filtroEmpleados   = "";

        //echo "Periodo: $periodos, Nomina: $nominas, Empleado: $empleados";

		if($nomEmple != ''){
			$filtroEmpleado = "AND em.idEmpleado = $nomEmple";
		}

		if($periodos != '*'){
			$filtroperiodo = "AND np.idtipop = $periodos";
		}

		if($nominas != '*'){
			$filtronomina = "AND np.idnomp = $nominas";
			$filtronomi="idnomp=np.idnomp and";
		}

		if($empleados != '*' && $empleados != ''){
			$filtroEmpleados = "AND em.idEmpleado = $empleados";
		}


		$sqlgeneral = "SELECT (SELECT COUNT(idnomp) FROM nomi_registro_entradas where $filtronomi idEmpleado=em.idEmpleado)AS numerodias,np.idnomp,np.autorizado,np.idtipop,np.numnomina,np.fechainicio, np.fechafin,pn.nombre,pn.idtipop,em.apellidoPaterno, em.apellidoMaterno,em.nombreEmpleado,em.idtipop,em.idEmpleado,em.codigo,em.nss,em.rfc,em.curp, re.horaentrada,re.iniciocomida,re.fincomida,re.horasalida,re.idEmpleado,re.fecha,re.dia,re.idnomp,re.idregistro from nomi_registro_entradas as re inner join nomi_empleados as em on em.idEmpleado=re.idEmpleado inner join nomi_tiposdeperiodos as pn on pn.idtipop=em.idtipop inner join nomi_nominasperiodo as np on np.idnomp = re.idnomp";

		if($fechaini!='' && $fechafin!='')
		{
				$sql = $this->query("$sqlgeneral where re.fecha between '$fechaini' and '$fechafin' $filtroEmpleado order by  nombreEmpleado asc,re.fecha asc");
		}
		else
		{
			if($periodos!='')
			{
				
				$sql = $this->query("$sqlgeneral where 1=1 $filtroperiodo $filtronomina $filtroEmpleados order by nombreEmpleado asc,re.fecha asc");
			}
		}
		return $sql;
	}


	function diacompletoentradas(){
		$sql=$this->query("SELECT re.*,em.nombreEmpleado,em.idEmpleado,em.apellidoPaterno,em.apellidoMaterno,	ttp.nombre 
			from temp_registroentradas  re
			inner join 
			nomi_empleados em
			on em.idEmpleado=re.idEmpleado	
			left join nomi_nominasperiodo np
			on np.idnomp=re.idnomp
			inner join nomi_tiposdeperiodos ttp
			on ttp.idtipop=np.idtipop
			where re.diacompleto=1
			order by re.idEmpleado;");
		return $sql;
	}

	function entradastemporales(){

		$sql=$this->query("SELECT re.idregistro,em.nombreEmpleado,em.idEmpleado,em.apellidoPaterno,em.apellidoMaterno,re.fecha as fechaoriginal,re.dia as diaoriginal,re.idnomp,
			re.horaentrada as horaEntradaOriginal, re.iniciocomida as horaInicioComidaOriginal,
			re.fincomida as horaFinComidaOriginal, re.horasalida as horaSalidaOriginal, te.horaentrada as horaEntradaModificado,
			te.inicioComida as horaInicioComidaModificado, te.fincomida as horaFinComidaModificado, te.horasalida as horaSalidaModificado ,
			te.fecha as fechanueva,te.dia as dianuevo,
			ttp.nombre,te.diacompleto
			From nomi_registro_entradas re
			Inner Join temp_registroentradas te
			On re.idregistro = te.idregistroentrada
			left join nomi_empleados em
			on re.idEmpleado=em.idEmpleado
			left join nomi_nominasperiodo np
			on np.idnomp=re.idnomp
			inner join nomi_tiposdeperiodos ttp
			on  ttp.idtipop=np.idtipop
			order by re.idEmpleado;");

		return $sql;

	}


function Mostrarautorizar($perfilactivo){

	$sql=$this->query("SELECT(SELECT  
		ap.idaccion
		from  accelog_perfiles_ac ap
			left join accelog_menu_acciones am
			on ap.idaccion=am.id
			where ap.idperfil='$perfilactivo' and ap.idaccion in(19)
			group by id)autorizado,
			(SELECT  
		ap.idaccion
		from  accelog_perfiles_ac ap
			left join accelog_menu_acciones am
			on ap.idaccion=am.id
			where ap.idperfil=$perfilactivo and ap.idaccion in(20)
			group by id)editar,	
			(SELECT  
		ap.idaccion
		from  accelog_perfiles_ac ap
			left join accelog_menu_acciones am
			on ap.idaccion=am.id
			where ap.idperfil=$perfilactivo and ap.idaccion in(21)
			group by id)registrahors;");
			
	if($sql->num_rows>0){
		return $sql->fetch_object();
	}else{
		return 0;
	}
}

function AutorizarEntradasEmple($idEmpleado,$idnomp,$diacompleto){
	
		if($idEmpleado !='')	{
			$filtroEmpleadoUpdate = " AND tre.idempleado = $idEmpleado and tre.idnomp =  $idnomp";
			$filtroEmpleadoDelete = " WHERE idEmpleado   = $idEmpleado and idnomp = $idnomp";
		}
		else{
			 $filtroEmpleadoUpdate = " AND tre.idnomp in (SELECT idnomp from temp_registroentradas group by idnomp) ";
		     $filtroEmpleadoDelete = "";
		}

			if ($diacompleto==1) {
				
				$sql1="INSERT INTO nomi_registro_entradas (horaentrada, iniciocomida, fincomida, horasalida, idEmpleado, fecha, dia, idnomp)
						SELECT horaentrada,iniciocomida, fincomida, horasalida, idempleado, fecha, dia, idnomp
							FROM temp_registroentradas 
							where diacompleto='1' 
							and idempleado=$idEmpleado and idnomp=$idnomp;".
							"DELETE FROM temp_registroentradas  
				 		 	where diacompleto='1' and idempleado=$idEmpleado;";

						if($this->multi_query($sql1)) {
								return 1;
							}else{
								return 0;
							}

			}else if ($diacompleto==2) {

			  	$sql="UPDATE nomi_registro_entradas AS re, temp_registroentradas AS tre
							 	set re.horaentrada 	=  tre.horaentrada,
							 	re.iniciocomida = tre.iniciocomida,
							 	re.fincomida	= tre.fincomida,
							 	re.horasalida	= tre.horasalida     
							 	WHERE re.idregistro = tre.idregistroentrada
							 	$filtroEmpleadoUpdate;"
								."DELETE FROM temp_registroentradas  
				 		 	     where diacompleto='2' and idempleado=$idEmpleado;" 
							 					    	;
			 			if($this->multi_query($sql)) {
								return 1;
							}else{
								return 0;
							}

			}else{

				$sql="UPDATE nomi_registro_entradas AS re, temp_registroentradas AS tre
	 					    set re.horaentrada 	=  tre.horaentrada,
	 					    	re.iniciocomida = tre.iniciocomida,
	 					    	re.fincomida	= tre.fincomida,
	 					    	re.horasalida	= tre.horasalida     
	 					    WHERE re.idregistro = tre.idregistroentrada
	 					    	$filtroEmpleadoUpdate;"."INSERT INTO nomi_registro_entradas (horaentrada, iniciocomida, fincomida, horasalida, idEmpleado, fecha, dia, idnomp)
					SELECT horaentrada,iniciocomida, fincomida, horasalida, idempleado, fecha, dia, idnomp
					FROM temp_registroentradas
					WHERE diacompleto='1';"."DELETE FROM temp_registroentradas  
				 		 	    $filtroEmpleadoDelete;";

								 					    	
			 	if($this->multi_query($sql)) {
					return 1;
				}else{
					return 0;
				}
			}
			
}	


function eliminarTodoAutorizacionEntradas($idEmpleado,$idnomp,$diacompleto){


			if ($diacompleto==1) {

	
				$sql1="DELETE FROM temp_registroentradas  
				 		 	where diacompleto='1' and idempleado=$idEmpleado and idnomp=$idnomp";

						if($this->query($sql1)) {
								return 1;
							}else{
								return 0;
							}

			}else if ($diacompleto==2) {

			  	$sql="DELETE FROM temp_registroentradas  
				 		 	     where diacompleto='2' and idempleado=$idEmpleado and idnomp=$idnomp;" 
							 					    	;
			 			if($this->query($sql)) {
								return 1;
							}else{
								return 0;
							}
			}else{

				$sql="TRUNCATE temp_registroentradas";
				 					    	
			 	if($this->query($sql)) {
					return 1;
				}else{
					return 0;
				}
			}
}	 


	function nominasActivas(){
		$sql = $this->queryarray("select c.idtipop,np.idtipop,np.fechainicio,np.fechafin,np.idnomp from nomi_nominasperiodo np inner join nomi_configuracion c on   c.idtipop=np.idtipop where autorizado=0 limit 1");
		return $sql;
	}

	function cargaPeriodo($idtipop){
		$sql = $this->query("select * from nomi_nominasperiodo where idtipop='$idtipop'");
		return $sql;
	}

	function cargaPeriodoD($tipo, $numnomina){
		

		if($tipo=='*'){
			$filtro='';
		}else{
			$filtro="and idtipop='$tipo'";
		}
		if($numnomina==''){
			$filtro2='';
		}else{
			$filtro2="and fechainicio > (select fechainicio from nomi_nominasperiodo where idnomp ='$numnomina' ) ";
		}
		
		 $sql = $this->queryarray("select * from nomi_nominasperiodo where (1=1) ".$filtro.$filtro2."  order by autorizado asc,fechainicio asc;");

		if($sql['total']>0){
			$JSON=array('success'=>1, 'data'=>$sql['rows']);
		}else{
			$JSON=array('success'=>0);
		}
		echo json_encode($JSON);
	}



	function listadoHoras($vali,$input, $idempleado, $idtipop, $idnomp){

		$horas = explode("_", $input);
		
		$id=$horas[0];
		$col=$horas[1];
		$colMod='';

		if($col==1){
			$colMod='horaentrada';
		}
		if($col==2){
			$colMod='iniciocomida';
		}
		if($col==3){
			$colMod='fincomida';
		}
		if($col==4){
			$colMod='horasalida';
		}

		$sql = "INSERT INTO temp_registroentradas (idregistroentrada, horaentrada, iniciocomida, fincomida, horasalida, idempleado, fecha, dia, idnomp,diacompleto)
				SELECT idregistro, horaentrada,iniciocomida, fincomida, horasalida, idempleado, fecha, dia, idnomp,2
				FROM nomi_registro_entradas where idregistro = '$id'
				AND NOT EXISTS
					(select  1 as Existe from temp_registroentradas  where idregistroentrada='$id') ;";

		if ($vali=='') {
		    
			$sql.="UPDATE temp_registroentradas set ".$colMod."=NULL where idregistroentrada='$id';";
			
		}else{

		    $sql.="UPDATE temp_registroentradas set ".$colMod."='$vali' where idregistroentrada='$id';";
		}
		//echo $sql;
		if($this->multi_query($sql)){
			while ($this->connection->next_result()) {;}
			$this->transaccion('Actualizacion entradas checador',$sql);
			return 1;
		}else{
			return 0;
		}
	}

	function tipoperiodo(){
		$sql = $this->query("select * from nomi_tiposdeperiodos where activo=1 order by nombre asc");
		return $sql;
	}

	function incidenciasfiltro(){

		$sql=$this->query("select idtipoincidencia,clave,nombre from nomi_tipoincidencias");
		return $sql;
	}

        /// R E P O R T E   DE   S O B R E R E C I B O/(P R E N O M I N A)
	function cargaEncabezadosPercepcionesFiltros($idtipop, $idnomp, $idEmpleado){

		if($idtipop != '*'){
			$filtroperiodo = " AND np.idtipop = $idtipop";
		}

		if($idnomp != '*'){
			$filtronomina = " AND cap.idnomp = $idnomp";
		}

		if($idEmpleado != '*'){
			$filtroEmpleados = " AND em.idEmpleado = $idEmpleado";
		}

  
		$sqlx="select distinct cp.idConcepto, c.Descripcion, c.idtipo
		From nomi_conf_prenomina cp Inner Join nomi_calculo_prenomina cap on cap.idconfpre = cp.idConcepto
		Inner Join nomi_conceptos c on cp.idConcepto =c.idConcepto 
		Inner join nomi_empleados em
		on em.idEmpleado=cap.idEmpleado
		Inner Join nomi_nominasperiodo np
		on np.idnomp = cap.idnomp
		Where (1=1)".$filtroperiodo.$filtronomina.$filtroEmpleados." order by cp.idConcepto";
		$sql = $this->query($sqlx);
		return $sql;
	}

	function cargaPercepcionesFiltros($idtipop,$idnomp,$idEmpleado){

//echo "Periodo: $idtipop, Nomina: $idnomp, Empleado: $idEmpleado, codigo: $codigode, codigodos: $codigoal";

		if($idEmpleado!='')
		{

			$sql = $this->query("call traerDatosCalculoPrenomina ('$idtipop','$idnomp','$idEmpleado')");
		}
		return $sql;
	}

	function infoEmpresa(){
		$sql=$this->query("select * from organizaciones;");
		if($sql->num_rows>0){
			return $sql->fetch_array();
		}else{
			0;
		}
	}
	function logo()
	{
		$myQuery = "SELECT logoempresa FROM organizaciones WHERE idorganizacion=1";
		$logo = $this->query($myQuery);
		$logo = $logo->fetch_assoc();
		return $logo['logoempresa'];
	}

	//R E P O R T E    D E   P R E N O M I N A
	function cargaPerceFiltros($idtipop,$idnomp,$idEmpleado,$codigode,$codigoal,$origen, $group){
		
		$filtroperiodo="";
		$filtrorigen="";
		$filtrogroup ="";

		//echo "Periodo: $idtipop, Nomina: $idnomp, Empleado: $idEmpleado, codigo: $codigode, codigodos: $codigoal";

		if($group){
			$filtrogroup = " group by e.idempleado, cp.idnomp, np.idtipop,cp.origen ";
		}

		if($idtipop != '*' &&  $idtipop != ''){
			$filtroperiodo = " AND np.idtipop = $idtipop";
		}

		if($idnomp != '*' && $idnomp != ''){
			$filtronomina = " AND cp.idnomp = $idnomp";
		}

		if($idEmpleado != '*' && $idEmpleado != ''  ){
			$filtroEmpleados = " AND cp.idEmpleado = $idEmpleado";
		}

		if ($origen!='') {
			$filtrorigen=" AND cp.origen=$origen";
		}

		$sqlY="SELECT IFNULL(cp.salario,e.salario)as salario,cp.diasvac,cp.diasfestivo,cp.diaslabproporcion,
			CASE c.idtipo
				WHEN 1 THEN cp.importe
			ELSE 0 
					END AS percepciones,
			CASE c.idtipo
				WHEN 2 THEN cp.importe
			ELSE 0 
					END AS deducciones,
			CASE cp.origen
				WHEN 0 THEN  'Prenomina' 
				WHEN 1 THEN  'Aguinaldo'
				WHEN 2 THEN  'Finiquito'
				WHEN 3 THEN  'PTU'
			ELSE '' 
			END AS origendes,e.idempleado,e.codigo,e.idDep,e.apellidoPaterno,e.apellidoMaterno,e.nombreEmpleado,e.idtipop,e.nss,e.rfc,e.curp,e.idturno,cp.idEmpleado,cp.idnomp,cp.idconfpre,cp.importe,cp.origen,cp.diaspagados AS pagadosdias,cp.diaslaborados,cp.aplicarecibo,c.idconcepto,c.concepto,c.descripcion,c.idtipo,c.activo,np.idnomp,np.idtipop,np.numnomina,np.fechainicio,np.fechafin,np.autorizado,tp.idtipop,tp.nombre,nt.idturno,nt.horas,nt.idjornada
			FROM nomi_empleados e
			LEFT JOIN nomi_calculo_prenomina cp
			ON  e.idempleado=cp.idEmpleado
			INNER JOIN nomi_conceptos c
			ON cp.idconfpre=c.idconcepto
			INNER JOIN nomi_nominasperiodo np
			ON cp.idnomp=np.idnomp
			INNER JOIN nomi_tiposdeperiodos tp
			ON tp.idtipop=np.idtipop
			LEFT JOIN nomi_turno nt
			ON   e.idturno=nt.idturno
			WHERE (c.idtipo=1 || c.idtipo=4) and cp.aplicarecibo = 1";

		if ($codigode!='' && $codigoal!='')
		{  
			
			$sql = $this->query("$sqlY and e.idEmpleado between $codigode and $codigoal".$filtroperiodo.$filtronomina. " AND cp.aplicarecibo=1 ".$filtrogroup." order by e.nombreEmpleado asc,np.numnomina asc,e.idempleado asc");
		}

		else 
			if ($origen!='*') {

				$sql = $this->query("$sqlY".$filtroEmpleados.$filtroperiodo.$filtronomina.$filtrorigen.$filtrogroup."  order by  e.nombreEmpleado asc,e.idempleado asc, np.idtipop asc, cp.idnomp asc");	 
			}
			
			else{
				if($idtipop!=''){

					$sql = $this->query("$sqlY".$filtroEmpleados.$filtroperiodo.$filtronomina." AND cp.aplicarecibo=1 ".$filtrogroup." order by  e.nombreEmpleado asc,cp.origen asc,e.idempleado asc");
				}
			}
			return  $sql;
		}



		function cargarcodigo($codigouno){
		
		 $sql = $this->queryarray("select * from nomi_empleados where salario > 0 and idEmpleado >= $codigouno order by idEmpleado asc;");

			if($sql['total']>0){
				$JSON=array('success'=>1, 'data'=>$sql['rows']);
			}else{
				$JSON=array('success'=>0);
			}
			echo json_encode($JSON);
		}




		// T E R M I N A   R E P O R T E   D E  P R E N O M I N A

		function cargaDeduccionFiltros($idtipop,$idnomp,$idEmpleado,$origen){

            //echo "Periodo: $idtipop, Nomina: $idnomp, Empleado: $idEmpleado, codigo: $codigode, codigodos: $codigoal";

			if($idtipop != '*'){
				$filtroperiodo = " AND np.idtipop = $idtipop";
			}

			if($idnomp != '*'){
				$filtronomina = " AND cp.idnomp = $idnomp";
			}

			if($idEmpleado != '*'){
				$filtroEmpleados = " AND cp.idEmpleado = $idEmpleado";
			}
	
			 $sqlY="SELECT cp.diasvac,cp.diasfestivo,cp.diaslabproporcion,e.idempleado,e.codigo,e.apellidoPaterno,e.apellidoMaterno,e.nombreEmpleado,e.salario,e.idtipop,e.nss,e.rfc,e.curp,cp.idEmpleado,cp.idnomp,cp.idconfpre,cp.importe,cp.origen,cp.diaspagados,cp.aplicarecibo,c.idconcepto,c.concepto,c.descripcion,c.idtipo,c.activo,np.idnomp,np.idtipop,np.numnomina,np.fechainicio,np.fechafin,np.autorizado,tp.idtipop,tp.nombre
			from nomi_empleados e
			INNER JOIN nomi_calculo_prenomina cp
			ON  e.idempleado=cp.idEmpleado
			INNER JOIN nomi_conceptos c
			ON cp.idconfpre=c.idconcepto
			INNER JOIN nomi_nominasperiodo np
			ON cp.idnomp=np.idnomp
			INNER JOIN nomi_tiposdeperiodos tp
			ON tp.idtipop=np.idtipop  
			WHERE  cp.aplicarecibo = 1 and c.idtipo=2";

			if ($idtipop=='*' or $idtipop=='3') {

				$sql = $this->query("$sqlY".$filtroperiodo.$filtronomina.$filtroEmpleados." AND origen=$origen order by np.idnomp asc,e.apellidoPaterno asc");
	
			}else{

				$sql = $this->query("$sqlY".$filtroperiodo.$filtronomina.$filtroEmpleados." ORDER BY np.idnomp asc,e.apellidoPaterno asc");

			}
			return  $sql;	
		}

//T E R M I N A  T O D O  D E  R E P O R T E   P R E N O M I N A

	function incidencias($fechaini,$fechafin,$nomEmple,$incidencia,$periodos,$nominas){

			$filtroEmpleado      = "";
			$filtroperiodo       = "";
			$filtronomina        = "";
			$filtroEmpleados     = "";
			$filtroIncidencia    = "";
	
			//echo "Periodo: $periodos, Nomina: $nominas, Empleado: $nomEmple,Incidencia:$incidencia ";

			if($nomEmple != '' && $nomEmple != '*'){
				$filtroEmpleado = "AND em.idEmpleado = $nomEmple";
			}

			if($periodos != '*'){
				$filtroperiodo = "AND pn.idtipop = $periodos";
			}

			if($nominas != '*'){
				$filtronomina = "AND ci.idnomp = $nominas";
			}

			if ($incidencia!='*') {
				$filtroIncidencia =	"AND ti.idtipoincidencia=$incidencia";
			}

			$sqlgeneralinci="SELECT CONCAT(em.nombreEmpleado, ' ', em.apellidoPaterno,' ',em.apellidoMaterno)AS nombreCompleto,case ci.autorizado when 0 then 'Activa' when 1 then 'Aplicada' end as autorizadoletras, np.idnomp, np.autorizado, np.numnomina, np.fechainicio, np.fechafin, pn.nombre as nom, pn.idtipop, em.idtipop, em.idEmpleado, ti.idtipoincidencia, ti.nombre, ci.idempleado, ci.fechaseleccion, ci.idtipoincidencia, ci.idnomp, ci.autorizado, em.nombreEmpleado, em.apellidoPaterno, em.apellidoMaterno, 0 as 'DiasAutorizados', ci.fechaseleccion as fechafinal, ci.idsobrecibo, ci.sobrerecibo from nomi_claveincidencias as ci left join nomi_empleados as em on em.idEmpleado=ci.idempleado left join nomi_tiposdeperiodos as pn on pn.idtipop=em.idtipop left join nomi_nominasperiodo as np on np.idnomp=ci.idnomp left join nomi_tipoincidencias as ti on ci.idtipoincidencia=ti.idtipoincidencia where ci.sobrerecibo in(0,1,2)";

			if($fechaini!='' && $fechafin!=''){

				$sql = $this->queryarray("$sqlgeneralinci and ci.fechaseleccion between '$fechaini'     and '$fechafin' $filtroEmpleado $filtroIncidencia");

			} else {

				$sql = $this->queryarray("$sqlgeneralinci $filtroperiodo $filtronomina $filtroEmpleado  $filtroIncidencia");

			}
			

			
			if($sql['total']>0){
				$JSON=array('success'=>1, 'data'=>$sql['rows'],'fechainicial'=>$fechaini,'fechafinal'=>$fechafin);
			}else{
				$JSON=array('success'=>0);
			}
				echo json_encode($JSON); 
		}

	



		/*R E P O R T E   D E   A C U M U L A D O*/

		function reporteAcumulado($nomEmple,$periodos,$nominas,$nominados,$origen){

			$filtroEmpleado    = "";
			$filtroperiodo     = "";
			$filtronomina      = "";
			$filtronominados   = "";
			$filtrorigen       = "";

			// echo "Periodo: $periodos, Nomina: $nominas, Nominados: $nominados, Empleado: $nomEmple,origen:$origen";

			if($nomEmple != '*'){
				$filtroEmpleado = "AND em.idEmpleado = $nomEmple";
			}

			if($periodos != '*'){
				$filtroperiodo = "AND np.idtipop = $periodos";
			}

			if($nominas != '*'){
				$filtronomina = "between $nominas";
			}

			if($nominados != '*'){
				$filtronominados = "AND $nominados";
			}

			if ($origen!='*') {
				$filtrorigen="AND cp.origen=$origen";
			}
/*aqui subsidio queda dentro de percepciones porq se suma a estas
 *  aunq en realidad es otro pago ver si mas adelante 
 * lo cambiamos o se queda a si
 */
			$sqlgeneral = "SELECT CASE c.idtipo
			WHEN 1 THEN cp.importe
			WHEN 4 THEN cp.importe
			ELSE 0 
			END AS percepciones,
			CASE c.idtipo
			WHEN 2 THEN cp.importe
			ELSE 0 
			END AS deducciones,
			CASE cp.origen
			when 0 THEN  'prenomina' 
			when 1 THEN  'aguinaldo'
			when 2 then  'finiquito'
			when 3 then  'PTU'
			else '' End as origen,
				cp.idEmpleado,cp.idcal,cp.idnomp,cp.idconfpre,c.idconcepto,c.idtipo,c.descripcion,cp.importe,cp.gravado,cp.exento,cp.idNominatimbre,cp.diaspagados,cp.diaslaborados,cp.valordias,cp.aplicarecibo,tp.idtipop,tp.fechainicio as fa,tp.nombre,np.idnomp,np.idtipop,np.numnomina,np.fechainicio,np.fechafin,em.apellidoPaterno,em.apellidoMaterno,em.nombreEmpleado,em.idtipop,em.idEmpleado,em.nss,em.rfc,em.curp
			FROM nomi_calculo_prenomina  cp 
			inner JOIN
			nomi_nominasperiodo np
			ON np.idnomp=cp.idnomp
			INNER JOIN nomi_empleados em
			ON cp.idEmpleado=em.idEmpleado
			INNER JOIN 
			nomi_tiposdeperiodos tp
			ON np.idtipop=tp.idtipop
			inner JOIN nomi_conceptos c
			ON c.idconcepto=cp.idconfpre";



			if($periodos!='' && $periodos!='3')
			{
				
				$sql = $this->query("$sqlgeneral where 1=1  $filtroperiodo and  np.idnomp   $filtronomina   $filtronominados $filtroEmpleado  $filtrorigen ORDER BY  cp.idEmpleado ASC,cp.idconfpre ASC ");
			}

			else if ($periodos=='3' && $nominas != '*') {
				
				$sql = $this->query("$sqlgeneral where 1=1  $filtroperiodo and  np.idnomp   and $nominas
					$filtroEmpleado  $filtrorigen ORDER BY  cp.idEmpleado ASC,cp.idconfpre ASC ");
				
			}

			else if ($periodos=='3' && $nominas == '*') {
				$sql = $this->query("$sqlgeneral where 1=1  $filtroperiodo and  np.idnomp   $filtronomina   $filtronominados $filtroEmpleado  $filtrorigen ORDER BY  cp.idEmpleado ASC,cp.idconfpre ASC ");
				
			}
			return $sql;
		}

		/*T E R M I N A   R E P O R T E   A C U M U L A D O */


// R E S U M  E N   A N A L I T I C O   P O R   D E P A R T A M E N T O 
function departamentos(){

$sql = $this->query("SELECT * FROM nomi_departamento");

return $sql;
}


function resumenAnaliticoDep($periodo,$nominauno,$nominados,$depart,$nomi){


// echo "string";
//echo " depa: $depart, Periodo: $periodo,nomiprincipal:$nomi, Nomina: $nominauno, Nominados: $nominados";

$filtroperiodo     = "";
$filtronomina      = "";
$filtronom         = "";
$filtrodep         = "";
$filtronominauno   = "";

if($depart != '*'){
$filtrodep = "AND e.idDep in($depart)";
}

if($periodo != '*'){
$filtroperiodo = "AND tp.idtipop = $periodo";
}

if($nomi != '*'){
$filtronom = "and cp.idnomp in($nomi)";
}


if($nominas != '*'){
$filtronomina = "between $nominauno";
}

if($nominados != '*'){
$filtronominados = "AND $nominados";
}


	$sqlgeneral ="SELECT *,
  (case when (perce>deduc) then (perce-deduc) else deduc-perce end)as total
	  	from (
        	SELECT IFNULL( SUM(CASE WHEN c.idTipo in(1,4) THEN cp.importe ELSE 0 END),0) as perce,
       	IFNULL( SUM(CASE WHEN c.idTipo in(2)  THEN cp.importe ELSE 0 END),0) as deduc,
 	   	c.idconcepto,c.concepto,c.descripcion,c.idtipo,e.apellidoPaterno,
 	   	e.apellidoMaterno,e.nombreEmpleado,e.idtipop,
        	cp.idEmpleado,cp.idconfpre,cp.importe,cp.idnomp,dep.idDep,dep.nombre,np.numnomina,np.fechainicio,np.fechafin,tp.nombre as nameperiodo
         from nomi_empleados e
        	left join nomi_calculo_prenomina cp
       	on e.idEmpleado=cp.idEmpleado
       	inner join nomi_departamento dep
        	on dep.idDep=e.idDep 
        	inner join nomi_nominasperiodo np 
        	on  np.idnomp=cp.idnomp
 	   	left join nomi_tiposdeperiodos tp
	  	on  tp.idtipop=np.idtipop and e.idtipop=tp.idtipop
 	  	inner join nomi_conceptos c
	  	on cp.idConfpre=c.idconcepto
      	where 1=1 $filtrodep $filtroperiodo $filtronom 
      	group by dep.idDep,cp.idconfpre order by dep.idDep,c.descripcion)tab";
if($depart!='')
{
	
$sql = $this->query("$sqlgeneral;");
}

return $sql;
}


//R E P O R T E   P R E N O M I N A  D E T A L L A D O 

function reportePrenominaDetallado($nomEmple,$periodos,$nominas){

	$filtroEmpleado    = "";
	$filtroperiodo     = "";
	$filtronomina      = "";


    //echo "Periodo: $periodos, Nomina: $nominas, Empleado: $nomEmple";

	if($nomEmple != '*'){
		
		$filtroEmpleado = "AND idEmpleado = $nomEmple";
	}

	if($periodos != '*'){
		$filtroperiodo = "where idtipop = $periodos";
	}

	if($nominas != '*'){
		$filtronomina = "AND idnomp  =$nominas";
	}

/*para no mover todo la consulta solo manipulare la vista para el tiempo extra el tiempo - y + de esta consulta no estan bien porq no va con horario*/

	$sqlgeneral ="SELECT *,idnomp,idconfpre,idEmpleado, 
			(CASE WHEN (base) then (base+entregado-retenido-imss+primavacacional+vacaciones) else 0 end) AS neto, 
			(CASE when (sueldoneto) then(sueldoneto-infonavit)else 0 end) suelinfon,			
			(CASE WHEN (minutosdeben>0) then ((salarioHora/60)*(minutosdeben)) else 0 end)as totalarestar,	 
			(CASE WHEN  (minutosextras>0) then ((salarioHora/60)*(minutosextras)) else 0 end)as tiempoextra,		
           (CASE WHEN (minutosextras>0) then ((base+entregado-retenido-imss+primavacacional+vacaciones-infonavit)+((salarioHora/60)*(minutosextras))) 
            else ((base+entregado-retenido-imss+primavacacional+vacaciones-infonavit)-(salarioHora/60)*(minutosdeben)) end)as totalfinal
			
			from (SELECT cp.idconfpre,cp.idEmpleado,				
		    (SELECT IFNULL((  select COUNT(re.idnomp)  from 
nomi_registro_entradas re 
left join nomi_empleados e on e.idEmpleado = re.idEmpleado
left join nomi_horarios_empleado_detalle h on h.idhorario = e.idhorario 
where e.idEmpleado=cp.idEmpleado and re.idnomp=cp.idnomp  and re.dia = h.dia and h.opcional=0
 ),0)) as DiasChe,
		    (SELECT IFNULL(sum(CASE WHEN idconfpre in(101) THEN importe ELSE 0 END),0)) as sueldo,  	  
		    (SELECT IFNULL(SUM(CASE WHEN idconfpre in(103) THEN importe ELSE 0 END),0)) as primaAsistencia, 
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(104) THEN importe ELSE 0 END),0)) as puntualidad,  
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(110) THEN valorneto ELSE 0 END),0)) as ispt, 
	 		(SELECT IFNULL(SUM(CASE WHEN idconfpre in(105) THEN valorneto ELSE 0 END),0)) as subsid,  
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(110) and aplicarecibo=1 THEN importe ELSE 0 END),0)) as retenido,  
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(105) and aplicarecibo=1 THEN importe ELSE 0 END),0)) as entregado,  
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(109)  THEN importe ELSE 0 END),0)) as imss, 
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(107)  THEN importe ELSE 0 END),0)) as primavacacional, 
			(SELECT IFNULL(SUM(CASE WHEN idconfpre in(18) THEN importe ELSE 0 END),0)) as vacaciones, 
			IFNULL(cp.diasvac,0) as diasvacaciones,
			IFNULL(e.salario/nt.horasdetalle,0) as salarioHora,
			IFNULL(cp.montoinfonavit,0) as infonavit,
			
			 (select (CASE e.activo WHEN 2 then hi.fecha else '' END)  from nomi_historial_empleado hi where hi.idEmpleado=e.idEmpleado and hi.tipo=2
         	order by  hi.fecha asc limit 1)as fechabaja,

			SUM(case when cp.idconfpre in(101,103,104)  then cp.importe else 0 end) as base,
			 cp.salario,cp.sueldoneto,e.codigo,e.fechaAlta,e.apellidoPaterno,e.apellidoMaterno,e.activo,
			 e.nombreEmpleado,e.nss,e.rfc,e.curp,e.idtipop,cp.sdi,cp.idnomp,
			 diferencias.minutosdemas, diferencias.minutosdemenos,nt.horasdetalle as horas,
			 minutosdemas as minutosextras,
			minutosdemenos as minutosdeben
			 from nomi_empleados e 
				 right join nomi_calculo_prenomina cp        
				 on e.idEmpleado=cp.idEmpleado
				 left join nomi_nominasperiodo np 
				 on np.idnomp=cp.idnomp 
				 left join nomi_tiposdeperiodos tp   
				 on tp.idtipop=np.idtipop 
				 left join nomi_turno nt 
				 on nt.idturno=e.idturno 
left join (
	select 	
		sum(case when a.minutosdiferenciaentrada >0 then a.minutosdiferenciaentrada else 0 end ) + 	
	   		sum(case when minutosdiferenciacomida >0 then minutosdiferenciacomida else 0 end ) + 
	   		sum(case when minutosdiferenciasalida >0 then minutosdiferenciasalida else 0 end ) as minutosdemas	,
	   (sum(case when minutosdiferenciaentrada <0 then minutosdiferenciaentrada else 0 end ) + 	
	   		sum(case when minutosdiferenciacomida <0 then minutosdiferenciacomida else 0 end ) + 
	   		sum(case when minutosdiferenciasalida <0 then minutosdiferenciasalida else 0 end )) * (-1) as minutosdemenos,a.idEmpleado, a.idnomp	
	From(
	
		select 
		TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.horaentrada), datetime ), CONVERT( CONCAT(re.fecha,' ', he.horaentrada ), datetime ))  as minutosdiferenciaentrada ,
			(TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha, ' ', re.fincomida ), datetime )) - he.mincomida) * (-1) as minutosdiferenciacomida,	 
	TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,he.horasalida ), datetime ), CONVERT( CONCAT(re.fecha,' ', re.horasalida ), datetime )) as minutosdiferenciasalida ,
		re.horaentrada entradachecador, he.horaentrada entradahorario,	re.horasalida salidachecador, he.horasalida salidahorario ,re.iniciocomida ,re.fincomida,re.idEmpleado,re.dia,he.mincomida,re.idnomp
		 from nomi_registro_entradas re 
		inner join nomi_empleados em on re.idEmpleado = em.idempleado
		inner join nomi_horarios_empleado_detalle he on he.idhorario = em.idhorario and he.dia = re.dia
		where re.idnomp  =$nominas
		
	) as a	group by a.idEmpleado, a.idnomp
)as diferencias on diferencias.idempleado = cp.idempleado and diferencias.idnomp = cp.idnomp
where cp.idconfpre in (101,103,104,110,105,109,107,18,111)  group by cp.idnomp,cp.idEmpleado order by cp.idEmpleado) tab";
if($nominas!='')
{
	
$sql = $this->query("$sqlgeneral  $filtroperiodo  $filtronomina  $filtroEmpleado order by nombreEmpleado asc ;");
}

return $sql;
}

//funcion nomActiperioSelecc sirve para verificar la nomina activa del periodo que busque
function nomActiperioSelecc($idtipop){

		$myQuery = "select idnomp from nomi_nominasperiodo where idtipop='$idtipop' and autorizado='0' limit 1;";
		$idnompActivasele = $this->query($myQuery);
		$idnompActivasele = $idnompActivasele->fetch_assoc();
		return $idnompActivasele['idnomp'];
}

function sumasConceptos($nomEmple,$periodos,$nominas){
	$filtroEmpleado    = "";
	$filtroperiodo     = "";
	$filtronomina      = "";

	if($nomEmple != '*'){
	$filtroEmpleado = "AND cp.idEmpleado = $nomEmple";
	}

	if($periodos != '*'){
	$filtroperiodo = "AND tp.idtipop = $periodos";
	}

	if($nominas != '*'){
	$filtronomina = "AND n.idnomp  =$nominas";
	}

	$sqlgeneral ="	
 			SELECT  sum(cp.importe) as importe, c.concepto, c.descripcion 
		 	 from nomi_conceptos c 
		 	 left join nomi_calculo_prenomina cp
		 	 on  cp.idconfpre=c.idconcepto
		 	 inner join  nomi_nominasperiodo n
		 	 on cp.idnomp=n.idnomp	 
		 	 inner join nomi_tiposdeperiodos tp 
		 	 on  n.idtipop=tp.idtipop
		 	 inner join nomi_empleados em
		 	 on cp.idEmpleado=em.idEmpleado
		 	 where  cp.idconfpre not in (101,103,104,110,105,109,107,18)";

	if($nominas!='')
	{          
	$sql = $this->query("$sqlgeneral  $filtroperiodo  $filtronomina $filtroEmpleado   group by c.concepto;");
	
	}
	return $sql;

}
/*este reporte se basa en el idagrupador del sat para saber de q estoy hablando si algo falla primero ver que hayan agrupado bien el id del sat*/
function sumasConceptosGlobal($ano,$periodos,$mes,$opc){
	// SELECT  sum(cp.importe) as importe, c.concepto, c.descripcion 
		 	 // from nomi_conceptos c 
		 	 // left join nomi_calculo_prenomina cp
		 	 // on  cp.idconfpre=c.idconcepto
		 	 // inner join  nomi_nominasperiodo n
		 	 // on cp.idnomp=n.idnomp	 
		 	 // inner join nomi_tiposdeperiodos tp 
		 	 // on  n.idtipop=tp.idtipop
		 	 // inner join nomi_empleados em
		 	 // on cp.idEmpleado=em.idEmpleado
		 	 // where  cp.idconfpre not in (101,103,104,110,105,109,107,18)
		 	 // and (  month(n.fechainicio) in ($mes) )
		 	 // and (  year(n.fechainicio) in ($ano) )
		 	 // $period
		 	 // group by c.concepto;
	$period = $in = "";
	if($periodos != 0){
		$period = "and ( tp.idtipop in ($periodos))";
	}
	if($opc == 1){// 1 extras y basicos
		$in = "not";
	}
	
	$sql = $this->query("SELECT  sum(cp.importe) as importe, con.idAgrupador,con.idtipo, c.descripcion,con.concepto 
		 from nomi_conceptos c 
		 	 left join nomi_calculo_prenomina cp on  cp.idconfpre=c.idconcepto and cp.aplicarecibo=1
		 	 inner join  nomi_nominasperiodo n on cp.idnomp=n.idnomp	 
		 	 inner join nomi_tiposdeperiodos tp on  n.idtipop=tp.idtipop
		 	 inner join nomi_empleados em  on cp.idEmpleado=em.idEmpleado
		 	 INNER JOIN nomi_conceptos con ON con.idconcepto=cp.idconfpre 
			 LEFT JOIN nomi_percepciones p ON p.idAgrupador=con.idAgrupador
			 LEFT JOIN nomi_deducciones d ON d.idAgrupador=con.idAgrupador
			 LEFT JOIN nomi_otros_pagos o ON o.idAgrupador=con.idAgrupador

		 where  (  month(n.fechainicio) in ($mes) )and (  year(n.fechainicio) in ($ano) ) $period and
		 	(CASE
			con.idtipo 
			WHEN 1 
			THEN
			(p.idAgrupador=con.idAgrupador OR con.idAgrupador=0 ) and p.idAgrupador $in in (1,8,18,42 )
			WHEN 2 
			THEN 
			(d.idAgrupador=con.idAgrupador OR con.idAgrupador=0 ) and d.idAgrupador $in in (1,2 )
			WHEN  4 
			THEN (o.idAgrupador=con.idAgrupador OR con.idAgrupador=0)  and o.idAgrupador $in  in (2 )
			ELSE con.idAgrupador=0 END) 		 	
		 group by c.concepto; ");
	if($sql->num_rows>0){
		return $sql;
	}else{
		return 0;
	}
}
function tabladetallepre( $nomEmple,$periodos,$nominas){


$filtroEmpleado    = "";
$filtroperiodo     = "";
$filtronomina      = "";

if($nomEmple != '*'){
$filtroEmpleado = "AND cp.idEmpleado = $nomEmple";
}

if($periodos != '*'){
$filtroperiodo = "AND tp.idtipop = $periodos";
}

if($nominas != '*'){
$filtronomina = "AND n.idnomp  =$nominas";
}


$sqlgeneral ="	
 SELECT cp.idEmpleado,cp.idnomp,cp.idconfpre,cp.importe,c.idconcepto,c.concepto,c.descripcion,c.idtipo,tp.idtipop,tp.nombre,em.idEmpleado,em.nombreEmpleado
 	 from nomi_calculo_prenomina cp
 	 inner join nomi_conceptos c
 	 on  cp.idconfpre=c.idconcepto
 	 inner join  nomi_nominasperiodo n
 	 on cp.idnomp=n.idnomp	 
 	 inner join nomi_tiposdeperiodos tp 
 	 on  n.idtipop=tp.idtipop
 	 inner join nomi_empleados em
 	 on cp.idEmpleado=em.idEmpleado
 	 where  cp.idconfpre not in (101,103,104,110,105,109,107,18) 
 	 ";

if($nominas!='')
{        
//echo   "$sqlgeneral $filtroperiodo $filtronomina $filtroEmpleado";
	$sql = $this->query("$sqlgeneral $filtroperiodo $filtronomina $filtroEmpleado;");
	
	}
	return $sql;
}

function infoRegPatronalRecibo(){
		$sql=$this->query("select r.registro from nomi_registropatronal r,nomi_configuracion c
 				where c.idregistrop=r.idregistrop and r.activo=-1;");
		if($sql->num_rows>0){
			return $sql->fetch_array();
		}else{
			0;
		}
	}
function eliminarHorario($idregistro){
	$sql = "delete from nomi_registro_entradas where idregistro=".$idregistro;
	if($this->query($sql)){
		return 1;
	}else{
		return 0;
	}
}
function fechasNominaActivaxperiodo($idtipop){
	$filtro = "";
	if($idtipop!='*' && $idtipop){
		$filtro ="and np.idtipop=$idtipop";
	}
	$sql = $this->query("select np.fechainicio,np.fechafin,np.idnomp,np.idtipop as periodotipo,tp.nombre 
		from nomi_nominasperiodo np 
		inner join nomi_tiposdeperiodos tp
			on  np.idtipop=tp.idtipop
			where autorizado=0  
			$filtro limit 1");
	if($sql->num_rows>0){
		return $sql->fetch_object();
	}else{
		return 0;
	}
}
function listaEmpleadosPeriodo($idtipop){

		return $this->query("select e.idEmpleado,e.codigo,e.fechaAlta,e.apellidoPaterno,e.apellidoMaterno,e.nombreEmpleado,e.salario,e.salario,e.idFormapago,e.email,e.nss,e.idEstadoCivil,e.idsexo,
		e.idestado,e.idmunicipio,e.rfc,e.curp,e.idestadosat,e.cp,e.numeroCuenta,e.claveinterbancaria,e.activo,e.idtipocontrato,e.idtipop,e.idbase,e.sbcfija,e.idDep,e.idPuesto,e.idtipoempleado,e.idbasepago,e.idturno,e.idregimencontrato,e.fonacot,e.afore,e.idregistrop
			 from nomi_empleados e  where idtipop=$idtipop");
	}

function listaEmpleadosactivo(){

		return $this->query("SELECT np.numnomina,np.idnomp,e.idEmpleado,e.nombreEmpleado,e.apellidoPaterno,e.idtipop from nomi_empleados e
			inner join  nomi_configuracion c on e.idtipop=c.idtipop inner join nomi_nominasperiodo np on e.idtipop=np.idtipop where np.autorizado=0  and e.activo!=2 and e.salario>0 group by e.idEmpleado");
	}

function verTE($idempleado,$idnomp){
	$sql = $this->query("select * from nomi_tiempoextra_detalle where idnomp=$idnomp and idEmpleado=$idempleado");
	return $sql;
}

function tiempoextradeta($nomina,$periodo,$minacumula,$mincuenta,$acumuladosemanal){
		
		$sql = $this->query("select concat(e.nombreEmpleado,' ',e.apellidoPaterno,' ',e.apellidoMaterno) as nombreempleado,t.horasdetalle,
				
				0 as minutosdiferenciaentrada,
				re.horaentrada entradachecador, d.horaentrada entradahorario,0 as minutosdiferenciasalida,
				
				re.horasalida salidachecador, d.horasalida salidahorario ,
				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha,' ', re.fincomida ), datetime )) as minitostomados,
				0 as minutosdiferenciacomida,
				re.iniciocomida ,re.fincomida,re.idEmpleado,re.dia,
				
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
 					(select s.nuevoSalario from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1) 
 				ELSE 
 					e.salario END ) salario,
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
						(select s.nuevoSDI from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1)
 				ELSE e.sbcfija END ) sdi,
 				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.horaentrada), datetime ), CONVERT( CONCAT(re.fecha,' ', d.horasalida ), datetime )) as minutosdeldiaopcional,
 	
 	/* se debe restar si comieron al tiempo completo de entrada a salida	 */	
 		(	CASE WHEN 	
 				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.horaentrada), datetime ), CONVERT( CONCAT(re.fecha,' ', d.horasalida ), datetime ))>0
 			THEN
 				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.horaentrada), datetime ), CONVERT( CONCAT(re.fecha,' ', d.horasalida ), datetime ))
 			ELSE 0 END
 				
 				 - 
 			
 			CASE WHEN 	
 				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha,' ', re.fincomida ), datetime )) >0 
 			THEN	
 				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha,' ', re.fincomida ), datetime ))
 			ELSE 0 END
 		)
 				 as totaldiaopcional,re.fecha
				
			from nomi_registro_entradas re 
				inner join nomi_horarios_empleado_detalle d on  d.dia = re.dia and d.opcional=1
			 	inner join nomi_empleados e on e.idhorario= d.idhorario and e.idEmpleado=re.idEmpleado
			 	left join nomi_historico_salarios s on  s.idEmpleado =  e.idEmpleado
				left join nomi_turno t on t.idturno = e.idturno
			where d.dia= re.dia and re.idnomp=$nomina and e.idtipop=$periodo
			
			UNION
/* despues se saca los minutos de mas de los dias normales de horario			 */
			select concat(e.nombreEmpleado,' ',e.apellidoPaterno,' ',e.apellidoMaterno) as nombreempleado,t.horasdetalle,
				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.horaentrada), datetime ), CONVERT( CONCAT(re.fecha,' ', d.horaentrada ), datetime )) as minutosdiferenciaentrada,
				re.horaentrada entradachecador, d.horaentrada entradahorario,
				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,d.horasalida ), datetime ), CONVERT( CONCAT(re.fecha,' ', re.horasalida ), datetime )) as minutosdiferenciasalida,
				re.horasalida salidachecador, d.horasalida salidahorario ,
				TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' ,re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha,' ', re.fincomida ), datetime )) as minitostomados,
				(TIMESTAMPDIFF (MINUTE, CONVERT( CONCAT(re.fecha, ' ' , re.iniciocomida), datetime ), CONVERT( CONCAT(re.fecha, ' ', re.fincomida ), datetime )) - d.mincomida) * (-1) minutosdiferenciacomida ,re.iniciocomida ,re.fincomida,re.idEmpleado,re.dia,
				
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
 					(select s.nuevoSalario from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1) 
 				ELSE 
 					e.salario END ) salario,
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
						(select s.nuevoSDI from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1)
 				ELSE e.sbcfija END ) sdi, 0 minutosdeldiaopcional, 0 totaldiaopcional,re.fecha
				
			from nomi_registro_entradas re 
				inner join nomi_horarios_empleado_detalle d on  d.dia = re.dia and d.opcional=0
			 	inner join nomi_empleados e on e.idhorario= d.idhorario and e.idEmpleado=re.idEmpleado
			 	left join nomi_historico_salarios s on  s.idEmpleado =  e.idEmpleado
				left join nomi_turno t on t.idturno = e.idturno
			where d.dia= re.dia and re.idnomp=$nomina and e.idtipop=$periodo
			order by nombreempleado,fecha
			
		");
		
		
		return $sql;
	}
	function listadoDempleadoparaTEdeta($idnomp,$idtipop){
		$sql = $this->query("select concat(e.nombreEmpleado,' ',e.apellidoPaterno,' ',e.apellidoMaterno) as nombreempleado,t.horasdetalle,
				re.idEmpleado,
				
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
 					(select s.nuevoSalario from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1) 
 				ELSE 
 					e.salario END ) salario,
				(CASE s.idEmpleado WHEN  e.idEmpleado THEN
						(select s.nuevoSDI from nomi_historico_salarios s where s.idEmpleado =  e.idEmpleado  order by s.fechaAplicacion desc limit 1)
 				ELSE e.sbcfija END ) sdi, 0 minutosdeldiaopcional, 0 totaldiaopcional
				
			from nomi_registro_entradas re 
				inner join nomi_horarios_empleado_detalle d on  d.dia = re.dia
			 	inner join nomi_empleados e on e.idhorario= d.idhorario and e.idEmpleado=re.idEmpleado
			 	left join nomi_historico_salarios s on  s.idEmpleado =  e.idEmpleado
				left join nomi_turno t on t.idturno = e.idturno
			where d.dia= re.dia and re.idnomp=$idnomp and e.idtipop=$idtipop 
			group by re.idEmpleado");
		return $sql;
	}

	function reporteTE($idEmpleado,$idnomp){
		$filtro = "";
		if($idEmpleado>0){
			$filtro = " and te.idEmpleado =$idEmpleado";
		}
		$sql = $this->query("select 
			te.*, concat( e.nombreEmpleado,' ',e.apellidoPaterno,' ',e.apellidoMaterno) as empleado ,t.idtipop
			from nomi_tiempoextra_detalle te
			inner join nomi_empleados e on e.idEmpleado = te.idEmpleado 
			inner join nomi_nominasperiodo t on t.idnomp=te.idnomp
			where te.idnomp =$idnomp  $filtro ");
		return $sql;
	}

	
	
	 /* R E P O R T E   D E   V A C A C I O N E S */

	function cargarvacaciones($idtipop,$idEmpleado, $anioselec){

		if($idtipop =='') $idtipop='*';

		if($idEmpleado=='') $idEmpleado='*';
	
		if($anioselec =='') $anioselec='*';
		
		
			$sql = $this->query("call procesovacaciones ('$idtipop','$idEmpleado', '$anioselec')");
			while ($this->connection->next_result()) {;}	
	
		    return $sql;
	

   

	 /*F I N   D E   R E P O R T E   D E   V A C A C I O N E S*/

}



function detalleVacaciones($nomEmple,$periodos,$AnioFecha){

	$filtroEmpleado    = "";
	$filtroperiodo     = "";
	$filtrofecha       = "";

	if($nomEmple != '*'){
		$filtroEmpleado = "v.idEmpleado = $nomEmple";
	}

	if($periodos != '*'){
		$filtroperiodo = "AND e.idtipop = $periodos";
	}


	if($AnioFecha != ''){
		$filtrofecha = "AND extract(Year from v.fechainicial) = $AnioFecha";
	}

	if($AnioFecha != ''){
		$filtrofechafin = "AND extract(Year from v.fechafinal) = $AnioFecha";
	}



	$sqlgeneral ="SELECT *,np.numnomina from nomi_vacaciones_sobrerecibo  v
	inner join nomi_empleados e
	on v.idEmpleado=e.idEmpleado 
	inner join nomi_tiposdeperiodos tp 
 	on e.idtipop=tp.idtipop
 	inner join nomi_nominasperiodo np
 	on np.idnomp=v.idnomp";


	$sql = $this->query("$sqlgeneral Where ($filtroEmpleado $filtroperiodo $filtrofecha) OR ($filtroEmpleado $filtroperiodo 
		$filtrofechafin);");
	return $sql;
}




function cargarEmpleados($tipo, $numnomina){

	if($tipo=='*'){
		$filtro='';
	}else{
		$filtro="and idtipop='$tipo'";
	}

	$sql = $this->queryarray("select * from nomi_empleados where (1=1) ".$filtro." and activo in(-1,3)  and idtipop is not null order by nombreEmpleado asc;");

	if($sql['total']>0){
		$JSON=array('success'=>1, 'data'=>$sql['rows']);
	}else{
		$JSON=array('success'=>0);
	}
	echo json_encode($JSON);
}


	function importinsertVaca($cadena){

		 $caden = explode(",", $cadena);
         $caden[0]; // tipo de captura
         $caden[1]; // clave de incidencia 
         $caden[2]; // fechainicial
         $caden[3]; // fechafinal
         $caden[4]; // fechapago
         $caden[5]; // codigo del empleado
         $caden[6]; // cantidad de dias

		 $existclaveincidencia = $this->query("SELECT clave FROM nomi_tipoincidencias WHERE idconsiderado=3 
		 and clave=$caden[1];");

	     $existEmpleado = $this->query("SELECT idEmpleado FROM nomi_empleados WHERE codigo=$caden[5]");

		if($existclaveincidencia->num_rows>0 && $existEmpleado->num_rows>0 ){
			
		   $sql = "call importarVacaciones ($caden[0],$caden[5],$caden[2],$caden[3],$caden[4],$caden[6],$caden[1]);"; 

	 if(!$this->query($sql)){
	 	return 0;
	 }
	 return 1;
		}else{
			return 0;
		}    
    }
	
	/*reporte resumen global*/
	function anosNominasPeriodo(){
		$sql = $this->query("select year(fechainicio) ano from nomi_nominasperiodo group by year(fechainicio) order by year(fechainicio) desc;");
		if($sql->num_rows>0){
			return $sql;
		}else{
			return 0;
		}
	}
	
	
}

?>