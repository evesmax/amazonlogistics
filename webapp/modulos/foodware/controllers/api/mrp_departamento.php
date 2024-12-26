<?php

	//Cargar la clase padre para este controlador
    require_once("controllers/api/common.php");
    //Cargar el modelo para este controlador
    require_once("models/api/mrp_departamento.php");
    //Cargar los archivos necesarios

	class MrpDepartamento extends Common
	{
		//Definir los filtros sobre los parametros que ingresen a la peticion, en caso de no necesitar parametros, dejar un array vacio
        public static   $INDEX = array();
        public static   $AREAS = array();
        public static $MODULOTIPOPRINT = array();
        public static $VER_PEDIDOS = array();
        public static $LISTAR_TERMINADOS = array();
        public static $OBTENER_PEDIDOS = array();
        public static $CANCELAR_PEDIDO = array();
        public static $INFO_VENTAS = array();

        function __construct(){
        	parent::__construct();
        }

        function __destruct(){
        	parent::__destruct();
        }

        public function areas()
        {
            parent::responder(MrpDepartamentoModel::areas());
        }

        public function moduloTipoPrint() {
            parent::responder(MrpDepartamentoModel::moduloTipoPrint());
        }

        public function ver_pedidos() {
            parent::responder(MrpDepartamentoModel::ver_pedidos($_REQUEST));
        }

        public function terminar_pedido() {
            parent::responder(MrpDepartamentoModel::terminar_pedido($_REQUEST));
        }

        public function cancelar_pedido() {
            parent::responder(MrpDepartamentoModel::cancelar_pedido($_REQUEST));
        }

        public function obtener_pedidos() {
            parent::responder(MrpDepartamentoModel::obtener_pedidos($_REQUEST));
        }

        public function info_ventas() {
            parent::responder(MrpDepartamentoModel::info_ventas($_REQUEST));
        }

	}

?>