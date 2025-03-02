<?php
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/config.php");

class Config extends Common
{
	public $ConfigModel;

	function __construct()
	{
		//Se crea el objeto que instancia al modelo que se va a utilizar

		$this->ConfigModel = new ConfigModel();
		$this->ConfigModel->connect();
	}

	function __destruct()
	{
		//Se destruye el objeto que instancia al modelo que se va a utilizar
		$this->ConfigModel->close();
	}

	//Funcion mainpage que genera la pagina por default en caso de no existir el controlador
	function mainPage()
	{
		$IsEmpty = $this->ConfigModel->IsEmpty();
		$Exercises = $this->ConfigModel->getAllExercises();
		require('views/config/mainpage.php');
	}

	//Metodo que genera la pantalla inicial de configuracion del ejercicio
	function configExercise()
	{
		$name = $this->ConfigModel->getCompanyName('1');
		$data = $this->ConfigModel->getExerciseInfo();
		$rfc = $this->ConfigModel->getRFC();
		$tipoConfiguracion = 1;
		if($tipoConfiguracion == 2)
		{
			$claves = $this->ConfigModel->claves();
			$sel='';
			while($cl = $claves->fetch_assoc())
			{
				$sel .= "<option value='".$cl['Clave']."'>".$cl['Clave']." / ".$cl['Descripcion']."</option>";
			}
		}
		else{ $sel = "<option value='0' selected>Sel</option>";}
		require('views/config/configExercise.php');
	}

	function configAccounts()
	{
		$name = $this->ConfigModel->getCompanyName('1');
		$data = $this->ConfigModel->getExerciseInfo();
		$circulante = $this->ConfigModel->circulante();
		$ishgasto = $this->ConfigModel->CuentaGasto();
		//$type_id_account = $this->ConfigModel->CuentaTipoCaptura();
		//$Accounts = $this->ConfigModel->getAccounts($type_id_account);// Seccion encargada de cargar Cuentas
		$Accounts=$this->ConfigModel->cuentasmayor();
		require('views/config/configAccounts.php');
	}

	//Guarda la configuracion o los cambio que se le hayan hecho
	function saveConfig()
	{	

		$open_periods = ( isset( $_POST['open_periods'] ) ) ? 1 : 0;
		$default_catalog = $_POST['default_catalog'];
		//Guarda o actualiza la tabla de configuracion
		
		if($_POST['default_catalog']==2 && $_POST["tipoCarga"] == 2){
			$cadena="";
			if (isset($_FILES["archivo"]) && is_uploaded_file($_FILES['archivo']['tmp_name'])) {
				$handle = fopen($_FILES['archivo']['tmp_name'], "r");
				//print_r($handle);
				$row = 1;
				
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { 
					$num = count($data); 
					$row++;
					$cadena=$cadena.$data[0].",'".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."',".$data[5].",".$data[6].",".$data[7].",".$data[8].",'".$data[9]."',".$data[10].",".$data[11].",".$data[12].",".$data[13].",".$data[14].",'".$data[15]."',".$data[16].",".$data[17].",".$data[18].",".$data[19].",".$data[20].",".$data[21]."),(";
    				//echo $data[21];
				}

				$sql = substr($cadena, 0, -2);

				$this->ConfigModel->importinsert($sql);
				

			} else{
				//echo "<script type='text/javascript'>alert('Ocurrio un error asegurese de haber cargado un archivo.');window.location = 'index.php?c=Config'</script>";
			}
		
		}else if($_POST["tipoCarga"] == 3)
		{
			//Carga de datos de contpaq
			$target_dir = dirname(__FILE__)."/../importar/";

			if (isset($_FILES["CONTPAC"])) {

			//echo  $target_dir.basename( $_FILES["contpaq"]["name"][0] );
			if($_FILES['CONTPAC']['name'][0]){
				if (move_uploaded_file($_FILES['CONTPAC']['tmp_name'][0], $target_dir.basename("cuentas.xls" ) )) {
					echo "El archivo de Cuentas fue subido correctamente<br/>";
				} else {
					echo "No se subio el archivo de Cuentas <br/>";
				}
			}
			//$target_dir.basename( $_FILES["contpaq"]["name"][1]
			if($_FILES['CONTPAC']['name'][1]){
				if (move_uploaded_file($_FILES['CONTPAC']['tmp_name'][1], $target_dir.basename("polizas.xls" ) )) {
					echo "El archivo de Polizas fue subido correctamente<br/>";
				} else {
					echo "No se subio el  archivo de Polizas<br/>";
				}
			}

				$strMask = $_POST["txtMascara"];
    			$strSeparator = $_POST["txtSeparador"];
				include(dirname(__FILE__)."/../importar/import.php");
			}
			else
			{
				echo "<script type='text/javascript'>alert('Ocurrio un error al cargar los datos');window.location = 'index.php?c=Config'</script>";
			}
			$default_catalog = 3;

		//Carga de datos de contpaq FIN
		}
		//return false;
		$this->ConfigModel->saveConfig($_GET['act'],1,$default_catalog,$_POST['structure'],$_POST['values'],$_POST['level'],$_POST['numpol'],$_POST['rfc'],$_POST['begin'],$_POST['period'],$_POST['periods'],$_POST['current_period'],$open_periods,$_POST['primera_vez'],$_POST['cl_num']);
		echo "<script type='text/javascript'>alert('Datos Guardados Satisfactoriamente.');window.location = 'index.php?c=Config'</script>";
	}

