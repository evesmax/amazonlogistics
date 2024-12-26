
			<?php
			class incidenciasModel extends CatalogosModel{
				
				/* I N C I D E N C I A S      I N T E R F A Z */
				function rangoFechas($fechainicio, $fechafin, $format){
			//genera array con el rango de fechas
					$range = array(); 
					if (is_string($fechainicio) === true) $fechainicio = strtotime($fechainicio);
					if (is_string($fechafin)    === true) $fechafin    = strtotime($fechafin);

					if ($fechainicio > $fechafin) return createDateRangeArray($fechafin, $fechainicio);

					do {
						$range[] = date($format, $fechainicio);
						$fechainicio = strtotime("+ 1 day", $fechainicio);
					} while($fechainicio <= $fechafin);
			
			return $range; 

				} 

		function empleadosDperiodo($fecha){
			$sql = $this->query("select e.*,
									(case e.activo when 1 then e.fechaAlta when 3 then  h.fecha end) as fechaActiva
								from 
									nomi_empleados e
								left join nomi_configuracion c on e.idtipop = c.idtipop
								left join nomi_historial_empleado h on h.idEmpleado = e.idEmpleado
								where  
								 	case e.activo when 1 then e.fechaAlta<='$fecha' when 3 then  h.fecha<='$fecha' end
									group by e.idEmpleado
								");
			if($sql->num_rows>0){
				return $sql;
			}else{
				return 0;
			}
		}
		
			//trae los empleados de la tabla de nomi_empleados
				// function empleadosDperiodo($fechaIni, $fechaFin){
				// 	$sql = $this->query("select e.* 
				// 					from 
				// 						nomi_empleados e
				// 					inner join 
				// 						nomi_configuracion c on e.idtipop = c.idtipop
				// 					where  
				// 						e.fechaAlta<='$fecha'");
				// 	if($sql->num_rows>0){
				// 		return $sql;
				// 	}else{
				// 		return 0;
				// 	}
				// }
				// 
				

			//funcion para obtener la info de la tabla que muestra el modal. indicamos las claves que se pueden combinar, es decir que puedo seleccionar en la tabla.
				function tablaincidencias(){ 

					$sql = $this->query("SELECT i.idtipoincidencia, i.clave,i.nombre as nomi,ci.nombre as nombcla,ci.idclasificadorincidencia,
						case when clave like 'HE_' or clave = 'RET' then 1 else 0 end as puedecombinar
						FROM nomi_tipoincidencias i
							INNER JOIN nomi_clasificacion_incidencias ci 
							ON i.idclasificadorincidencia=ci.idclasificadorincidencia
							where idtipoincidencia not in(1,2,5,12,13,17,18);");

					if($sql->num_rows>0){
						return $sql;
					}else{
						return 0;
					}
				}


				//funcion para obtener los datos que se muestran en el modal//que se despliegan en incidencias
				function valoresincidencias($valor){

					$sql = $this->query("SELECT * from  nomi_claveincidencias;");
					if($sql->num_rows>0){
						return $sql;
					}else{
						return 0;
					}

				}


			//funcion para llamar el procedimiento para  mostrar las incidencias.
				function claveIncidencias($idnomp){
					
					$sql = $this->query("call traerIncidencias(".$idnomp.")");
					if($sql->num_rows>0){
						return $sql;
					}else{
						return 0;
					}

				}

			//mando a llamar el procedimiento (almacenaincidencia) para guardar la clave, el valor,la fechaseleccion, 
			function almacenaincidencia ($arregloDatos, $idempleado, $idnomp){
				$success=0;
				for ($i=0; $i< count($arregloDatos); $i++){
						 $valor=$arregloDatos[$i]->valor;
						 $clave= (string)$arregloDatos[$i]->clave;
						 	
						 $fecha=$arregloDatos[$i]->fecha;
						 $tipoincidencia = $arregloDatos[$i]->tipoincidencia;
						 $sql="call almacenaIncidencia(".$tipoincidencia.",'".$clave."','".$fecha."',".$valor.",".$idnomp.",".$idempleado.")"; /*llama al SP almacenaIncidencia*/
						 //print $sql;
						 if(!$this->query($sql)){
							$success= 0;
						 }else{
							 $success=1;
						 }  
				}
				return $success;
			}

		  //Traer el listado de los periodos en incidencias.
			function nominasPeriodoInci(){
					$sql = $this->query("SELECT nomi.*, periodoactual.idtipop,periodoactual.periodosfuturos,/*tomamos los campos seleccionados de la subconsulta*/
											  case 
													when 
														nomi.idnomp < periodoactual.idnomp /*si el periodo de nominasperiodos es menor al periodo de la subconsulta entonces es periodo anteriors*/
														then 'p_anterior' /*los periodos anteriores al actual*/
													when 
														nomi.idnomp =periodoactual.idnomp  
														then 'p_actual'  /*periodo actual*/
													when 
														nomi.idnomp>periodoactual.idnomp 
														then 'p_futuro'    /*periodo futuro*/
											   END as clasedeperiodo,/*creamos un campo en la consulta si es para saber la clase de periodo, anterior,actual o futuro*/
											  case 
											   	when 
											   		nomi.idnomp=periodoactual.idnomp /**/
											   		then 1 /*asiganmos el valor de 1 al campo editable que indica si el periodo es editable*/
											   	else 
											   		case when nomi.idnomp>periodoactual.idnomp then periodoactual.periodosfuturos=1 /*si el idnomp de nomi es mayor al idnomp del periodo actual de la subconsulta, entonces los periodos futuros son 1*/
											   		end
											   end as editable /*Creamos un campo en la consulta para saber los que tengan 1 podran ser editados*/
									from nomi_nominasperiodo as nomi, /**/
										
										(select idnomp, p.idtipop,c.periodosfuturos				/*hacemos una subconsulta para traer el periodo actual*/
										 from nomi_nominasperiodo p 				/*(el primero que encuentra como autorizado=0)*/
										 	inner join nomi_configuracion c 
										 		on p.idtipop=c.idtipop /*idtipop de nomi_nominasperiodo es igual a idtipop de nomi_configuracion*/
										 where 
										    p.autorizado=0 
										order by numnomina asc limit 1 /*ordena por el campo numnomina mostrando solo el primer elemento que tenga 0 en el autorizdo*/
										) as periodoactual /*el periodo actual se tomara el primer 0 que encuentre la subconsonsulta*/
									where nomi.idtipop=periodoactual.idtipop
									");
					return $sql;
			}


			function eliminarincidencia($idempleado, $idnomp, $fecha){
				
				$sql ="delete from nomi_claveincidencias where idempleado=$idempleado and idnomp=$idnomp and fechaseleccion='$fecha'";
				//print $sql;
				if($this->query($sql)){
					return 1;
				
				}else{
					return 0;
				}
		   }


		//A L M A C E N A   D I A   F E S T I V O   E N  I N C I D E N C I A S (MODAL)
		function  almacenaDiaFest($fecha,$idnomp){

            $filtrovaca='';

			$sqlc = $this->query("select 1 from  nomi_claveincidencias where idtipoincidencia=18 and fechaseleccion='$fecha';");
			if($sqlc->num_rows>0){

				$delete = $this->query("DELETE cl.* from nomi_claveincidencias cl
		  			inner join nomi_tipoincidencias ti
		  			on 	   cl.idtipoincidencia=ti.idtipoincidencia  		
		  			where (cl.idtipoincidencia=18 and cl.fechaseleccion='$fecha' and cl.idnomp=$idnomp) or (ti.idclasificadorincidencia in(2) 
		  			and cl.fechaseleccion='$fecha' and ti.idconsiderado not in(3) and cl.idnomp=$idnomp);");
			}


            $vac= $this->query("select idEmpleado from  nomi_claveincidencias where fechaseleccion='$fecha' and idtipoincidencia=17 and idnomp=$idnomp");

            if($vac->num_rows>0){
            	$filtrovaca="and idempleado not in(select idEmpleado from  nomi_claveincidencias where fechaseleccion='$fecha' and idtipoincidencia=17 and idnomp=$idnomp)";
				}else{
				//$filtrovaca='';
				}
			echo $sql=$this->query("INSERT INTO nomi_claveincidencias(idtipoincidencia,idnomp,idempleado,fechaSeleccion,clave,valor,autorizado,sobrerecibo,idsobrecibo)
				SELECT 18,$idnomp, idEmpleado,'$fecha','FEST',0,0,0,null
				from ( 
				SELECT e.idEmpleado,e.activo,e.fechaAlta,np.fechafin,he.fecha,np.autorizado,(case when activo=-1 then fechaAlta<=fechafin
				when activo in(3) then fecha<=fechafin
				end)as empleados,
				(select idEmpleado from  nomi_claveincidencias where fechaseleccion='$fecha' and idtipoincidencia=17 and idnomp=$idnomp)vac
				from nomi_nominasperiodo np
				inner join nomi_configuracion c on   
				c.idtipop=np.idtipop 
				inner join nomi_empleados e
				on c.idtipop=e.idtipop
				left join nomi_historial_empleado he
				on e.idempleado = he.idempleado
				and e.activo = he.tipo
				where autorizado=0
				group by idEmpleado)bns where empleados=1  $filtrovaca 
				 order by idEmpleado asc;");

			if($sql->num_rows>0){
				return 1;			
			}else{
				return 0;
			}
		}

	}

		?>
