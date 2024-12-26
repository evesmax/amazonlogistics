<?php

	include "parametros.php";

	$limite = 5;

?>

<!DOCTYPE html>
<html>

	<head>
		<title>Tienda NetwareCont</title>

		<script src="utilerias/orbit/jquery_min.js" type="text/javascript"></script>
		<script src="utilerias/orbit/jquery_orbit.js" type="text/javascript"></script>
		<link rel="stylesheet" href="utilerias/orbit/orbit.css">
		<link rel="stylesheet" href="../../netwarelog/catalog/css/estilo.css">

		<script type="text/javascript">

			var slide = 0;
			var totalslides = 0;

     		$(window).load(function() {
         		$('#featured').orbit({
				     animation: 'horizontal-push',    // fade, horizontal-slide, vertical-slide, horizontal-push
				     animationSpeed: 800,                // how fast animtions are
				     timer: true, 			 // true or false to have the timer
				     advanceSpeed: 4000, 		 // if timer is enabled, time between transitions 
				     pauseOnHover: false, 		 // if you hover pauses the slider
				     startClockOnMouseOut: false, 	 // if clock should start on MouseOut
				     startClockOnMouseOutAfter: 1000, 	 // how long after MouseOut should the timer start again
				     directionalNav: true, 		 // manual advancing directional navs
				     captions: true, 			 // do you want captions?
				     captionAnimation: 'fade', 		 // fade, slideOpen, none
				     captionAnimationSpeed: 800, 	 // if so how quickly should they animate in
				     bullets: true,			 // true or false to activate the bullet navigation
				     bulletThumbs: false,		 // thumbnails for the bullets
				     bulletThumbLocation: '',		 // location from this file where thumbs will be
				     afterSlideChange: function(){} 	 // empty function 
				});
				
     		});
		</script>

		<style>
			body{
				margin:0px;
				margin-left:20px;
				font-family: Helvetica, Tahoma, Arial, Trebuchet;
				color:gray;
			}
			#encabezado{
				background: #efefef;				
			}
			#busqueda{
				margin-left:25px;
			}
			#btnbuscar{
				cursor: pointer;
			}
			#featured{
				-webkit-box-shadow:  0px 0px 20px 5px rgba(0, 0, 0, 0.4);
				        
				        box-shadow:  0px 0px 20px 5px rgba(0, 0, 0, 0.4); 	
			}
			#seccioninferior{
				margin-left:0px;
			}
			.subtitulo{
				font-size: 14px;
				font-weight: bold;
				/*border-bottom:1px solid gray;*/
				background: #efefef ;
				padding: 10px;
			}
			.aplicacion{
				font-size: 10px;
				font-weight: normal;				
			}
			.aplicaciontexto{
				color:#555555;
				text-decoration: none;
			}
			.aplicaciontexto:hover{
				text-decoration: underline;
			}

		</style>


	</head>

	<body>		
		
		<div align="right">
		<!-- BUSQUEDA -->
		<table id="busqueda">
			<tbody><tr>
				<td><a class='aplicaciontexto' href=''>Mis compras</a></td>
				<td>&nbsp;</td>
				<td><input size="60" type="search" placeholder="Buscar Aplicación" /> </td>
				<td><img id="btnbuscar" src="../../netwarelog/catalog/img/preview.png" /></td>
				<td>&nbsp; &nbsp; &nbsp; </td>
			</tr></tbody>
		</table><br>
		</div>




		<!-- LONAS -->
		<table id="encabezado" width="100%">
			<tbody>
				<tr>					
					<td align="left" width="650">
						<div id="featured"> 
							<?php
								$sql = "
										select idaplicacion 
										from tienda_aplicaciones_promocionar 
										where fechainicio <= now() and fechafin >= now() 
										";
								$result_aplicaciones = $conexion->consultar($sql);
								$totalslides = 0;
								while($rs = $conexion->siguiente($result_aplicaciones)){
									?>

										<a href="https://www.yahoo.com.mx">
											<img 
												border="0" 
												src="lonas/<?php echo $rs{'idaplicacion'} ?>.jpg"
												alt="Haga click para ver más información"												
											/>
										</a>										

									<?php
									$totalslides++;
								}
								$conexion->cerrar_consulta($result_aplicaciones);

							?>
							<script>
								totalslides = <?php echo $totalslides; ?>;
							</script>

							<!--
				     		<a href="https://www.yahoo.com.mx"><img border="0" src="lonas/overflow.jpg" alt="Overflow: Hidden No More" /></a>
				     		<a href="https://www.yahoo.com.mx"><img border="0" src="lonas/captions.jpg" alt="HTML Captions" /></a>
				     		<a href="https://www.yahoo.com.mx"><img border="0" src="lonas/features.jpg" alt="and more features" /></a>
				     		-->
						</div>	
					</td>
					<td align="left">						
						&nbsp;
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
			</tbody>
		</table>
		
		<br><br><br>





		
		<!--SECCION INFERIOR:  TOP / CATEGORIAS / NUEVAS !-->		


		<table id="seccioninferior" width="100%" border="0">
			<tbody>
				<tr>
					<td width="25%" class="subtitulo">Mejor Calificadas</td>
					<td></td>
					<td width="25%" class="subtitulo">Más Compradas</td>
					<td></td>
					<td width="25%" class="subtitulo">Nuevas</td>
					<td></td>
					<td width="25%" class="subtitulo">Categorías</td>
					<td></td>
				</tr>

				<tr valign="top">
					<!--DESTACADAS-->
					<td class="aplicacion">
					<?php 
						$sql = " 
								select a.idaplicacion, a.nombre, avg(c.calificacion) as calificacion
								from tienda_aplicaciones a inner join tienda_aplicaciones_calificacion c
								     on a.idaplicacion = c.idaplicacion
								group by a.idaplicacion, a.nombre
								order by c.calificacion desc, a.nombre 
								limit 0,".$limite."
								";
						//echo $sql;
						$result_aplicaciones = $conexion->consultar($sql);
						while($rs = $conexion->siguiente($result_aplicaciones)){
							?>
								<a href=''>
								<table>
									<tbody>
										<tr>
											<td><?php 
												$calificacion = 0;
												if(isset($rs{'calificacion'})){
													$calificacion = $rs{'calificacion'};
												}
												//Estrellas
												for($c=4;$c>=0;$c--){
													if($c>=$calificacion){
														?>
															<img src="utilerias/img/e_g.png" />
														<?php
													} else {
														?>
															<img src="utilerias/img/e.png" />
														<?php														
													}				
												}

											?>
											<td><img src="iconos/<?php echo $rs{'idaplicacion'} ?>.png" /></td>
											<td class="aplicaciontexto">
												<b><?php echo $rs{'nombre'}; ?></b>												
											</td>
										</tr>
									</tbody>
								</table>
								</a>
							<?php						
						}
						$conexion->cerrar_consulta($result_aplicaciones);						
					?>
					</td>
					<td></td>

					<!--Más Compradas-->
					<td class="aplicacion">
					<?php 
						$sql = " 
								select a.idaplicacion, a.nombre, count(c.idaplicacion) as cuantas
								from tienda_aplicaciones a inner join tienda_aplicaciones_compradas c
								     on a.idaplicacion = c.idaplicacion
								group by a.idaplicacion, a.nombre
								order by cuantas desc, a.nombre 
								limit 0,".$limite."
								";
						//echo $sql;
						$result_aplicaciones = $conexion->consultar($sql);
						while($rs = $conexion->siguiente($result_aplicaciones)){
							$cuantas = 0;
							if($rs{'cuantas'}!=null){
								$cuantas = $rs{'cuantas'};	
							} 

							?>
								<a href='' title="Comprada: <?php echo $cuantas; ?> veces.">
								<table>
									<tbody>
										<tr>
											<td><img src="iconos/<?php echo $rs{'idaplicacion'} ?>.png" /></td>
											<td class="aplicaciontexto"><b><?php echo $rs{'nombre'}; ?></b></td>
										</tr>
									</tbody>
								</table>
								</a>
							<?php						
						}
						$conexion->cerrar_consulta($result_aplicaciones);						
					?>
					</td>
					<td></td>


					<!--Nuevas-->
					<td class="aplicacion">
					<?php 
						$sql = " 
								select a.idaplicacion, a.nombre, 
										concat(day(a.fechaalta),'/',month(a.fechaalta),'/',year(a.fechaalta)) as alta
								from tienda_aplicaciones a 
								order by fechaalta desc
								limit 0,".$limite."
								";
						//echo $sql;
						$result_aplicaciones = $conexion->consultar($sql);
						while($rs = $conexion->siguiente($result_aplicaciones)){

							?>
								<a href='' title="Añadida: <?php echo $rs{'alta'}; ?>">
								<table>
									<tbody>
										<tr>
											<td><img src="iconos/<?php echo $rs{'idaplicacion'} ?>.png" /></td>
											<td class="aplicaciontexto"><b><?php echo $rs{'nombre'}; ?></b></td>
										</tr>
									</tbody>
								</table>
								</a>
							<?php						
						}
						$conexion->cerrar_consulta($result_aplicaciones);						
					?>
					</td>
					<td></td>



					<!--Categorías-->
					<td class="aplicacion">
					<?php 
						$sql = " 
								select c.idcategoria, c.nombre									
								from tienda_aplicaciones_categorias c 
								order by c.nombre								
								";
						//echo $sql;
						$result_aplicaciones = $conexion->consultar($sql);
						while($rs = $conexion->siguiente($result_aplicaciones)){

							?>								
								<a href=''>
								<table>
									<tbody>
										<tr>	
											<td> &nbsp; </td>										
											<td class="aplicaciontexto"><b><?php echo $rs{'nombre'}; ?></b></td>
										</tr>
									</tbody>
								</table>
								</a>
							<?php						
						}
						$conexion->cerrar_consulta($result_aplicaciones);						
					?>
					</td>
					<td></td>					


				</tr>


			</tbody>
		</table>
		
		<br><br>
		<hr>
		Contacto: 1629-128 @netwarecont &nbsp; Soporte Técnico: netwarecont@netwaremonitor.com 


	</body>


</html>
<?php
$conexion->cerrar();
?>