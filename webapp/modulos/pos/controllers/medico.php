<?php
//ini_set('display_errors', 1);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/medico.php");

class Medico extends Common
{
    public $MedicoModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar

        $this->MedicoModel = new MedicoModel();
        $this->MedicoModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->MedicoModel->close();
    } 
/*    function indexImportarProductos(){
        require('views/producto/importar_products.php');
    }
*/
    function subeLayout()
    {
        $directorio = "importacion/";
        if (isset($_FILES["layout"])) 
        {
                if($_FILES['layout']['name'])
                {
                    //die($directorio.basename("hoteles_temp.xls" ));
                    if (move_uploaded_file($_FILES['layout']['tmp_name'], $directorio.basename("destinos_temp.xls" ) )) 

                    {
                        echo "Validando archivo...<br/>";
                        include($directorio."import_destinos.php");
                    } 
                    else 
                    {
                        echo "No se subio el archivo de Productos <br/>";
                    }
                }
        }
    }

    function inactivarLay()
    {
        echo $this->MedicoModel->inactivarLay($_POST['id'],$_POST['num']);
    }

    function reactivarLay()
    {
        echo $this->MedicoModel->reactivarLay($_POST['id'],$_POST['num']);
    }

    function confirmarLay()
    {
        $this->MedicoModel->confirmar($_POST['num']);
        $this->MedicoModel->borrar(98);
    }


    function guardar() {
        $args = array(
            'clave'     => FILTER_SANITIZE_STRING,
            'nombre'    => FILTER_SANITIZE_STRING,
            'estatus'   => FILTER_SANITIZE_NUMBER_INT
        );

        $params = filter_input_array(INPUT_POST, $args);

        echo json_encode( $this->MedicoModel->guardar( $params['clave'] , $params['nombre'] , $params['estatus'] ) );
    }

    function editar()
    {
        $args = array(
            'id'        => FILTER_SANITIZE_NUMBER_INT,
            'clave'     => FILTER_SANITIZE_STRING,
            'nombre'    => FILTER_SANITIZE_STRING,
            'estatus'   => FILTER_SANITIZE_NUMBER_INT
        );

        $params = filter_input_array(INPUT_POST, $args);

        echo json_encode( $this->MedicoModel->editar( $params['id'] , $params['clave'] , $params['nombre'] , $params['estatus'] ) );
    }

    function indexGridMedicos(){
        $medicos = $this->MedicoModel->mostrar();
        require('views/medico/GridMedicos.php');
    }

    function index()
    {   
        $idMedico = $_GET['idMedico'];

        $paises = $this->MedicoModel->paises();
        $estados = $this->MedicoModel->estados();
        $municipiosFc = $this->MedicoModel->municipios();

        if($idMedico!='')
            $datosMedico = $this->MedicoModel->datosMedico($idMedico);
        //print_r($datosMedico);die;
        require('views/medico/medicoForm.php');
    }
    
    function buscaVendedores() {

        echo json_encode( $this->MedicoModel->buscaVendedores($_GET['patron']) );

    } 

    function guardaMedico(){
   
        $idMedico = $_POST['id'];


        if($idMedico!=''){  
            $medico = $this->MedicoModel->updateMedico($idMedico,$_POST['codigo'],$_POST['nombre'],$_POST['cedula'],$_POST['direccion'],$_POST['numext'],$_POST['numint'],$_POST['colonia'],$_POST['cp'],$_POST['pais'],$_POST['estado'],$_POST['municipio'],$_POST['ciudad'],$_POST['tel1'],$_POST['comisionventa'],$_POST['comisioncobranza'],$_POST['vendedor']); 
        }else{
            $medico = $this->MedicoModel->guardaMedico($_POST['codigo'],$_POST['nombre'],$_POST['cedula'],$_POST['direccion'],$_POST['numext'],$_POST['numint'],$_POST['colonia'],$_POST['cp'],$_POST['pais'],$_POST['estado'],$_POST['municipio'],$_POST['ciudad'],$_POST['tel1'],$_POST['comisionventa'],$_POST['comisioncobranza'],$_POST['vendedor']);           
         }

        echo json_encode($medico);
    }
}
?>
