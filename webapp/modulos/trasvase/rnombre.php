<?php
include("bd.php");

            $desc1="";
            $desc2="";
            $cantidadconversion=0; 

if(isset($_GET["producto"])){   
        $desc="Cantidad";
        $producto=$_GET["producto"]; //Producto Origen
        $productoD=$_GET["productoD"]; //Producto Destino 
        $cantidadp=$_GET["cantidadp"];  //Cantidad Principal
        $tipo=$_GET["tipo"];
        //Asignacion de Etiqueta Cantidad Principal
        if ($tipo==1){
                //Obtiene Etiqueta Descripcion Cantidad principal
                $desc1="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,u.factor FROM inventarios_productos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$producto;
                //echo $sQuery."<br>";  
                    $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc1 = $rs["descripcionunidad"];
                }
                $conexion->cerrar_consulta($result);
                //Asignacion de Etiqueta Cantidad Secundaria
                $edita=0;
                $desc2="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,ifnull(i.factor,0) factor ,i.idtipounidadmedida edita FROM inventarios_unidadesproductos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$producto." Limit 1";
                //echo $sQuery."<br>";
                    $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc2 = $rs["descripcionunidad"];
                        $factor= $rs["factor"];
                        $edita=$rs{"edita"};
                }
                $conexion->cerrar_consulta($result);
                $cantidadconversion=0;
                $cantidadconversion=str_replace(',','',$cantidadp)*str_replace(',','',$factor);
                
                //Obteniendo el Factor de Conversion del Producto Destino
                $factorD=1;
                $sQuery = "SELECT u.descripcionunidad,ifnull(i.factor,0) factor ,i.idtipounidadmedida edita FROM inventarios_unidadesproductos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$productoD." Limit 1";
                echo $sQuery."<br>";
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $factorD= $rs["factor"];
                }
                $conexion->cerrar_consulta($result);

            echo $desc1."|".$desc2."|".number_format($cantidadconversion,3)."|".$edita."|".$factorD;
        }
}


?>
