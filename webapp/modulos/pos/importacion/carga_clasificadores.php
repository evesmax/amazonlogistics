Cargando Registros...

<?php
//echo 'dkjjfkjfjfjff<br>';
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
	//ini_set("display_errors",1);
	//ini_set('memory_limit', '-1');
	//error_reporting(E_WARNING);

	require_once '../../libraries/Excel/reader.php';

	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('CP1251');
	$data->read(dirname(__FILE__).'/productos_clasificadores_temp.xls');

	$dato = array();
	$sigue = 1;

	//Departamento
	for ($i = 10; $i <= $data->sheets[0]['numRows']; $i++) {
		$dato[1] = utf8_encode(trim($data->sheets[0]["cells"][$i][1])); //# codigo
		$dato[2] = utf8_encode(trim($data->sheets[0]["cells"][$i][2])); //Tecnologia

		//echo '('.$i.')';
		//echo 'DEpartamento('.$dato[1].') '.$dato[2].'<br>';

		if ($dato[1] != '' && $dato[2] != ''){
			$this->ProductoModel->guardarLayDepa($dato);
		}else {
			//echo 'kkk';
			      // $this->InventariosModel->borrar();
			echo "<br /><b style='color:red;'>Existen registros con campos obligatorios vacios en los departamentos y no se guardaron los registros, revise su layout.</b>";
			echo "<br /><br /><a style='background-color:gray; color:black;text-decoration:none;width:200px;height:50px;border:1px solid black;' href='index.php?c=producto&f=indexGridProductos'>Regresar</a>";
			$sigue = 0;
			break;
		}
	}

	//Familia
	if($sigue) {
		for ($i = 10; $i <= $data->sheets[1]['numRows']; $i++) {
			$dato[1] = utf8_encode(trim($data->sheets[1]["cells"][$i][1])); //# lote
			$dato[2] = utf8_encode(trim($data->sheets[1]["cells"][$i][2])); //fecha lote
			$dato[3] = utf8_encode(trim($data->sheets[1]["cells"][$i][3])); //fecha caducidad
			//echo 'Familia('.$dato[1].') '.$dato[2].'--Depa='.$dato[3].'<br>';

			if($dato[1] != '' && $dato[2] != '' && $dato[3] != ''){
				$this->ProductoModel->guardarLayFam($dato);
			}else {
				//$this->InventariosModel->borrar();
				echo "<br /><b style='color:red;'>Existen registros con campos obligatorios vacios en las familias y no se guardaron los registros, revise su layout.</b>";
				echo "<br /><br /><a style='background-color:gray; color:black;text-decoration:none;width:200px;height:50px;border:1px solid black;' href='index.php?c=producto&f=indexGridProductos'>Regresar</a>";
				$sigue = 0;
				break;
			}
		}
	}

	//Linea
	if($sigue) {
		for ($i = 10; $i <= $data->sheets[2]['numRows']; $i++) {
			$dato[1] = utf8_encode(trim($data->sheets[2]["cells"][$i][1])); //id producto
			$dato[2] = utf8_encode(trim($data->sheets[2]["cells"][$i][2])); //ids caracteristicas
			$dato[3] = utf8_encode(trim($data->sheets[2]["cells"][$i][3]));
			//echo 'Linea('.$dato[1].') '.$dato[2].'--Fam='.$dato[3].'<br>';


			if($dato[1] != '' && $dato[2] != '' && $dato[3] != ''){
				$this->ProductoModel->guardarLayLin($dato);
			}else {
				//$this->InventariosModel->borrar();
				echo "<br /><b style='color:red;'>Existen registros con campos obligatorios vacios en las lineas y no se guardaron los registros, revise su layout.</b>";
				echo "<br /><br /><a style='background-color:gray; color:black;text-decoration:none;width:200px;height:50px;border:1px solid black;' href='index.php?c=producto&f=indexGridProductos'>Regresar</a>";
				break;
			}
		}
	}

	unlink(dirname(__FILE__).'/productos_clasificadores_temp.xls');
	echo "<script type='text/javascript'>window.location = 'index.php?c=producto&f=indexGridProductos'</script>";
?>
