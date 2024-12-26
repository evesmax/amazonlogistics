<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli_manual.php"); // funciones mySQLi

    class ProduccionModel extends Connection{

        function nl2brCH($string)
        {
            return preg_replace('/\R/u', '<br/><br/>', $string);
        }
        
        function getSeriesProd($idProducto){
            $myQuery = "SELECT a.*, b.nombre, b.id as ida from app_producto_serie a 
            inner join app_almacenes b on b.id= a.id_almacen
            where a.id_producto='$idProducto' AND a.estatus=0";
                $series = $this->queryArray($myQuery);
                if($series['total']>0){
                    foreach ($series['rows'] as $k2 => $v2) {
                        $arrSeries[]=array('idSerie'=>$v2['id'].'-'.$v2['ida'], 'serie'=>'Serie: '.$v2['serie'].' ('.$v2['nombre'].')', 'serie2' => $v2['serie']);
                    }
                }else{

                }

            return $arrSeries;

        }

        function addProductoProduccion($idProducto){
          

            $myQuery = "SELECT a.id, a.codigo, if(a.descripcion_corta='',a.nombre,a.descripcion_corta) as descripcion_corta, a.precio as costo, x.clave, a.tipo_producto,if(a.minimoprod is null,0,a.minimoprod) as minimo, a.factor FROM app_productos a
                INNER join app_unidades_medida x on x.id=a.id_unidad_venta
                WHERE a.id='$idProducto'  group by a.id;";

            $producto = $this->query($myQuery);
            return $producto;
        }

        function getProductos5()
        {
            $myQuery = "SELECT id, nombre FROM app_productos WHERE (tipo_producto='8' or tipo_producto='9') ORDER BY nombre;";
            $productos = $this->query($myQuery);
            return $productos;


        }


        function getLastOrden()
        {
            $myQuery = "SELECT if(MAX(id) is NULL,1,MAX(id)+1) as id from prd_orden_produccion;";
            $nreq = $this->query($myQuery);
            return $nreq;
        }

        function getUsuario(){
            session_start();
            $idusr = $_SESSION['accelog_idempleado'];

            $myQuery = "SELECT concat(nombre,' ',apellido1) as username, idempleado from empleados where idempleado='$idusr';";
            $nreq = $this->query($myQuery);
            //session_destroy();
            return $nreq;
        }

        function getSucursales()
        {

           
            $myQuery = "SELECT a.id_sucursal as idSuc, b.nombre as nombre from app_almacenes a
             LEFT JOIN mrp_sucursal b on b.idSuc=a.id_sucursal group by a.id_sucursal order by b.nombre;";
            $nreq = $this->query($myQuery);
            return $nreq;
        }


        function activar($id){
         $myQuery = "UPDATE app_requisiciones set pr=1 where idprereq='$id';";
                $this->queryArray($myQuery);
        }

        function savePaso2($idsProductos,$accion,$idop,$paso,$clotes,$idprod,$almacen,$idap){

            $idusr = $_SESSION['accelog_idempleado'];
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            if($clotes>0){
                //Valida Session de lotes
                $cslote=count($_SESSION['v_rePr'][1]);

                if($clotes!=$cslote){
                    echo 'nolote';
                    exit();
                }
            }


            $myQuery = "SELECT invprod from prd_configuracion WHERE id=1;";
            $config = $this->queryArray($myQuery);
            if($config['total']>0){
                $invprod=$config['rows'][0]['invprod'];
            }else{
                $invprod = 0;
            }

            if($invprod==1){
                $tt=3;
                $txtt=' Apartado ';
            }else{
                $tt=0;
                $txtt=' ';
            }


            $myQuery = "SELECT * from prd_utilizados WHERE id_oproduccion='$idop' LIMIT 1;";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $last_id=$rr['rows'][0]['id'];
            }else{
                $myQuery = "INSERT INTO prd_utilizados (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
                $last_id = $this->insert_id($myQuery);
            }
         
            $myQuery = "DELETE FROM prd_utilizados_detalle WHERE id_utilizado='$last_id';";
            $query = $this->query($myQuery);
            

            if($last_id>0){
                $cad='';

                $productos = explode('___', $idsProductos);
           
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idPadre=$exp[0];
                    $idHijo=$exp[1];
                    $cant=$exp[2];
                    $elcost = 0;
                    $elunit = 0;
                    $cardexmulti='';
                    
                    
                    if($clotes>0 && array_key_exists($idHijo, $_SESSION['v_rePr'][1])){
                        $caracteristica=0;
                        $ciclo=explode(',', $_SESSION['v_rePr'][1][$idHijo][$caracteristica]['cantslotes']);
                        $cadlotillo='';

                            foreach ($ciclo as $kk => $vv) {
                                $desgl_cl=explode('-', $vv);
                                if($desgl_cl[2]!=0){
                                    $elcost = 0;
                                    $elunit = 0;
                                    $cardexmulti.="('".$idHijo."','0','".$desgl_cl[0]."','".$desgl_cl[2]."','".$elcost."','".$desgl_cl[1]."','0','".$creacion."','".$idusr."','0','".$elunit."','Orden de produccion / usarInsumo -".$idop."','0'),";

                                    $cadlotillo.=$desgl_cl[0].'=>'.$desgl_cl[2].',';
                                }
                            }

                            $cadlotillotrim=trim($cadlotillo,',');
                        
                    }else{

                        $cadlotillotrim=null;
                        $cardexmulti.="('".$idHijo."','0','0','".$cant."','".$elcost."','".$almacen."','0','".$creacion."','".$idusr."','".$tt."','".$elunit."','Orden de produccion /".$txtt."usarInsumo -".$idop."','0')";

                    }

                    $cadrdcardextrim=trim($cardexmulti,',');
                    $myQuery = "INSERT INTO app_inventario_movimientos (id_producto,id_pedimento,id_lote,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) VALUES ".$cadrdcardextrim.";";
                        $this->query($myQuery);

                    $cad.="('".$last_id."','".$idHijo."','".$cant."','".$cadlotillotrim."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_utilizados_detalle (id_utilizado,id_insumo,cantidad,lotes) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
            }


            $myQuery = "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            $query = $this->query($myQuery);

            echo $last_id;

        }

        function savePaso15($costo15_adicional,$costo15_terminado,$idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            $myQuery = "DELETE FROM prd_costo_produccion WHERE id_oproduccion='$idop';";
            //$query = $this->query($myQuery);

            
            $myQuery .= "INSERT INTO prd_costo_produccion (id_oproduccion,fecha_registro,id_usuario,id_paso,costo_adicional,costo_total) VALUES ('$idop', '$creacion', '$paso', 15,'$costo15_adicional','$costo15_terminado');";
            //$this->query($myQuery);


            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);
			if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }
           // echo 1;

        }

        function savePaso1($idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_orden_produccion SET estatus='9', fecha_p='$creacion' WHERE id='$idop';";
            //$query = $this->query($myQuery);

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);

            if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }

        }


        function savePaso10($caja10_operador,$caja10_peso,$idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "DELETE FROM prd_caja WHERE id_oproduccion='$idop';";
            //$query = $this->query($myQuery);

            
            $myQuery.= "INSERT INTO prd_caja (id_oproduccion,operador,peso) VALUES ('$idop','$caja10_operador','$caja10_peso');";
            //$last_id = $this->insert_id($myQuery);

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);
            
            if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }
            

        }

        function savePaso9($accion,$idop,$paso,$idap){//krmn finaliza produccion

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_orden_produccion SET estatus='10', fecha_f='$creacion' WHERE id='$idop';";
           // $query = $this->query($myQuery);

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }

            //echo 1;

        }
        
        function savePaso6($lote6_nolote,$lote6_fechafab,$lote6_fechacad,$idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "SELECT id from app_producto_lotes WHERE no_lote='$lote6_nolote' LIMIT 1;";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $last_id=$rr['rows'][0]['id'];
                $myQuery = "UPDATE app_producto_lotes SET fecha_fabricacion='$lote6_fechafab', fecha_caducidad='$lote6_fechacad' WHERE id='$last_id';";
                $this->query($myQuery);
            }else{
                $myQuery = "INSERT INTO app_producto_lotes (no_lote,fecha_fabricacion,fecha_caducidad) VALUES ('$lote6_nolote','$lote6_fechafab','$lote6_fechacad');";
                $last_id = $this->insert_id($myQuery);
            }

            $myQuery = "DELETE FROM prd_lote_detalles WHERE id_oproduccion='$idop';";
            //$query = $this->query($myQuery);

            
            $myQuery .= "INSERT INTO prd_lote_detalles (id_oproduccion,id_lote,id_usuario) VALUES ('$idop','$last_id',3);";
            //$this->query($myQuery);


            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);

            if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }

        }

        

        function savePaso17($accion,$idop,$paso,$idp,$costo,$cant,$almacen,$idap){
            session_start();
            $idusr = $_SESSION['accelog_idempleado'];

            $myQuery = "SELECT id_lote FROM prd_lote_detalles WHERE id_oproduccion='$idop';";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $idlote=$rr['rows'][0]['id_lote'];
            }else{
                $idlote=0;
            }

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            /* BLOQUE DESAPARTADO APARTADOS */
            $myQuery = "SELECT * FROM app_inventario_movimientos WHERE referencia='Orden de produccion / Apartado usarInsumo -".$idop."';";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $cad='';
                foreach ($rr['rows'] as $k => $v) {
                    $cad.="('".$v['id_producto']."', '".$v['cantidad']."', '".$v['importe']."', '".$v['id_almacen_origen']."', '".$v['id_almacen_destino']."', '".$creacion."', '".$idusr."', '0', '".$v['costo']."', 'Orden de produccion / usarInsumo -".$idop."', '1', '".$v['id_lote']."'),";
                }

                $cad=trim($cad,',');
                $qq = "INSERT INTO  app_inventario_movimientos (id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) VALUES ".$cad.";";
                $query = $this->query($qq);

                $qqq = "UPDATE app_inventario_movimientos SET estatus=0 WHERE referencia='Orden de produccion / Apartado usarInsumo -".$idop."';";
                $this->query($qqq);

            }
            /* FIN BLOQUE DESAPARTADO APARTADOS */

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');
			//verificamos si existe merma en esta orden de produccion
			$sql = $this->query("select sum(cantidad) as merma from prd_merma_detalle m inner join prd_merma pm on pm.id_oproduccion=$idop and pm.id=id_merma where id_insumo=$idp");
			if($sql->num_rows>0){
				$s = $sql->fetch_object();
				if($s->merma>0){
					$cant-=$s->merma;
				}
			}
			
            $referencia='Orden de produccion-'.$idop;
            $importe=$cant*$costo;
			
            
            $myQuery = "INSERT INTO  app_inventario_movimientos (id_producto,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,estatus,id_lote) VALUES ( '".$idp."','".$cant."','".$importe."','0','".$almacen."','".$creacion."','".$idusr."','1','".$costo."','".$referencia."','1','".$idlote."') ;";
            //$query = $this->query($myQuery);

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);

            if($this->dataTransact($myQuery) === true){
            		return 1;
            }else{
            		return 0;
            }
 
        }


        function savePaso11($idsProductos,$accion,$idop,$paso,$idap,$idempo,$opc,$ppf){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            //$myQuery = "DELETE FROM prd_matpro WHERE id_oproduccion='$idop';";
            //$query = $this->query($myQuery);

			if($opc==0){
       

	            $myQuery = "INSERT INTO prd_matp (id_oproduccion,id_operador,id_pa,f_ini,cantppf) VALUES ('".$idop."','".$idempo."','".$idap."','".$creacion."',".$ppf.") ;";
	
	            $last_id = $this->insert_id($myQuery);
	
	
	
	            $d = explode('___', $idsProductos);
	
	            $cad='';
	            foreach ($d as $k => $v) {
	                $r=explode('###', $v);
	                $idpersonal=$r[0];
	
	                $ins=explode(',', $r[1]);
	
	
	
	                foreach ($ins as $k2 => $v2) {
	
	                    $kkk=explode('#', $v2);
	
	
	                    $idprod=$kkk[0];
	                    $cant=$kkk[1];
	                    $cad.="('','','".$idprod."','".$cant."','".$idap."','".$last_id."'),";
	                }
	                # code...
	            }
	
	            $cad=trim($cad,',');
	
	 
	
	            $myQuery = "INSERT INTO prd_matpro (id_oproduccion,id_operador,id_insumo,cantidad,id_pasoaccion,id_mp) VALUES ".$cad." ;";
	            $query = $this->query($myQuery);
			}else{
	            $myQuery = "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
	            $query = $this->query($myQuery);
			}

            echo $query;

        }

        function savePaso16($idsProductos,$accion,$idop,$paso,$ideti,$idap){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');
            $padre_tipos=explode('_#_', $idsProductos);
			$myQuer="";
            foreach ($padre_tipos as $ka => $va) {
                $ccc= explode('>>', $va);
                $cadena=$ccc[0];
                $peso  =$ccc[1];
                $code  =$ccc[2];
                $myQuery = "INSERT INTO prd_gencode (id_op,id_etiqueta,codigo,peso) VALUES ('".$idop."','".$ideti."','".$code."','".$peso."') ;";
                $last_id = $this->insert_id($myQuery);
        
                $cad='';
                $tipos= explode(',', $cadena);
                foreach ($tipos as $k => $v) {
                    $exp=explode('##', $v);
                    $idtipo=$exp[0];
                    $valor=$exp[1];

                    $cad.="('".$last_id."','".$idtipo."','".$valor."'),";

                }
            
                $cadtrim = trim($cad, ',');
                $myQuer .= "INSERT INTO prd_gencode_detalle (id_gencode,id_tipo_eti,code) VALUES ".$cadtrim.";";
                //$query = $this->query($myQuery);

            }

            $myQuer .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuer/);
			if($this->dataTransact($myQuer) === true){
            		return 1;
            }else{
            		return 0;
            }
           // return $query;

        }

        function savePaso5($idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');
			$myQuery = "";
                $productos = explode('___', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idMaq=$exp[0];
                    $maquinaria=$exp[1];

                    $myQuery .= "UPDATE prd_personal_detalle SET maquinaria='$maquinaria' WHERE id='$idMaq';";
                    //$query = $this->query($myQuery);
                }

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
          
           if($this->dataTransact($myQuery) === true){
           	 return 1;
           }else{
           	 return 0;
           }
 			

            //echo $query;

        }

        function savePaso4($idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            $myQuery = "SELECT * from prd_personal WHERE id_oproduccion='$idop' LIMIT 1;";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $last_id=$rr['rows'][0]['id'];
            }else{
                $myQuery = "INSERT INTO prd_personal (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
                $last_id = $this->insert_id($myQuery);
            }
         
            $myQuery = "DELETE FROM prd_personal_detalle WHERE id_personal='$last_id';";
            //$query = $this->query($myQuery);
            


            if($last_id>0){
                $cad='';
                $productos = explode('___', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idEmpleado=$exp[0];
                    $maq=$exp[1];

                    
                    $cad.="('".$last_id."','".$idEmpleado."','".$maq."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery .= "INSERT INTO prd_personal_detalle (id_personal,id_empleado,maquinaria) VALUES ".$cadtrim.";";
                //$query = $this->query($myQuery);
            }

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
           // $query = $this->query($myQuery);

           // echo $last_id;
           if($this->dataTransact($myQuery) === true){
           	 return $last_id;
           }else{
           	 return 0;
           }
 			

        }

        function savePaso3($idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            $myQuery = "SELECT * from prd_peso WHERE id_oproduccion='$idop' LIMIT 1;";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $last_id=$rr['rows'][0]['id'];
            }else{
                $myQuery = "INSERT INTO prd_peso (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
                $last_id = $this->insert_id($myQuery);
            }
         
            $myQuery = "DELETE FROM prd_peso_detalle WHERE id_peso='$last_id';";
           // $query = $this->query($myQuery);
            

            if($last_id>0){
                $cad='';
                $productos = explode('___', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idPadre=$exp[0];
                    $idHijo=$exp[1];
                    $cant=$exp[2];
                    
                    $cad.="('".$last_id."','".$idHijo."','".$cant."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery .= "INSERT INTO prd_peso_detalle (id_peso,id_insumo,peso) VALUES ".$cadtrim.";";
                //$query = $this->query($myQuery);
            }

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            if($this->dataTransact($myQuery) === true){
           	 return $last_id;
           }else{
           	 return 0;
           }
            //echo $last_id;

        }

        function savePaso14($idsProductos,$accion,$idop,$paso,$almacen,$idap){

            $idusr = $_SESSION['accelog_idempleado'];
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            // $myQuery = "SELECT * from prd_merma WHERE id_oproduccion='$idop' LIMIT 1;";
            // $rr = $this->queryArray($myQuery);
            // if($rr['total']>0){
                // $last_id=$rr['rows'][0]['id'];
                // echo 1;
                // exit();
            // }else{
                $myQuery3 = "INSERT INTO prd_merma (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion','$idusr');";
                $last_id = $this->insert_id($myQuery3);


                $cpro = explode('___', $idsProductos);
                $cpro=count($cpro);
                $myQuery2 = "INSERT INTO app_merma (fecha,usuario,productos,importe) VALUES ('$creacion','$idusr','$cpro',0);";
                $last_id_merma = $this->insert_id($myQuery2);
           // }
         
            // $myQuery = "DELETE FROM prd_merma_detalle WHERE id_merma='$last_id';";
            // $query = $this->query($myQuery);
            

            if($last_id>0){
                $cad='';
                $cardexmulti='';
                $cardmerma='';
                $productos = explode('___', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idPadre=$exp[0];
                    $idHijo=$exp[1];
                    $cant=$exp[2];
					$tipomerma=$exp[3];
					$observa=$exp[4];
                    
                    $cad.="('".$last_id."','".$idHijo."','".$cant."'),";

                    $elcost = 0;
                    $elunit = 0;
                    $cardexmulti.="('".$idHijo."','0','0','".$cant."','".$elcost."','".$almacen."','0','".$creacion."','".$idusr."','0','".$elunit."','Registro de mermas / mermas -".$idop."','0'),";


                    $cardmerma.="('".$last_id_merma."','".$idHijo."','".$cant."',0,'".$idusr."','".$almacen."','".$observa."','0',$tipomerma,'0','0'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery .= "INSERT INTO prd_merma_detalle (id_merma,id_insumo,cantidad) VALUES ".$cadtrim.";";
               // $query = $this->query($myQuery);

                // $cadrdcardextrim=trim($cardexmulti,',');
                // $myQuery = "INSERT INTO app_inventario_movimientos (id_producto,id_pedimento,id_lote,cantidad,importe,id_almacen_origen,id_almacen_destino,fecha,id_empleado,tipo_traspaso,costo,referencia,origen) VALUES ".$cadrdcardextrim.";";
                // $query = $this->query($myQuery);



                $cardmerma=trim($cardmerma,',');
                $myQuery .= "INSERT INTO app_merma_datos (id_merma,id_producto,cantidad,precio,usuario,almacen,observaciones,caracteristicas,tipo,idlote,idproveedor) VALUES ".$cardmerma.";";
                //$query = $this->query($myQuery);
            }

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
            //$query = $this->query($myQuery);
 		if($this->dataTransact($myQuery) === true){
           	 return $last_id;
           }else{
           	 return 0;
           }
           // echo $last_id;
                    
        }

        function savePaso7($idsProductos,$accion,$idop,$paso,$idap){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            $myQuery = "SELECT * from prd_batch WHERE id_oproduccion='$idop' LIMIT 1;";
            $rr = $this->queryArray($myQuery);
            if($rr['total']>0){
                $last_id=$rr['rows'][0]['id'];
            }else{
                $myQuery = "INSERT INTO prd_batch (id_oproduccion,fecha_registro,id_usuario) VALUES ('$idop','$creacion',3);";
                $last_id = $this->insert_id($myQuery);
            }
         
            $myQuery = "DELETE FROM prd_batch_detalle WHERE id_batch='$last_id';";
            //$query = $this->query($myQuery);
            

            if($last_id>0){
                $cad='';
                $productos = explode('___', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>#', $v);
                    $idPadre=$exp[0];
                    $idHijo=$exp[1];
                    $cant=$exp[2];
                    
                    $cad.="('".$last_id."','".$idHijo."','".$cant."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery .= "INSERT INTO prd_batch_detalle (id_batch,id_insumo,cantidad) VALUES ".$cadtrim.";";
                //$query = $this->query($myQuery);
            }

            $myQuery .= "INSERT INTO prd_ini_proceso (id_oproduccion,id_paso,id_accion,fecha_guardado,id_accion_producto) VALUES ('$idop','$paso','$accion','$creacion',$idap);";
           // $query = $this->query($myQuery);
 		if($this->dataTransact($myQuery) === true){
           	 return $last_id;
           }else{
           	 return 0;
           }
           // echo $last_id;

        }

        function modiAgrupa($iduserlog,$nombre,$id_eti){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_agrupas SET nombre='$nombre', fecha_registro='$creacion', id_usuario='$iduserlog' WHERE id='$id_eti';";
            return $this->query($myQuery);


           // return 1;

        }

        function modiEtiqueta($idsProductos,$iduserlog,$obs,$nombre,$id_eti){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_etiquetas SET nombre_etiqueta='$nombre', fecha_registro='$creacion', id_usuario='$iduserlog' WHERE id='$id_eti';";
            $this->query($myQuery);

            $myQuery = "DELETE FROM prd_etiquetas_detalle WHERE id_etiqueta='$id_eti';";
            $this->query($myQuery);


            $last_id = $id_eti;

            if($last_id>0){
                $cad='';
                $productos = explode('--c--', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>', $v);
                    $idprod=$exp[0];
                    $cant=$exp[1];
                    
                    $cad.="('".$last_id."','".$idprod."','".$cant."',1),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_etiquetas_detalle (id_etiqueta,id_tipo,digitos,estatus) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
            }

            return $last_id;


        }

        function saveAgrupa($iduserlog,$nombre){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "INSERT INTO prd_agrupas (nombre,estatus,fecha_registro,id_usuario) VALUES ('$nombre',1,'$creacion','$iduserlog');";

            $last_id = $this->insert_id($myQuery);

    

            return $last_id;

        }

        function saveEtiqueta($idsProductos,$iduserlog,$obs,$nombre){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "INSERT INTO prd_etiquetas (nombre_etiqueta,estatus,fecha_registro,id_usuario) VALUES ('$nombre',1,'$creacion','$iduserlog');";

            $last_id = $this->insert_id($myQuery);

            if($last_id>0){
                $cad='';
                $productos = explode('--c--', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>', $v);
                    $idprod=$exp[0];
                    $cant=$exp[1];
                    
                    $cad.="('".$last_id."','".$idprod."','".$cant."',1),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_etiquetas_detalle (id_etiqueta,id_tipo,digitos,estatus) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
            }

            return $last_id;

        }

        function saveOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$sol,$lotesprd){
            //backtoback
			if(!$sucursal){
				$sucursal = $this->sucursalUsuario($iduserlog);
			}
			
			
			
			
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');
 			$productos = explode('--c--', $idsProductos);
			foreach ($productos as $k => $v) {
			 	$exp=explode('>', $v);
                $idprod=$exp[0];
                $cant1=$exp[1];
				
            		$myQuery = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,lote) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','".$this->nl2brCH($obs)."','$prioridad','$sol','".$lotesprd[$idprod]."');";

            		$last_id = $this->insert_id($myQuery);

	            if($last_id>0){
	                $cad='';
	               
	                $cad.="('".$last_id."','".$idprod."','".$cant1."'),";
	
	            }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
           

            $sq="SELECT gen_aut_op FROM prd_configuracion WHERE id=1;";
            $config = $this->queryArray($sq);
            if($config['total']>0){
                $genop = $config['rows'][0]['gen_aut_op'];
            }else{
                $genop = 0;
            }

            if($genop==0){
                return $last_id;
                exit();
            }


            //Empieza a generar ordenes de produccion si se tienen productos tipo 8 dentro de la formulacion
            $myQuery="SELECT
                p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$last_id' AND m.id_producto=x.id_producto
                WHERE
                p.status=1
                AND
                p.tipo_producto=8
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$last_id') group by p.id;";

            

            $prodsReq = $this->queryArray($myQuery);

            if($prodsReq['total']>0){


                foreach ($prodsReq['rows'] as $k => $v) {
                    $sql = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,dependencia) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','".$this->nl2brCH($obs)."','$prioridad','$sol','".$last_id."');";
                    $last_id_sp = $this->insert_id($sql);

                    $ncan=$v['cantidad']*$cant1;
                    $q = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ('".$last_id_sp."','".$v['idProducto']."','".$ncan."');";
                    $query = $this->query($q);
                    # code...
                }

                    //=== SEGUNDO NIVEL 
                    $myQuery2="SELECT
                    p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                    p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                    (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                    (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti
                    FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                    pro.id_producto=p.id
                    INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$last_id_sp' AND m.id_producto=x.id_producto
                    WHERE
                    p.status=1
                    AND
                    p.tipo_producto=8
                    AND
                    m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$last_id_sp') group by p.id;";

                    $prodsReq2 = $this->queryArray($myQuery2);

                    if($prodsReq2['total']>0){

                        foreach ($prodsReq2['rows'] as $k2 => $v2) {
                            $sql2 = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad,solicitante,dependencia) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','".$this->nl2brCH($obs)."','$prioridad','$sol','".$last_id."-".$last_id_sp."');";
                            $last_id_sp2 = $this->insert_id($sql2);

                            $ncan2=$v2['cantidad']*$ncan;
                            $q2 = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ('".$last_id_sp2."','".$v2['idProducto']."','".$ncan2."');";
                            $query = $this->query($q2);
                            # code...
                        }

                    }

            }
 		}

            return $last_id;

        }

        

        function savePre($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$orden,$sol){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');


            $o=explode('--c--', $idsProductos);

             
            $arraypro=array();

            foreach ($o as $key => $value) {

                $q=explode('>', $value);
                $idpro=$q[0];
if($idpro!=0){ 

                if (array_key_exists($idpro, $arraypro)) {
                    $arraypro[$q[0]][]=array('idpadre'=>$q[1], 'idproducto'=>$q[2], 'cantidad'=>$q[3],'precio'=>$q[4]);
                }else{

                    $arraypro[$q[0]][]=array('idpadre'=>$q[1], 'idproducto'=>$q[2], 'cantidad'=>$q[3],'precio'=>$q[4]);

                }
            }
                

            
            }


    $myQuery3="SELECT fecha_inicio,fecha_entrega from prd_orden_produccion where id='$id_op'";

                   $result = $this->query($myQuery3);
  
               $row = $result->fetch_array();

               $fecha=$row['fecha_inicio'];
               $fecha_entrega=$row['fecha_entrega'];

       
            foreach ($arraypro as $k => $v) {

   
                $myQuery = "INSERT INTO prd_prerequisicion (id_op,id_usuario,id_proveedor,observaciones_pre,fecha_creacion,activo,subtotal,total) VALUES ('$id_op','$iduserlog','$k','".$this->nl2brCH($obs)."','$creacion','1','0','0');";

                $last_id = $this->insert_id($myQuery);

   
                $myQuery2="SELECT id_moneda from app_productos a 
                 JOIN prd_orden_produccion_detalle b on b.id_orden_produccion='$id_op'
                where a.id=b.id_producto limit 1";

                   $result = $this->query($myQuery2);
  
               $row = $result->fetch_array();
               $moneda=$row['id_moneda'];
               if($moneda==null || $moneda=='' || $moneda==0){
                    $moneda=1;
               }

               if ($orden==1){
                    $au=1;
               }else{
                    $au=0;
               }
            $myQuery = "INSERT INTO app_requisiciones (id_solicito,id_tipogasto,id_almacen,id_moneda,id_proveedor,urgente,inventariable,observaciones,fecha,fecha_entrega,activo,tipo_cambio,pr,subtotal,total,id_usuario,fecha_creacion,idoproduccion,idprereq) VALUES ('$sol','7','1','$moneda','$k','0','1','".$this->nl2brCH($obs)."','$fecha','$fecha_entrega','$au','0','2','$ttt','$ttt','$iduserlog','$creacion','$id_op','$last_id');";

                $last_id2 = $this->insert_id($myQuery);

                if ($orden==1){

 $myQuery = "INSERT INTO app_ocompra (id_proveedor,id_usrcompra,observaciones,fecha,fecha_entrega,activo,id_requisicion,subtotal,total,id_almacen,id_usuario,fecha_creacion,tipo) VALUES ('$k','1','".$this->nl2brCH($obs)."','$fecha','$fecha_entrega','1','$last_id2','$ttt','$ttt','1','$iduserlog','$creacion','1');";

                $last_id3 = $this->insert_id($myQuery);

                }



                if($last_id>0){
                    $cad='';
                    $cad2='';
                      $cad3='';
                    $ptotal=0;
                    foreach ($arraypro[$k] as $k2 => $v2) {
                                   $ptotal+=($v2['precio']*$v2['cantidad']);
                                   $costo=$v2['precio'];
                        $cad.="('".$last_id."','".$v2['idproducto']."','1','1','".$v2['cantidad']."','".$v2['idpadre']."'),";
                        $cad2.="('".$last_id2."','".$v2['idproducto']."','sestemp','1','1','".$v2['cantidad']."','0'),";
                          $cad3.="('".$last_id3."','".$v2['idproducto']."','sestemp','1','1','".$v2['cantidad']."','".$costo."','0','0'),";


                    }
                    $cadtrim = trim($cad, ',');
                     $cadtrim2 = trim($cad2, ',');
                         $cadtrim3 = trim($cad3, ',');
                    $myQuery = "INSERT INTO prd_prerequisicion_datos (id_prerequisicion,id_producto,estatus,activo,cantidad,id_producto_padre) VALUES ".$cadtrim.";";
                    $query = $this->query($myQuery);


                     $myQuery = "INSERT INTO app_requisiciones_datos (id_requisicion,id_producto,ses_tmp,estatus,activo,cantidad,caracteristica) VALUES ".$cadtrim2.";";
                    $query = $this->query($myQuery);

                      if ($orden==1){
                         $myQuery = "INSERT INTO app_ocompra_datos (id_ocompra,id_producto,ses_tmp,estatus,activo,cantidad,costo,impuestos,caracteristica) VALUES ".$cadtrim3.";";
                    $query = $this->query($myQuery);

                      }

                    $myQuery = "UPDATE app_requisiciones SET subtotal='$ptotal',total='$ptotal' WHERE id='".$last_id2."';";
                    $query = $this->query($myQuery);

            if ($orden==1){
                    $myQuery = "UPDATE app_ocompra SET subtotal='$ptotal',total='$ptotal' WHERE id='".$last_id3."';";
                    $query = $this->query($myQuery);}

                }

            }
           

            $myQuery = "UPDATE prd_orden_produccion SET estatus='2' WHERE id='".$id_op."';";
            $query = $this->query($myQuery);


            echo 'p';

        }

        function saveUsar($id_op,$iduserlog){
        		/*explosion masiva*/
        		if( is_array($id_op) ){
        			$id_op = implode(",",$id_op);
        		}
				
			date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_orden_produccion SET estatus=4 WHERE id in (".$id_op.");";
            $query = $this->query($myQuery);

            return 1;

        }
/*insumos variables cambio de fortmula cuando cambias los variables*/
		function updateInsumosVariables($idproduc,$idinsumo,$cantidad){
			$sql = $this->query("update app_producto_material set  cantidad =$cantidad  where id_producto=$idproduc and id_material=$idinsumo");
			
		}
/*fin variables*/
        function modifyOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op,$ttt,$sol,$lote){
			date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s'); 
			//se cambiara el update para q funcione el multiple orden en edicion ya q dentro de una edicion puedes agregar mas prd
			// $this->saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lote);
			 //return 1;
// 			
			
// 			
           
			
			$productos = explode('--c--', $idsProductos);
			$multiple = count($productos);
			//si hay mas de un producto esq es multiple 
			if($multiple>1){
				$myQuery = "DELETE FROM prd_orden_produccion WHERE id=$id_op;";
				$myQuery .= "DELETE FROM prd_orden_produccion_detalle WHERE id_orden_produccion=$id_op;";
		// 			  
		           if( $this->multi_query($myQuery) ){
		           		 while ($this->connection->next_result()) {;}	
		           		return $this->saveOP($idsProductos, $fecha_registro, $fecha_entrega, $prioridad, $sucursal, $option, $obs, $iduserlog, $id_op, $ttt, $sol, $lote);
		           }else{
		           		return 0;
		           }
			}else{
                foreach ($productos as $k => $v) {
                    $exp=explode('>', $v);
                    $idprod=$exp[0];
                    $cant=$exp[1];
                    

                }
			
	            $myQuery = "UPDATE prd_orden_produccion SET id_usuario='$iduserlog', id_sucursal='$sucursal', fecha_registro='$creacion', fecha_inicio='$fecha_registro', fecha_entrega='$fecha_entrega', solicitante='$sol',observaciones='".$this->nl2brCH($obs)."', prioridad='$prioridad', lote='$lote[$idprod]' WHERE id='$id_op'  ";
	            $this->query($myQuery);
	
	            $myQuery = "DELETE FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$id_op';";
	            $this->query($myQuery);
	
	
	
	            $last_id = $id_op;
	            if($last_id>0){
	                $cad='';
	                
	                    $cad.="('".$last_id."','".$idprod."','".$cant."'),";
	
	               
	                $cadtrim = trim($cad, ',');
	                $myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ".$cadtrim.";";
	                $query = $this->query($myQuery);
	            }
	            return $id_op;
            }

        }


function autorizar($id){

 $myQuery = "UPDATE prd_orden_produccion set autorizado=1 where id='$id'";



            $resultb = $this->query($myQuery);


           return $resultb;
}


        function bandera(){


 			$myQuery = "SELECT aut_ord_prod,genoc_sinreq,insumosvariables,explosionmat,regordenp,mostrar_prov_op FROM prd_configuracion ;";



            $resultb = $this->query($myQuery);
             

            $row = $resultb->fetch_array();

            return $row;
        }

        function getEmpleados()
        {
            $myQuery = "SELECT a.idEmpleado as idempleado, concat(a.nombreEmpleado,' ',a.apellidoPaterno,' ',a.apellidoMaterno) as nombre, b.nombre as nomarea FROM nomi_empleados a
            left join app_area_empleado b on b.id=a.id_area_empleado ORDER BY a.nombreEmpleado;";
            $empleados = $this->query($myQuery);
            return $empleados;
        }


        function listaOrdenesP(){
            $myQuery = "SELECT a.id, pr.nombre,pd.cantidad,SUBSTRING(a.fecha_registro,1,10) as fr, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as usuario, a.estatus, a.autorizado,pr.insumovariable
            FROM prd_orden_produccion a
            INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
            left JOIN mrp_sucursal d on d.idSuc=a.id_sucursal 
            inner join prd_orden_produccion_detalle pd on pd.id_orden_produccion=a.id
            inner join app_productos pr on pr.id=pd.id_producto
            ORDER BY a.id desc;";



            $listaReq = $this->query($myQuery);

            return $listaReq;

        }

        function listaAgrupas(){
            $myQuery = 'SELECT a.id, a.nombre, case
               when a.estatus=1
               then "<span class=\"label label-primary\" style=\"cursor:pointer;\">Activa </span>"
               when a.estatus=0
               then "<span class=\"label label-default\" style=\"cursor:pointer;\">Inactiva</span>"
               end as label,

               case
            when a.estatus=1 
            then concat("<button style= \" margin-top:4px;\"  onclick=\"verEti(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Ver</button> <button style= \" margin-top:4px;\"  onclick=\"elimiarEti(", a.id ,");\" class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Eliminar</button>")
            when a.estatus=0
            then concat("<button disabled=\"disabled\" style= \" margin-top:4px;\"  onclick=\"verEti(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Ver</button> <button disabled=\"disabled\" style= \" margin-top:4px;\"  onclick=\"elimiarEti(", a.id ,");\" class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Eliminar</button>")
            else concat("")
            end as boton

                 FROM prd_agrupas a ORDER BY id desc';

               $listaReq = $this->query($myQuery);
            return $listaReq;
        }

        function listaEtiquetas(){
            $myQuery = 'SELECT a.id, a.nombre_etiqueta, case
               when a.estatus=1
               then "<span class=\"label label-primary\" style=\"cursor:pointer;\">Activa </span>"
               when a.estatus=0
               then "<span class=\"label label-default\" style=\"cursor:pointer;\">Inactiva</span>"
               end as label,

               case
            when a.estatus=1 
            then concat("<button style= \" margin-top:4px;\"  onclick=\"verEti(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Ver</button> <button style= \" margin-top:4px;\"  onclick=\"elimiarEti(", a.id ,");\" class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Eliminar</button>")
            when a.estatus=0
            then concat("<button disabled=\"disabled\" style= \" margin-top:4px;\"  onclick=\"verEti(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Ver</button> <button disabled=\"disabled\" style= \" margin-top:4px;\"  onclick=\"elimiarEti(", a.id ,");\" class=\"btn btn-danger btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Eliminar</button>")
            else concat("")
            end as boton

                 FROM prd_etiquetas a ORDER BY id desc';

               $listaReq = $this->query($myQuery);
            return $listaReq;
        }

        function listaOrdenesPre(){
            $myQuery = 'SELECT a.id_op, a.id,   SUBSTRING(a.fecha_creacion,1,10) as fc, d.razon_social,
               case
               when a.activo=1
               then "<span class=\"label label-primary\" style=\"cursor:pointer;\">En espera de insumos </span>"
               when a.activo=3
               then "<span class=\"label label-success\" style=\"cursor:pointer;\">Insumos recibidos</span>"
               end as label,
            case
            when c.pr=1 
            then concat("<button style= \" margin-top:4px;\" disabled=\"true\" onclick=\"activar(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Activar</button>")
            else concat("<button style= \" margin-top:4px;\" onclick=\"activar(", a.id ,");\" class=\"btn btn-primary btn-xs\"><span class=\"glyphicon glyphicon-edit\"></span> Activar</button>")
            end as boton

            FROM prd_prerequisicion a
            left JOIN app_requisiciones c on c.idprereq=a.id
            INNER JOIN mrp_proveedor d on d.idPrv=a.id_proveedor 
            ORDER BY a.id desc;';



            $listaReq = $this->query($myQuery);
            return $listaReq;

        }


        function listaOrdenes(){

            $myQuery = '(SELECT a.id,concat("Pasos (",(select count(*) from prd_pasos_producto where id_producto=c.id),")") as pasos,a.fecha_registro,a.fecha_p,a.fecha_f,c.nombre,b.cantidad,d.clave,e.nombre,f.nombre,concat(g.nombreEmpleado," ",g.apellidoPaterno," ",g.apellidoMaterno) as sol,  ((select count(*) from prd_ini_proceso where id_oproduccion=a.id)*100)/count(i.id_paso) as porcentaje,
case a.estatus
when 0 then "<span class=\"label label-danger\" style=\"cursor:pointer;\">Orden eliminada</span>"
when 1 then "<span class=\"label label-default\" style=\"cursor:pointer;\">Registro inicial</span>"

when 2 then "<span class=\"label label-warning\" style=\"cursor:pointer;\">En espera de insumos</span>"
when 3 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 4 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 9 then "<span class=\"label label-info\" style=\"cursor:pointer;\">Produccion iniciada</span>"
when 10 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Produccion finalizada</span>"
end as estatus,concat("<button class=\"btn btn-primary btn-xs\" onClick=seg(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento</button><br><button class=\"btn btn-primary btn-xs\" onClick=seg2(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento Material Proceso</button><br><button class=\"btn btn-primary btn-xs\" onClick=segl(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento ligero</button>") as acciones

            from prd_orden_produccion a
            left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join app_unidades_medida d on d.id=c.id_unidad_venta
            left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
            
            group by a.id
            ORDER BY a.id desc)
            
            union all
            (select a.id,h.descripcion as pasos,"","","","","","","","","",(((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id)) as porcentaje,"",""
            from prd_orden_produccion a
                left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
               left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
                group by a.id,h.id
            ORDER BY a.id desc,h.id asc) order by 1 desc
            ;';





            $listaReq = $this->query($myQuery);
        
            return $listaReq;

        }
                function listaOrdenesf($ffin,$fini,$prod,$suc,$sol,$est){
                   

if($ffin!=''&&$fini!=''){
$filtro=$filtro.' and date(a.fecha_registro) between '.str_replace("-","",$fini).' and '.str_replace("-","",$ffin).' ';}
                    
$prod2=explode(",",$prod);
$sol2=explode(",",$sol);
$suc2=explode(",",$suc);
$est2=explode(",",$est);


                    if(!in_array("null", $prod2)){$filtro=$filtro." and c.id IN (".$prod.") ";}
  if(!in_array("null", $sol2)){$filtro=$filtro." and g.idEmpleado IN (".$sol.") ";

}
   if(!in_array("null", $suc2)){$filtro=$filtro." and e.idSuc IN (".$suc.") ";}
   if(!in_array("null", $est2)){$filtro=$filtro." and a.estatus IN (".$est.") ";}


            $myQueryf = '(SELECT a.id,concat("Pasos (",(select count(*) from prd_pasos_producto where id_producto=c.id),")") as pasos,a.fecha_registro,a.fecha_p,a.fecha_f,c.nombre,b.cantidad,d.clave,e.nombre,f.nombre,concat(g.nombreEmpleado," ",g.apellidoPaterno," ",g.apellidoMaterno) as sol,  ((select count(*) from prd_ini_proceso where id_oproduccion=a.id)*100)/count(i.id_paso) as porcentaje,
case a.estatus
when 0 then "<span class=\"label label-danger\" style=\"cursor:pointer;\">Orden eliminada</span>"
when 1 then "<span class=\"label label-default\" style=\"cursor:pointer;\">Registro inicial</span>"

when 2 then "<span class=\"label label-warning\" style=\"cursor:pointer;\">En espera de insumos</span>"
when 3 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 4 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 9 then "<span class=\"label label-info\" style=\"cursor:pointer;\">Produccion iniciada</span>"
when 10 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Produccion finalizada</span>"
end as estatus,concat("<button class=\"btn btn-primary btn-xs\" onClick=seg(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento</button><br><button class=\"btn btn-primary btn-xs\" onClick=seg2(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento Material Proceso</button><br><button class=\"btn btn-primary btn-xs\" onClick=segl(",a.id,") style=margin-top:4px;><span class=glyphicon glyphicon-edit></span>Seguimiento ligero</button>") as acciones

            from prd_orden_produccion a
            left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join app_unidades_medida d on d.id=c.id_unidad_venta
            left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
            where 1=1 '.$filtro.'
            group by a.id
            ORDER BY a.id desc)
            
            union all
            (select a.id,h.descripcion as pasos,"","","","","","","","","",(((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id)) as porcentaje,"",""
            from prd_orden_produccion a
                left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
               left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
            where 1=1 '.$filtro.'
                group by a.id,h.id
            ORDER BY a.id desc,h.id asc) order by 1 desc
            ;';

            $listaReq = $this->query($myQueryf);
            return $listaReq;

        }

         function seg($id){

            $myQuery = '(SELECT concat("Pasos (",(select count(*) from prd_pasos_producto where id_producto=c.id),")") as pasos,a.id,c.nombre,b.cantidad,case 
when sum(k.cantidad*l.costo) is null then 0
when sum(k.cantidad*l.costo) is not null then sum(k.cantidad*l.costo)
end as costo,((select count(*) from prd_ini_proceso where id_oproduccion=a.id)*100)/count(i.id_paso) as porcentaje,
case a.estatus
when 0 then "<span class=\"label label-danger\" style=\"cursor:pointer;\">Orden eliminada</span>"
when 1 then "<span class=\"label label-default\" style=\"cursor:pointer;\">Registro inicial</span>"

when 2 then "<span class=\"label label-warning\" style=\"cursor:pointer;\">En espera de insumos</span>"
when 3 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 4 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 9 then "<span class=\"label label-info\" style=\"cursor:pointer;\">Produccion iniciada</span>"
when 10 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Produccion finalizada</span>"
end as estatus,SEC_TO_TIME( SUM( TIME_TO_SEC(i.tiempo)))

            from prd_orden_produccion a
            left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join app_unidades_medida d on d.id=c.id_unidad_venta
            left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
            left join prd_prerequisicion j on j.id_op=a.id
            left join prd_prerequisicion_datos k on k.id_prerequisicion=j.id
            left join app_costos_proveedor l on l.id_producto=k.id_producto and l.id_proveedor=j.id_proveedor
            where a.id='.$id.'
            group by a.id
            ORDER BY a.id desc)
            
            union all
            (select h.descripcion as pasos,a.id,"","","",(((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id)) as porcentaje,
 case 
when 
          (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))=0 then "<span class=\"label label-default\" style=\"cursor:pointer;\">No iniciado</span>"

when ((((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))<100 and (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))>0) then "<span class=\"label label-warning\" style=\"cursor:pointer;\">Iniciado</span>"
when 
            (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))=100 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Finalizado</span>"
end as estatus


                ,SEC_TO_TIME( SUM( TIME_TO_SEC(i.tiempo)))
            from prd_orden_produccion a
                left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
               left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
               where a.id='.$id.'
                group by a.id,h.id
            ORDER BY a.id desc,h.id asc) order by 1 desc
            ;';





            $listaReq = $this->query($myQuery);
        
            return $listaReq;

        }


