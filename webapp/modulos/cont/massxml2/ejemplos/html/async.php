<?php
// error_reporting(1);
// ini_set('display_errors', 1);
require dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'DescargaMasivaCfdi.php';
require dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'UtilCertificado.php';

// Obtener configuracion
$config = require dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php';

// Preparar variables
$rutaDescarga = $config['rutaDescarga'];
$maxDescargasSimultaneas = $config['maxDescargasSimultaneas'];

// Instanciar clase principal
$descargaCfdi = new DescargaMasivaCfdi();

function json_response($data, $success=true) {
  header('Cache-Control: no-transform,public,max-age=300,s-maxage=900');
  header('Content-Type: application/json');
  return json_encode(array(
    'success' => $success,
    'data' => $data
  ));
}

if(!empty($_POST)) {

  if(!empty($_POST['sesion'])) {
    $descargaCfdi->restaurarSesion($_POST['sesion']);
  }

  $accion = empty($_POST['accion']) ? null : $_POST['accion'];
  
  if($accion == 'login_ciec') {
    if(!empty($_POST['rfc']) && !empty($_POST['pwd'])) {

      // iniciar sesion en el SAT
      $ok = $descargaCfdi->iniciarSesionCiec($_POST['rfc'],$_POST['pwd']);
      if($ok) {
        echo json_response(array(
          'mensaje' => 'Se ha iniciado la sesión',
          'sesion' => $descargaCfdi->obtenerSesion()
        ));
      }else{
        echo json_response(array(
          'mensaje' => 'Ha ocurrido un error al iniciar sesión. Intente nuevamente',
        ));
      }
    }else{
      echo json_response(array(
        'mensaje' => 'Proporcione todos los datos',
      ));
    }
  }elseif($accion == 'login_ciecc') {
    if(!empty($_POST['rfc']) && !empty($_POST['pwd']) && !empty($_POST['captcha'])) {

      // iniciar sesion en el SAT
      $ok = $descargaCfdi->iniciarSesionCiecCaptcha($_POST['rfc'],$_POST['pwd'],$_POST['captcha']);
      if($ok) {
        echo json_response(array(
          'mensaje' => 'Se ha iniciado la sesión',
          'sesion' => $descargaCfdi->obtenerSesion()
        ));
      }else{
        echo json_response(array(
          'mensaje' => 'Ha ocurrido un error al iniciar sesión. Intente nuevamente',
        ));
      }
    }else{
      echo json_response(array(
        'mensaje' => 'Proporcione todos los datos',
      ));
    }
  }elseif($accion == 'login_fiel') {
    if(!empty($_FILES['cer']) && !empty($_FILES['key']) && !empty($_POST['pwd'])) {

      // preparar certificado para inicio de sesion
      $certificado = new UtilCertificado();
      $ok = $certificado->loadFiles(
        $_FILES['cer']['tmp_name'],
        $_FILES['key']['tmp_name'],
        $_POST['pwd']
      );

      if($ok) {
        // iniciar sesion en el SAT
        $ok = $descargaCfdi->iniciarSesionFiel($certificado);
        if($ok) {
          echo json_response(array(
            'mensaje' => 'Se ha iniciado la sesión',
            'sesion' => $descargaCfdi->obtenerSesion()
          ));
        }else{
          echo json_response(array(
            'mensaje' => 'Ha ocurrido un error al iniciar sesión. Intente nuevamente',
          ));
        }
      }else{
        echo json_response(array(
          'mensaje' => 'Verifique que los archivos corresponden con la contraseña e intente nuevamente',
        ));
      }
    }else{
      echo json_response(array(
        'mensaje' => 'Proporcione todos los datos',
      ));
    }
  }elseif($accion == 'buscar-recibidos') {
    $filtros = new BusquedaRecibidos();
    $filtros->establecerFecha($_POST['anio'], $_POST['mes'], $_POST['dia']);

    $xmlInfoArr = $descargaCfdi->buscar($filtros);
    if($xmlInfoArr){
      $items = array();
      foreach ($xmlInfoArr as $xmlInfo) {
        $items[] = (array)$xmlInfo;
      }
      echo json_response(array(
        'items' => $items,
        'sesion' => $descargaCfdi->obtenerSesion()
      ));
    }else{
      echo json_response(array(
        'mensaje' => 'No se han encontrado CFDIs',
        'sesion' => $descargaCfdi->obtenerSesion()
      ));
    }
  }elseif($accion == 'buscar-emitidos') {
   $filtros = new BusquedaEmitidos();
    
    $inicio = $_POST['dia_e'];
    $fin = $_POST['dia_e'];
    if(!intval($_POST['dia_e']))
        {
            $inicio = 1;
            $d = $_POST['anio_e']."-".$_POST['mes_e']."-01";
            $fin = date("t",strtotime($d));
        }  
    if($_POST['dia_e'] == 0){
        $num = 0;
        while(strcasecmp($inicio,$fin) != 0){
            $num ++;
            $data_save = [];
            $filtros->establecerFechaInicial($_POST['anio_e'], $_POST['mes_e'], $inicio);
            $filtros->establecerFechaFinal($_POST['anio_e'], $_POST['mes_e'], $fin);
            $filtros->establecerHoraInicial('00','00','00');
            $filtros->establecerHoraFinal('23','59','59');
            $data_save = $descargaCfdi->buscar($filtros);
            $last_row = (count($data_save) -1);
            $fecha_emision = $data_save[$last_row]->fechaEmision;
            $fecha_emision_array = explode("-",$fecha_emision);
            $fecha_emision = explode('T',$fecha_emision_array[2]);
            $inicio_save = $inicio;
            $inicio = $fecha_emision[0];
            if($num == 4){
                //Limite del ciclo Solo 3 veces puede sobrepasar los registros 500 por rango de fechas
                $inicio = $fin;
            }else{
                if((count($data_save)) >= 500){
                    while($inicio_save <= $inicio){
                        //Busqueda por días
                        $filtros->establecerFechaInicial($_POST['anio_e'], $_POST['mes_e'], $inicio_save);
                        $filtros->establecerFechaFinal($_POST['anio_e'], $_POST['mes_e'], $inicio_save);
                        $data = $descargaCfdi->buscar($filtros);
                        if(count($data) >= 500){
                            //Busqueda por Horas
                            $xmlInfoArr = get_filtros($filtros,$descargaCfdi); 
                            
                        }else{
                            $xmlInfoArr[] = $data;
                        }
                        $inicio_save ++;
                    }
                    $inicio ++;
                }else{
                    $xmlInfoArr[] = $data_save;
                    if(count($data_save) < 500){
                        break;
                    } 
                }
            }
        }
        $data = [];
        for($x = 0; $x < count($xmlInfoArr); $x++){
            for($y = 0; $y < count($xmlInfoArr[$x]); $y ++){
                $data [] = $xmlInfoArr[$x][$y];
            }
        }
        $xmlInfoArr = $data;
    }else{
        $filtros->establecerFechaInicial($_POST['anio_e'], $_POST['mes_e'], $inicio);
        $filtros->establecerFechaFinal($_POST['anio_e'], $_POST['mes_e'], $fin);
        $data = $descargaCfdi->buscar($filtros);
        if(count($data) >= 500){
            //Busqueda por Horas
            $xmlInfoArr = get_filtros($filtros,$descargaCfdi);             
        }else{
            $xmlInfoArr[] = $data;
        }
        $data = [];
        for($x = 0; $x < count($xmlInfoArr); $x++){
            for($y = 0; $y < count($xmlInfoArr[$x]); $y ++){
                $data [] = $xmlInfoArr[$x][$y];
            }
        }
        $xmlInfoArr = $data;
    }
    if($xmlInfoArr){
        $folio_array = [];
        foreach($xmlInfoArr as $xmlInfo){
            if($xmlInfo->urlDescargaXml == null || $xmlInfo->urlDescargaXml == ''){
                $folio_array[] = $xmlInfo->folioFiscal;
            }
        }
        for($x = 0; $x < count($folio_array); $x ++){
            for($y = 0; $y < count($xmlInfoArr); $y++ ){
                if($xmlInfoArr[$y]->folioFiscal == $folio_array[$x]){
                    unset($xmlInfoArr[$y]);
                }
            }
        }
      $items = array();
      foreach ($xmlInfoArr as $xmlInfo) {
        $items[] = (array)$xmlInfo;
      }
      echo json_response(array(
        'items' => $items,
        // 'sesion' => $descargaCfdi->obtenerSesion()
      ));
    }else{
      echo json_response(array(
        'mensaje' => 'No se han encontrado CFDIs'
      ));          
    }
  }elseif($accion == 'descargar-recibidos' || $accion == 'descargar-emitidos') {
    $descarga = new DescargaAsincrona($maxDescargasSimultaneas);

    if(!empty($_POST['xml'])) {
      foreach ($_POST['xml'] as $folioFiscal => $url) {
        // $descargaCfdi->guardarXml($url, $ruta, $folioFiscal);
        $descarga->agregarXml($url, $rutaDescarga, $folioFiscal);
      }
    }
    if(!empty($_POST['acuse'])) {
      foreach ($_POST['acuse'] as $folioFiscal => $url) {
        // $descargaCfdi->guardarAcuse($url, $ruta, $folioFiscal);
        if($ulr != null && $url != 'null'){
          $descarga->agregarAcuse($url, $rutaDescarga, $folioFiscal);
        }
      }
    }

    $descarga->procesar();
    $descarga->renombrar_y_BD();

    $str = 'Descargados: '.$descarga->totalDescargados().'.'
      . ' Errores: '.$descarga->totalErrores().'.'
      . ' Duración: '.$descarga->segundosTranscurridos().' segundos.'
      ;
    echo json_response(array(
      'mensaje' => $str,
      'sesion' => $descargaCfdi->obtenerSesion()
    ));
  }
}