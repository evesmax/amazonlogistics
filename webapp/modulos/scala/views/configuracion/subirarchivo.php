<?php
//$targetFolder = '../uploads/'; // Relative to the root
//$targetFolder = '../../images/'; // Relative to the root
$name = $_GET['name'];


//$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) /* && $_POST['token'] == $verifyToken */) {
	$tempFile = $_FILES['Filedata']['tmp_name'];  // archivo original
	//$targetPath = $targetFolder;
	//$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name']; /// archivo final
	
	// Validate the file type
	$fileTypes = array('PNG','png','jpg'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);

	//archivo y direcion nueva
	$targetFile = '../../images/'.$name.'.'.$fileParts['extension'];
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		$output = array("success" => true, "archivo" => $targetFile, "message" => "Archivo cargado con exito", "name" => $name.'.'.$fileParts['extension']);
	} else {
		$output = array("success" => false, "message" => "Tipo de archivo invalido");
	}

	
}else{
	$output = array("success" => false, "message" => "Seleccione un archivo png o jpg");
}

echo json_encode($output);
?>