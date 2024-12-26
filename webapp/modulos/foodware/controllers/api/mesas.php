<?php

    //ini_set("display_errors", 1); error_reporting(E_ALL);
	//Cargar la clase padre para este controlador
    require_once("controllers/api/common.php");
    //Cargar el modelo para este controlador
    require_once("models/api/mesas.php");
    //Cargar los archivos necesarios

	class Mesas extends Common
	{
		//Definir los filtros sobre los parametros que ingresen a la peticion, en caso de no necesitar parametros, dejar un array vacio
        public static   $INDEX = array();
        public static   $OBTENERMESAS = array();
        public static   $INSERTARCOMANDA = array();
        public static   $INSERTARCOMENSAL = array();
        public static   $BORRARCOMENSAL = array();
        public static   $CERRARCOMANDA = array();
        public static   $OBTENERCOMENSALES = array();
        public static   $AGREGARMESA = array();
        public static $LISTAR_RESERVACIONES = array();
        public static $LISTAR_CLIENTES_RESERVACION = array();
        public static $GUARDAR_RESERVACION = array();
        public static $MUDAR_COMANDA = array();
        public static $CLOSE_COMANDA = array();
        public static $PEDIR = array();
        public static $ASIGNAR_MESA = array();
        public static $GUARDAR_PEDIDO = array();

        function __construct(){
        	parent::__construct();
        }

        function __destruct(){
        	parent::__destruct();
        }

		public function index()
		{

		}

        public function obtenerMesas()
        {
            parent::responder(MesasModel::obtenerMesas($_REQUEST));
        }

        public function insertarComanda()
        {
            parent::responder(MesasModel::insertarComanda($_REQUEST));
        }

        public function insertarComensal()
        {
            parent::responder(MesasModel::insertarComensal($_REQUEST));
        }

        public function borrarComensal()
        {
            parent::responder(MesasModel::borrarComensal($_REQUEST));
        }

        public function cerrarComanda()
        {
            parent::responder(MesasModel::cerrarComanda($_REQUEST));
        }

        public function obtenerComensales()
        {
            parent::responder(MesasModel::obtenerComensales($_REQUEST));
        }

        public function agregarMesa()
        {
            $id_insertado;
            $array_respuestas = array();
            $registros = array();

            $id_insertado = MesasModel::agregarMesa($_REQUEST);

            $registros[$_REQUEST["nombre_mesa"]] = $id_insertado["id_insertado"];
            $array_respuestas[]= $registros;

            parent::responder(array('status' => true, 'registros'=> $array_respuestas));
        }

        public function listar_reservaciones(){
          parent::responder(MesasModel::listar_reservaciones($_REQUEST));
          //parent::responder(array('status' => true, 'registros'=> $_REQUEST));
        }

        public function listar_clientes_reservacion(){
          parent::responder(MesasModel::listar_clientes_reservacion($_REQUEST));
        }

        public function guardar_reservacion(){
          parent::responder(MesasModel::guardar_reservacion($_REQUEST));
        }

        public function mudar_comanda(){
            parent::responder(MesasModel::mudar_comanda($_REQUEST));
            /*$id_insertado = MesasModel::mudar_comanda($_REQUEST);
            $array_respuestas[] = $id_insertado;
            parent::responder(array('status' => true, 'registros' => $array_respuestas));*/
        }

        public function close_comanda(){
            parent::responder(MesasModel::close_comanda($_REQUEST));
        }

        public function pedir() {
            parent::responder(MesasModel::pedir($_REQUEST));
        }

        public function asignar_mesa() {
            parent::responder(MesasModel::asignar_mesa($_REQUEST));
        }

        public function guardar_pedido() {
            parent::responder(MesasModel::guardar_pedido($_REQUEST));
        }
	}

?>