function seg2($id){

            $myQuery = '(SELECT concat("Pasos (",(select count(*) from prd_pasos_producto where id_producto=c.id),")") as pasos,a.id,c.nombre,b.cantidad,case 
when sum(k.cantidad*l.costo) is null then 0
when sum(k.cantidad*l.costo) is not null then sum(k.cantidad*l.costo)
end as costo,((select count(*) from prd_ini_proceso where id_oproduccion=a.id)*100)/count(i.id_paso) as porcentaje,
case a.estatus
when 0 then "<span class=\"label label-danger\" style=\"cursor:pointer;\">Orden eliminada</span>"
when 1 then "<span class=\"label label-default\" style=\"cursor:pointer;\">Registro inicial</span>"

when 2 then "<span class=\"label label-warning\" style=\"cursor:pointer;\">En espera de insumos</span>"
when 3 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 4 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 9 then "<span class=\"label label-info\" style=\"cursor:pointer;\">Produccion iniciada</span>"
when 10 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Produccion finalizada</span>"
end as estatus,SEC_TO_TIME( SUM( TIME_TO_SEC(i.tiempo))),"","","","","","1" as orden,""

            from prd_orden_produccion a
            left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join app_unidades_medida d on d.id=c.id_unidad_venta
            left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
            left join prd_prerequisicion j on j.id_op=a.id
            left join prd_prerequisicion_datos k on k.id_prerequisicion=j.id
            left join app_costos_proveedor l on l.id_producto=k.id_producto and l.id_proveedor=j.id_proveedor
            where a.id='.$id.'
            group by a.id
            ORDER BY a.id desc)
            
            union all
            (select h.descripcion as pasos,a.id,"","","",(((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id)) as porcentaje,
 case 
when (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))=0 then "<span class=\"label label-default\" style=\"cursor:pointer;\">No iniciado</span>"

when ((((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))<100 and (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))>0) then "<span class=\"label label-warning\" style=\"cursor:pointer;\">Iniciado</span>"
when (((select count(*) from prd_ini_proceso where id_oproduccion=a.id and id_paso=h.id)*100)/count(i.id))=100 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Finalizado</span>"
end as estatus


                ,SEC_TO_TIME( SUM( TIME_TO_SEC(i.tiempo))),"","","","","","2" as orden,h.id        
                from prd_orden_produccion a
                left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
               left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
               where a.id='.$id.'
                group by a.id,h.id
            ORDER BY a.id desc,h.id asc)
                 union all
       (select alias,a.id,"","","","","",tiempo,"",pieza,"","","","3" as orden,h.id
            from prd_orden_produccion a
                 left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join prd_pasos_producto h on h.id_producto=c.id
            left join prd_pasos_acciones_producto i on i.id_paso=h.id
               where a.id='.$id.'
                
            ORDER BY a.id desc,h.id asc
            ) order by 2 desc,15 asc,14 asc
             
            ;
          ';





            $listaReq = $this->query($myQuery);
        
            return $listaReq;

        }

              function segl($id){

            $myQuery = '(select a.id,c.nombre,b.cantidad,case 
when sum(n.cantidad*o.costo) is null then 0
when sum(n.cantidad*o.costo) is not null then sum(n.cantidad*o.costo)
end as costo,
case a.estatus
when 0 then "<span class=\"label label-danger\" style=\"cursor:pointer;\">Orden eliminada</span>"
when 1 then "<span class=\"label label-default\" style=\"cursor:pointer;\">Registro inicial</span>"

when 2 then "<span class=\"label label-warning\" style=\"cursor:pointer;\">En espera de insumos</span>"
when 3 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 4 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Lista para producir</span>"
when 9 then "<span class=\"label label-info\" style=\"cursor:pointer;\">Produccion iniciada</span>"
when 10 then "<span class=\"label label-success\" style=\"cursor:pointer;\">Produccion finalizada</span>"
end as estatus,sum(i.cantidad),sum(j.cantidad),l.clave

            from prd_orden_produccion a
            left join prd_orden_produccion_detalle b on b.id_orden_produccion=a.id
            left join app_productos c on c.id=b.id_producto
            left join app_unidades_medida d on d.id=c.id_unidad_venta
            left join mrp_sucursal e on e.idSuc=a.id_sucursal
            left join almacen f on f.idAlmacen=e.idAlmacen
            left join  nomi_empleados g on g.idEmpleado=a.solicitante
             left join prd_utilizados h on h.id_oproduccion=a.id
             left join prd_utilizados_detalle i on i.id_utilizado=h.id
                  left join prd_merma k on k.id_oproduccion=a.id
             left join prd_merma_detalle j on j.id_merma=k.id
         left join app_unidades_medida l on l.id=c.id_unidad_venta
                left join prd_prerequisicion m on m.id_op=a.id
            left join prd_prerequisicion_datos n on n.id_prerequisicion=m.id
               left join app_costos_proveedor o on o.id_producto=n.id_producto and o.id_proveedor=m.id_proveedor
            where a.id='.$id.'
            group by a.id
            ORDER BY a.id desc)
            union all
            (SELECT
                90 as ids, p.nombre, 
               "","","",i.cantidad,j.cantidad, uni.clave
                FROM app_productos p 
                INNER JOIN app_producto_material m ON p.id=m.id_material 
                LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra 
                LEFT JOIN app_costos_proveedor pro ON pro.id_producto=p.id
                    left join prd_utilizados h on h.id_oproduccion='.$id.'
             left join prd_utilizados_detalle i on i.id_utilizado=h.id and i.id_insumo=p.id
                  left join prd_merma k on k.id_oproduccion='.$id.'
             left join prd_merma_detalle j on j.id_merma=k.id and j.id_insumo=p.id
             left join app_unidades_medida uni on uni.id=p.id_unidad_venta
                WHERE
                p.status=1
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='.$id.') group by p.id) order by 1 asc;';





            $listaReq = $this->query($myQuery);
        
            return $listaReq;

        }

        function editarEtiqueta($idEti){
            $myQuery = "SELECT a.*, concat(b.nombre,' ',b.apellidos) as username FROM prd_etiquetas a
             INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
             WHERE a.id='$idEti';";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }

        function editarAgrupas($idEti){
            $myQuery = "SELECT a.*, concat(b.nombre,' ',b.apellidos) as username FROM prd_agrupas a
             INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
             WHERE a.id='$idEti';";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }


        function tiposEtiqueta($idEti,$m){

            $myQuery="SELECT a.*
                    from prd_etiquetas_detalle a
                    inner join prd_etiquetas b on b.id=a.id_etiqueta
                    WHERE a.id_etiqueta='$idEti' order by a.id desc;";

                    $prodsReq = $this->query($myQuery);
            return $prodsReq;

        }

        function editarordenp($idop){

            $myQuery = "SELECT a.id, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe, d.idSuc as idsuc, a.solicitante as idsol,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as username, a.estatus, a.prioridad, a.observaciones, b.idempleado,a.solicitante as idsol, concat('(',f.codigo,') ',f.nombre) as nombre, e.cantidad, f.peso_dimension,a.lote 
            FROM prd_orden_produccion a 
            INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
            left JOIN mrp_sucursal d on d.idSuc=a.id_sucursal
            left JOIN prd_orden_produccion_detalle e on e.id_orden_produccion=a.id
            left JOIN app_productos f on f.id=e.id_producto
            WHERE a.id='$idop';";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }

        

        function productosOp($idop,$m){
                    
            if($m==1){

                   $myQuery="SELECT a.*, c.id, c.codigo, c.nombre as nomprod, c.series, c.lotes, c.pedimentos, c.precio as precioorig, x.clave,c.minimoprod as minimos,c.insumovariable
                    from prd_orden_produccion_detalle a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_orden_produccion='$idop' group by a.id;";

            }else{
                  $myQuery="SELECT c.id, c.codigo, c.nombre as nomprod, a.cantidad, c.series, c.lotes, c.pedimentos, if(a.precio is null,0,a.precio) as costo,  if(sum(ee.cantidad) is null,0,sum(ee.cantidad)) as cantidadr, a.id_lista, c.precio as precioorig, x.clave, a.caracteristica, c.tipo_producto,c.minimoprod as minimos from app_requisiciones_datos_venta a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    left join app_envios_datos ee on ee.id_envio='$idEnv'
                     INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_requisicion='$idReq' group by a.id;";
            }

            

            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }
        function productosOpMasiva($idop){
                    
           

                   $myQuery="SELECT a.*, c.id, c.codigo, c.nombre as nomprod, c.series, c.lotes, c.pedimentos, c.precio as precioorig, x.clave,c.minimoprod as minimos,c.insumovariable
                    from prd_orden_produccion_detalle a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_orden_produccion in ($idop) group by a.id;";

            $prodsReq = $this->query($myQuery);
            return $prodsReq->fetch_assoc();


        }

        
        
        
        function sqlPaso2($idop,$idproducto){

                $myQuery="SELECT if(b.cantidad is null,0,b.cantidad) as cantUti
                FROM prd_utilizados a 
                INNER JOIN prd_utilizados_detalle b ON b.id_utilizado=a.id
                WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso3($idop,$idproducto){

                $myQuery="SELECT if(b.peso is null,0,b.peso) as pesoUti
                FROM prd_peso a 
                INNER JOIN prd_peso_detalle b ON b.id_peso=a.id
                WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso14($idop,$idproducto){

                $myQuery="SELECT if(b.cantidad is null,0,b.cantidad) as merma
                FROM prd_merma a 
                INNER JOIN prd_merma_detalle b ON b.id_merma=a.id
                WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function listar_pasos_op($idop){
            $sql="SELECT a.id as id_paso, a.descripcion as nombre_paso, a.id_producto, b.id as id_accion_producto, c.id as id_accion, if(b.alias='',c.nombre,b.alias) as nombre_accion, c.tiempo_hrs, d.nombre, if(e.id is null,0,1) as pasorealizado, b.tipo 
                from prd_pasos_producto a
                inner join prd_pasos_acciones_producto b on b .id_paso=a.id
                inner join prd_acciones c on c.id=b.id_accion
                inner join app_productos d on d.id=a.id_producto
                left join prd_ini_proceso e on e.id_oproduccion='$idop' and e.id_paso=b.id_paso and e.id_accion=c.id  and e.id_accion_producto = b.id
                where a.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') order by b.id asc, c.id asc;";
            // return $sql;
            $result = $this->queryArray($sql);

            return $result;
    }


        function sqlPaso7($idop,$idproducto){

                $myQuery="SELECT if(b.cantidad is null,0,b.cantidad) as cbatch
                FROM prd_batch a 
                INNER JOIN prd_batch_detalle b ON b.id_batch=a.id
                WHERE a.id_oproduccion='$idop' AND b.id_insumo='$idproducto';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso6($idop){

                $myQuery="SELECT b.no_lote, b.fecha_fabricacion, b.fecha_caducidad
                FROM prd_lote_detalles a 
                INNER JOIN app_producto_lotes b ON b.id=a.id_lote
                WHERE a.id_oproduccion='$idop';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso10($idop){

                $myQuery="SELECT a.*
                FROM prd_caja a
                WHERE a.id_oproduccion='$idop';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso15($idop){

                $myQuery="SELECT *
                FROM prd_costo_produccion 
                WHERE id_oproduccion='$idop';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function sqlPaso4($idop){

                $myQuery="SELECT c.idEmpleado, concat(c.nombreEmpleado,' ',c.apellidoPaterno) as nombre, maquinaria, a.id as idmaq
                FROM prd_personal_detalle a 
                INNER JOIN prd_personal b ON b.id=a.id_personal
                INNER JOIN nomi_empleados c ON c.idEmpleado=a.id_empleado
                WHERE b.id_oproduccion='$idop';";
            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function matProceso($idop){
            $myQuery="SELECT * FROM prd_matpro where id_oproduccion='$idop'; ";
            $p = $this->queryArray($myQuery);

            if($p['total']>0){
                $arreglo=array();
                foreach ($p['rows'] as $k => $v) {
                    if(array_key_exists($v['id_operador'], $arreglo)){
                        if(array_key_exists($v['id_insumo'], $arreglo[$v['id_operador']])){
                            $arreglo[$v['id_operador']][$v['id_insumo']]=$v['cantidad'];
                        }else{
                            $arreglo[$v['id_operador']][$v['id_insumo']]=$v['cantidad'];
                        }
                    }else{
                        $arreglo[$v['id_operador']][$v['id_insumo']]=$v['cantidad'];
                    }
                }
            }else{
                $arreglo=0;
            }


            return $arreglo;

        }


        function getEtiquetaPrint($idop){
             $myQuery="SELECT e.nombre, a.codigo, concat('Fecha de caducidad: ',substr(c.fecha_caducidad,1,10)) as fecha, concat('No. Lote: ',c.no_lote) as lote, concat(a.peso,'Kg') as peso  from prd_gencode a
                left join prd_lote_detalles b on b.id_oproduccion=a.id_op
                left join app_producto_lotes c on c.id=b.id_lote
                inner join prd_orden_produccion_detalle d on d.id_orden_produccion=a.id_op
                inner join app_productos e on e.id=d.id_producto
                WHERE a.id_op='$idop';";
            $q = $this->queryArray($myQuery);
            return $q;

        }

        function getEtiqueta($idop,$paso,$accion){
            $myQuery="SELECT a.id, a.id_etiqueta, a.id_tipo, a.digitos, a.estatus, b.nombre_etiqueta from prd_etiquetas_detalle a 
inner join prd_etiquetas b on b.id=a.id_etiqueta 
inner join prd_pasos_acciones_producto c on c.id_etiqueta=b.id 
WHERE c.id_paso='$paso' AND c.id_accion='$accion' ORDER BY a.id_tipo asc;";
$q = $this->queryArray($myQuery);
            return $q;

        }

        function historial11($idop, $idap ,$opc){
            $myQuery="SELECT  b.id,b.id_operador as idOperador, concat(e.nombreEmpleado,' ',e.apellidoPaterno) as nombreemp, b.f_ini, if(b.f_fin is null,0,b.f_fin) as f_fin,
			f.id as idProducto, f.nombre, a.cantidad, b.id_pa,b.cantppf
			from prd_matpro a 
			inner join prd_matp b on b.id=a.id_mp
			inner join prd_personal c on c.id_oproduccion=b.id_oproduccion
			inner join prd_personal_detalle d on d.id_personal=c.id and d.id_empleado=b.id_operador
			inner join nomi_empleados e on e.idEmpleado=d.id_empleado
			inner join app_productos f on f.id=a.id_insumo
			where b.id_oproduccion='$idop' ";
			if($opc == 0){
				$myQuery.="AND b.id_pa='$idap';";
			}
$q = $this->queryArray($myQuery);
            return $q;

        }

        function getUsados($idop, $idap, $idProd,$accion){
        	if($accion == 11){ $valor ="AND b.id_pa='$idap'";}else{$valor="";}
            $myQuery="SELECT a.id_insumo, round(sum(a.cantidad),3) as tot_real 
            FROM prd_matpro a 
            INNER JOIN prd_matp b ON b.id=a.id_mp 
            WHERE b.id_oproduccion='$idop' $valor and a.id_insumo='$idProd' GROUP BY a.id_insumo;";

$q = $this->queryArray($myQuery);
            return $q;

        }

        function finalizar($id){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "UPDATE prd_matp SET f_fin='$creacion' WHERE id='$id';";
            $this->query($myQuery);

        }

        function getAlmacen($idop){
            $myQuery="SELECT a.id as idalmacen from app_almacenes a 
inner join prd_orden_produccion c on c.id_sucursal=a.id_sucursal
WHERE c.id='$idop' limit 1;";

                $p = $this->queryArray($myQuery);
                return $p;
        }

        function costoOpInv($idop){

            $myQuery="SELECT
                sum(pro.costo) as costo
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                WHERE
                p.status=1
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop');";

                $p = $this->queryArray($myQuery);
                return $p;

        }

        function getAgrupados($idp,$idedicion){

            if ($idedicion=='') {
                $filtro='';
               
            }else{
                 $filtro="and id='$idedicion'";
            }
            //echo "SELECT * from prd_agrupacion WHERE id_producto='$idp' $filtro order by nombre_agrupacion;";
            $myQuery="SELECT * from prd_agrupacion WHERE id_producto='$idp' $filtro order by nombre_agrupacion;";

            $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;


        }

        function guardaGrupo($idp,$nombre,$guardaredicion){

            session_start();
            if( !isset($_SESSION['insumos_producto']) ){
                echo 0;
                exit();
            }

             $myQuery="DELETE FROM prd_agrupacion WHERE id='$guardaredicion';";
            $this->queryArray($myQuery);

            $myQuery="DELETE FROM prd_agrupacion_detalle WHERE id_agrupacion='$guardaredicion';";
            $this->queryArray($myQuery);

            $myQuery = "INSERT INTO prd_agrupacion (nombre_agrupacion,id_producto) VALUES ('$nombre','$idp');";
            $last_id = $this->insert_id($myQuery);



            $cad='';
            foreach ($_SESSION['insumos_producto'] as $k => $v){
                //print_r($_SESSION['insumos_producto']);
                $cad.="('".$last_id."','".$v['cantidad']."','".$v['id']."'),";
            }

            $cad=trim($cad,',');



             $sql = "INSERT INTO prd_agrupacion_detalle (id_agrupacion, cantidad, id_insumo) VALUES ".$cad." ";

            $this->query($sql);
            echo 1;



        }

        function eliAgrupados($id){
            $myQuery="DELETE FROM prd_agrupacion WHERE id='$id';";
            $this->queryArray($myQuery);

            $myQuery="DELETE FROM prd_agrupacion_detalle WHERE id_agrupacion='$id';";
            $this->queryArray($myQuery);

        }

        function productosExplosion($idp,$idedicion){

            if ($idedicion=='') {
                $filtro='';
               
            }else{
                 $filtro="and e.id_agrupacion ='$idedicion'";
            }

            $myQuery="SELECT a.id_material as id, a.cantidad, b.codigo, b.nombre, c.clave as claveunidad,c.nombre as nombreunidad,
if(round(sum(e.cantidad),4) is null,0,round(sum(e.cantidad),4)) as usada, if(a.cantidad-round(sum(e.cantidad),4) is null,a.cantidad,a.cantidad-round(sum(e.cantidad),4)) as disponible,e.id_agrupacion
FROM app_producto_material a
                inner join app_productos b on b.id=a.id_material
                left JOIN app_unidades_medida c on c.id=b.id_unidad_compra
                left join prd_agrupacion d on d.id_producto=a.id_producto
                left join prd_agrupacion_detalle e on e.id_agrupacion=d.id and e.id_insumo=a.id_material
                where a.id_producto='$idp' and a.status=1  $filtro group by a.id;";

            $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;


        }

        function buscaAgrupadas($idop){

            $myQuery="SELECT id,dependencia from prd_orden_produccion where id ='$idop';";
            $r1 = $this->queryArray($myQuery);
            $depende = $r1['rows'][0]['dependencia'];


            if($depende==0){
                $idPadre=$idop;
            }else{
                $exp=explode('-', $depende);
                $idPadre=$exp[0];
            }


            $myQuery="SELECT id from prd_orden_produccion where id='".$idPadre."' and estatus=4 UNION ALL SELECT id from prd_orden_produccion where estatus=4 and dependencia like '".$idPadre."%';";
            $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;

        }

        function productosOpExplosion($idop){
            $m=1;
            if($m==1){

            $myQuery="SELECT
                p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti, p.insumovariable,x.cantidad as cantproduct,
				
				 (select sum(e.cantidad*y.cantidad) FROM app_productos r INNER JOIN app_producto_material e ON r.id=e.id_material LEFT JOIN app_unidades_medida g ON g.id=r.id_unidad_compra                
 				INNER JOIN prd_orden_produccion_detalle y on y.id_orden_produccion= $idop AND e.id_producto=y.id_producto
                WHERE
                r.status=1
                AND
                e.id_producto in  (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion=$idop) and g.clave =u.clave) as cantidadunidad
               
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$idop' AND m.id_producto=x.id_producto
                WHERE
                p.status=1
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') group by p.id;";

            }

            

            $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;


        }
//explosion masiva
         function productosOpExplosionMasiva($idops){
                $myQuery="SELECT
                p.id AS idProducto, p.nombre, 
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales, p.lotes, 							sum(m.cantidad*x.cantidad) as canti, p.insumovariable,x.cantidad as cantproduct,
				
					 (select sum(e.cantidad*y.cantidad)
				FROM app_productos r 
				INNER JOIN app_producto_material e ON r.id=e.id_material 
				LEFT JOIN app_unidades_medida g ON g.id=r.id_unidad_compra                
	 			INNER JOIN prd_orden_produccion_detalle y on y.id_orden_produccion in($idops) AND e.id_producto=y.id_producto
	                WHERE
	                r.status=1
	                AND
	                e.id_producto in  (SELECT id_producto FROM prd_orden_produccion_detalle 
	                WHERE id_orden_produccion in($idops)) and g.clave =u.clave) as cantidadunidad,
	                x.id_orden_produccion
	                
	             FROM app_productos p 
	             left JOIN app_producto_material m ON p.id=m.id_material 
	             LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra 
	             left JOIN prd_orden_produccion_detalle x on x.id_orden_produccion in($idops) AND m.id_producto=x.id_producto
	            WHERE
	                p.status=1
	                AND
	                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion in($idops)) group by p.id;";



             $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;
           


        }
        
        //fin explosion masiva
        function productosOpExplosionProceso($idop,$idap){//krmn

            $myQuery="SELECT
                p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs, p.lotes, (m.cantidad*x.cantidad) as canti, IFNULL(cc.cantidad,0) as cantproceso,x.cantidad as totaldeproduct
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                INNER JOIN prd_orden_produccion_detalle x on x.id_orden_produccion='$idop' AND m.id_producto=x.id_producto
                inner join prd_pasos_acciones_producto aa on aa.id='$idap'
                left join prd_agrupacion bb on bb.id=aa.id_agrupacion
                left join prd_agrupacion_detalle cc on cc.id_agrupacion=bb.id and cc.id_insumo=p.id
                WHERE
                p.status=1
                AND
                m.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$idop') group by p.id;";


            $prodsReq = $this->queryArray($myQuery);
            return $prodsReq;


        }

        function proveedoresCostoOP($proveedores){

       

            $myQuery = "SELECT a.costo, a.id_proveedor, b.razon_social FROM app_costos_proveedor a inner join mrp_proveedor b on b.idPrv=a.id_proveedor where a.id in($proveedores);";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }
        function proveedoresCostoOParaMasivo($proveedores){
			$myQuery = "SELECT a.costo, a.id_proveedor, b.razon_social FROM app_costos_proveedor a inner join mrp_proveedor b on b.idPrv=a.id_proveedor where a.id_producto=$proveedores;";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }

        function delEtiqueta($id){
            $myQuery = "UPDATE prd_etiquetas SET estatus=0 WHERE id='$id';";
            $update = $this->query($myQuery);
            return $update;
        }

        function delAgrupas($id){
            $myQuery = "UPDATE prd_agrupas SET estatus=0 WHERE id='$id';";
            $update = $this->query($myQuery);
            return $update;
        }

        function delOP($idop){
            $myQuery = "UPDATE prd_orden_produccion SET estatus=0 WHERE id='$idop';";
            $update = $this->query($myQuery);
            return $update;
        }

        function getExistencias($idProducto,$caracteristicas)
        {
            $caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
            if($caracteristicas != '0'){
                    $carac = " AND id_producto_caracteristica =\"".$caracteristicas."\" ";
            }else{
                $carac='';
            }

            


                 $myQuery2="SELECT a.id,a.codigo_manual, a.codigo_sistema, a.nombre, 
@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
 = ".$idProducto." ".$carac."  AND id_pedimento = 0 AND id_lote = 0  ) AS entradas,
@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
 = ".$idProducto." ".$carac."   AND id_pedimento = 0 AND id_lote = 0  ) AS salidas,
(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
FROM app_almacenes a WHERE a.activo = 1 and a.id=1
ORDER BY a.codigo_sistema;";

                $totpedis = $this->queryArray($myQuery2);
                $cant=0;
                foreach ($totpedis['rows'] as $k2 => $v2) {
                    //$cant+=$v2['cantidad'];

                    if($v2['cantidad']>0){
                        $arrPedis[]=array('idAlmacen'=>$v2['id'].'-'.$v2['cantidad'].'-#*-'.$v2['nombre'], 'cantidad'=>$v2['cantidad'], 'almacen'=>$v2['nombre']);
                    }
                }

                
            
            
            return $arrPedis;

        }

        function getLotes($idProducto,$caracteristicas)
        {
            $caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
            if($caracteristicas != '0'){
                    $carac = " AND id_producto_caracteristica =\"".$caracteristicas."\" ";
            }else{
                $carac='';
            }

            $myQuery = "SELECT a.id,a.no_lote from app_producto_lotes a
                inner join app_inventario_movimientos b on b.id_lote=a.id
                WHERE b.id_producto='$idProducto'
                group by a.id;";

            $pedimentos = $this->queryArray($myQuery);

            $arrPedis=array();
            foreach ($pedimentos['rows'] as $k => $v) {
 

                $myQuery2="SELECT a.id, a.codigo_manual, a.codigo_sistema, a.nombre, 
@e := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
 = ".$idProducto." ".$carac." AND id_pedimento = 0 AND id_lote = ".$v['id']."  ) AS entradas,
@s := (SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
 = ".$idProducto." ".$carac." AND id_pedimento = 0 AND id_lote = ".$v['id']."  ) AS salidas,
(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
FROM app_almacenes a WHERE a.activo = 1
ORDER BY a.codigo_sistema;";

                $totpedis = $this->queryArray($myQuery2);
                $cant=0;
                foreach ($totpedis['rows'] as $k2 => $v2) {
                    //$cant+=$v2['cantidad'];

                    if($v2['cantidad']>0){
                        $arrPedis[]=array('idLote'=>$v['id'].'-'.$v2['id'].'-'.$v2['cantidad'].'-#*-'.$v['no_lote'].' ('.$v2['nombre'].')', 'cantidad'=>$v2['cantidad'], 'numero'=>'Lote: '.$v['no_lote'].' - '.$v2['nombre']);
                    }
                }

                
            }
            
            return $arrPedis;

        }

        function getExistenciasNueva($idProducto,$caracteristicas,$lote){
            $caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
            $myQuery2="SELECT sum(if(cantidad is null,0,cantidad)) as cantidad, sum(if(apartados is null,0,apartados)) as apartados FROM app_inventario WHERE id_producto='$idProducto' AND caracteristicas =\"$caracteristicas\"; ";
            $totpedis = $this->queryArray($myQuery2);
            $cantidad = $totpedis['rows'][0]['cantidad']-$totpedis['rows'][0]['apartados'];
            if($cantidad=='' || $cantidad==NULL){
                $cantidad=0;
            }
            return $cantidad;
        }

        function getExistenciasT($idProducto,$caracteristicas,$lote)
        {
            $caracteristicas = preg_replace('/([0-9])+/', '\'\0\'', $caracteristicas);
            if($caracteristicas != '0'){
                    $carac = " AND id_producto_caracteristica =\"".$caracteristicas."\" ";
            }else{
                $carac='';
            }

            


                 $myQuery2="SELECT a.id, a.codigo_manual, a.codigo_sistema, a.nombre, 
@e := sum((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_destino = a.id AND id_producto
 = ".$idProducto." ".$carac."  AND id_pedimento = 0 AND id_lote = 0  )) AS entradas,
@s := sum((SELECT SUM(cantidad) FROM app_inventario_movimientos WHERE id_almacen_origen = a.id AND id_producto
 = ".$idProducto." ".$carac."   AND id_pedimento = 0 AND id_lote = 0  )) AS salidas,
(IFNULL(@e,0) - IFNULL(@s,0)) AS cantidad
FROM app_almacenes a WHERE a.activo = 1 and a.id=1
ORDER BY a.codigo_sistema;";

                $totpedis = $this->queryArray($myQuery2);
                // $cant=0;
                // foreach ($totpedis['rows'] as $k2 => $v2) {
                //     //$cant+=$v2['cantidad'];

                //     if($v2['cantidad']>0){
                //         $arrPedis[]=array('idAlmacen'=>$v2['id'].'-'.$v2['cantidad'].'-#*-'.$v2['nombre'], 'cantidad'=>$v2['cantidad'], 'almacen'=>$v2['nombre']);
                //     }
                // }

                $cantidad = $totpedis['rows'][0]['entradas']-$totpedis['rows'][0]['salidas'];

            
            return $cantidad;

        }
		function accion18Existe($idop,$idaccionpaso){
			$sql = $this->query("SELECT count(b.id) as id
                from prd_pasos_producto a
                inner join prd_pasos_acciones_producto b on b .id_paso=a.id and b.id_accion=18
                inner join app_productos d on d.id=a.id_producto
                where a.id_producto in (SELECT id_producto FROM prd_orden_produccion_detalle WHERE id_orden_produccion=$idop) 
                and b.id>$idaccionpaso");
			$en = $sql->fetch_object();
			return $en->id;
			
		}
		function ordenPrdIniciada($idproduct){
			$sql = $this->query("select * from prd_orden_produccion_detalle pd
			inner join prd_orden_produccion p on p.id=pd.id_orden_produccion and p.estatus=4
			where pd.id_producto=$idproduct;");
			//si tiene iniciada una orden del producto
			if($sql->num_rows>0){
				return 1;
			}else{
				return 0;
			}
		}
		function sucursalUsuario($iduser){
			$sql = $this->query("select idSuc from administracion_usuarios u where u.idempleado=$iduser");
			if($sql->num_rows>0){
				$s = $sql->fetch_object();
				return $s->idSuc;
			}else{
				return 0;
			}
		}
		function tipoMerma(){
			$sql =$this->query("select * from app_merma_tipo;");
			if($sql->num_rows>0){
				return $sql;
			}else{
				return 0;
			}
		}

        function editaragrupacion($ideditar){
        
              $myQuery = $this->query("SELECT p.id,d.id_producto FROM prd_orden_produccion p
                          inner join prd_orden_produccion_detalle d on d.id_orden_produccion=p.id
                          WHERE p.estatus=9 and d.id_producto=$ideditar;");

                if($myQuery->num_rows>0){
                    return 1;
                 }else{
                    return 0;
            }


        }

    }
?>