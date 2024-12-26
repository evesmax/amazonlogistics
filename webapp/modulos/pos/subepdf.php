<?php 
//print_r($_FILES);


if ($_FILES['archivo']["error"] > 0)
  {
  //echo "Error: " . $_FILES['archivo']['error'] . "<br>";
  }
else
  {
	 move_uploaded_file($_FILES['archivo']['tmp_name'],"../facturas/portal/" . $_FILES['archivo']['name']);
	$res =  array('nombre' => $_FILES['archivo']['name'] );
  /*echo "Nombre: " . $_FILES['archivo']['name'] . "<br>";
  echo "Tipo: " . $_FILES['archivo']['type'] . "<br>";
  echo "Tamaño: " . ($_FILES["archivo"]["size"] / 1024) . " kB<br>";
  echo "Carpeta temporal: " . $_FILES['archivo']['tmp_name']; */
}
  /*ahora co la funcion move_uploaded_file lo guardaremos en el destino que queramos*/


echo json_encode($res);
?>