<?php
//ini_set('display_errors', 1);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/inventario.php");

class Inventario extends Common
{
    public $InventarioModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar

        $this->InventarioModel = new InventarioModel();
        $this->InventarioModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->InventarioModel->close();
    } 

    function indexGrid(){
        $inventarioActual = $this->InventarioModel->indexGrid();
        //print_r($inventarioActual['productos']);
        require('views/inventario/kardex.php');
    }
    function kardex(){
        $idProducto = $_POST['idProducto'];
        $almacen = $_POST['almacen'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];
        $tipo = $_POST['tipo'];
        $kardex  = $this->InventarioModel->kardex($idProducto,$almacen,$desde,$hasta,$tipo);

        echo json_encode($kardex);
    }
    function inventarioActual(){
        //$inventario  = $this->InventarioModel->inventarioActual();
        $inventarioActual = $this->InventarioModel->productos();
        require('views/inventario/inventarioActual.php');
    }
    function indexMermas(){
        require('views/inventario/mermas.php');
    }
    function mermasForm(){

        $productos = $this->InventarioModel->productos();

        session_start();

        //echo json_encode($_SESSION);

        $productos['usuarios'][0]['idempleado'] = $_SESSION['accelog_idempleado'];
        $productos['usuarios'][0]['usuario'] = $_SESSION['accelog_login'];

        require('views/inventario/mermasForm.php');
    }
    function agregaMerma(){
        $producto = $_POST['producto'];
        $cantidad = $_POST['cantidad'];
        $almacen = $_POST['almacen'];
        $precio = $_POST['precio'];
        $usuario = $_POST['usuario'];
        $carac = $_POST['carac'];

        $proveedor = $_POST['proveedor'];
        $tipo = $_POST['tipo'];
        $precio2 = $_POST['precio2'];

        $merma = $this->InventarioModel->agregaMerma($producto,$cantidad,$almacen,$precio,$usuario,$carac,$proveedor,$tipo,$precio2);

        echo json_encode($merma);
    }
    function guardaMerma(){
        $productos = $_POST['productos'];
        $idnewmerma = $_POST['idnewmerma'];

        $rest = $this->InventarioModel->guardaMerma($productos,$idnewmerma);
        echo json_encode($rest);
    }
    function indexGridMermas(){
        $mermasList = $this->InventarioModel->mermasList();
      

        require('views/inventario/indexGridMermas.php');
    }
    function detalleMerma(){
        $idMerma = $_POST['idMerma'];
        $res = $this->InventarioModel->detalleMerma($idMerma);

        echo json_encode($res);
    }
    function inicialInventario(){
        $idProducto = $_POST['idProducto'];
        $idalmacen = $_POST['idalmacen'];
        $desde = $_POST['desde'];
        $hasta = $_POST['hasta'];
        $R1 = $_POST['R1'];
        $iddep = $_POST['iddep'];
        $idfa = $_POST['idfa'];
        $idli = $_POST['idli'];

        $inventario  = $this->InventarioModel->inventarioActual($idProducto,$idalmacen,$desde,$hasta,$R1,$iddep,$idfa,$idli);

        echo json_encode($inventario);
    }
    function buscarInvActual(){
        $almacen = $_POST['almacen'];
        $producto = $_POST['producto'];
        $res = $this->InventarioModel->buscaInventario($producto,$almacen);
        echo json_encode($res);

    }
    function movsProducto(){
        $idProducto = $_POST['id'];
        $res = $this->InventarioModel->movsProducto($idProducto);

        echo json_encode($res);
    }
    function ajustarInve(){
        $idProducto = $_POST['producto'];
        $idAlmacen = $_POST['almacen'];
        $tipoMovi = $_POST['movimiento'];
        $cantidad = $_POST['cantidad'];
        $costo = $_POST['costo'];
        $obser = $_POST['obser'];

        $res = $this->InventarioModel->ajustarInve($idProducto,$idAlmacen,$tipoMovi,$cantidad,$costo,$obser);

        echo json_encode($res);
    }
    function indexEtiquetado2(){

        $productosGrid = $this->InventarioModel->productos();
        require('views/inventario/indexEtiquetado.php');
    }

    function indexEtiquetado(){
        if( isset($_GET['filtrado'])  ) {

            $productosGrid = $this->InventarioModel->productos2(1,  $_GET['sucursal'],$_GET['departamento'],$_GET['familia'],$_GET['linea']);
        }
        else {
            $productosGrid = $this->InventarioModel->productos2(0,  $_GET['sucursal'],$_GET['departamento'],$_GET['familia'],$_GET['linea']);
        }

        

        require('views/inventario/indexEtiquetado2.php');
    }
    function buscarSucursales() {
        echo json_encode( $this->InventarioModel->buscarSucursales( $_GET['patron'] ) );
    }
    function labelPrintFile(){
        require('views/inventario/etiquetas.php');
    }
    function obtenCaracteristicas(){
        $idProducto = $_POST["id"];
        $cantidadInicial = $_POST["cantidad"];
        $idProv = $_POST["idProv"];

        $car = $this->InventarioModel->obtenCaracteristicas($idProducto,$idProv);        
        
        echo json_encode($car);
    }
    function exisLote(){        
        $idProducto = $_POST["idProducto"];
        $idlote = $_POST["idlote"];
        $exis = $this->InventarioModel->exisLote($idProducto,$idlote);                
        echo $exis;
    }


    function caract2id($caract){
        return preg_replace(["/=>/", "/,/", "/'/" ], ["H", "_", ""], $caract);
    }
    function id2caract($id)
    {
        return preg_replace(['/H/','/_/','/(\d+)/' ], [ '=>',',', "'\${1}'"], $id);
        //return preg_replace(["/H/","/^/", "/$/", "/_/"], ["'=>'", "'", "'", "','"], $id);
    }
    function caract2nombre($caracteristicas ,$caract)
    {
        $caract = $this->caract2id($caract);
        $caracteristicasProductoTmp = explode( '_' , $caract );
        $caracteristicasEtiqueta = '';
        foreach ($caracteristicasProductoTmp as $key => $val) {
            $caracteristica = explode( 'H' , $val);
            $caracteristica = $this->buscarCaracteristica($caracteristicas , $caracteristica);

            $caracteristicasEtiqueta .= "{$caracteristica['CRARACTERISTICA_PADRE']}:{$caracteristica['CARACTERISTICA_HIJA']} ," ;
        }
        return $caracteristicasEtiqueta;
    }
    function buscarCaracteristica($caracteristicas , $caracteristica)
    {
        foreach ($caracteristicas as $key => $value) {
            if($value['ID_P'] == $caracteristica[0] && $value['ID_H'] == $caracteristica[1] )
                return $value;
        }
    }
    function obtenerSeries($id, $caracteristica) {
        $series =  $this->InventarioModel->obtenerSeries( $id, addslashes( $caracteristica ) );
        foreach ($series as $key => $value) {
            $value = (int) $value;
        }
        return json_encode( $series );
    }


    function recalculoinventario()
    {
        $almacenes = $this->InventarioModel->obtenerAmacenes();
        $proveedores = $this->InventarioModel->obtenerProveedores();
        require('views/inventario/recalculoinventario.php');
    }

    function recalculoexistencias()
    {
        $almacen = $_GET['almacen'];
        $proveedor = $_GET['proveedor'];
        $productos = $_GET['productos'];//var_dump( ( $productos == "(  )" || strpos( $productos, '-1')  ) );die;
        $caracteristicas = $this->InventarioModel->getCaracteristicas();
        $valorInventario = $this->InventarioModel->valorInventario($almacen, $proveedor, $productos);
        require('views/inventario/recalculoexistencias.php');
    }
    function realizarAjusteExistencias()
    {
        $ajustesInventario =  $_POST['ajustesInventario'];
        echo $this->InventarioModel->realizarAjusteExistencias($ajustesInventario);
    }