	function saveConfigAccounts()
	{
		//Si la cuenta cambio, cambia el valor de los registros del punto de venta hacia esa cuenta.
		/*if ( $this->ConfigModel->getNumAccounts() > 0 )
		{
			if(intval($_POST['clientes']) != $this->ConfigModel->getConfigAccount('CuentaClientes'))
				$this->ConfigModel->updateAccount(6,$_POST['clientes']);

			if(intval($_POST['ventas']) != $this->ConfigModel->getConfigAccount('CuentaVentas'))
				$this->ConfigModel->updateAccount(78,$_POST['ventas']);

			if(intval($_POST['IVA']) != $this->ConfigModel->getConfigAccount('CuentaIVA'))
				$this->ConfigModel->updateAccount(7,$_POST['IVA']);
			
			if(intval($_POST['caja']) != $this->ConfigModel->getConfigAccount('CuentaCaja'))
				$this->ConfigModel->updateAccount(3,$_POST['caja']);

			if(intval($_POST['TR']) != $this->ConfigModel->getConfigAccount('CuentaTR'))
				$this->ConfigModel->updateAccount(80,$_POST['TR']);

			if(intval($_POST['bancos']) != $this->ConfigModel->getConfigAccount('CuentaBancos'))
				$this->ConfigModel->updateAccount(65,$_POST['bancos']);

			}*/
			if(!isset($_POST['defaultimp'])){ $_POST['defaultimp']=0;}
			if(!isset($_POST['retencion'])){ $_POST['retencion']=0;}
			if(!isset($_POST['defaultieps'])){ $_POST['defaultieps']=0;}
			$this->ConfigModel->saveConfigAccounts($_POST['compras'],$_POST['ventas'],$_POST['devoluciones'],$_POST['clientes'],$_POST['IVA'],$_POST['caja'],$_POST['TR'],$_POST['bancos'],$_POST['capital'],$_POST['flujo'],$_POST['proveedores'],$_POST['utilidad'],$_POST['perdida'],$_POST['ivapendientepago'],$_POST['ivapagado'],$_POST['ivapendientecobro'],$_POST['ivacobrado'],$_POST['iepspendientepago'],$_POST['iepspagado'],$_POST['iepspendientecobro'],$_POST['iepscobrado'],$_POST['deudores'],$_POST['ish'],$_POST['ivaretenido'],$_POST['isrretenido'],$_POST['defaultimp'],$_POST['retencion'],$_POST['iepsgasto'],$_POST['defaultieps']);
			echo "<script type='text/javascript'>alert('Datos Guardados Satisfactoriamente.');window.location = 'index.php?c=Config&f=configAccounts'</script>";
		}

	//Establece el ejercicio actual
		function Establecer()
		{
			$this->ConfigModel->Establecer($_POST['activo']);
			echo "<script language='javascript'>alert('El ejercicio ha sido establecido como actual'); window.location = \"index.php?c=Config\"</script>";
		}

	//Metodo que cierra el ejercicio si la poliza del periodo 13 ha sido generada
		function CloseExercise()
		{
			$cerrar = $this->ConfigModel->CloseExercise($_POST['Id'],$_POST['Ejercicio']);
			echo $cerrar;
		}

	//Guarda el primer ejercicio que se genera asi como su configuracion inicial
		function FirstExercise()
		{
			$this->ConfigModel->FirstExercise($_POST['Ejercicio']);
		}
		function configPDV()
		{
			$name = $this->ConfigModel->getCompanyName('1');
			$data = $this->ConfigModel->getExerciseInfoPDV();
			$Accounts = $this->ConfigModel->cuentasAfectables();
			require('views/config/configPDV.php');
		}

		function saveConfigPDV()
		{
			$conectar = ( isset( $_POST['conectar'] ) ) ? 1 : 0;
			$this->ConfigModel->saveConfigPDV($_POST['historial'],$conectar,$_POST['corte'],$_POST['anterior_corte'],$_POST['ventas'],$_POST['clientes'],$_POST['IVA'],$_POST['caja'],$_POST['bancos']);
			
			if(intval($_POST['historial']) AND !intval($_POST['anterior_corte']))
			{
				$this->ConfigModel->saveHistoryPDV($_POST['ejercicio']);	
			}

			echo "<script type='text/javascript'>alert('Datos Guardados Satisfactoriamente.');window.location = 'index.php?c=Config&f=configPDV'</script>";
		}

		function passAdmin()
		{
			echo $this->ConfigModel->passAdmin($_POST['Pass']);
		}

		function ReiniciarContabilidad()
		{
			$this->ConfigModel->ReiniciarContabilidad();

			$spin = 1;
			while($spin <= 2)
			{
				if($spin == 1) $ast = "*/*";
				if($spin == 2) $ast = "*";

				$files = glob("xmls/facturas/$ast"); 
				foreach($files as $file)
				{ 
					if(is_file($file))
				    	unlink($file); 
					else
						rmdir($file);
				}
				$spin++;
			}

			mkdir("xmls/facturas/temporales/",0777);
		}
	}
	?>