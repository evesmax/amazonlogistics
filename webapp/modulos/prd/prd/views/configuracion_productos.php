<!-- /**
* @author Anali M.
*/ -->
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Configuración de Productos</title>
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
	<script src="../../libraries/jquery.min.js"></script>
	<script type="text/javascript" src="js/configproductos.js"></script>
	<script src="../../libraries/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container well" style="margin-top: 20px;"> 
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-xs-12 col-md-12"><h3>Configuración de Productos</h3></div>
		</div>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#1" data-toggle="tab">Tipos de productos</a></li>
			<li><a href="#2" data-toggle="tab"> Atributos</a></li>
		</ul>
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="tab-content">
					<div class="tab-pane active" id="1"> <!-- Div 1 -->
						<div class="col-sm-12">
							<div class="row">
								<div class="col-md-3">
									<input type="checkbox" name="marcaTodoTipo" id="marcaTodoTipo"/>
									<label for="marcaTodoTipo">Seleccionar todo</label>
								</div>
								<div class="col-md-9">
									<input type="checkbox" name="marcaTodoVendible" id="marcaTodoVendible"/>
									<label for="marcaTodoVendible">Seleccionar todo</label>	
								</div>
							</div>

							<hr>
							<?php  $productos = $Array['types'];
							foreach($productos as $clave => $valor){?>
							<div class="checkbox">
								<div class="row div1">
									<div class="col-md-3 todos1">
										<label>
											<input type="checkbox" value="<?php echo $valor['id'];?>" class='identificar'
											<?php   if($valor['visible']==1){ echo 'checked';}else{ echo '';}?>>
											<?php echo $valor['nombre']; ?> 
										</label>
									</div>
									<div class="col-md-9 todos3">
										<label>
											<input type="checkbox" class="vendible" name="vendible"
											<?php if( $valor['vendible']==1){ echo 'checked';}else{ echo '';}?>> Vendible
										</label>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>	
					</div> <!-- Div 1 -->

					<div class="tab-pane" id="2"> <!-- Div2 -->
						<div class="col-sm-12">
							<div class="row">
								<div class="col-md-3">
									<input type="checkbox" name="marcaTodoAtt" id="marcaTodoAtt"/>
									<label for="marcaTodoAtt">Seleccionar todo</label>
								</div>
							</div>

							<hr>
							<?php  
							$productos = $Array['attributes'];

							foreach($productos as $clave => $valor) { ?>
							<div class="checkbox">
								<div class="row div2">
									<div class="col-md-3 todos2">
										<span <?php if ($valor['id']==1) {
											?> class="glyphicon glyphicon-tags"; 
											<?php }
											else if ($valor['id']==2){ ?> 
											class="glyphicon glyphicon-list"; 
											<?php }
											else if ($valor['id']==3){ ?> 
											class="glyphicon glyphicon-list-alt"; 
											<?php }
											else if ($valor['id']==4){ ?> 
											class="glyphicon glyphicon-briefcase"; 
											<?php }
											else if ($valor['id']==5){ ?> 
											class="glyphicon glyphicon-scale"; 
											<?php }
											else if ($valor['id']==6){ ?> 
											class="glyphicon glyphicon-heart";  
											<?php }
											else{ ?>
												class="glyphicon glyphicon-list"; 
											<?php  } ?>>	
										</span>
										<label>
											<input type="checkbox" id="<?php echo $valor['nombre'];?>" value="<?php echo $valor['id'];?>" class='identificar2'
											<?php if( $valor['visible']==1){echo 'checked';}else{echo '';}?>>
											<?php echo $valor['nombre']; ?> 
										</label>
									</div>
								</div>
							</div>
							<?php  } ?>
						</div>
					</div> <!-- Div2 -->
				</div>
				<div class="row" style="margin-top: 40px;">
					<div class="col-md-12">
						<center>
							<button class='btn btn-primary' id="save" onclick='guardarconfprod()' 
							data-loading-text="<i class='fa fa-refresh fa-spin'></i>">
							<span class='glyphicon glyphicon-cloud'></span> Guardar 
						</button>
					</center>
				</div> 
			</div>
		</div>
	</div> 
</div><!-- div de container well -->
</body>
</html>