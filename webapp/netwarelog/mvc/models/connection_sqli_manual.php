<?php

	//Esta es la clase de coneccion Padre que hereda los atributos a los modelos
	class Connection
	{
		protected $connection;

		//Conecta a la base de datos
		public function connect($tienda = false)
		{
			//Cuidado con estas líneas de terror
			global $api_lite;
			if(!isset($api_lite)){
				if(isset($_SESSION["ws"])){
					if(!isset($_REQUEST["netwarstore"]) && !isset($_REQUEST["factura_mailing"])) require($_SESSION["ws"]."../../netwarelog/webconfig.php");
					else require "../webapp/netwarelog/webconfig.php";
				}
				else{
					if(!isset($_REQUEST["netwarstore"]) && !isset($_REQUEST["factura_mailing"])) require("../../netwarelog/webconfig.php");
					else require "../webapp/netwarelog/webconfig.php";
				}
			}
			else require $api_lite . "../appministra_api/webconfig.php";

			if($tienda){
				$bd = "netwarstore";
				$usuariobd = "nmdevel";
				$clavebd = "nmdevel";
			}

			if(!$this->connection = mysqli_connect($servidor,$usuariobd,$clavebd,$bd))
			{
				echo "<br><b style='color:red;'>Error al tratar de conectar</b><br>";
			}
			$this->connection->set_charset('utf8');// Previniendo errores con SetCharset
		}
		/*************************************************************************************************************
		* CALL SP
		*************************************************************************************************************/
		protected function getDataTable($sql){
				$array   = array();
				$res     = $this->connection->query($sql) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<br>Error:<br>".$query);;
				//LIBERAMOS EL BUFER AL HACER LA CONEXION
				$this->freeResultsConnection();
				return $res;
		}
		/*************************************************************************************************************
		* ESTE METODO SE USA PARA LIBERAR TODO LO QUE SE QUEDE EN EL BUFFER DE LA CONEXION ,
		* POR QUE AVECES QUIERES MANDAR LLAMAR EL MISMO STORED PROCEDURE VARIAS VECES Y MARCA
		* EL SIGUIENTE ERROR :
		* Commands out of sync; you can't run this command now
		* PAGINA DE EJEMPLO DEL ERROR Y SOLUCION
		* https://www.lawebdelprogramador.com/foros/MySQL/1545154-EXPERTOS-POR-FAVOR-Commands-out-of-sync-you-cant-run-this-command-now.html
		*************************************************************************************************************/
		private function freeResultsConnection(){
			do{
				if($result=mysqli_store_result($this->connection)){
					mysqli_free_result($result);
				}
			}while(mysqli_more_results($this->connection) && mysqli_next_result($this->connection));
		}

		/*************************************************************************************************************
		* MULTI QUERY RETURN DATASET FROM STORED PROCEDURE
		*************************************************************************************************************/
		protected function getDataSet($sql){
				$dataset = array();

				if (mysqli_multi_query($this->connection,$sql))
				{
				  do{
							$array   = array();
					    if ($result=mysqli_store_result($this->connection))
							{
								while ($row = mysqli_fetch_assoc($result))
								{
									$array[] = $row;
								}
					      mysqli_free_result($result);
							}
							$dataset[] = $array;
				  }while (mysqli_next_result($this->connection));
				}
				if($mysqli_error=mysqli_error($this->connection)){  // check & declare variable in same step to avoid duplicate func call
    			echo "<div style=\"color:red;\">Query Key = ",key($q),", Query = ",current($q),", Syntax Error = $mysqli_error query $sql</div>";
				}
				return $dataset;
			}


		//funcion que cierra la coneccion
		public function close()
		{
			$this->connection->close();
		}
		//Funcion que genera las consultas genericas a la base de datos
		protected function query($query,$nombreproceso=null)
		{

			$result = $this->connection->query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<br>Error:<br>".$query);
			if($nombreproceso)
				$this->transaccion($nombreproceso,$query);
			return $result;
		}

		protected function multi_query($query,$nombreproceso=null)
		{

			$result = $this->connection->multi_query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<be>Error:<br>".$query);
			if($nombreproceso)
				$this->transaccion($nombreproceso,$query);
			return $result;
		}

		protected function insert_id($query,$nombreproceso=null)
		{
			if(stristr($query, 'insert'))
			{
				$this->connection->query($query) or die("<b style='color:red;'>Error en la consulta.</b><br /><br />".$this->connection->error."<be>Error:<br>".$query);
				if($nombreproceso)
					$this->transaccion($nombreproceso,$query);
				return $this->connection->insert_id;
			}
			else
			{
				return "La consulta no incluye un INSERT.";
			}
		}
        protected function queryArray($sql, $relational = true, $nombreproceso = null)
         {
            try{
								$this->freeResultsConnection();
                if (empty($sql)){
                    throw new Exception("empty SQL");
                }
                $this->sql = $sql;
                //$this->connect();

                $result = $this->connection->query($sql);
                if($nombreproceso)
					$this->transaccion($nombreproceso,$sql);

                if (!$result) {
                    return array("status"=>false, "total" => $sql,"msg"=>" Favor de contactar con el area de soporte y facilitar el mensaje de error que esta entre parentesis.( ".$this->connection->error ." )");
                }

                $this->affectedRows = mysqli_num_rows($result);

                $fields = array();
                while ($finfo = mysqli_fetch_field($result)) {
                    $fields[] = $finfo->name;
                }

                $rows = array();

                if  ($relational) {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                        $rows[] = $row;
                    }

                }else {
                    while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
                        foreach ($row as $key => $value){
                            $rows[$key][] = $value;
                        }
                    }
                }
                $insert_id = $this->connection->insert_id;
                //$this->close();
                return array("status" => true, "total" =>  $this->affectedRows, "fields" => $fields, "rows" => $rows,"insertId"=>$insert_id);

            }catch(Exception $e){
                //$this->close();
                return array("status" => false, "msg" => $e->getMessage());
            }
        }
		//Metodo para generar transaccion con la base de datos
		protected function dataTransact($data)
		{
			$this->connection->autocommit(false);
			if($this->connection->query('BEGIN;'))
			{
				if($this->connection->multi_query($data))
				{
					do {
						/* almacenar primer juego de resultados */
						if ($result = $this->connection->store_result()) {
							while ($row = $result->fetch_row()) {
								echo $row[0];
							}
							$result->free();
						}

					} while ($this->connection->more_results() && $this->connection->next_result());

					$this->connection->commit();
					return true;
				}
				else
				{
					$error = $this->connection->error;
					//echo "Chiales esto trono!";
					$this->connection->rollback();
					return $error;
				}
			}
			else
			{
				$error = $this->connection->error;
				$this->connection->rollback();
				return $error;
			}
		}
		//Termina transaccion-----------

		public function transaccion($nombreproceso,$sql){
						date_default_timezone_set('America/Mexico_City');
            $fecha_actual = strtotime(date("d-m-Y H:i:00",time()));
            $fecha_s1 = strtotime("31-12-".(date("Y")-1)." 00:00:00"); //SEMESTRE 1
            $fecha_s2 = strtotime("30-06-".date("Y")." 23:59:59"); //SEMESTRE 2

            //echo "31-12-".(date("Y")-1)." 00:00:00"."<br>";
            //echo "30-06-".date("Y")." 00:00:00"."<br>";
            //echo " $fecha_actual > $fecha_s1 ".($fecha_actual > $fecha_s1)."<br>";
            //echo " $fecha_actual<=$fecha_s2 ".($fecha_actual<=$fecha_s2)."<br>";

            $nombretabla_transacciones = "netwarelog_transacciones_".date("Y")."_";

            if(($fecha_actual > $fecha_s1)&&($fecha_actual<=$fecha_s2)){
                //echo "PRIMER SEMESTRE";
                $nombretabla_transacciones.="s1";
            }else{
                //echo "SEGUNDO SEMESTRE";
                $nombretabla_transacciones.="s2";
            }


                //SE CREA LA TABLA EN CASO DE NO EXISTIR

			$sqltabla = "
			CREATE  TABLE IF NOT EXISTS ".$nombretabla_transacciones." (
			  fecha datetime NOT NULL ,
			  usuario VARCHAR(255) NOT NULL ,
			  nombreproceso VARCHAR(500) NOT NULL ,
			  sqlproceso VARCHAR(5000) NULL,
				ip VARCHAR(100) NOT NULL )
			";
			$sqltabla.="ENGINE = InnoDB;";
                        //echo $sql;
                        $this->connection->query($sqltabla);
			//mysql_query($sql, $this->cbase);


                $usuario = "N/A"; //Puede existir un proceso donde aún el usuario no se haya logeado.
                if(isset($_SESSION["accelog_login"])){
                    $usuario = $_SESSION["accelog_login"];
                }


                $sql = str_replace("'", "\"", $sql);


                //echo $_SERVER['SERVER_ADDR'];
                $sql  = "insert into ".$nombretabla_transacciones."
                             (fecha, usuario, nombreproceso, sqlproceso, ip)
                             values
                             (now(), '".$usuario."','".$nombreproceso."','".$sql."','".$_SERVER["REMOTE_ADDR"]."') ";
                $this->connection->query($sql);

								//Insertar Fecha de Acceso en la BD Transversal
								$arrInstanciaG = $_SESSION["accelog_nombre_instancia"];
                                //echo $arrInstanciaG;
								$fechaultimoacceso=date('Y-m-d H:i:00', time());
								$servidor  = "34.66.63.218";
								$objCon = mysqli_connect($servidor, "nmdevel", "nmdevel", "netwarstore");
								$strSql = "update customer set fechaultimoacceso='".$fechaultimoacceso."' where instancia='".$arrInstanciaG."'";
								mysqli_query($objCon,$strSql);
								mysqli_query($objCon,$strSql);
								mysqli_close($objCon);

        }
	}
