<?php

// Consolida versiones legasy de repolog y repologv1

ini_set('display_errors', '0');

    $usuario=$_SESSION["accelog_idempleado"];
    $idorg = $_SESSION["accelog_idorganizacion"];
    include("parametros.php");
    
    $idreporte = $reportId;
    $_SESSION["repolog_idreporte"]=$idreporte;
    
    $sql = "select * from repolog_reportes where idreporte=".$idreporte;
    $result = $conexion->consultar($sql);
    $url_include = "";
	$nombrereporte="";

    $sql = "";
    while($rs=$conexion->siguiente($result)){
        $descripcion = $rs{'descripcion'};
        $sql.=" select ".$rs{'sql_select'};
        $sql.=" from ".$rs{'sql_from'};
        if(($rs{'sql_where'}!=null)&&($rs{'sql_where'}!=""))            $sql.=" where ".$rs{'sql_where'};
        if(($rs{'sql_groupby'}!=null)&&($rs{'sql_groupby'}!=""))    $sql.=" group by ".$rs{'sql_groupby'};
        if(($rs{'sql_having'}!=null)&&($rs{'sql_having'}!=""))          $sql.=" having ".$rs{'sql_having'};
        if(($rs{'sql_orderby'}!=null)&&($rs{'sql_orderby'}!=""))       $sql.=" order by ".$rs{'sql_orderby'};
        $idestiloomision = $rs{"idestiloomision"};
        $url_include = $rs{"url_include"};
        $url_include_despues = $rs{"url_include_despues"};
        $nombrereporte=$rs{"nombrereporte"};
        $subtotales_agrupaciones = $rs{"subtotales_agrupaciones"};
        $subtotales_funciones = $rs{"subtotales_funciones"};
        $subtotales_subtotal = $rs{"subtotales_subtotal"};
    }    
    $conexion->cerrar_consulta($result);

    $_SESSION["url_include"] =$url_include;
    $_SESSION["url_include_despues"] =$url_include_despues;
    $_SESSION["desc"] = $descripcion;
    $_SESSION["sequel"]=$sql;
    $_SESSION["iestilo"] = $idestiloomision;
    $_SESSION["nombrereporte"] = $nombrereporte;
    $_SESSION["subtotales_agrupaciones"] = $subtotales_agrupaciones;
    $_SESSION["subtotales_funciones"] = $subtotales_funciones;   
    $_SESSION["subtotales_subtotal"] = $subtotales_subtotal;   
    $caracter_pregunta = "[";


    //Filtros 

    $sql = $_SESSION["sequel"];
    $idestiloomision = $_SESSION["iestilo"];
    $descripcion = $_SESSION["desc"];


		//CSRF
		$reset_vars = true;
		include "../catalog/clases/clcsrf.php";


    //Obteniendo etiquetas y posición
    $filtros_etiquetas = array();
    $filtros_posicion_inicio = array();
    $filtros_posicion_fin = array();
    $filtros_cuantos = 0;
    

    //Recorriendo cadena de sql
    $armando_parametro = false;
    $posicion_inicio = 0;
    $etiqueta = "";
    //echo $sql;
    for($i = 0; $i<=strlen($sql); $i++){
        $caracter = substr($sql,$i,1);
        //echo $caracter;
        if($armando_parametro){
            if($caracter=="]"){

                $filtros_cuantos++;
                $filtros_etiquetas[$filtros_cuantos] = $etiqueta;
                $filtros_posicion_inicio[$filtros_cuantos] = $posicion_inicio;
                $filtros_posicion_fin[$filtros_cuantos] = $i;

                $armando_parametro = false;
                $posicion = 0;
                $etiqueta = "";

            } else {
                $etiqueta.=$caracter;
            }
        }
        if($caracter=="["){
            $armando_parametro = true;
            $posicion_inicio = $i;
        }

    }

    $_SESSION["filtros_cuantos"] = $filtros_cuantos;
    $_SESSION["filtros_etiquetas"] = $filtros_etiquetas;
    $_SESSION["filtros_posicion_inicio"] = $filtros_posicion_inicio;
    $_SESSION["filtros_posicion_fin"] = $filtros_posicion_fin;
    
    //Filtros Procesar

    $filtros_cuantos = $_SESSION["filtros_cuantos"];
    $filtros_etiquetas = $_SESSION["filtros_etiquetas"];
    $filtros_posicion_inicio = $_SESSION["filtros_posicion_inicio"];
    $filtros_posicion_fin = $_SESSION["filtros_posicion_fin"];

    $filtros_valores = array();
    $filtros_valores_hum = array();



    for($i = 1; $i<=$filtros_cuantos; $i++){
        
        $etiqueta = $filtros_etiquetas[$i];

        $pos_barra=strrpos($etiqueta,"#");
        if(is_numeric($pos_barra)){
            $anual = $conexion->escapalog($_REQUEST["f".$i."_3"]);
            $mes = $conexion->escapalog($_REQUEST["f".$i."_1"]);
            if(strlen($mes)<2){
                $mes = '0' . $mes;
            }
            $dia = $conexion->escapalog($_REQUEST["f".$i."_2"]);
            if(strlen($dia)<2){
                $dia = '0' . $dia;
            }
            $fecha = $anual."-".$mes."-".$dia;
            $filtros_valores[$i] = $fecha;
            $sql = str_replace("[".$etiqueta."]", $fecha,$sql);
            
            $filtros_valores_hum[$i] = $dia."/".$mes."/".$anual;

        } else {

            $valor = $conexion->escapalog($_REQUEST["txt".$i]);
            $filtros_valores[$i] = $valor;
            $sql = str_replace("[".$etiqueta."]", $valor,$sql);

            $pos_barra=strrpos($etiqueta,"@");
            if(is_numeric($pos_barra)){
                $filtros_valores_hum[$i] = $conexion->escapalog($_REQUEST["dep".$i]);
            } else {                
                $filtros_valores_hum[$i] = $valor;
            }
                        
        }
    }

    $_SESSION["sequel"] = $sql;

    //PARAMETROS ALMACENADOS EN LA SESSION POR SI SE UTILIZAN EN ALGUN PROCESO
    $_SESSION["repolog_valores"] = $filtros_valores;
    $_SESSION["repolog_valores_hum"] = $filtros_valores_hum;
    $_SESSION["repolog_cuantos"] = $filtros_cuantos;
    $_SESSION["repolog_filtros"] = $filtros_etiquetas;
    
    
    ///////


    ?>

    <script>
    $('#nmloader_div',window.parent.document).hide();
    </script>