<?php

require('common.php');
require("models/filters.php");

class Filters extends Common
{
    private $FiltersModel;

    function __construct()
    {
        $this->FiltersModel = new FiltersModel();
        $this->FiltersModel->connect();
    }

    function __destruct()
    {
        $this->FiltersModel->close();
    }


    public function almacenes()
    {
      $almacenes = $this->FiltersModel->obtenerAmacenes();
      echo json_encode($almacenes);
    }

    public function sucursales()
    {
      $sucursales = $this->FiltersModel->obtenerSucursales();
      echo json_encode($sucursales);
    }

    public function clientes()
    {
      $clientes = $this->FiltersModel->obtenerClientes();
      echo json_encode($clientes);
    }

    public function proveedores()
    {
      $proveedores = $this->FiltersModel->obtenerProveedores();
      echo json_encode($proveedores);
    }

    public function productos()
    {
      $productos = $this->FiltersModel->obtenerProductos();
      echo json_encode($productos);
    }

    public function unidadesDeMedida()
    {
      $unidadesDeMedida = $this->FiltersModel->obtenerUnidadesDeMedida();
      echo json_encode($unidadesDeMedida);
    }

    public function permisoAccion()
    {
      $permiso = $this->FiltersModel->obtenerPermisoAccion($_GET['action']);
      if( count($permiso) )
        echo json_encode(true);
      else
        echo json_encode(false);
    }

    function buscarClasificadores() {
      if( isset( $_GET['linea'] ) && $_GET['linea'] != 0 )
          $parentClasific = $_GET['linea'];
      else if( isset( $_GET['familia'] ) && $_GET['familia'] != 0 )
          $parentClasific = $_GET['familia'];
      else if( isset( $_GET['departamento'] ) && $_GET['departamento'] != 0 )
          $parentClasific = $_GET['departamento'];
      else
          $parentClasific = 0;

      echo $this->FiltersModel->buscarClasificadores( $_GET['clasificador'], $_GET['patron'] , $parentClasific );
    }

}
