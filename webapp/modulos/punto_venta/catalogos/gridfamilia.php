<?php
include("gridClass.php");
$filtro=1;if(strlen(@$_POST["filtro"])>3){$filtro=@$_POST["filtro"];}
$elimina=0;
if(is_numeric(@$_GET["elimina"])){$elimina=@$_GET["elimina"];}
if(is_numeric(@$_POST["elimina"])){$elimina=@$_POST["elimina"];}
/*PARAMETRIZAR*/
$grid=new Grid("idFam",@$_POST["paginacion"],$filtro,$elimina);
$consulta="select f.idFam,f.nombre,p.nombre n2 from mrp_familia f left join mrp_departamento p on p.idDep=f.idDep where ".$filtro." order by f.nombre";
$campos=array("ID","Nombre","Departamento");
/*END PARAMETRIZAR*/
$mensaje="";
switch(@$_POST["funcion"])
{
	case "elimina": $mensaje=$grid->elimina(@$_POST["id"],'mrp_familia','idFam',
	array(
		 'Lineas'=>'mrp_linea-idFam'
		 
	));
	break;
}

$grid->query(stripslashes($consulta));
$grid->setHeader($campos);
if(is_numeric(@$_POST["page"]))
{echo $grid->render(@$_POST["page"]);}
else{?>	
<html>
	<head>
		
		<link type="text/css" rel="stylesheet" title="estilo" href="../../../netwarelog/catalog/css/view.css">
		<link type="text/css" rel="stylesheet" title="estilo" href="../../../netwarelog/catalog/css/estilo.css">
        <?php include('../../../netwarelog/design/css.php');?>
        <LINK href="../../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

       <script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script> 
		<script type="text/javascript" src="js/familia.js"></script>	
		
		<script  type="text/javascript" src="js/jTPS.js"></script>
    <link rel="stylesheet" type="text/css" href="../reportes/css/csstest.css">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type="text/javascript">
		<?php  
			if(strlen($mensaje)>5)
			{
		?>
			alert('<?php echo $mensaje;?>');		
		<?php
			}
		?>
</script>
	</head>
        <body>
        <style type="text/css">
			.nmcatalogbusquedainputtext{
				background-color: unset;
				border: 1px solid gray;
				box-shadow: unset;
			}
			.nmcatalogbusquedafoot {
			    background-color: #eaeaea;
			    border-top: 1px solid black;
			    color: unset;
			}
			.nminputtext {
			    background-color: white;
			    border: 1px solid gray;
			    box-shadow: unset;
			    margin-left: 0.5em;
			    margin-right: 0.5em;
			}
			.nminputbutton {
			    background-color: #337ab7;
			    border: unset;
			    border-radius: 3px;
			    color: white;
			    font-size: 15px;
			    padding: 4px 20px;
			}
			.nminputselect {
			    background-color: white;
			    border: 1px solid gray;
			    box-shadow: unset;
			    margin-left: 0.5em;
			    padding: 6px 30px;
			}
        </style>
		<div id="grid">
			<?php echo $grid->render(1); ?>
		</div> 
		<script type="text/javascript"> 
		$(document).ready(function() {

			$('#datos').jTPS({
				perPages : ['TODO'],
				scrollStep : 1,
				scrollDelay : 30,
				clickCallback : function() {
				}
			});

			$("#gridOpc").width($("#datos").width());

		}); 
</script>             
	</body>
</html>
<?php } ?>