<?php
require('controllers/prenomina.php');
require("models/cierreanual.php");

class Cierreanual extends Prenomina
{
	public $PrenominaModel;
	public $CierreanualModel;

	function __construct()
	{

		$this->CierreanualModel = new CierreanualModel();
		$this->PrenominaModel = $this->CierreanualModel;
		$this->CierreanualModel->connect();

	}

	function __destruct()
	{
		$this->CierreanualModel->close();
	}

	function viewCierre(){
		$periodos = $this->PrenominaModel->tipoperiodosinextra();
		require("views/cierreanual/viewcierreanual.php");
	}
	function nuevoPeriodoAnual(){
		$ultimo = $this->CierreanualModel->ultimoAutNominaAno($_REQUEST['periodo']);
		$generado = $this->CierreanualModel->ejercicioGenerado($_REQUEST['periodo']);
		//si el periodo no tiene autorizada la ultima nomina del ano no podra crear otro ejercicio
		if($ultimo == 0){
			echo 3;
		}else{
			if($generado==1){
				echo 4;
			}else{
				$peri = $this->CierreanualModel->periodoCierre($_REQUEST['periodo']);
				$fechafinultimo1 = $this->CierreanualModel->ultimaNominaPeriodo($_REQUEST['periodo']);
				$fechafinultimo1= explode("/", $fechafinultimo1);

				$fechafinultimo = $fechafinultimo1[0];
				$ejercicioanterior = $fechafinultimo1[1];
				$fechainicioPeriodo= strtotime ( '+1 day' , strtotime ( $fechafinultimo) ) ;
				$fechainicioPeriodo = date ( 'Y-m-d' , $fechainicioPeriodo );
				$periodo = $this->insertPeriodoProcesoCierre($ejercicioanterior+1,$peri->diasperiodo, $peri->idtipop, $peri->diaspago, $fechainicioPeriodo, 0);
				echo $periodo;
			}
		}
	}
//cierre anual
 function insertPeriodoProcesoCierre($ejercicio,$diasperiodo,$idtipop,$diaspago,$fechainicioPeriodo,$extraordinario){
	date_default_timezone_set('America/Mexico_City');

	$fechainicio = explode('-',$fechainicioPeriodo);

	/*para periodo quincenal se hara por meses
			 * porque en este no se puede unir dos meses en un periodo
			 * osea q la segunda quincena debe quedar con el resto de dias ya sean mas de 15 o menos de 15
			 * en el mes
			 */

	//$eliminaprevios = $this ->CatalogosModel->eliminaNominasdelperiodo($idtipop);


	if($extraordinario == 0 ){
		if($diasperiodo == 15){
			//$fecha = $fechainicio[0]."-01-01";
			$fecha = $fechainicioPeriodo;
			$numnomia = 1;

			//if( $eliminaprevios==1){
			for ($peri = 1; $peri <= 12; $peri++) {
				$menos = 0;
					$dias1 = 14;//siempre seran 14 dias para tomar la quincena porque ya estara en dia 1 mas 14 son los 15
					$menos += 1;	//sumamos siempre un dia porq asi iniciaria el periodo en 1 o 16 y se debe contar
					if($peri != 1 ){
						$nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
						$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
						$fecha = $nuevafecha;
					}

					/* definimos los inicios de bimestre */
					$iniciobimentre = 0;
					if($peri == 1 || $peri == 3 || $peri == 5 || $peri == 7 || $peri == 9 || $peri == 11){
						$iniciobimentre = 1;
					}
					/* definimos los fin de bimestre */
					$finbimentre = 0;
					if($peri == 2 || $peri == 4 || $peri == 6 || $peri == 8 || $peri == 10 || $peri == 12){
						$finbimentre = 1;
					}
					/* definimos los inicio ejercicio */
					$inicioejercicio = 0;
					if($peri == 1){
						$inicioejercicio = 1;
					}
					/* definimos los fin ejercicio */
					$finejercicio = 0;
					if($peri == 12){
						$finejercicio = 1;
					}

					//$array[$peri][] = $fecha;
					$nuevafecha = strtotime ( '+'.$dias1.' day' , strtotime ( $fecha ) ) ;
					$menos += $dias1;
					$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
					$fecha2 = $nuevafecha;
					//$array[$peri][] = $fecha;

					//$sql = $this->CatalogosModel->insertPeriodo($idtipop, $numnomia, $fecha, $fecha2, $fechainicio[0], $peri, $diaspago, 1, $iniciobimentre, $inicioejercicio, 0, 0, 0);
					$sql .="INSERT INTO nomi_nominasperiodo ( idtipop, numnomina, fechainicio, fechafin, ejercicio, mes, diaspago, iniciomes, iniciobimentreimss, inicioejercicio, finmes, finbimentreimss, finejercicio)
					VALUES
					( $idtipop, $numnomia, '$fecha', '$fecha2', $ejercicio, $peri, $diaspago, 1, $iniciobimentre,$inicioejercicio, 0, 0, 0);
					";

			//
					$nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha2 ) ) ;
					$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
					$menos += 1;
					$fecha3 = $nuevafecha;
					//$array[$peri][] = $fecha;

					//sacamos todos los dias del mes para saber de cuanto sera la segunda quincena
					$numero = cal_days_in_month(CAL_GREGORIAN,$peri, $fechainicio[0]) ;
					//y restamos a esos dias la primera quincena
					$numero -= $menos ;
					$nuevafecha = strtotime ( '+'.$numero.' day' , strtotime ( $fecha3 ) ) ;
					$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
					$fecha = $nuevafecha;
					//$array[$peri][] = $fecha;
					$numnomia++;
					//$sql = $this->CatalogosModel->insertPeriodo($idtipop, $numnomia, $fecha3, $fecha, $fechainicio[0], $peri, $diaspago, 0, 0, 0, 1, $finbimentre, $finejercicio);
					$sql .="INSERT INTO nomi_nominasperiodo ( idtipop, numnomina, fechainicio, fechafin, ejercicio, mes, diaspago, iniciomes, iniciobimentreimss, inicioejercicio, finmes, finbimentreimss, finejercicio)
					VALUES
					( $idtipop, $numnomia, '$fecha3', '$fecha', $ejercicio, $peri, $diaspago, 0, 0,0, 1, $finbimentre, $finejercicio);
					";
			//
					$numnomia++;
				}

			//}

			}else{
			/* para los periodos diferentes de 15 dias se debera tomar en cuenta la fecha de inicio
					 * descontando a los 365 dias del ano los dias transcurridos
					 * de ser asi del periodo y estos seran divididos entre el total de dias
					 * del periodo para sacar el numero de nominas que tendra
					 * el ejercicio
					 * SE HARA SOLO PARA SEMANAL POR QUE KERLAB A SI LO UTILIZA QUINCENAL Y SEMANAL
					 */
		$fechaini = explode('-',$fechainicioPeriodo);
			$array = array();

			//$datetime1 = new DateTime($fechaini[0].'-01-01');
			//$datetime2 = new DateTime($fechainicioPeriodo);
			//$interval  = $datetime1->diff($datetime2);
			$diasano   = 365; //- $interval->format('%R%a');
					//sacamos el total de nominas del ano
			$totalnomina = $diasano/$diasperiodo;
			$totalnominabimestral = 365/$diasperiodo;
					//dividimos el total de nominas por los dias trabajados
					//para saber cuantas nominas iran en un mes
			$nominaenunmes =  number_format($totalnomina/12,0);
			$nominaenunmesbimestral =  number_format($totalnominabimestral/12,0);

					/* para los periodos que no sean quincena el sistema debe evaluar
					 * los periodos dentro del mes a corde a los dias de periodo
					 * y de estos sacar toda la nomina del ejercicio
					 *
					 */
					$fecha = $fechainicioPeriodo;
					$iniciobimentre = 0;
					$fechaorigen = explode('-', $fecha);
					$fechacomparacion = $fechacomparacionbimestre =1;
					$nominaenunbimestre = $nominaenunmesbimestral*2;
					if($fechaorigen[1] == 2 || $fechaorigen[1] == 4 || $fechaorigen[1] == 6 || $fechaorigen[1] == 8 || $fechaorigen[1] == 10 || $fechaorigen[1] == 12 ){
						$fechacomparacionbimestre = $nominaenunbimestre;
					}

					//$eliminaprevios = $this ->CatalogosModel->eliminaNominasdelperiodo($idtipop);
					//if( $eliminaprevios == 1){
					for ($peri = 1; $peri <= number_format($totalnomina); $peri++) {

						$inicioejercicio = 0;
						if($peri == 1 ){
							$inicioejercicio = 1;
							$iniciomes = 1;
							$iniciobimentre = 1;
							$finmes = 0;
							$finejercicio = 0;
							$finbimentre = 0;
						}


						if($peri != 1 ){
							$nuevafecha = strtotime ( '+1 day' , strtotime ( $fecha ) ) ;
							$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
							$fecha = $nuevafecha;
						}

						$array[$peri][]=$fecha;
						$dias = $diasperiodo - 1;
						$nuevafecha = strtotime ( '+'.$dias.' day' , strtotime ( $fecha ) ) ;
						$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
						$fecha = $nuevafecha;
						$s = explode('-', $nuevafecha);

						$array[$peri][] = $fecha;
						$array[$peri]['iniciomes'] = $iniciomes;
						$array[$peri]['finmes'] = $finmes;
						$array[$peri]['iniciobime'] = $iniciobimentre;
						$array[$peri]['finbime'] = $finbimentre;
						$array[$peri]['inicioejericicio'] = $inicioejercicio;
						$array[$peri]['finejercicio'] = $finejercicio;
						if( $fechacomparacion == $nominaenunmes ){
							$array[$peri]['finmes'] = 1;
							$iniciomes = 1;
							$fechacomparacion = 1;
							$finmes = 0;

						}else{
							$iniciomes = 0;
							$fechacomparacion++;
						}
						if( $fechacomparacionbimestre == $nominaenunbimestre ){
							$array[$peri]['finbime'] = 1;
							$finbimentre = 0;
							$fechacomparacionbimestre = $iniciobimentre = 1;
						}else{
							$iniciobimentre = 0;
							$fechacomparacionbimestre++;
						}
						if(number_format($totalnomina) == $peri){
							$array[$peri]['finejercicio'] = 1;
						}
							// $sql = $this->CatalogosModel->insertPeriodo($idtipop, $peri, $array[$peri][0], $array[$peri][1], $fechainicio[0], $s[1], $diaspago, $array[$peri]['iniciomes'], $array[$peri]['iniciobime'], $array[$peri]['inicioejericicio'], $array[$peri]['finmes'], $array[$peri]['finbime'], $array[$peri]['finejercicio'] );
						$sql .="INSERT INTO nomi_nominasperiodo ( idtipop, numnomina, fechainicio, fechafin, ejercicio, mes, diaspago, iniciomes, iniciobimentreimss, inicioejercicio, finmes, finbimentreimss, finejercicio)
						VALUES
						( $idtipop, $peri, '".$array[$peri][0]."', '".$array[$peri][1]."', $ejercicio, $s[1], $diaspago, ".$array[$peri]['iniciomes'].", ".$array[$peri]['iniciobime'].",".$array[$peri]['inicioejericicio'].", ".$array[$peri]['finmes'].", ".$array[$peri]['finbime'].", ".$array[$peri]['finejercicio'].");
						";
					}
					//}

				}
				$envio = $this->PrenominaModel->insertPeriodo($sql);
			}else{

				$ejercicioext = explode('-', $fechainicioPeriodo);
				$sql = "INSERT INTO nomi_nominasperiodo ( idtipop, numnomina, fechainicio, fechafin, ejercicio, mes, diaspago, iniciomes, iniciobimentreimss, inicioejercicio, finmes, finbimentreimss, finejercicio)
				VALUES
				( $idtipop, 1, '$fechainicioPeriodo', '".$ejercicioext[0].'-12-31'."',". $ejercicioext[0].", 0, 0, 0, 0,0, 0,0, 0);
				";
				$envio = $this->PrenominaModel->insertSQL($sql);
			}

			if($envio==1){
				return 1;
			}else{
				return 0;
			}
		}


}

?>
