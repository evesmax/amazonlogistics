<?php
ini_set('memory_limit','16M');
ini_set('display_errors', 1);
set_time_limit(300);
	$modulo = (isset($_GET['modulo'])) ? $_GET['modulo'] : 0;

  	include('headers.php');
	include('conexiondb.php');


    $idusr = $_SESSION['accelog_idempleado'];
    $SQL = "SELECT concat(nombre,' ',apellido1) as username, idempleado from empleados where idempleado='$idusr';";
    $result = $mysqli->query($SQL);
	$row = $result->fetch_array();
	$username_global=$row['username'];
	$id_username_global=$row['idempleado'];

	if($modulo=="datos_generales"){ include('jscontratista.php'); exit(); }
	if($modulo=="alta_obra"){ include('jsaltaobra.php'); exit(); }
	if($modulo=="usuarios_obras"){ include('jsusuarios_obra.php'); exit(); }
    if($modulo=="notilog"){ include('notilog.php'); exit(); }
    if($modulo=="traspaso"){ include('traspaso.php'); exit(); }

	if(!isset($_COOKIE['xtructur'])){
		$idperfil = preg_replace('/\(|\)/','',$_SESSION['accelog_idperfil']);

		if($idperfil==2){
	    	$SQL = "SELECT id,obra FROM constru_generales WHERE borrado=0 ORDER BY obra;";
		}else{
			$SQL = "SELECT a.id,a.obra FROM constru_generales a 
	    left join constru_obrasusuario b on  a.id=b.idobra  
	    WHERE a.borrado=0 AND b.iduser='$idperfil' group by a.id ORDER BY obra;";

		}
	    


		$result = $mysqli->query($SQL);

		while($row = $result->fetch_array() ) {
       		$obras[]=$row;
    	}

    	include('access_obras.php');
    	exit();
 
	}else{
		$cookie_xtructur = unserialize($_COOKIE['xtructur']);
		$idses_obra = $cookie_xtructur['id_obra'];
		$nombre_obra = $cookie_xtructur['obra'];
		$obra_ini = $cookie_xtructur['obra_ini'];
		$obra_fin = $cookie_xtructur['obra_fin'];
	?>
	<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="css/dialogo.css" type="text/css">
	<style>
	  @media print{
	    #imprimir,#filtros,#excel, #botones
	    {
	      display:none;
	    }
	    #logo_empresa
	    {
	      display:block;
	    }
	    .table-responsive{
	      overflow-x: unset;
	    }
	    #imp_cont{
	      width: 100% !important;
	    }
	  }
	  .btnMenu{
	    border-radius: 0; 
	    width: 100%;
	    margin-bottom: 0.3em;
	    margin-top: 0.3em;
	  }
	  .row
	  {
	      margin-top: 0.5em !important;
	  }
	  .titulo, h5, h4, h3{
	      background-color: #eee;
	      padding: 0.4em;
	  }
	  .modal-title{
	    background-color: unset !important;
	    padding: unset !important;
	  }
	  .nmwatitles, [id="title"] {
	    padding: 8px 0 3px !important;
	    background-color: unset !important;
	  }
	  .select2-container{
	    width: 100% !important;
	  }
	  .select2-container .select2-choice{
	    background-image: unset !important;
	    height: 31px !important;
	  }
	  .twitter-typeahead{
	    width: 100% !important;
	  }
	  .tablaResponsiva{
	      max-width: 100vw !important; 
	      display: inline-block;
	  }
	  
	  .cfinal{
	  	margin-bottom: 5em;
	  }
	  .ui-widget-content{
		border: 1px solid #c0c0c0;
	  }
	</style>
		<body>
			<div class="container" style="width:100%">
    			<div class="row">
      				<div class="col-sm-10 col-sm-offset-1">
						<div class="row" style="background-color:#fff; margin-left: 0; margin-right: 0;">
							<div class="col-sm-10" style="padding: 5px;">
								<input id="nombre_obra_x" type="hidden" value="<?php echo $nombre_obra; ?>">
								<b>Obra seleccionada:</b> <?php echo $nombre_obra; ?>
							</div>

							<div class="col-sm-2" style="padding:0px;">
							<button id="btnGenReq" class="btn btn-primary btn-sm pull-right" onclick="cerrar_session('<?php echo $modulo; ?>');" id="btnGenReq"> Cambiar de obra</button>

			
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<section id="swi_cont">
									<?php	
									switch ($modulo) {
										case 'proforma':
											include('jsproforma2.php');
										break;

										case 'explosion_insumos':
											include('jsexplosion.php');
										break;

										case 'asignar_familias':
											include('jsasignarfam.php');
										break;

										case 'crear_presu_control':
											include('jspresupuesto.php');
										break;

										case 'extra_control':
											include('jsextra.php');
										break;

										case 'extra_aut':
											include('jsextraaut.php');
										break;

										case 'adic_control':
											include('jsadic.php');
										break;

										case 'adic_aut':
											include('jsadicaut.php');
										break;

										case 'nocob_control':
											include('jsnocob.php');
										break;

											case 'nocob_aut':
											include('jsnocobaut.php');
										break;

										case 'planeacion':
											include('jsagrupador.php');
										break;

										case 'arbol':
											include('jsarbol.php');
										break;

										case 'asignar_planeacion':
											include('jsasignacion.php');
										break;
										
										case 'visualizar_pcontrol':
											include('jsvisualizarpcontrol.php');
										break;

										case 'requisiciones':
											include('jsrequisiciones.php');
										break;

										case 'pedidos':
											include('jspedidos2.php');
										break;

										case 'alta_tecnicos':
											include('jstecnicos.php');
										break;

										case 'alta_administrativos':
											include('jsadministrativo.php');
										break;

										case 'alta_subcontratistas':
											include('jssubcontratistas.php');
										break;

										case 'alta_proveedores':
											include('jsproveedores.php');
										break;

										case 'alta_destajista':
											include('jsdestajista.php');
										break;

										case 'alta_obreros':
											include('jsobreros.php');
										break;

										case 'pu_destajos':
											include('jspudestajos.php');
										break;

										case 'pu_subcontratos':
											include('jspusubcontratos.php');
										break;

										case 'desgloce_indirectos':
											include('desgloce_indirectos.php');
										break;

										case 'materiales':
											include('jsmateriales.php');
										break;

										case 'alta_fam_obreros':
											include('jsfamobreros.php');
										break;

										case 'alta_fam_tecnicos':
											include('jsfamtecnicos.php');
										break;

										case 'tab_obreros':
											include('jstabobreros.php');
										break;

										case 'tab_tecnicos':
											include('jstabtecnicos.php');
										break;

										case 'visualizar_requi':
											include('jsvisualizar_requi.php');
										break;

										case 'visualizar_pedi':
											include('jsvisualizar_pedi.php');
										break;

										case 'arbols':
											include('jsarbols.php');
										break;

										case 'indicarpu':
											include('jsindicarpu.php');
										break;

										case 'entradas':
											include('jsentradas.php');
										break;

										case 'visualizar_entradas':
											include('jsvisualizar_entradas.php');
										break;

										case 'visualizar_salidas':
											include('jsvisualizar_salidas.php');
										break;

										case 'salidas':
											include('jssalidas.php');
										break;

										case 'costo_acumulado':
											include('jscacumulado.php');
										break;

										case 'costo_directo':
											include('jscdirecto.php');
										break;

										case 'costo_indirecto':
											include('jscindirecto.php');
										break;

										case 'alta_ps':
											include('jsps.php');
										break;

										case 'construccion':
											include('construccion.php');
										break;

										case 'tomaduria':
											include('jstomaduria.php');
										break;

										case 'prenomina':
											include('jsprenomina.php');
										break;

										case 'prenomina_auth':
											include('jsprenomina_aut.php');
										break;

										case 'est_destajistas':
											include('jsest_destajistas.php');
										break;

										case 'est_subcontratistas':
											include('jsest_subcontratistas.php');
										break;

										case 'est_indirectos':
											include('jsest_indirectos.php');
										break;

										case 'est_cc':
											include('jsest_chica.php');
										break;

										case 'est_cliente':
											include('jsest_cliente.php');
										break;

										case 'est_proveedores':
											include('jsest_prov.php');
										break;

										case 'nom_tom_oce':
											include('jstomaduria_tec.php');
										break;

										case 'nom_oce':
											include('jsprenomina_tec.php');
										break;

										case 'nom_ocen':
											include('jsprenominacen_tec.php');
										break;

										case 'prenom_oce':
											include('jsprenominaver_tec.php');
										break;

										case 'prenom_ocen':
											include('jsprenominavercen_tec.php');
										break;


										case 'unovsuno':
											//include('jsunovsuno.php');
											include('jsunovsuno3.php');
										break;

										case 'ingresos_egresos':
											//include('jsunovsuno.php');
											include('ingresos_egresos.php');
										break;

										case 'remesas':
											include('jsremesas3.php');
										break;

										case 'cheques':
											include('jsremesas2.php');
										break;

										case 'estado_resultados':
											include('jsestado.php');
										break;

										case 'control_indirectos':
											include('jscontrol_indirectos.php');
										break;

										case 'acumulado_detalle':
											include('jsacumulado_detalle.php');
										break;

										case 'cat_partidas':
											include('jscat_partidas.php');
										break;

										case 'cat_especialidades':
											include('jscat_especialidades.php');
										break;

										case 'retenciones':
											include('jsretenciones.php');
										break;

										case 'est_cliente_bit':
											include('jsest_cliente_bit_all.php');
										break;

										case 'est_destajistas_bit':
											include('jsest_destajistas_bit_all.php');
										break;

										case 'est_subcontratistas_bit':
											include('jsest_subcontratistas_bit_all.php');
										break;

										case 'est_proveedores_bit':
											include('jsest_prov_bit_all.php');
										break;

										case 'inventarios':
											include('jsinventarios.php');
										break;

										case 'programa_obra':
											include('programaobra.php');
										break;

										case 'config':
											include('config.php');
										break;

										case 'gantt':
											include('pogantt.php');
										break;

										case 'tablero':
											include('tablero.php');
										break;

										case 'aut_cuentaspp':
											include('aut_cuentaspp.php');
										break;

                                        case 'viz_cc':
											include('jsviz_chica.php');
										break;
										
										case 'viz_ind':
											include('jsviz_ind.php');
										break;

                                         case 'historialnom':
											include('historialnom.php');
										break;
										case 'historialest':
											include('historialest.php');
										break;
										case 'historialpas':
											include('historialpas.php');
										break;

										case 'historialpres':
											include('historialpres.php');
										break;

										case 'historialcompras':
											include('historialcompras.php');
										break;

										case 'recetas':
											include('recetas.php');
										break;

										case 'tsalida':
											include('jstsalida.php');
										break;

										case 'tentrada':
											include('jstentrada.php');
										break;

                                        case 'cobros':
											include('jscobrar.php');
										break;

										case 'cobrado':
											include('jscobrados.php');
										break;

										case 'imp_proveedores':
											include('impprov.php');
										break;

										case 'imp_subcontratistas':
											include('impsubc.php');
										break;
										
										default:
											# code...
										break;
										
									}
									?>
								</section>
							</div>
						</div>
					</div>
				</div>
			</div>
		</body>
		<?php
	}

?>