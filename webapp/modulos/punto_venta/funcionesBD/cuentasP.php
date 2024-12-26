<?php
	include("../../../netwarelog/webconfig.php");
	
	$funcion = $_POST['funcion'];
	if($funcion != "compruebaChequeOTarjetaDuplicados")
	{
		$connection = new mysqli($servidor,$usuariobd,$clavebd,$bd);
		$funcion($connection);
		mysqli_close($connection);
	}
	else 
	{
		$funcion();
	}
	
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

	function cargaDatosCuenta($connection)
	{
		$id = $_POST["id"];

			$result = $connection->query("SELECT c.fechacargo, c.fechavencimiento, c.concepto, c.monto, c.saldoabonado, c.saldoactual, c.estatus, p.razon_social FROM cxp c, mrp_proveedor p WHERE c.idCxp = ".$id." and c.idProveedor=p.idPrv");
			
			if($result->num_rows < 0 || $result->num_rows=='' ){
               $result = $connection->query("SELECT c.fechacargo, c.fechavencimiento, c.concepto, c.monto, c.saldoabonado, c.saldoactual, c.estatus FROM cxp c WHERE c.idCxp = ".$id);				
			}

        if($result->num_rows > 0){
			if($row = $result->fetch_array())
				echo $row["fechacargo"].'$$$^^^###///'.$row["fechavencimiento"].'$$$^^^###///'.$row["concepto"].'$$$^^^###///'.$row["monto"].'$$$^^^###///'.$row["saldoabonado"].'$$$^^^###///'.$row["saldoactual"].'$$$^^^###///'.$row["estatus"].'$$$^^^###///'.$row["razon_social"].'$$$^^^###///';	
		}
		else
			echo "Hubo un problema al cargar los datos. Recargue la pagina";
	    }
		

	/*	if($result->num_rows > 0){
			if($row = $result->fetch_array())
				echo $row["fechacargo"].'$$$^^^###///'.$row["fechavencimiento"].'$$$^^^###///'.$row["concepto"].'$$$^^^###///'.$row["monto"].'$$$^^^###///'.$row["saldoabonado"].'$$$^^^###///'.$row["saldoactual"].'$$$^^^###///'.$row["estatus"].'$$$^^^###///'.$row["razon_social"].'$$$^^^###///';	
		}
		else
			echo "Hubo un problema al cargar los datos. Recargue la pagina";
	    } */
	
	function cargaFormasDePago($connection)
	{
		$result = $connection->query("SELECT idFormapago, nombre FROM forma_pago");
		$select_formas_pago = "<select id='forma_pago' style='width: 100%;' onchange='compruebaChequeOTarjetaRegalo();' class='form-control'>";
		$select_formas_pago .= "<option value=''>Selecciona una forma de pago</option>";
		
		if($result->num_rows > 0)
		{
			while($row = $result->fetch_array())
			{
				$select_formas_pago .= "<option value='".$row['idFormapago']."'>".utf8_decode($row['nombre'])."</option>";
			}
		}
		$select_formas_pago .= "</select></div>";
		echo $select_formas_pago;
	}
	
	function consultaPagos($connection)
	{
		$id = $_POST["id"];
		$result = $connection->query("	SELECT p.idCxppagos, p.fecha, p.monto, p.saldoinicial, p.saldofinal, p.idFormapago, p.referencia, f.nombre 
										FROM cxp_pagos p
										INNER JOIN forma_pago f ON f.idFormapago = p.idFormapago
										WHERE idCxp = ".$id." ORDER BY idCxppagos;");
		$tabla_pagos = "<div id='pagos' style='height:200px; width: 100%; border:1px solid #98ac31; overflow:auto; padding: 10px;' >";
		
		if($result->num_rows > 0)
		{			
			$tabla_pagos .= "	
			
			<table cellpadding='0' cellspacing='0' width='100%' style='min-width: 100%; font-size: 12px;'>
				<tr>
					<th class='nmcatalogbusquedatit' width=25% style='border-bottom: 1px solid #98ac31;'>Fecha</th>
					<th class='nmcatalogbusquedatit' width=10% style='border-bottom: 1px solid #98ac31;'>Abono</th>
					<th class='nmcatalogbusquedatit' width=40% style='border-bottom: 1px solid #98ac31;'>Forma de pago</th>
					<th class='nmcatalogbusquedatit' width=10% style='border-bottom: 1px solid #98ac31;'>Saldo inicial</th>
					<th class='nmcatalogbusquedatit' width=25% style='border-bottom: 1px solid #98ac31;'>Saldo final</th>
				</tr>";
			
			$i = 0;
			$cont=0;
			while($row = $result->fetch_array())
				$rows[] = $row;
			foreach($rows as $row)
			{
				if ($cont%2==0)//Si el contador es par pinta esto en la fila del grid
				{
    				$color='nmcatalogbusquedacont_1';
				}
				else//Si es impar pinta esto
				{
    				$color='nmcatalogbusquedacont_2';
				}
				 $cont++;
				if($row["idFormapago"] == 2 || $row["idFormapago"] == 3)
				{	
					$tabla_pagos .= "
					<tr class='".$color."'>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='fecha_".$i."'>			".$row["fecha"]."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='monto_".$i."'>			$".number_format($row["monto"], 2, '.', ',')."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='formapago_".$i."'>		".$row["nombre"]." (No.".$row["referencia"].")"."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='saldoinicial_".$i."'>	$".number_format($row["saldoinicial"], 2, '.', ',')."	</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; '><div id='saldofinal_".$i."' style='text-align: right;'>	$".number_format($row["saldofinal"], 2, '.', ',')."		</div></td>
						 <input type='hidden' id='idCxppago_".$i."' value=".$row["idCxppagos"].">
					 </tr>";
				}
				else 
				{
					$tabla_pagos .= "
					<tr class='".$color."'>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='fecha_".$i."'>			".$row["fecha"]."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='monto_".$i."'>			$".number_format($row["monto"], 2, '.', ',')."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='formapago_".$i."'>		".$row["nombre"]."			</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #EEEEEE;'><div id='saldoinicial_".$i."'>	$".number_format($row["saldoinicial"], 2, '.', ',')."	</div></td>
						 <td style='border-bottom: 1px solid #EEEEEE; '><div id='saldofinal_".$i."' style='text-align: right;'>	$".number_format($row["saldofinal"], 2, '.', ',')."		</div></td>
						 <input type='hidden' id='idCxppago_".$i."' value=".$row["idCxppagos"].">
					 </tr>";
				}
				
				$i++;
			}
			$tabla_pagos .= "<input id='cont_pagos' value=".$i." type='hidden'>";
			$tabla_pagos .= "</table>";	
			
			echo $tabla_pagos;		
		}
		else
			echo "<center><div style='color: #98ac31;'>No se han registrado pagos para esta cuenta</div></center>";
	}
	
	function agregaPago($connection)
	{
		$fecha_abono = $_POST["fecha_abono"];
		$abono = $_POST["abono"];
		$forma_pago = $_POST["forma_pago"];
		$id_forma_pago = $_POST["id_forma_pago"];
		$id = $_POST["id"];
		$saldo_actual = $_POST["saldo_actual"];
		$fven = $_POST["fven"];
		$referencia = $_POST["referencia"];
		
		session_start();
		
		if (!isset($_SESSION["fecha_abono_array"]))
		    $_SESSION["fecha_abono_array"] = array();
		if (!isset($_SESSION["abono_array"]))
		    $_SESSION["abono_array"] = array();
		if (!isset($_SESSION["forma_pago_array"]))
		    $_SESSION["forma_pago_array"] = array();
		if (!isset($_SESSION["id_forma_pago_array"]))
		    $_SESSION["id_forma_pago_array"] = array();
		if (!isset($_SESSION["referencia_array"]))
		    $_SESSION["referencia_array"] = array();
		
		array_push($_SESSION["fecha_abono_array"], $fecha_abono);
		array_push($_SESSION["abono_array"], $abono);
		array_push($_SESSION["forma_pago_array"], $forma_pago);
		array_push($_SESSION["id_forma_pago_array"], $id_forma_pago);
		array_push($_SESSION["referencia_array"], $referencia);
		
		$grid_pagos = "<form action='../cxp/cuenta.php' type='POST'>
							<input type='hidden' name='id' value=".$id.">
							<input type='hidden' name='fven' value=".$fven.">
							<h4>Pagos a agregar</h4>
							<div class='col-xs-12 tablaResponsiva'>
							<div class='table-responsive'>
							<table cellpadding='0' cellspacing='0' width=100%>
							<tr>
								<th class='nmcatalogbusquedatit' width=10% style='border-bottom: 1px solid #98ac31;'>Borrar</th>
								<th class='nmcatalogbusquedatit' width=20% style='border-bottom: 1px solid #98ac31;'>Fecha</th>
								<th class='nmcatalogbusquedatit' width=40% style='border-bottom: 1px solid #98ac31;'>Forma de pago</th>
								<th class='nmcatalogbusquedatit' width=30% style='border-bottom: 1px solid #98ac31;'>Abono</th>
							</tr>";
		
		
		for($i=0; $i<count($_SESSION["abono_array"]); $i++)
		{
			$grid_pagos .= "<tr>";
			$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'><center><input type='checkbox' name='chk".$i."' value='1'></center></td>";
			$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".$_SESSION["fecha_abono_array"][$i]."</td>";
           
		   	if($_SESSION["id_forma_pago_array"][$i] == 2 || $_SESSION["id_forma_pago_array"][$i] == 3)
			{
				$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".utf8_decode($_SESSION["forma_pago_array"][$i]." (no. ".$_SESSION['referencia_array'][$i].")")."</td>";
			}
			else
		    {
		    	$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".utf8_decode($_SESSION["forma_pago_array"][$i])."</td>";
            }
            
            $grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; '>".number_format($_SESSION["abono_array"][$i], 2, '.', ',')."</td>";
          	$grid_pagos .= "</tr>";
		}
			$nuevo_saldo_actual = $saldo_actual - array_sum($_SESSION["abono_array"]);
			
			$grid_pagos .= "<tr><td colspan='2' style='text-align: left; border-top: 1px solid #777777;'><input type='submit' id='borrar' value='Borrar seleccionados' class='btn btn-danger btnMenu'/></td>";
			$grid_pagos .= "<td colspan='2' style='text-align: right; border-top: 1px solid #777777;'>Saldo en caso de agregarse los pagos:  <label style='color: #98ac31;'>$ ".number_format($nuevo_saldo_actual, 2, '.', ',')."</label></td></tr>";
			$grid_pagos .= "<input type='hidden' id='saldo_final_preliminar' value='".$nuevo_saldo_actual."'>";
			$grid_pagos .= "<tr><td style='text-align: right;'><input type='button' id='registrar_pagos' value='Registrar pagos' onclick='registraPagos();' class='btn btn-primary btnMenu'/></td></tr>";
			
		$grid_pagos .= "</table></div></div></form>";
		echo $grid_pagos;
		
	}

	function cargaPrePagos($connection)
	{	
		session_start();
		$id = $_POST["id"];
		$saldo_actual = $_POST["saldo_actual"];
		$fven = $_POST["fven"];
		
		if(isset($_SESSION["fecha_abono_array"]) && isset($_SESSION["abono_array"])
			&& isset ($_SESSION["forma_pago_array"]) && isset($_SESSION["id_forma_pago_array"]) && isset($_SESSION["referencia_array"])
			&& count($_SESSION["fecha_abono_array"]) > 0)
		{
			$grid_pagos = "<form action='../cxp/cuenta.php' type='POST'>
							<input type='hidden' name='id' value=".$id.">
							<input type='hidden' name='fven' value=".$fven.">
							<h4>Pagos a agregar</h4>
							<div class='col-xs-12 tablaResponsiva'>
							<div class='table-responsive'>
							<table cellpadding='0' cellspacing='0' width=100%>
							<tr>
								<th class='nmcatalogbusquedatit' width=10% style='border-bottom: 1px solid #98ac31;'>Borrar</th>
								<th class='nmcatalogbusquedatit' width=20% style='border-bottom: 1px solid #98ac31;'>Fecha</th>
								<th class='nmcatalogbusquedatit' width=40% style='border-bottom: 1px solid #98ac31;'>Forma de pago</th>
								<th class='nmcatalogbusquedatit' width=30% style='border-bottom: 1px solid #98ac31;'>Abono</th>
							</tr>";
							
							
			for($i=0; $i<count($_SESSION["abono_array"]); $i++)
			{
				$grid_pagos .= "<tr>";
				$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'><center><input type='checkbox' name='chk".$i."' value='1'></center></td>";
				$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".$_SESSION["fecha_abono_array"][$i]."</td>";
	           	
	           	if($_SESSION["id_forma_pago_array"][$i] == 2 || $_SESSION["id_forma_pago_array"][$i] == 3)
				{
					$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".utf8_decode($_SESSION["forma_pago_array"][$i]." (no. ".$_SESSION['referencia_array'][$i].")")."</td>";
				}
				else
			    {
			    	$grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; border-right: 1px solid #999999;'>".utf8_decode($_SESSION["forma_pago_array"][$i])."</td>";
	            }
			    
			    $grid_pagos .= "<td style='border-bottom: 1px solid #EEEEEE; '>".number_format($_SESSION["abono_array"][$i], 2, '.', ',')."</td>";
	          	$grid_pagos .= "</tr>";
			}
				$nuevo_saldo_actual = $saldo_actual - array_sum($_SESSION["abono_array"]);
				
				$grid_pagos .= "<tr><td colspan='2' style='text-align: left; border-top: 1px solid #777777;'><input type='submit' id='borrar' value='Borrar seleccionados' class='btn btn-danger btnMenu'/></td>";
				$grid_pagos .= "<td colspan='2' style='text-align: right; border-top: 1px solid #777777;'>Saldo en caso de agregarse los pagos:  <label style='color: #98ac31;'>$ ".number_format($nuevo_saldo_actual, 2, '.', ',')."</label></td></tr>";
				$grid_pagos .= "<input type='hidden' id='saldo_final_preliminar' value='".$nuevo_saldo_actual."'>";
				$grid_pagos .= "<tr><td style='text-align: right;'><input type='button' id='registrar_pagos' value='Registrar pagos' onclick='registraPagos();' class='btn btn-primary btnMenu'/></td></tr>";
				
			$grid_pagos .= "</table></div></div></form>";
			echo $grid_pagos;
		}
	}

	function crearCuenta($connection)
	{
		$fecha_cargo = $_POST["fecha_cargo"];
		$fecha_vencimiento = $_POST["fecha_vencimiento"];
		$monto = $_POST["monto"];			
		$saldo_abonado = $_POST["saldo_abonado"];	
		$saldo_actual = $_POST["saldo_actual"]; 	
		$concepto = $_POST["concepto"];
		$prove = $_POST["prove"];

		if ($result = $connection->query("INSERT INTO cxp (fechacargo, fechavencimiento, concepto, monto, saldoabonado, saldoactual,idProveedor) VALUES ('".$fecha_cargo."', '".$fecha_vencimiento."', '".$concepto."', ".$monto.", ".$saldo_abonado.", ".$saldo_actual.",'".$prove."')"))
		{
			echo 1;
		}
		else 
		{
			echo 0;
		}	
	}
	
	function registraPagos($connection)
	{
		session_start();
		$id = $_POST["id"];
		$saldo_actual = $_POST["saldo_actual"];
		$saldo_abonado = $_POST["saldo_abonado"];
		
		$saldo_final;
		$saldo_inicial;
		$acumulado_abonos;
		
		date_default_timezone_set('America/Mexico_City'); 
		$fecha = date("Y-m-d");
		
		for($i=0; $i<count($_SESSION["abono_array"]); $i++)
		{
			$acumulado_abonos += $_SESSION['abono_array'][$i];
			if($i == 0)
			{
				$saldo_final = $saldo_actual - $_SESSION['abono_array'][$i];
				$result = $connection->query("INSERT INTO cxp_pagos (fecha, monto, saldoinicial, saldofinal, idCxp, idFormapago, referencia) VALUES ('".$fecha."', ".$_SESSION['abono_array'][$i].", ".$saldo_actual.", ".$saldo_final.", ".$id.", ".$_SESSION['id_forma_pago_array'][$i].", ".$_SESSION['referencia_array'][$i].")");
			}
			else
			{
				$saldo_actual = $saldo_final;
				$saldo_final = $saldo_actual - $_SESSION['abono_array'][$i];
				$result = $connection->query("INSERT INTO cxp_pagos (fecha, monto, saldoinicial, saldofinal, idCxp, idFormapago, referencia) VALUES ('".$fecha."', ".$_SESSION['abono_array'][$i].", ".$saldo_actual.", ".$saldo_final.", ".$id.", ".$_SESSION['id_forma_pago_array'][$i].", ".$_SESSION['referencia_array'][$i].")");
			}

			if($_SESSION["id_forma_pago_array"][$i] == 3)
			{
				$result = $connection->query("UPDATE tarjeta_regalo SET usada = 1 WHERE numero = ".$_SESSION["referencia_array"][$i]);
			}
		}
		$saldo_abonado += $acumulado_abonos;
		$result = $connection->query("UPDATE cxp SET saldoabonado = ".$saldo_abonado.", saldoactual = ".$saldo_final." WHERE idCxp = ".$id);	
	}
	
	function compruebaTarjetaRegalo($connection)
	{
		$ref = $_POST["referencia"];
		$valida;
		$result = $connection->query("SELECT id, numero, valor, usada FROM tarjeta_regalo WHERE numero = '".$ref."'");
		
		if($result->num_rows > 0)
		{
			while($rows = $result->fetch_array())
			{
				$row = $rows;
				if($row["usada"] == 1)
				{
					$valida = "Usada";
				}
				else
				{
					if($row["valor"] <= 0)
					{
						$valida = "Agotada";
					}
					else 
					{
						$valida = $row["valor"];
					}
				}	
			}
		}
		else
		{
			$valida = "No existe";
		}
		echo $valida;
	}
	
	function compruebaChequeOTarjetaDuplicados()
	{
		$ref = $_POST["referencia"];
		$id_forma_pago = $_POST["id_forma_pago"];
		$valido = true;
		
		session_start();
		
		for($i=0; $i<count($_SESSION["referencia_array"]); $i++)
		{
			if($ref == $_SESSION["referencia_array"][$i] && $id_forma_pago == $_SESSION["id_forma_pago_array"][$i])
			{
				$valido = false;
			}	
		}
		
		if($valido == false)
		{
			if($id_forma_pago == 2)
			{
				echo 2;
			}
			else if($id_forma_pago == 3)
			{
				echo 3;
			}
		}
		else
		{
			echo 1;
		}
	}
	
	
	
	
	
	
	
	
?>