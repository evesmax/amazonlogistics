<?php
//Carga la clase de coneccion con sus metodos para consultas o transacciones
//require("models/connection.php"); // funciones mySQL 
require("models/connection_sqli.php"); // funciones mySQLi

    class ConfigModel extends Connection{

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
          

            $myQuery = "SELECT a.id, a.codigo, if(a.descripcion_corta='',a.nombre,a.descripcion_corta) as descripcion_corta, a.precio as costo, x.clave, a.tipo_producto FROM app_productos a
                INNER join app_unidades_medida x on x.id=a.id_unidad_venta
                WHERE a.id='$idProducto'  group by a.id;";

            $producto = $this->query($myQuery);
            return $producto;
        }

        function getProductos5()
        {
            $myQuery = "SELECT id, nombre FROM app_productos WHERE tipo_producto='5' ORDER BY nombre;";
            $productos = $this->query($myQuery);
            return $productos;


        }

        function getUsuario(){
            session_start();
            $idusr = $_SESSION['accelog_idempleado'];

            $myQuery = "SELECT concat(nombre,' ',apellido1) as username, idempleado from empleados where idempleado='$idusr';";
            $nreq = $this->query($myQuery);
            //session_destroy();
            return $nreq;
        }

        function getLastOrden()
        {
            $myQuery = "SELECT if(MAX(id) is NULL,1,MAX(id)+1) as id from prd_orden_produccion;";
            $nreq = $this->query($myQuery);
            return $nreq;
        }

        function getSucursales()
        {
            $myQuery = "SELECT idSuc, nombre from mrp_sucursal order by nombre;";
            $nreq = $this->query($myQuery);
            return $nreq;
        }

        function saveConfig($opcion,$gap,$apop,$notc,$hereda,$insdir,$ocop,$ocsinr,$deaalm,$salins,$capaso,$gaop,$invprod,$agins,$insumvar,$explomate,$ordenprod,$mostprovee,$productPedidos,$reabasto_insumos,$ord_x_lotes){
            
            if($opcion==1){
                $myQuery = "UPDATE prd_configuracion SET gen_aut_ped='$gap', aut_ord_prod='$apop', not_correo='$notc', heredar_op='$hereda', req_insumos='$insdir', gen_aut_op='$gaop', agins='$agins',explosionmat='$explomate',regordenp='$ordenprod',ord_x_lotes=$ord_x_lotes  WHERE id='1';";
                $this->query($myQuery);
            }

            if($opcion==2){
                $myQuery = "UPDATE prd_configuracion SET oc_seareq='$ocop', genoc_sinreq='$ocsinr', mostrar_prov_op='$mostprovee' WHERE id='1';";
                $this->query($myQuery);
            }

            if($opcion==3){
                $myQuery = "UPDATE prd_configuracion SET designar_almacen='$deaalm', salida_autinsumos='$salins', invprod='$invprod' WHERE id='1';";
                $this->query($myQuery);
            }

            if($opcion==4){
                $myQuery = "UPDATE prd_configuracion SET capaso='$capaso', insumosvariables = '$insumvar',
                produccion_pedidos = '$productPedidos',reabasto_insumos=$reabasto_insumos  WHERE id='1';";
                $this->query($myQuery);
            }

        }

        function loadConfig(){
            $myQuery = "SELECT * FROM prd_configuracion WHERE id=1;";
            $resultModel = $this->query($myQuery);
            return $resultModel;
        }

        function saveOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $myQuery = "INSERT INTO prd_orden_produccion (id_usuario,id_sucursal,fecha_registro,fecha_inicio,fecha_entrega,estatus,observaciones,prioridad) VALUES ('$iduserlog','$sucursal','$creacion','$fecha_registro','$fecha_entrega','1','".$this->nl2brCH($obs)."','$prioridad');";

            $last_id = $this->insert_id($myQuery);

            if($last_id>0){
                $cad='';
                $productos = explode('--c--', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>', $v);
                    $idprod=$exp[0];
                    $cant=$exp[1];
                    
                    $cad.="('".$last_id."','".$idprod."','".$cant."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
            }

            return $last_id;

        }

        function savePre($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op){
            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s');

            $o=explode('--c--', $idsProductos);


            $arraypro=array();

            foreach ($o as $key => $value) {

                $q=explode('>', $value);
                $idpro=$q[0];

                if (array_key_exists($idpro, $arraypro)) {
                    $arraypro[$q[0]][]=array('idpadre'=>$q[1], 'idproducto'=>$q[2], 'cantidad'=>$q[3]);
                }else{

                    $arraypro[$q[0]][]=array('idpadre'=>$q[1], 'idproducto'=>$q[2], 'cantidad'=>$q[3]);

                }
                

            
            }


            foreach ($arraypro as $k => $v) {
   
                $myQuery = "INSERT INTO prd_prerequisicion (id_op,id_usuario,id_proveedor,observaciones_pre,fecha_creacion,activo,subtotal,total) VALUES ('$id_op','$iduserlog','$k','".$this->nl2brCH($obs)."','$creacion','1','0','0');";

                $last_id = $this->insert_id($myQuery);

                if($last_id>0){
                    $cad='';
                    foreach ($arraypro[$k] as $k2 => $v2) {
                        $cad.="('".$last_id."','".$v2['idproducto']."','1','1','".$v2['cantidad']."','".$v2['idpadre']."'),";
                    }
                    $cadtrim = trim($cad, ',');
                    $myQuery = "INSERT INTO prd_prerequisicion_datos (id_prerequisicion,id_producto,estatus,activo,cantidad,id_producto_padre) VALUES ".$cadtrim.";";
                    $query = $this->query($myQuery);
                }

            }

            $myQuery = "UPDATE prd_orden_produccion SET estatus='2' WHERE id='".$id_op."';";
            $query = $this->query($myQuery);

            echo 'p';

        }

        function modifyOP($idsProductos,$fecha_registro,$fecha_entrega,$prioridad,$sucursal,$option,$obs,$iduserlog,$id_op){

            date_default_timezone_set("Mexico/General");
            $creacion=date('Y-m-d H:i:s'); 

            $myQuery = "UPDATE prd_orden_produccion SET id_usuario='$iduserlog', id_sucursal='$sucursal', fecha_registro='$creacion', fecha_inicio='$fecha_registro', fecha_entrega='$fecha_entrega', observaciones='".$this->nl2brCH($obs)."', prioridad='$prioridad' WHERE id='$id_op'  ";
            $this->query($myQuery);

            $myQuery = "DELETE FROM prd_orden_produccion_detalle WHERE id_orden_produccion='$id_op';";
            $this->query($myQuery);

            $last_id = $id_op;
            if($last_id>0){
                $cad='';
                $productos = explode('--c--', $idsProductos);
                foreach ($productos as $k => $v) {
                    $exp=explode('>', $v);
                    $idprod=$exp[0];
                    $cant=$exp[1];
                    
                    $cad.="('".$last_id."','".$idprod."','".$cant."'),";

                }
                $cadtrim = trim($cad, ',');
                $myQuery = "INSERT INTO prd_orden_produccion_detalle (id_orden_produccion,id_producto,cantidad) VALUES ".$cadtrim.";";
                $query = $this->query($myQuery);
            }
            return $id_op;

        }

        function listaOrdenesP(){
            $myQuery = "SELECT a.id, SUBSTRING(a.fecha_registro,1,10) as fr, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe,d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as usuario, a.estatus
            FROM prd_orden_produccion a
            INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
            INNER JOIN mrp_sucursal d on d.idSuc=a.id_sucursal 
            ORDER BY a.id desc;";



            $listaReq = $this->query($myQuery);
            return $listaReq;

        }

        function listaOrdenesPre(){
            $myQuery = "SELECT a.id_op, a.id,   SUBSTRING(a.fecha_creacion,1,10) as fc, d.razon_social
            FROM prd_prerequisicion a
            INNER JOIN mrp_proveedor d on d.idPrv=a.id_proveedor 
            ORDER BY a.id desc;";



            $listaReq = $this->query($myQuery);
            return $listaReq;

        }

        function editarordenp($idop){

            $myQuery = "SELECT a.id, SUBSTRING(a.fecha_inicio,1,10) as fi, SUBSTRING(a.fecha_entrega,1,10) as fe, d.idSuc as idsuc, d.nombre as sucursal, concat(b.nombre,' ',b.apellidos) as username, a.estatus, a.prioridad, a.observaciones, b.idempleado FROM prd_orden_produccion a 
            INNER JOIN administracion_usuarios b on b.idempleado=a.id_usuario
            INNER JOIN mrp_sucursal d on d.idSuc=a.id_sucursal 
            WHERE a.id='$idop';";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }

        function productosOp($idop,$m){
                    
            if($m==1){

                   $myQuery="SELECT a.*, c.id, c.codigo, c.nombre as nomprod, c.series, c.lotes, c.pedimentos, c.precio as precioorig, x.clave
                    from prd_orden_produccion_detalle a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_orden_produccion='$idop' group by a.id;";

            }else{
                  $myQuery="SELECT c.id, c.codigo, c.nombre as nomprod, a.cantidad, c.series, c.lotes, c.pedimentos, if(a.precio is null,0,a.precio) as costo,  if(sum(ee.cantidad) is null,0,sum(ee.cantidad)) as cantidadr, a.id_lista, c.precio as precioorig, x.clave, a.caracteristica, c.tipo_producto from app_requisiciones_datos_venta a
                    INNER JOIN app_productos c on c.id = a.id_producto
                    left join app_envios_datos ee on ee.id_envio='$idEnv'
                     INNER join app_unidades_medida x on x.id=c.id_unidad_venta
                    WHERE a.id_requisicion='$idReq' group by a.id;";
            }

            

            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function productosOpExplosion($idop,$idproducto){
            $m=1;
            if($m==1){

                $myQuery="SELECT
                p.id AS idProducto, p.nombre, IF(p.tipo_producto=4, ROUND(p.costo_servicio, 2), IFNULL(pro.costo,0)) AS costo,
                p.id_unidad_compra AS idunidadCompra, p.id_unidad_venta AS idunidad, p.tipo_producto, p.descripcion_corta,
                (SELECT nombre FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad,
                (SELECT clave FROM app_unidades_medida uni WHERE uni.id=p.id_unidad_venta) AS unidad_clave, p.codigo, u.factor, m.cantidad, m.opcionales AS opcionales,  GROUP_CONCAT(pro.id) as idcostoprovs
                FROM app_productos p INNER JOIN app_producto_material m ON p.id=m.id_material LEFT JOIN app_unidades_medida u ON u.id=p.id_unidad_compra LEFT JOIN app_costos_proveedor pro ON
                pro.id_producto=p.id
                WHERE
                p.status=1
                AND
                m.id_producto ='$idproducto' group by p.id;";

            }

            

            $prodsReq = $this->query($myQuery);
            return $prodsReq;


        }

        function proveedoresCostoOP($proveedores){

       

            $myQuery = "SELECT a.costo, a.id_proveedor, b.razon_social FROM app_costos_proveedor a inner join mrp_proveedor b on b.idPrv=a.id_proveedor where a.id in($proveedores);";
            $datosReq = $this->query($myQuery);
            return $datosReq;

        }

        function delOP($idop){
            $myQuery = "UPDATE prd_orden_produccion SET estatus=0 WHERE id='$idop';";
            $update = $this->query($myQuery);
            return $update;
        }

    //funcion para actualizar los valores de las tablas atributos y tipos de productos. AM
    function UpdateActtr($types,$attributes){

        $myQuery =
        "update  app_atributo_producto   
         inner join 
            ( ";
        foreach ($attributes as $item ){  
            $myQuery .= "       Select ".$item->id." as id, ".$item->visible. " as visible union all" ;
        }
       
        $myQuery = substr($myQuery, 0,-10);
        $myQuery .= "
            ) as valores_atributos
                on app_atributo_producto.id = valores_atributos.id
                set app_atributo_producto.visible = valores_atributos.visible
                

            ;";


           if(!$this->query($myQuery)){
                echo 0;
            }else{
                echo 1;
        }
        
        $myQuery2 =
        "update  app_tipo_producto   
         inner join 
            ( ";
        foreach ($types as $item ){  
             $myQuery2 .= "       Select ".$item->id." as id, ".$item->visible. " as visible, ".$item->vendible. " as vendible union all" ;
        }
        $myQuery2 = substr($myQuery2, 0, -10);
        $myQuery2 .= "
            ) as valores_atributos
                on app_tipo_producto.id = valores_atributos.id
                set app_tipo_producto.visible = valores_atributos.visible,
                app_tipo_producto.vendible = valores_atributos.vendible;";


            if(!$this->query($myQuery2)){
                echo 0;
            }else{
                echo 1;
        }
        
        }
       
       // Función para regresar a la vista los valores seleccionados AM
        function configproductos(){

            $attributes = "SELECT * FROM app_atributo_producto;";
            $attr = $this->queryArray($attributes);

            $types ="SELECT * FROM app_tipo_producto;";
            $type = $this->queryArray($types);  
            
            return array('attributes' => $attr['rows'],'types'=>$type['rows']);

}



// R E P O R T E   D E   S E G U I M I E N T O   D E  
// AM

    function llenarReporteSeguimiento($ordenprod,$producto,$fecha){

        $filtroOrdenprod   = "";
        $filtroProducto    = "";
        $filtroFecha       = "";
        $range1 = substr($fecha,0,10); 
        $range2 = substr($fecha,13,10);
         

        //echo "ordenprod: $ordenprod, producto: $producto, fecha: $fecha,fecha2: $fecha2";

        if($ordenprod != ''){
             $filtroOrdenprod = "AND prd_orden_produccion_detalle.id_orden_produccion in($ordenprod)";
          }

         if($producto != ''){
            $filtroProducto = "AND prd_orden_produccion_detalle.id_producto in($producto)";
         }

          if($fecha != ''){
            $filtroFecha = "AND DATE_FORMAT(prd_ini_proceso.fecha_guardado, '%Y-%m-%d') >= '$range1' and DATE_FORMAT(prd_ini_proceso.fecha_guardado, '%Y-%m-%d') <='$range2'";
         }

            $sql=$this->query("SELECT app_productos.id,prd_orden_produccion_detalle.id_orden_produccion,
                app_productos.nombre, prd_pasos_producto.descripcion, prd_pasos_acciones_producto.alias,(case when prd_ini_proceso.fecha_guardado then 'Finalizado' else 'Pendiente' end) as 'estatus', prd_ini_proceso.fecha_guardado,prd_pasos_producto.id as idpaso,
                prd_pasos_acciones_producto.id as pasoprd,prd_pasos_acciones_producto.id_accion as accion,prd_orden_produccion_detalle.cantidad,
                (CASE WHEN prd_pasos_acciones_producto.id_accion in (11,18)    then
                    (SELECT sum(mt.cantppf) 
                    from prd_matp mt 
                    inner join prd_pasos_acciones_producto pa on pa.id = mt.id_pa
                    where pa.id_accion in (11,18) and mt.id_pa=pasoprd  and mt.f_fin>0)
                    else ''
                    END
                 ) as cantidadpproducida,
                 (CASE WHEN prd_pasos_acciones_producto.id_accion in (11,18) then (select prd_orden_produccion_detalle.cantidad-cantidadpproducida) else '' end) as pendiente 
                from 
                prd_orden_produccion_detalle
                 
                inner join
                app_productos on app_productos.id=prd_orden_produccion_detalle.id_producto 
                inner join 
                prd_pasos_producto on prd_pasos_producto.id_producto=prd_orden_produccion_detalle.id_producto 
                inner join
                prd_pasos_acciones_producto on prd_pasos_acciones_producto.id_paso=prd_pasos_producto.id 
                left join 
                prd_ini_proceso on prd_ini_proceso.id_accion_producto = prd_pasos_acciones_producto.id and prd_ini_proceso.id_oproduccion=prd_orden_produccion_detalle.id_orden_produccion where 1=1 $filtroOrdenprod $filtroProducto $filtroFecha order by id_orden_produccion asc, prd_pasos_producto.id asc,prd_pasos_acciones_producto.id asc;");


        return $sql;
   }

   function pasos_producto(){
     $sql = $this->query("select * from prd_orden_produccion where estatus in(4,9);");
     return $sql;

   }

   function app_productos(){
     $sql = $this->query("select p.id,tp.id as idtipo,p.tipo_producto,p.nombre from app_productos p
        inner join app_tipo_producto tp
        on p.tipo_producto = tp.id where tp.id in(8,9) and tp.visible = 1;");
     return $sql;

   }

  

    }
?>