<?php

//ini_set("display_errors", 1); error_reporting(E_ALL);
class DispersionModel extends SobrereciboModel{

	function tipoPago(){
		$sql = $this->query("SELECT * from nomi_tipopago;");
		return $sql;
	}


	function nominaAutorizada(){

		$sql= $this->query("SELECT distinct np.idnomp,np.idtipop,np.numnomina,np.fechainicio,np.fechafin,tp.idtipop,tp.nombre,cp.dispersionStatus,np.autorizado from nomi_nominasperiodo np
			left join nomi_tiposdeperiodos tp
			on np.idtipop=tp.idtipop 
			left join nomi_calculo_prenomina cp
			on cp.idnomp=np.idnomp
			
			where np.autorizado=1  and ifnull(cp.dispersionstatus,0) = 0 
			order by  idnomp asc;");
		return $sql;
	}


	function cargaDeDatos($periodos){

		$filtroperiodo  = '';
		//echo "Periodo: $periodos";
		if($periodos != '*'){
			$filtroperiodo = "and cp.idnomp = $periodos";
		}

		if($periodos!=''){

 			$sql=$this->query("SELECT *,
				(case when (perce>deduc) then (perce-deduc) else deduc-perce end)as total
				from (
		 			SELECT  
		 				IFNULL( SUM(CASE WHEN n.idTipo in(1,4) THEN cp.importe ELSE 0 END) ,0) as perce,
		 				IFNULL( SUM(CASE WHEN n.idTipo in(2)   THEN cp.importe ELSE 0 END) ,0) as deduc,
				 		cp.idEmpleado,cp.idconfpre,n.idconcepto,n.concepto,n.descripcion,				
				 		e.codigo,e.apellidoPaterno,e.apellidoMaterno,e.nombreEmpleado,e.idtipop,e.idbanco,
				 		e.numeroCuenta, cp.dispersionStatus,cp.importe,np.idnomp,np.numnomina,np.fechainicio, 
				 		np.fechafin,b.Clave,e.tipocuenta 
				 	from nomi_empleados e 
				 	inner join nomi_calculo_prenomina cp on e.idEmpleado=cp.idEmpleado 
				 	left join nomi_nominasperiodo np on np.idnomp=cp.idnomp 
				 	inner join cont_bancos b on b.idbanco=e.idbanco 
				 	inner join nomi_conceptos n on cp.idconfpre=n.idconcepto 
				 	WHERE 1=1  $filtroperiodo and cp.dispersionStatus=0 and cp.aplicarecibo=1  GROUP BY e.idEmpleado ORDER BY cp.idEmpleado)as tb;");
		}
		return $sql;	
		
	}


	function actualizaStatus($idemple,$idnomp,$consecutivo,$fechainicio,$txtfecha,$tipopago, $tableData){


		$sql.= "Insert INTO  nomi_dispersion 
		(idConsecutivo, fechaAplicacion, fechaAdelanto, monto, tipoPago, estatus, idEmpleado, idnomp) \n";
		
		$data = json_decode($tableData);
		$cantidad = count($data);
		for( $i = 0 ; $i <  $cantidad ; $i++ ){ 

			$sql.= "Select $consecutivo, '$fechainicio','$txtfecha','".str_replace(",", "", $data[$i][4])."', $tipopago, 1, ".$data[$i][1].", $idnomp ";
			if ($i < $cantidad -1)
				$sql.= "Union All\n";  
			else 
				$sql.= ";";
			for ($j = 1 ; $j < count($data[$i]) ; $j++ );
            // echo $data[$i][$j].",";
		};

	for( $i = 0 ; $i <  $cantidad ; $i++ ){ 

		$sql.="UPDATE nomi_calculo_prenomina  SET 
		dispersionStatus= 1
		Where idnomp=$idnomp
		AND idEmpleado = ".$data[$i][1].";";
	}"\n";
	if($this->multi_query($sql)){
		return 1;

	}else{
		return 0;
	}
}


function cargarDatosDispersos(){
	$sql=$this->query("
		SELECT 
		d.*,np.nombrepago FROM nomi_dispersion d 
		INNER JOIN  nomi_tipopago np
		ON  d.tipoPago=np.tipopago;");

	return $sql;

}



function accionEliminarDispersion($idEmpleado,$idnomp){
	$sql = "DELETE FROM nomi_dispersion WHERE idEmpleado=$idEmpleado AND idnomp=$idnomp;".

	"UPDATE  
	nomi_calculo_prenomina 
	SET 
	dispersionStatus= 0
	WHERE 
	idEmpleado = $idEmpleado
	and dispersionStatus=1 and idnomp=$idnomp;";

	if($this->multi_query($sql)){
		return 1;

	}else{
		return 0;
	}

}

}

?>