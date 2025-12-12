<?php
include("bd.php");

if(isset($_GET["producto"])){   
        $desc="Cantidad";
        $producto=$_GET["producto"];
        $cantidadp=$_GET["cantidadp"];  //Cantidad Principal
        $tipo=$_GET["tipo"];
        $cartaporte=$_GET["cartaporte"];
        //Asignacion de Etiqueta Cantidad Principal
        if ($tipo==1){
                //Obtiene Etiqueta Descripcion Cantidad principal
                $desc1="Cantidad";
                $sQuery = "SELECT u.descripcionunidad,u.factor FROM inventarios_productos i 
                    inner join inventarios_unidadesmedida u on u.idunidadmedida=i.idunidadmedida 
                    where i.idproducto=".$producto;
                //echo $sQuery."<br>"; 
                $edita=$sQuery."/ - /"; 
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
                //echo "<script>alert('".$sQuery."');</script>";
                $edita.=$sQuery;
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $desc2 = $rs["descripcionunidad"];
                        $factor= $rs["factor"];
                        $edita=$rs{"edita"};
                }
                $conexion->cerrar_consulta($result);
                $cantidadconversion=0;
                $cantidadconversion=str_replace(',','',$cantidadp)*str_replace(',','',$factor);
           
            $duplicada=0;
            if(isset($_GET["cartaporte"])){
                $cartaporte=$_GET["cartaporte"];
                $idtransportista=$_GET["idtransportista"];
                $duplicada=0;
                $sQuery = "Select count(cartaporte) cuantas from logistica_ordenesentrega where idtransportista=".$idtransportista." and cartaporte='".$cartaporte."'";
                //echo "<script>alert('".$sQuery."');</script>";
                $result = $conexion->consultar($sQuery);
                while($rs = $conexion->siguiente($result)){
                        $duplicada = $rs["cuantas"];
                }
                $conexion->cerrar_consulta($result); 
            } 
            echo $desc1."|".$desc2."|".number_format($cantidadconversion,3)."|".$edita."|".$duplicada."|";
        }
}
?>