/*    function recalculocostoinventario1()
    {
        $almacen = $_GET['almacen'];
        $caracteristicas = $this->InventarioModel->getCaracteristicas();
        $valorInventario = $this->InventarioModel->valorInventario($almacen);
        require('views/inventario/recalculocostoinventario.php');
    }*/
    function recalculocostoinventario()
    {
        //$almacen = $_GET['almacen'];
        $proveedor = $_GET['proveedor'];
        $productos = $_GET['productos'];
        $caracteristicas = $this->InventarioModel->getCaracteristicas();
        $valorInventario = $this->InventarioModel->valorInventarioGrl($proveedor, $productos);
        require('views/inventario/recalculocostoinventario.php');
    }
    function realizarAjusteCostos()
    {
        $ajustesInventario = $_POST['ajustesInventario'] ;
        echo $this->InventarioModel->realizarAjusteCostosGrl($ajustesInventario);
    }





    function indexReporteConsignacion()
    {
        $almacenes = $this->InventarioModel->obtenerAmacenes();
        $proveedores = $this->InventarioModel->obtenerProveedores();
        require('views/inventario/reporteConsignacion.php');
    }

    function reporteConsignacion() 
    {
        $desde = $_GET['desde'];
        $hasta = $_GET['hasta'];
        $proveedor = $_GET['proveedor'];
        $almacen = $_GET['almacen'];

        $res = $this->InventarioModel->reporteConsignacion($desde, $hasta, $proveedor, $almacen);
        echo json_encode( $res );
    }

    function buscarProductos() {

        echo $this->InventarioModel->buscarProductos( $_GET['patron'] , $_GET['proveedor'] );
    }

    function guardartipo(){
        $tipo = $_POST['tipo'];
        $res = $this->InventarioModel->guardartipo($tipo);
        echo json_encode($res);
    }

    function uploadfile() {
        $output_dir = "images/mermas/";

        if (isset($_FILES["myfile"])) {
            //Filter the file types , if you want.
            if ($_FILES["myfile"]["error"] > 0) {
                echo "Error: " . $_FILES["file"]["error"] . "<br>";
            } else {
                //move the uploaded file to uploads folder;
                move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir . $_FILES["myfile"]["name"]);
                $output_dir . $_FILES["myfile"]["name"];
            }
            echo json_encode( array('direccion' => $output_dir . $_FILES["myfile"]["name"]));
        }
    }

    function indexAntibioticoKardex() {
        $usuarios = $this->InventarioModel->obtenerUsuarios();
        $medicos = $this->InventarioModel->obtenerMedicos();
        $productos = $this->InventarioModel->obtenerProductos();
        $proveedores = $this->InventarioModel->obtenerProveedores();

        require('views/inventario/kardexAntibioticos.php');
    }

    function generaKardexAntibiotico() {
        $desde = $_GET['desde'];
        $hasta = $_GET['hasta'];
        $usuario = $_GET['usuario'];
        $medico = $_GET['medico'];
        $producto = $_GET['producto'];
        $proveedor = $_GET['proveedor'];
        $res = $this->InventarioModel->generaKardexAntibiotico($desde, $hasta, $usuario, $medico, $producto, $proveedor);
    }

    function a_getProvProducto(){
      $idProveedor=$_POST['idProveedor'];    
      $resultReq = $this->InventarioModel->getProvProducto($idProveedor);
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_assoc()) {
          $productos[]=$r;
        }
        $JSON = array('success' =>1, 'datos'=>$productos);
      }else{
        $JSON = array('success' =>0);
      }
      
      echo json_encode($JSON);

    }

}


?>
