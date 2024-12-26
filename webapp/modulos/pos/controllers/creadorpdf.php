<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador

require("models/creadorpdf.php");


class CreadorPdf extends Common
{
    public $PdfModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar

        $this->PdfModel = new PdfModel();
        $this->PdfModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->PdfModel->close();
    }
  
    
    function crearpdfComplementos(){
        $res = $this->PdfModel->crearpdfComplementos();
        echo json_encode($res);
    }
    function creapdfcomp(){
        
         require('views/caja/creapdfcomple.php');
    }


}

?>
