<?php


//Define Variables
$sqlcampos="";
$notasreporte="";
$areporte="";
$pinicial=0;
$ec=1;  //Elementos Conceptos
$ep=1;  //Elementos Periodos
$aconceptos=array("concepto","sqlvalor");

$usuario=$_SESSION["accelog_idempleado"];

//Recibe filtros 

        $uw=strpos($_SESSION["sequel"],'where');
        $uo=strpos($_SESSION["sequel"],'And (rt.idempleado');
        $ct=strlen($_SESSION["sequel"]);
        $td=($ct-$uo)*-1;
        $sqlwhere=substr($_SESSION["sequel"],$uw,$td);
        
        $idinst=substr($sqlwhere,16);

//Abre las caracteristicas del reporte dependiendo del numero
        $sqlrinst="SELECT rt.idcierre,rd.nombrecampo, rd.sqlvalor, rt.notasreporte   
                        FROM rinstitucionales_titulo rt 
                        INNER JOIN rinstitucionales_detalle rd ON rt.idinst=rd.idinst
                    $sqlwhere order by rd.orden";
        
        $resultado = $conexion->consultar($sqlrinst);
        while($rs = $conexion->siguiente($resultado)){
            $pinicial=$rs{"idcierre"};
            $notasreporte=$rs{"notasreporte"};
            $aconceptos["concepto"][$ec]=$rs{"nombrecampo"};
            $aconceptos["sqlvalor"][$ec]=$rs{"sqlvalor"};
            $ec++;
        }
        $conexion->cerrar_consulta($resultado);
        

//Determina los campos en funcion del periodo inicial
        $sqlpi="";
        if($pinicial>0){
            $sqlpi=" Where rt.idcierre>=$pinicial";
        }
        $sqlrinst="SELECT rt.idcierre,rt.nombrecierre,rt.fechainicial, date_format(rt.fechafinal,'%d-%m-%y') fechafinal 
                    FROM cierre rt
                    $sqlpi Order by rt.idcierre";
        
        $speriodos="";
        $periodos="";
        $nperiodos=1;
        $campos=1;
        $coma="";
        $resultado = $conexion->consultar($sqlrinst);
        while($rs = $conexion->siguiente($resultado)){
            $speriodos.="$coma format(rt.c$campos,3) '".$rs{"nombrecierre"}."'";
            $periodos.="$coma c$campos";
            $mperiodos[$nperiodos]=$rs{"idcierre"};
            $nperiodos++;
            
            
            $coma=",";
            $campos++;
            $ffinal=$rs{"fechafinal"};
        }
        $conexion->cerrar_consulta($resultado);
        $ct=strlen($speriodos);
        $speriodos=substr($speriodos,1,$ct-2)."<br><b>$ffinal</b>'";
        

        
$areporte=array("concepto",$periodos);


//Sustituye los campos del sql
$sql=str_replace("@campos",$speriodos,$sql);



//Genera las consultas y llena el arreglo principal
                $er=1; //Elementos Reporte
                $rescampos="";
                for ($c = 1; $c <=$ec-1; $c++){
                    $areporte["concepto"][$er]=$aconceptos["concepto"][$c];
                    
                    //Recupera Sql para campos y hace cnsultas
                    for ($f=1; $f<=$nperiodos-1;$f++){
                        $areporte["c$f"][$c]=regresavalor($aconceptos["sqlvalor"][$c],$mperiodos[$f],$conexion);
                    }
                    
                    //Llena campos variables
                    
                    $er++;
                }
                $coma=",";
                $values="";
                for ($i=1; $i <=$er-1; $i++){
                    $v="";
                    for ($f=1; $f<=$nperiodos-1;$f++){
                        $v.=$coma.$areporte["c$f"][$i];
                    }
                    
                    
                    $values.=
                           "(
                                '".$areporte["concepto"][$i]."','$usuario','$idinst' $v
                            ),";
                    
                    
                    
                }
                
                
                //Consulta para eliminar registros Anteriores del Mismo Usuario
		$sqldelete="delete from reporte_institucionales where idempleado=$usuario";
                    //echo $sqldelete."<br><br>";
                $conexion->consultar($sqldelete);
                
                
                $insert=" Insert Into reporte_institucionales (nombrecampo,idempleado,idinst,$periodos) Values ";
                //Agrega Valores Reporte
                $values=substr($values, 0, -1); //elimina la ultima coma
                    //echo $insert." ".$values."<br>";
		
                $conexion->consultar($insert." ".$values);
                






//Funciones de Arreglos
function regresavalor($sqlvalor,$idcierre,$conexionh){
        $valor=0;
        //Altera y completa Sql
        $filtro=" And idcierre=$idcierre ";

		//Sustituye Variables
		$sqlvalor=str_replace('@filtro',$filtro,$sqlvalor);
		
		$sqlvalor.=$filtro;
        //echo $sqlvalor."<br>";
        
        $resultado = $conexionh->consultar($sqlvalor);
        while($rs = $conexionh->siguiente($resultado)){
            $valor=$rs{"valor"};
        }
        $conexionh->cerrar_consulta($resultado);
    
    return $valor;
}

?>
