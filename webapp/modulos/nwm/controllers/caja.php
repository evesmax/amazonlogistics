<?php

require('common.php');
require("models/caja.php");

class Caja extends Common
{
    private $CajaModel;

    function __construct()
    {
        $this->CajaModel = new CajaModel();
        $this->CajaModel->connect();
    }

    function __destruct()
    {
        $this->CajaModel->close();
    }

    public function devolucionesVenta()
    {
      $devolucionesVenta['data'] = $this->CajaModel->devolucionesVenta($_GET);
      echo json_encode($devolucionesVenta);
    }

    public function detalleDevolucioneVenta()
    {
      $detalleDevolucioneVenta['data'] = $this->CajaModel->detalleDevolucioneVenta($_GET);
      $caracteristicas = $this->CajaModel->getCaracteristicas();
      $movimientosAux['data'] = [];
      foreach ($detalleDevolucioneVenta['data'] as $key => $value){
          //ATRIBUTOS
          $value['ATRIBUTOS'] = $value['caracteristicas'] != '\'0\'' ? $this->caract2nombre($caracteristicas ,$value['caracteristicas']) : '';
          $value['ATRIBUTOS'] .= $value['lote'] ? "LOTE: {$value['lote']}<br/>" : '';
          array_push($movimientosAux['data'], $value);
      }
      echo json_encode($movimientosAux);
    }

    public function detalleDevolucioneVentaSeries()
    {
      $detalleDevolucioneVentaSeries['data'] = $this->CajaModel->detalleDevolucioneVentaSeries($_GET);
      echo json_encode($detalleDevolucioneVentaSeries);
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

}
