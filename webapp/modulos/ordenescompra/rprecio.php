<?php
include("bd.php");

if(isset($_GET["producto"])){   
        $desc="Cantidad";
        $producto=$_GET["producto"];
        $preciopb=$_GET["preciopb"];  //Cantidad Principal
        $tipo=$_GET["tipo"];
        $desc1="";
        $desc2="";
        $edita="";
        //Asignacion de Etiqueta Cantidad Principal
        if ($tipo==1){
                //Obtiene Etiqueta Descripcion Cantidad principal
                $desc1="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,u.factor FROM inventarios_productos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$producto;
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
                    $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc2 = $rs["descripcionunidad"];
                        $factor= $rs["factor"];
                        $edita=$rs{"edita"};
                }
                $conexion->cerrar_consulta($result);
                //$cantidadconversion=0;
                //$cantidadconversion=str_replace(',','',$cantidadp)*str_replace(',','',$factor);
                $preciotonelada=str_replace(',','',$preciopb)/str_replace(',','',$factor);
            echo $preciotonelada."|".$desc1."|".$desc2."|".$edita."|";
        }
        if ($tipo==2){
                
                //Obtiene Etiqueta Descripcion Cantidad principal
                $norma=0;
                $sQuery = "SELECT idnorma from relaciones_normasproductos where idproducto=$producto order by idnorma limit 1";
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $norma = $rs{"idnorma"};
                }
                $conexion->cerrar_consulta($result);
                
            echo $norma."|loco";

        }
}


?>
