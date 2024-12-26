<?php

require('common.php');
require("models/inventario.php");

class Inventario extends Common
{
    private $InventarioModel;

    function __construct()
    {
        $this->InventarioModel = new InventarioModel();
        $this->InventarioModel->connect();
    }

    function __destruct()
    {
        $this->InventarioModel->close();
    }

    public function productosEnInventario()
    {
      $productosEnInventario['data'] = $this->InventarioModel->obtenerProductosEnInventario($_GET);
      echo json_encode($productosEnInventario);
    }

    public function caracteristicasEnInventario()
    {
      $caracteristicasEnInventario['data'] = $this->InventarioModel->obtenerCaracteristicasEnInventario($_GET);
      $caracteristicas = $this->InventarioModel->getCaracteristicas();
      foreach ($caracteristicasEnInventario['data'] as $key => $value)
        $caracteristicasEnInventario['data'][$key]['caracteristicas'] = $this->caract2nombre($caracteristicas ,$value['caracteristicas']);

      echo json_encode($caracteristicasEnInventario);
    }

    public function lotesEnInventario()
    {
      $lotesEnInventario['data'] = $this->InventarioModel->obtenerLotesEnInventario($_GET);
      echo json_encode($lotesEnInventario);
    }

    public function seriesEnInventario()
    {
      $seriesEnInventario['data'] = $this->InventarioModel->obtenerSeriesEnInventario($_GET);
      echo json_encode($seriesEnInventario);
    }

    public function kardex()
    {
      $movimientos['data'] = $this->InventarioModel->obtenerMovimientos($_GET);
      $caracteristicas = $this->InventarioModel->getCaracteristicas();
      $cantidadInicial = 0;
      $cantidadActual = 0;
      $valorInicial = 0;
      $valordActual = 0;
      $movimientosAux['data'] = [];
      foreach ($movimientos['data'] as $key => $value){
        $cantidadInicial = $cantidadActual;
        $valorInicial = $valorActual;
        switch ($value['TIPO_MOVIMIENTO']) {
          case '0': //SALIDA
          $cantidadActual = $cantidadInicial - $value['CANTIDAD_SALDO'];
          $valorActual = $valorInicial - $value['COSTO_SALDO'];
            break;
          case '1': //ENTRADA
          $cantidadActual = $cantidadInicial + $value['CANTIDAD_SALDO'];
          $valorActual = $valorInicial + $value['COSTO_SALDO'];
            break;
          case '2': //TRASPASO
          if(  strpos( $value['DETALLE'], 'Destino:' ) !== false ) {
            $cantidadActual = $cantidadInicial - $value['CANTIDAD_SALDO'];
            $valorActual = $valorInicial - $value['COSTO_SALDO'];
          }
          else {
            $cantidadActual = $cantidadInicial + $value['CANTIDAD_SALDO'];
            $valorActual = $valorInicial + $value['COSTO_SALDO'];
          }
            break;
          case '3': //APARTADO
          default:
            // code...
            break;
        }

        $fecha = $value['FECHA'];
        if(  !( ( strcmp (  $_GET['startTime']." 00:00:00" , $fecha ) <= 0 ) &&
              ( strcmp (  $_GET['endTime']." 23:59:59" , $fecha ) >= 0 ) )      ) {
          // array_splice($movimientos['data'], $key, 1);
          // unset($value);
        }
        else {
          //ATRIBUTOS
          $value['ATRIBUTOS'] = $value['caracteristicas'] != '\'0\'' ? $this->caract2nombre($caracteristicas ,$value['caracteristicas']) : '';
          $value['ATRIBUTOS'] .= $value['lote'] ? "LOTE: {$value['lote']}<br/>" : '';
          $value['CANTIDAD_SALDO'] = $cantidadActual;
          $value['COSTO_SALDO'] = $valorActual;
          $value['COSTO_UNITARIO_SALDO'] = $valorActual / $cantidadActual ;
          array_push($movimientosAux['data'], $value);
        }
      }
      echo json_encode($movimientosAux);
    }

    public function kardexMovimientoSeries()
    {
      $seriesMovimiento['data'] = $this->InventarioModel->obtenerSeriesMovimiento($_GET);
      echo json_encode($seriesMovimiento);
    }

    private function caract2id($caract){
      return preg_replace(["/=>/", "/,/", "/'/" ], ["H", "_", ""], $caract);
    }
    private function id2caract($id)
    {
      return preg_replace(['/H/','/_/','/(\d+)/' ], [ '=>',',', "'\${1}'"], $id);
    }
    private function caract2nombre($caracteristicas ,$caract)
    {
      $caract = $this->caract2id($caract);
      $caracteristicasProductoTmp = explode( '_' , $caract );
      $caracteristicasEtiqueta = '';
      foreach ($caracteristicasProductoTmp as $key => $val) {
        $caracteristica = explode( 'H' , $val);
        $caracteristica = $this->buscarCaracteristica($caracteristicas , $caracteristica);
        $caracteristicasEtiqueta .= "{$caracteristica['CRARACTERISTICA_PADRE']}:{$caracteristica['CARACTERISTICA_HIJA']} <br/>" ;
      }
      return $caracteristicasEtiqueta;
    }
    private function buscarCaracteristica($caracteristicas , $caracteristica)
    {
      foreach ($caracteristicas as $key => $value) {
        if($value['ID_P'] == $caracteristica[0] && $value['ID_H'] == $caracteristica[1] )
          return $value;
      }
    }

    public function antiguedadInventario() {
      if( isset( $_GET['product'] ) && $_GET['product'] != 0 ) {
        $parentClasific = $_GET['product'];
        $clasificado = 4;
      }   
      else if( isset( $_GET['line'] ) && $_GET['line'] != 0 ) {
        $parentClasific = $_GET['line'];
        $clasificado = 3;
      } 
      else if( isset( $_GET['family'] ) && $_GET['family'] != 0 ) {
        $parentClasific = $_GET['family'];
        $clasificado = 2;
      }   
      else if( isset( $_GET['department'] ) && $_GET['department'] != 0 ) {
        $parentClasific = $_GET['department'];
        $clasificado = 1;
      }
      else {
        $parentClasific = 0;
        $clasificado = 0;
      }
          

      $antiguedadInventario['data'] = $this->InventarioModel->antiguedadInventario( $clasificado , $parentClasific );
      echo json_encode( $antiguedadInventario );
    }

    public function detalleAntiguedadInventario() {
      $detalleAntiguedadInventario['data'] = $this->InventarioModel->detalleAntiguedadInventario($_GET);
      echo json_encode($detalleAntiguedadInventario);
    }

    public function rotacionInventario()
    {
      if( isset( $_GET['product'] ) && $_GET['product'] != 0 ) {
        $_GET['parentClasific'] = $_GET['product'];
        $_GET['clasificado'] = 4;
      }     
      else if( isset( $_GET['department'] ) && $_GET['department'] != 0 ) {
        $_GET['parentClasific'] = $_GET['department'];
        $_GET['clasificado'] = 1;
      }
      else {
        $_GET['parentClasific'] = 0;
        $_GET['clasificado'] = 0;
      }

      $rotacionInventario['data'] = $this->InventarioModel->rotacionInventario($_GET);
      echo json_encode($rotacionInventario);
    }

}
