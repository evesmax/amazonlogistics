<?php
			
			if ($_SESSION['catalog_nuevo']==0){

				$linkcot="modulos/produccion/produccion_imprimir.php?folio=".$catalog_id_utilizado;
				echo "<A href='".$url_dominio.$linkcot."'><img src='../../netwarelog/repolog/img/impresora.png' border='0'>Produccion</A>";
						

			}
			//echo $_SESSION['catalog_nuevo'];
			
?>