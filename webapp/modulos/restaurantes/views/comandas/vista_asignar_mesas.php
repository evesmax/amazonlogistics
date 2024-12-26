	
	<div id="exTab2">	
		<ul class="nav nav-tabs">			
			<?php foreach ($areas as $key => $valor) { ?>
				<li <?php if($valor['id'] == $area_princ['id']) { ?> class="active" <?php } ?>><a  href="#tab_area_<?php echo $valor['id']?>" data-toggle="tab"><?php echo $valor['area']?></a></li>
			<?php } ?>
		</ul>

		<div class="tab-content ">
			<?php foreach ($areas as $key => $valor) { $si_exist = 0;?>

				<div class="tab-pane <?php if($valor['id'] == $area_princ['id']) { ?>active<?php } ?>" id="tab_area_<?php echo $valor['id']?>">					
	          		<?php foreach ($mesas as $key2 => $row) { 
	          			if($row['idDep'] == $valor['id']){ $si_exist = 1;?>

	          				<div class="pull-left" style="padding:5px">
								<button 
									id="btna_<?php echo $row['mesa'] ?>" 
									type="button" 
									class="btn btn-default btn-lg"  
									onclick="comandas.asignar_ch({
										id_mesa: <?php echo $row['mesa'] ?>,
									})" 
									style="padding: 1px;
									<?php if($row['junta']) { 
											?> width:180px;  <?php 
										} else { 
											?> width:110px; <?php } 
										?> position:relative; height: 110px; font-size:13px;">
												<?php if($row['status'] == 1) { ?>										 	

													<?php } else { ?>
														<?php if($row['id_tipo_mesa'] == 7) { ?>
															<section style="text-align: center; border-radius: 15%; margin-left: auto; margin-right: auto; width: 100%; height: 50px; background-color: #423228" ></section>
														<?php }else if ($row['id_tipo_mesa'] == 9){ ?>
															<section style="text-align: center; border-radius: 15%; margin-left: auto; margin-right: auto; width: 80%; height: 80px; background-color: #423228" ></section>
														<?php } else { ?> 
															<img style="max-width:100%; width: auto; height:100%" src="<?php echo $row['imagen'] ?>">
														<?php } ?>							
													<?php } ?>

												<div style="font-size: 12px;color: white;
												text-align: center;	
													  position: absolute;
													  transform: translate(-50%, -50%);
													  left: 50%;
													  top: 50%;"><?php echo $row['nombre_mesa'] ?>																		  	
												</div>
								</button>
							</div>

	          		<?php 
	          			}?>
	          		<?php
					// Valida que halla mesas en esa area
						if ($si_exist == 0 && $key2 == (count($mesas_juntar) - 1)) {?>
							<div align="center">
								<?php if($idioma == 1) { ?> No hay mesas disponibles <?php } else { ?> No vacant tables <?php }?></span>
							</div><?php
						} ?>
	          		<?php } ?>
				</div>

			<?php } ?>
			
		</div>
	</div>