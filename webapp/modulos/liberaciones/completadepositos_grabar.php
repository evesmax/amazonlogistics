<?php
include("bd.php");
include("../../netwarelog/webconfig.php");
session_start();

//Recibe variables de ventana anterior.

$elementos=0;
$idcliente=0;
$idordencompra=0;
$sqldep="";

$fechamysql=date("Y-m-d H:i:s");

$elementos=$_REQUEST["elementos"];
$idcliente=$_REQUEST["idcliente"];
$idordencompra=$_REQUEST["idordencompra"];
$depositos=array("fecha","refdeposito","aplicar","idorigen");
$e=0;

        //Obtiene Fabricante
        $sqlfab="SELECT idfabricante FROM ventas_ordenesdecompra WHERE idordencompra=$idordencompra";
        $result = $conexion->consultar($sqlfab);
        while($rs = $conexion->siguiente($result)){
            $idfabricante=$rs{"idfabricante"};
        }
        $conexion->cerrar_consulta($result);


        //Consulta detallado de saldos a fabor del cliente
        $sqldep="SELECT sc.idsaldocliente, sc.idcliente,sc.fecha, sc.referenciadeposito deposito, sum(sc.saldo) saldo 
                    FROM ventas_saldosclientes sc 
                    WHERE sc.idcliente=$idcliente AND sc.saldo>0
                    AND idsaldocliente IN 
                    (SELECT idsaldocliente FROM ventas_saldosclientes_detalle WHERE foliodoctoorigen IN 
                    (SELECT idordencompra FROM ventas_ordenesdecompra WHERE idfabricante=$idfabricante))
                    GROUP BY sc.idcliente,sc.fecha,sc.referenciadeposito
                    Order By sc.fecha";
        $result = $conexion->consultar($sqldep);
        while($rs = $conexion->siguiente($result)){
            $depositos["idorigen"][$e]=$rs{"idsaldocliente"};
            $depositos["fecha"][$e]=$rs{"fecha"};
            $depositos["refdeposito"][$e]=$rs{"deposito"};
            $depositos["aplicar"][$e]=$_REQUEST["aplicar_$e"];
            $e++;
        }
        $conexion->cerrar_consulta($result);   
        
        $coma="";$c=0;
        $sqlinsertdep=" Insert Into ventas_ordenescompra_depositos (refdeposito, importe, fechadeposito, idordencompra, idorigen) values ";
        $sqlinsertsaldos="Insert Into ventas_saldosclientes_detalle (idsaldocliente, fechamovimiento, importe, doctoorigen, foliodoctoorigen) values ";
//Genera depositos a Orden de Compra Depositos
        for ($c = 0; $c < $elementos; $c++) {            
            $sqlinsertdep.="$coma ('".$depositos["refdeposito"][$c]."','".$depositos["aplicar"][$c]."','".$depositos["fecha"][$c]."','".$idordencompra."','".$depositos["idorigen"][$c]."')";
            
            $sqlinsertsaldos.="$coma ('".$depositos["idorigen"][$c]."','".$fechamysql."','".$depositos["aplicar"][$c]."','8','$idordencompra')";
            
            $coma=",";
        }
        echo "Depositos: $sqlinsertdep <br>"; //Inserta Depositos en los registrados en la orden de compra
        $conexion->consultar($sqlinsertdep);
        echo "Saldos: $sqlinsertsaldos <br>"; //Genera un registro y afecta ventas_saldosclientes    
        $conexion->consultar($sqlinsertsaldos);
        
        $sqlupdate="";$idsaldocliente=0;
        //Actualiza Saldos
        for ($c = 0; $c < $elementos; $c++) {  
            $idsaldocliente=$depositos["idorigen"][$c];
            $sqlupdate="UPDATE ventas_saldosclientes SET 
                importeaplicado=ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$idsaldocliente),0),
                saldo=importeinicial-ifnull((SELECT sum(importe) FROM ventas_saldosclientes_detalle WHERE idsaldocliente=$idsaldocliente),0)
              WHERE idsaldocliente=$idsaldocliente"; // Actualiza el Titulo de la tabla de saldos clientes
            
            echo "$sqlupdate <br>";
            $conexion->consultar($sqlupdate);
        }
        
        //exit();
        ?>
            <script type="text/javascript">
            window.location="<?php echo $url_dominio; ?>modulos/liberaciones/liberacion.php?idordencompra=<?php echo $idordencompra; ?>&idfabricante=-1";
            </script>
        <?php
        
?>