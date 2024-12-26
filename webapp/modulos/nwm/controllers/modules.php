<?php

require('common.php');

class Modules extends Common
{
    function inventario(){
      require('views/inventario.php');
    }
    function kardex(){
      require('views/kardex.php');
    }
    function antiguedadDeInventario() {
      require('views/antiguedadDeInventario.php');
    }
    function devolucionesVenta() {
      require('views/devolucionesVenta.php');
    }
    function rotacionDeInventario() {
      require('views/rotacionDeInventario.php');
    }
    function test(){
      require('vue/src/index.php');
    }
}
