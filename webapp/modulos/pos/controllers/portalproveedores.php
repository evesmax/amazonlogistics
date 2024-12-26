<?php
//ini_set('display_errors', 1);
//Carga la funciones comunes top y footer
require('common.php');

//Carga el modelo para este controlador
require("models/proveedores.php");

class Portalproveedores extends Common
{
    public $ProveedoresModel;

    function __construct()
    {
        //Se crea el objeto que instancia al modelo que se va a utilizar

        $this->ProveedoresModel = new ProveedoresModel();
        $this->ProveedoresModel->connect();
    }

    function __destruct()
    {
        //Se destruye el objeto que instancia al modelo que se va a utilizar
        $this->ProveedoresModel->close();
    }

    function a_adjuntarxml(){
      $idoc=$_POST['idoc'];
      $resultReq =  $this->ProveedoresModel->listaRecepcionesAdju($idoc);
      $res=array();
      $res['rows']='';
      $res['total']=0;

      if($resultReq->num_rows>0){
        $sumatotal=0;
        while ($r = $resultReq->fetch_array()) {
          $res['rows'][]=$r;
          $sumatotal+=$r['total'];
        }
        $res['total']=$sumatotal;
      }

      $res['xmls']='';
      $res['totalxmls']=0;
      $resultReq =  $this->ProveedoresModel->listaXmlsCompra($idoc);
      if($resultReq->num_rows>0){
        $sumaxml=0;
        while ($r = $resultReq->fetch_array()) {
          $r['fecha_subida']=substr($r['fecha_subida'],0,10);
          $res['xmls'][]=$r;
          $sumaxml+=$r['imp_factura'];
        }
        $res['totalxmls']=$sumaxml;
      }else{
        $res['xmls']=0;
        $res['totalxmls']=0;
      }

      echo json_encode($res);
    }

    function a_guardaXmlAdju(){



    $fac_folio=$_POST['fac_folio'];
    $fac_fecha=$_POST['fac_fecha'];
    $fac_total=$_POST['fac_total'];
    $fac_uuid=$_POST['fac_uuid'];
    $fac_concepto=$_POST['concepto'];
    $xmlfile=$_POST['xmlfile'];
    $idoc=$_POST['idoc'];
    $fac_subtotal=$_POST['fac_subtotal'];
    $cadena = $_POST['cadena'];

      $resu = $this->ProveedoresModel->guardaXmlAdju($fac_folio,$fac_fecha,$fac_total,$fac_uuid,$fac_concepto,$xmlfile,$idoc,$fac_subtotal,$cadena);
      echo $resu;
    }

    function a_verificarPagos(){
        $idoc = $_POST['idoc'];
        $pagos = $this->ProveedoresModel->verificarPagos($idoc);
        echo $pagos;
    }
    function partidaOrden(){
      $idoc = $_POST['idOc'];
      $partidas = $this->ProveedoresModel->partidaOrden($idoc);
      echo json_encode($partidas);
    }

    function pagos_detalle()
  {
    $tabla = '';
    $datos = $this->ProveedoresModel->pagos_detalle($_POST['id'],$_POST['t'],$_POST['ori']);
    $saldo = $this->ProveedoresModel->info_car_fac($_POST['id'],$_POST['t'],$_POST['cp'],$_POST['ori']);
    $saldo = $saldo['cargo'];

    while($d = $datos->fetch_assoc())
    {
      $d['fecha_pago'] = explode(" ",$d['fecha_pago']);
      if(intval($d['origen']) == 3)
        $origen = "Bancos";
      else
        $origen = "Appministra";

      if(intval($d['activo']))
      {
        $saldo_final = floatval($saldo) - floatval($d['abono']);
        $check = "<input type='checkbox' class='chk_det' id='chk-".$d['id_rel']."'>";
        $abono = "$ ".number_format(round($d['abono'],2),2)." MXN";
        $abono_cant = $d['abono'];
        $ticket = "<a href='javascript:printer_s(".$d['id_pago'].",\"".$_POST['t']."\",".$_POST['cp'].",".$d['id_rel'].",".$_POST['ori'].")'><span class='glyphicon glyphicon-print' title='Ver Ticket'></span></a>";
        if($d['id_poliza_mov'] != '0' || $d['id_poliza_mov'] != '')
        {
          $poliza = explode(',',$d['id_poliza_mov']);
          $poliza = $this->ProveedoresModel->buscaPoliza($poliza[0]);
          if(intval($poliza))
            $link = "<a href='../cont/index.php?c=CaptPolizas&f=ModificarPoliza&id=$poliza' target='_blank'>$abono</a>";
        }
      }
      else
      {
        $saldo_final = floatval($saldo);
        $check = "<i style='color:red;'>Cancelado</i>";
        $abono = "<strike>$ ".number_format(round($d['abono'],2),2)." MXN</strike>";
        $abono_cant = 0;
        $ticket = "--";
        $link = "$abono";
      }


      // $tabla .= "<tr><td>".$d['fecha_pago'][0]."</td><td cantidad='".$abono_cant."'>$link</td><td>$origen</td><td>".$d['forma_pago']."</td><td>$ ".number_format(round($saldo,2),2)." MXN</td><td cantidad='".round($saldo_final,2)."'>$ ".number_format(round($saldo_final,2),2)." MXN</td><td>$ticket</td><td>$check</td></tr>";

      $tabla .= "<tr><td>".$d['fecha_pago'][0]."</td><td cantidad='".$abono_cant."'>$link</td><td>$origen</td><td>".$d['forma_pago']."</td><td>$ ".number_format(round($saldo,2),2)." MXN</td><td cantidad='".round($saldo_final,2)."'>$ ".number_format(round($saldo_final,2),2)." MXN</td></tr>";

      $saldo = $saldo_final;
    }
    echo $tabla;
  }

    function detalle()
  {
    $listaFormasPago = $this->ProveedoresModel->listaFormasPago();
    $listaMonedas = $this->ProveedoresModel->listaMonedas();
    $datos_cli_prov = $this->ProveedoresModel->info_car_fac($_GET['id'],$_GET['t'],$_GET['cp'],$_GET['ori']);

    require("views/portalprove/detalle.php");
  }

    function listaCargosFacturas()
  {
    $datos = array();
    $_POST['cobrar_pagar']=1;

    $listaCargos = $this->ProveedoresModel->listaCargos($_POST['idPrvCli'],$_POST['cobrar_pagar']);
    while($l = $listaCargos->fetch_assoc())
    {
      $vencimiento = new DateTime($l['fecha_pago']);
      if(intval($l['diascredito']))
        $vencimiento->add(new DateInterval('P'.$l['diascredito'].'D'));

      $abonado = (floatval($l['cargo']) * floatval($l['tipo_cambio'])) - floatval($l['saldo']);
      
      $datetime1 = new DateTime(date('Y-m-d'));
        $datetime2 = $vencimiento;
        $interval = $datetime1->diff($datetime2);
        $difer = $interval->format('%R%a');

        if(intval($difer) >= 61)//Al corriente
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-success'>Al Corriente</span></center>";

        if(intval($difer) <= 60 && intval($difer) >= 0)//por vencer
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-warning'>Por vencer</span></center>";

        if(intval($difer) < 0)//vencido
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-danger'>Cuenta Vencida</span></center>";   

        if(number_format($l['saldo'],2) <= 0)//saldada
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-default'>Cuenta Saldada</span></center>";              

      array_push($datos,array(
            'fech_cargo' => $l['fecha_pago'],
            'fecha_venc' => $vencimiento->format('Y-m-d'),
            'concepto' => "<a href='index.php?c=portalproveedores&f=detalle&id=".$l['id']."&t=c&cp=".$_POST['cobrar_pagar']."'>".$l['concepto']."</a>",
            'folio' => '---',
            'moneda' => $l['moneda'],
            'monto' => "$ ".number_format($l['cargo'],2),
            'abonado' => "$ ".number_format($abonado,2),
            'actual' => "<span class='actual' cantidad='".$l['saldo']."'>$ ".number_format($l['saldo'],2)."</span>",
            'estatus' => $estatus_m,
            'ov' => '-',
            'actual_im' => $l['saldo']
              ));
    }

    $listaFacturas = $this->ProveedoresModel->listaFacturas($_POST['idPrvCli'],$_POST['cobrar_pagar']);
    while($l = $listaFacturas->fetch_assoc())
    {
      //$foliosFac = $this->CuentasModel->foliosFac($l['id_oc']);
      //$file   = "../cont/xmls/facturas/temporales/".$l['xmlfile'];
      //$texto  = file_get_contents($file);
      //$texto  = preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $texto);
      //$texto  = preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $texto);
      //$xml  = new DOMDocument();
      //$xml->loadXML($texto);
      //$xp = new DOMXpath($xml);
      //$desc = $this->getpath("//@descripcion");
      $vencimiento = new DateTime($l['fecha_fac']);
      if(intval($l['diascredito']))
        $vencimiento->add(new DateInterval('P'.$l['diascredito'].'D'));
      $desc = $l['desc_concepto'];
      $datetime1 = new DateTime(date('Y-m-d'));
        $datetime2 = $vencimiento;
        $interval = $datetime1->diff($datetime2);
        $difer = $interval->format('%R%a');

        if(intval($difer) >= 61)//Al corriente
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-success'>Al Corriente</span></center>";

        if(intval($difer) <= 60 && intval($difer) >= 0)//por vencer
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-warning'>Por vencer</span></center>";

        if(intval($difer) < 0)//vencido
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-danger'>Cuenta Vencida</span></center>";


      $estilo = '';
      if(strtotime($vencimiento->format('Y-m-d')) < strtotime(date()))
        $estilo = "style='color:red;'";


        $nuevoImp = floatval($l['importe_pesos']);


      $saldo = $nuevoImp - floatval($l['pagos']);
      if(number_format($saldo,2) <= 0)//saldada
          $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-default'>Cuenta Saldada</span></center>";    
                              
      

        $abonado = floatval($nuevoImp) - floatval($saldo);
        
        if(intval($_POST['cobrar_pagar']))
        {
          if(intval($l['origen']) == 4)
          {
            $url = "";
            $idovc = "-";
          }
          else
          {
            $url = "index.php?c=compras&f=ordenes&id_oc=".$l['id_oc']."&v=1";
            //$idovc = "<a href='$url' target='_blank'>".$l['id_oc']."</a>";
            $idovc = $l['id_oc'];
          }
        }
        else
        {
          if(intval($l['origen']) == 1)
          {
            $url = "index.php?c=ventas&f=ordenes&id_oventa=".$l['id_oventa']."&v=1";
            //$idovc = "<a href='$url' target='_blank'>".$l['id_oventa']."</a>";
            $idovc = $l['id_oventa'];
          }
          if(intval($l['origen']) == 2)
          {
            $url = "../pos/ticket.php?idventa=".$l['id_oventa']."&print=0";
            //$idovc = "<a href='$url' target='_blank'>".$l['id_oventa']."</a>";
            $idovc = $l['id_oventa'];
          }
          if(intval($l['origen']) == 4)
          {
            $url = "";
            $idovc = "-";
          }
          
        }
          
        if(!isset($l['folio']))
        {
          $fac = $l['xmlfile'];
          $fac = explode('_',$fac);
          if($fac[0] != '')
            $l['folio'] = $fac[0];
          else
            $l['folio'] = $fac[2];
        }

        $fol = "(".$l['folio'].") ";
        if(intval($l['origen']) == 4)
          $fol = '';

        array_push($datos,array(
              'fech_cargo' => $l['fecha_fac'],
              'fecha_venc' => $vencimiento->format('Y-m-d'),
              'concepto' => "<a href='index.php?c=portalproveedores&f=detalle&id=".$l['id']."&t=f&cp=".$_POST['cobrar_pagar']."&ori=".$l['origen']."'>$fol $desc</a>",
              'folio' => $l['folio_fac'],
              'moneda' => $l['Moneda'],
              'monto' => "$ ".number_format($l['imp_factura'],2),
              'abonado' => "$ ".number_format($abonado,2),
              'actual' => "<span class='actual' cantidad='$saldo'>$ ".number_format(round($saldo,2),2)."</span>",
              'estatus' => $estatus_m,
              'ov' => $idovc,
              'actual_im' => $saldo
                ));
      
    }
    echo json_encode($datos);
  }


    function a_adjuntarxml2(){
      $idoc=$_POST['idoc'];
      $resultReq =  $this->ProveedoresModel->listaRecepcionesAdju($idoc);
      $res=array();
      $res['rows']='';
      $res['total']=0;

      if($resultReq->num_rows>0){
        $sumatotal=0;
        while ($r = $resultReq->fetch_array()) {
          $res['rows'][]=$r;
          $sumatotal+=$r['total'];
        }
        $res['total']=$sumatotal;
      }

      $res['xmls']='';
      $res['totalxmls']=0;
      $resultReq =  $this->ProveedoresModel->listaXmlsCompra($idoc);
      if($resultReq->num_rows>0){
        $sumaxml=0;
        while ($r = $resultReq->fetch_array()) {
          $r['fecha_subida']=substr($r['fecha_subida'],0,10);
          $res['xmls'][]=$r;
          $sumaxml+=$r['imp_factura'];
        }
        $res['totalxmls']=$sumaxml;
      }else{
        $res['xmls']=0;
        $res['totalxmls']=0;
      }

      echo json_encode($res);
    }


    function quitar_tildes($cadena) {
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹","/");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","");
    $texto = str_replace($no_permitidas, $permitidas ,$cadena);
    return $texto;
  }

    function existeXML($nombreArchivo){
    $ruta = "../cont/xmls/facturas/";
    $directorio = opendir($ruta);
    $rutas="";
    while($carpeta = readdir($directorio)){
      if($carpeta != '.' && $carpeta != '..' && $carpeta != '.file' && $carpeta !='.DS_Store'){
          if (is_dir($ruta.$carpeta)){
              $dir = opendir($ruta.$carpeta);
              while($archivo = readdir($dir))
          {
            if($archivo != '.' && $archivo != '..' && $archivo != '.file' && $archivo !='.DS_Store' && $archivo != '.file.rtf'){
              $archivo = str_replace("-Cobro", "", $archivo);
              $archivo = str_replace("-Pago", "", $archivo);
              $archivo = str_replace("Parcial-", "", $archivo);
              $archivo = str_replace("-Nomina", "", $archivo);
              $archiv = $this->quitar_tildes($archivo."");
              $nombreArchiv= $this->quitar_tildes($nombreArchivo);
              
              if (preg_match("/".$nombreArchiv."/i", $archiv)){//i para no diferenciar mayus y minus
              //if($nombreArchivo == $archivo){
                if($carpeta!="repetidos"){
                  if($carpeta!="temporales"){
                    $poliza =  $this->CaptPolizasModel->GetAllPolizaInfoActiva($carpeta);
                    if($poliza!=0){
                      switch($poliza['idtipopoliza']){
                        case 1: $p="Ingresos"; break;
                        case 2: $p="Egresos"; break;
                        case 3: $p="Diario"; break;
                      }
                      $rutas.= " (Poliza:".$poliza['numpol']." ".$p." ".$poliza['fecha'].")";
                    }
                  }else{
                    $rutas.= " (Almacen)";
                  }
                }
              }
              
            }
          }
              
            }
      }
    
        }
    return $rutas;
  } 

    function valida_xsd($version,$xml) 
  {

    libxml_use_internal_errors(true);   
    switch ($version) 
    {
        case "2.0":
          $ok = $xml->schemaValidate("../cont/xmls/valida_xmls/xsds/cfdv2complemento.xsd");
          break;
        case "2.2":
          $ok = $xml->schemaValidate("../cont/xmls/valida_xmls/xsds/cfdv22complemento.xsd");
          break;
        case "3.0":
          $ok = $xml->schemaValidate("../cont/xmls/valida_xmls/xsds/cfdv3complemento.xsd");
          break;
        case "3.2":
          $ok = $xml->schemaValidate("../cont/xmls/valida_xmls/xsds/cfdv32.xsd");
          break;
        default:
          $ok = 0;
    }
    return $ok;
  }

  function getpath($qry) 
  {
    global $xp;
    $prm = array();
    $nodelist = $xp->query($qry);
    foreach ($nodelist as $tmpnode)  
    {
        $prm[] = trim($tmpnode->nodeValue);
      }
    $ret = (sizeof($prm)<=1) ? $prm[0] : $prm;
    return($ret);
  }

  function valida_en_sat($rfc,$rfc_receptor,$total,$uuid) 
  {
      error_reporting(E_ALL);
      require_once('../cont/xmls/valida_xmls/nusoap/nusoap.php');
      error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING|E_DEPRECATED));
      $url = "https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc?wsdl";

      $soapclient = new nusoap_client($url,$esWSDL=true);
      $soapclient->soap_defencoding = 'UTF-8'; 
      $soapclient->decode_utf8 = false;

      $rfc_emisor = utf8_encode($rfc);
      $rfc_receptor = utf8_encode($rfc_receptor);
      $impo = (double)$total;
      $impo=sprintf("%.6f", $impo);
      $impo = str_pad($impo,17,"0",STR_PAD_LEFT);

      $uuid = strtoupper($uuid);

      $factura = "?re=$rfc_emisor&rr=$rfc_receptor&tt=$impo&id=$uuid";

      $prm = array('expresionImpresa'=>$factura);

      $buscar=$soapclient->call('Consulta',$prm);

      //echo "Status del C&oacute;digo: ".$buscar['ConsultaResult']['CodigoEstatus']."<br>";
      //echo "Status: ".$buscar['ConsultaResult']['Estado']."<br>";
      if($buscar['ConsultaResult']['Estado'] == "Cancelado")
      {
        return 0;
      }
      else
      {
        return 1;
      }

  }

    function subeFactura()
    {
        $nn=0;
        global $xp;
        $facturasNoValidas = $facturasValidas = '';
        $numeroInvalidos = $numeroValidos = $no_hay_problema = 0;
        $nombre = "";
        $maximo = count($_FILES['factura']['name']);
        $maximo = (intval($maximo)-1);
        $ruta = "../cont/xmls/facturas/temporales/";
      
        $extension = end(explode('.', $_FILES['factura']['name'][0]));
        if($extension == "zip")
        {
          $zipoxml = "tempo.zip";
        }
        if($extension == "xml")
        {
          $zipoxml = "tempo.xml";
        }

        if(move_uploaded_file($_FILES["factura"]["tmp_name"][0], $ruta.$zipoxml))
        {
          $zip = new ZipArchive;
          if($extension == "xml")
          {
            mkdir($ruta."tempo/", 0777);
            copy($ruta.$zipoxml,$ruta."tempo/".$zipoxml);
            unlink($ruta.$zipoxml);
            $zip->open($ruta.'tempo.zip', ZipArchive::CREATE);
            $zip->addFile($ruta."tempo/".$zipoxml,"tempo/".$zipoxml);
            $zip->close();    
            unlink($ruta."tempo/".$zipoxml);
            rmdir($ruta."tempo/");
          }
          mkdir($ruta."ziptempo/", 0777);
          

          if ($zip->open($ruta."tempo.zip") === TRUE)
          {
              $zip->extractTo($ruta."ziptempo/");
              $zip->close();
              //unlink($ruta."tempo.zip");
              
              if($extension == "xml")
            {
              $foldername = "tempo";
            }

            if($extension == "zip")
            {
              $foldername = $_FILES['factura']['name'][0];
                $foldername = str_replace('.zip', '', $foldername);
            }

              if($directorio = opendir($ruta."ziptempo/$foldername/"))
            {
              while ($archivo = readdir($directorio)) 
              {
                if(is_dir($ruta."ziptempo/$foldername/$archivo"))
                {
                  rmdir($ruta."ziptempo/$foldername/$archivo/");
                }
                elseif($archivo != '.' AND $archivo != '..' AND $archivo != '.DS_Store' AND $archivo != '.file')
                {
                  //Comienza obtener UUID---------------------------
                  $file   = $ruta."ziptempo/$foldername/".$archivo;
                  $texto  = file_get_contents($file);
                  $texto  = preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $texto);
                  $texto  = preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $texto);
                  $xml  = new DOMDocument();
                  $xml->loadXML($texto);
                  

                  $xp = new DOMXpath($xml);

                  $data['uuid']   = $this->getpath("//@UUID");
                  $data['folio']  = $this->getpath("//@folio");
                  $data['emisor'] = $this->getpath("//@nombre");
                  $data['version'] = $this->getpath("//@version");
                  $data['fecha'] = $this->getpath("//@FechaTimbrado");
                  $data['descripcion'] = $this->getpath("//@descripcion");

                  //$comprobante = $xp->query("/cfdi:Conceptos");
                  /*
                  $elements = $xp->query("//cfdi:Conceptos");

                  if (!is_null($elements)) {
                    foreach ($elements as $element) {
                      //var_dump($element->childNodes);
                      foreach ($element->childNodes as $ele2) {
                        var_dump($ele2);
                      }
                      //echo "<br/>[". $element->nodeName. "]";
                      foreach ($nodes as $node) {
                        var_dump($node->parentNode);
                        echo '1'.$node->parentNode. "\n";
                      }
                      exit();
                    }
                  }
*/
                      $version = $data['version'];

                  
                  $data['total'] = $this->getpath("//@total");
                  $data['subTotal'] = $this->getpath("//@subtotal");
                  $data['rfc'] = $this->getpath("//@rfc");
                  
                  $tipo = explode('.',$archivo);
                  //Termina obtener UUID---------------------------
          
                  $rfcOrganizacion= $this->ProveedoresModel->rfcOrganizacion();
                 
                  if($data['rfc'][0] == $rfcOrganizacion['RFC'])
                  {
                    $nombre = $data['emisor'][1];
                  }
                  elseif($data['rfc'][1] == $rfcOrganizacion['RFC'])
                  {
                    $nombre = $data['emisor'][0];
                    
                  }
                  //echo $version[0];
                  if($this->valida_xsd($version[0],$xml) && strtolower($tipo[1]) == "xml")
                  {

                    if($rfcOrganizacion['RFC'] != $data['rfc'][0] &&  $rfcOrganizacion['RFC']!= $data['rfc'][1])
                    {


                      $noOrganizacion = 0;
                      $numeroInvalidos++;
                      $facturasNoValidas .= $archivo."(RFC no de Organizacion),\n";
                    }
                    else
                    { 
                      
                      $noOrganizacion = 1; 
                    }

                    
                    $nombreArchivo = $data['folio']."_".$nombre."_".$data['uuid'].".xml";
                    if($noOrganizacion){
                      
                      $almacen="facturas/repetidos/";
                      $validaexiste = $this->existeXML($nombreArchivo);
                      $repetidos=0;
                      if($validaexiste){
                        $numeroInvalidos++;
                        $noOrganizacion=0;
                        $facturasNoValidas .= $archivo." Ya existe en $validaexiste.\n";
                         $repetidos=1;
                         mkdir ($almacen,0777);
                        rename($file, $almacen.$this->quitar_tildes($nombreArchivo));

                      }else{ $noOrganizacion = 1; }
                    }

                    if($noOrganizacion)
                    {
                      copy($ruta."ziptempo/$foldername/".$archivo,$ruta."/".$this->quitar_tildes($nombreArchivo));
                      $numeroValidos++;
                      $facturasValidas .= $archivo.",\n";
                    }
                    unlink($ruta."ziptempo/$foldername/".$archivo);
                    
                  }
                  else
                  {
                    unlink($ruta."ziptempo/$foldername/".$archivo);
                    $numeroInvalidos++;
                    $facturasNoValidas .= $archivo.",\n";
                  }
                }
              }
              $folder_invalido = 0;
              $files = glob($ruta."ziptempo/$foldername/*/*"); 
                  foreach($files as $file)
                  { 
                    if(is_file($file))
                      unlink($file); 
                    elseif(is_dir($file))
                      rmdir($file);
                    $folder_invalido++;
                  }
              $files = glob($ruta."ziptempo/$foldername/*"); 
                  foreach($files as $file)
                  { 
                    if(is_file($file))
                      unlink($file); 
                    elseif(is_dir($file))
                      rmdir($file);
                  }   
              //rmdir($ruta."ziptempo/$foldername/$foldername/");   
              rmdir($ruta."ziptempo/$foldername/.DS_Store");
              rmdir($ruta."ziptempo/$foldername/");
              rmdir($ruta."ziptempo/");
              unlink($ruta."tempo.zip");
              if(!intval($folder_invalido))
                $funciono = 1;
              else
                $funciono = 0;
            }
            else
            {
              unlink($ruta."tempo.zip");
              $files = glob($ruta.'ziptempo/*/*'); 
              foreach($files as $file)
              { 
                if(is_file($file))
                  unlink($file); 
              }
              $files = glob($ruta.'ziptempo/*'); 
              foreach($files as $file)
              { 
                if(is_file($file))
                  unlink($file); 
              }
              if($directorio = opendir($ruta."ziptempo/"))
              {
                while ($archivo = readdir($directorio)) 
                {
                  if(is_dir($ruta."ziptempo/$archivo"))
                  {
                    rmdir($ruta."ziptempo/$archivo/");
                  }
                }
              }
              
              rmdir($ruta."ziptempo/");
              $funciono = 0;
            }
          }
        }

       /* if( ($funciono*1)>0  && ($numeroValidos*1)>0 ){
          session_start();

        }*/
        
        $data['descripcion']=addslashes($data['descripcion']);
        $datosfactura=$data['folio'].'##'.$data['fecha'].'##'.$data['total'].'##'.$data['uuid'].'##'.$data['descripcion'].'##'.$data['subTotal'];

        echo $funciono."-/-*".$numeroValidos."-/-*".$facturasValidas."-/-*".$numeroInvalidos."-/-*".$facturasNoValidas."-/-*".$repetidos."-/-*".$nombreArchivo."-/-*".$datosfactura;
    }


    function a_listaOrdenesRecepcion(){
      $idProveedor=$_GET['id'];
      $resultReq =  $this->ProveedoresModel->listaOrdenesCompra($idProveedor);
      $listas=array();
      $listas['data']='';
      if($resultReq->num_rows>0){
        while ($r = $resultReq->fetch_array()) {
          $link='<a  class="btn btn-default btn-xs btn-block">'.$r['idoc'].'</span></a>';
          $elimin='';
          /*if($r['urgente']==0){
            $r[7]='<span class="label label-default" style="cursor:pointer;">Normal</span>';
          }
          if($r['urgente']==1){
            $r[7]='<span class="label label-danger" style="cursor:pointer;">Urgente</span>';
          } */
         /* if($r['activo']==0){
            $elimin='<a onclick="editReq('.$r['idoc'].');"  class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-edit"></span> Editar</a>
            <a onclick="eliminaReq('.$r['idoc'].')" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-remove"></span> Borrar</a>';
            $r[5]='<span class="label label-warning" style="cursor:pointer;">Pendiente autorizar</span>';
          } */
          /*if($r['activo']==1){

            $r[5]='<span class="label label-success" style="cursor:pointer;">Autorizada</span>';
          } */
           if($r['estatusPartida'] < 15){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-warning" style="cursor:pointer;">No Recibida</span>';
          }
          if($r['estatusPartida'] > 20 &&  $r['estatusPartida'] < 76 ){
              $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
              $r[5]='<span class="label label-success" style="cursor:pointer;">Recibida</span>';
              $elimin='<a style="margin-top:4px" onclick="adjuntarxml('.$r['idoc'].');"  class="btn btn-default btn-xs"><span class="glyphicon glyphicon-upload"></span> Adjuntar factura\'s</a>';
          }

          if($r['estatusPartida'] > 76 && $r['estatusPartida'] < 99){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-success" style="cursor:pointer;">Programada para Pago</span>';
            $elimin='<a style="margin-top:4px" onclick="adjuntarxml('.$r['idoc'].');"  class="btn btn-default btn-xs"><span class="glyphicon glyphicon-upload"></span> Adjuntar factura\'s</a>';
          } 

          if($r['estatusPartida']==100){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-default" style="cursor:pointer;">Pagada</span>';
            $elimin='';
          }

         
          if($r['estatusPartida']==99){

            $link='<a href="#" class="btn btn-danger btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-danger" style="cursor:pointer;">Cancelada</span>';
          }

          if($r['estatusPartida'] > 49 && $r['estatusPartida'] < 76){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-success" style="cursor:pointer;">Recibida</span>';
            $elimin='<a style="margin-top:4px" onclick="adjuntarxml('.$r['idoc'].');"  class="btn btn-default btn-xs"><span class="glyphicon glyphicon-upload"></span> Adjuntar factura\'s</a>';
          }

          if($r['estatusPartida'] > 14 && $r['estatusPartida'] < 21){

            $link='<a href="#" class="btn btn-default btn-xs disabled btn-block">'.$r['idoc'].'</a>';
            $r[5]='<span class="label label-warning" style="cursor:pointer;">No Recibida</span>';
            $elimin='<a style="margin-top:4px" onclick="adjuntarxml('.$r['idoc'].');"  class="btn btn-default btn-xs"><span class="glyphicon glyphicon-upload"></span> Adjuntar factura\'s</a>';
          } 
          





          $r[6]=$elimin;
          $listas['data'][]=$r;
        }
      }else{
        $listas['data']=array();
      }

      echo json_encode($listas);
    }

    

    function listaCargosFacturasx()
    {
        $datos = array();
        $_POST['cobrar_pagar']=0;
        $listaCargos = $this->ClienteModel->listaCargos($_POST['idPrvCli'],$_POST['cobrar_pagar']);
        while($l = $listaCargos->fetch_assoc())
        {
            $vencimiento = new DateTime($l['fecha_pago']);
            if(intval($l['diascredito']))
                $vencimiento->add(new DateInterval('P'.$l['diascredito'].'D'));

            $abonado = (floatval($l['cargo']) * floatval($l['tipo_cambio'])) - floatval($l['saldo']);
            
            $datetime1 = new DateTime(date('Y-m-d'));
                $datetime2 = $vencimiento;
                $interval = $datetime1->diff($datetime2);
                $difer = $interval->format('%R%a');

                if(intval($difer) >= 61)//Al corriente
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-success'>Al Corriente</span></center>";

                if(intval($difer) <= 60 && intval($difer) >= 0)//por vencer
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-warning'>Por vencer</span></center>";

                if(intval($difer) < 0)//vencido
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-danger'>Cuenta Vencida</span></center>";     

                if(number_format($l['saldo'],2) <= 0)//saldada
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-default'>Cuenta Saldada</span></center>";                            

            array_push($datos,array(
                        'fech_cargo' => $l['fecha_pago'],
                        'fecha_venc' => $vencimiento->format('Y-m-d'),
                        'concepto' => $l['concepto'],
                        'monto' => "$ ".number_format($l['cargo'],2)." ".$l['moneda'],
                        'abonado' => "$ ".number_format($abonado,2)." MXN",
                        'actual' => "<span class='actual' cantidad='".$l['saldo']."'>$ ".number_format($l['saldo'],2)." MXN</span>",
                        'estatus' => $estatus_m,
                        'ov' => '-'
                            ));
        }

        $listaFacturas = $this->ClienteModel->listaFacturas($_POST['idPrvCli'],$_POST['cobrar_pagar']);
        while($l = $listaFacturas->fetch_assoc())
        {
            //$foliosFac = $this->CuentasModel->foliosFac($l['id_oc']);
            //$file     = "../cont/xmls/facturas/temporales/".$l['xmlfile'];
            //$texto    = file_get_contents($file);
            //$texto    = preg_replace('{<Addenda.*/Addenda>}is', '<Addenda/>', $texto);
            //$texto    = preg_replace('{<cfdi:Addenda.*/cfdi:Addenda>}is', '<cfdi:Addenda/>', $texto);
            //$xml  = new DOMDocument();
            //$xml->loadXML($texto);
            //$xp = new DOMXpath($xml);
            //$desc = $this->getpath("//@descripcion");
            $vencimiento = new DateTime($l['fecha_factura']);
            if(intval($l['diascredito']))
                $vencimiento->add(new DateInterval('P'.$l['diascredito'].'D'));
            $desc = $l['desc_concepto'];
            $datetime1 = new DateTime(date('Y-m-d'));
                $datetime2 = $vencimiento;
                $interval = $datetime1->diff($datetime2);
                $difer = $interval->format('%R%a');

                if(intval($difer) >= 61)//Al corriente
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-success'>Al Corriente</span></center>";

                if(intval($difer) <= 60 && intval($difer) >= 0)//por vencer
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-warning'>Por vencer</span></center>";

                if(intval($difer) < 0)//vencido
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-danger'>Cuenta Vencida</span></center>";


            $estilo = '';
            if(strtotime($vencimiento->format('Y-m-d')) < strtotime(date()))
                $estilo = "style='color:red;'";


                $nuevoImp = floatval($l['importe_pesos']);


            $saldo = $nuevoImp - floatval($l['pagos']);
            if(number_format($saldo,2) <= 0)//saldada
                    $estatus_m = "<center><span style='display:block !important;width:100% !important;' class='label label-default'>Cuenta Saldada</span></center>";        
                                                            
            

                $abonado = floatval($nuevoImp) - floatval($saldo);
                
                if(intval($_POST['cobrar_pagar']))
                {
                    $url = "index.php?c=compras&f=ordenes&id_oc=".$l['id_oc']."&v=1";
                    $idovc = $l['id_oc'];
                }
                else
                {
                    if(intval($l['origen']) == 1)
                        $url = "index.php?c=ventas&f=ordenes&id_oventa=".$l['id_oventa']."&v=1";
                    if(intval($l['origen']) == 2)
                        $url = "../pos/ticket.php?idventa=".$l['id_oventa']."&print=0";
                    $idovc = $l['id_oventa'];
                }
                    
                        
                array_push($datos,array(
                            'fech_cargo' => $l['fecha_factura'],
                            'fecha_venc' => $vencimiento->format('Y-m-d'),
                            'concepto' => $l['folio']." $desc",
                            'monto' => "$ ".number_format($l['imp_factura'],2)." ".$l['Moneda'],
                            'abonado' => "$ ".number_format($abonado,2)." MXN",
                            'actual' => "<span class='actual' cantidad='$saldo'>$ ".number_format(round($saldo,2),2)." MXN</span>",
                            'estatus' => $estatus_m,
                            'ov' => "<a href='$url' target='_blank'>$idovc</a>"
                                ));
            
        }
        echo json_encode($datos);
    }

    function guardaCliente(){
         $idCliente = $_POST['idCliente'];
         //$codigo = $_POST['codigo'];
         $nombre = $_POST['nombre'];
         $tienda = $_POST['tienda'];
         $numint = $_POST['numint']; 
         $numext = $_POST['numext'];
         $direccion = $_POST['direccion'];
         $colonia = $_POST['colonia']; 
         $cp = $_POST['cp'];
         $pais = $_POST['pais'];
         $estado = $_POST['estado'];  
         $municipio = $_POST['municipio'];
         $email = $_POST['email'];
         $celular = $_POST['celular'];
         $tel1 =  $_POST['tel1'];
         $tel2 = $_POST['tel2'];
         $ciudad = $_POST['ciudad'];

         // $cumpleanos = $_POST['cumpleanos'];
         // $rfc = $_POST['rfc'];
         // $curp = $_POST['curp'];
         // $diasCredito = $_POST['diasCredito'] ;
         // $limiteCredito = $_POST['limiteCredito'];
         // $moneda = $_POST['moneda'];
         // $listaPrecio = $_POST['listaPrecio'];
         // $regimenFact = $_POST['regimenFact'];


         // $idComunFact = $_POST['idComunFact'];
         // $razonSocial = $_POST['razonSocial'];
         // $emailFacturacion = $_POST['emailFacturacion'];
         // $direccionFact = $_POST['direccionFact'];
         // $numextFact = $_POST['numextFact'];
         // $numintFact = $_POST['numintFact'];
         // $coloniaFact = $_POST['coloniaFact'];
         // $cpFact = $_POST['cpFact'];
         // $paisFact = $_POST['paisFact'];
         // $estadoFact = $_POST['estadoFact'];
         // $municipiosFact = $_POST['municipiosFact'];
         // $ciudadFact = $_POST['ciudadFact'];
         // $tipoDeCredito = $_POST['tipoDeCredito'];
         // $descuentoPP = $_POST['descuentoPP'];
         // $interesesMoratorios = $_POST['interesesMoratorios'];
         // $perVenCre = $_POST['perVenCre'];
         // $perExLim = $_POST['perExLim'];
         // $comisionVenta = $_POST['comisionVenta'];
         // $comisionCobranza = $_POST['comisionCobranza'];
         // $empleado = $_POST['empleado'];
         // $enviosDom = $_POST['enviosDom'];
         // $tipoClas = $_POST['tipoClas'];

         // $banco = $_POST['banco'];
         // $numCuenta = $_POST['numCuenta'];
         // $cuentaCont = $_POST['cuentaCont'];

         // $bandera = $_POST['flag'];


          
            $cliente = $this->ClienteModel->updateClientePortal($idCliente,$nombre,$tienda,$numint,$numext,$direccion,$colonia,$cp,$estado,$municipio,$email,$celular,$tel1,$tel2,$ciudad,$pais); 
         

        

        echo json_encode($cliente);
    }

    function guardaProveedor(){

         $idProveedor = $_POST['idProveedor'];
         $calle = $_POST['calle'];
         $no_ext = $_POST['no_ext'];
         $no_int = $_POST['no_int'];
         $cp = $_POST['cp'];
         $colonia = $_POST['colonia'];
         $selectPais = $_POST['selectPais'];
         $selectEstado = $_POST['selectEstado'];
         $selectMunicipio = $_POST['selectMunicipio'];
         $ciudad = $_POST['ciudad'];
         $nombre_contacto = $_POST['nombre_contacto'];
         $email =  $_POST['email'];
         $telefono = $_POST['telefono'];
         $web = $_POST['web'];

         // $cumpleanos = $_POST['cumpleanos'];
         // $rfc = $_POST['rfc'];
         // $curp = $_POST['curp'];
         // $diasCredito = $_POST['diasCredito'] ;
         // $limiteCredito = $_POST['limiteCredito'];
         // $moneda = $_POST['moneda'];
         // $listaPrecio = $_POST['listaPrecio'];
         // $regimenFact = $_POST['regimenFact'];


         // $idComunFact = $_POST['idComunFact'];
         // $razonSocial = $_POST['razonSocial'];
         // $emailFacturacion = $_POST['emailFacturacion'];
         // $direccionFact = $_POST['direccionFact'];
         // $numextFact = $_POST['numextFact'];
         // $numintFact = $_POST['numintFact'];
         // $coloniaFact = $_POST['coloniaFact'];
         // $cpFact = $_POST['cpFact'];
         // $paisFact = $_POST['paisFact'];
         // $estadoFact = $_POST['estadoFact'];
         // $municipiosFact = $_POST['municipiosFact'];
         // $ciudadFact = $_POST['ciudadFact'];
         // $tipoDeCredito = $_POST['tipoDeCredito'];
         // $descuentoPP = $_POST['descuentoPP'];
         // $interesesMoratorios = $_POST['interesesMoratorios'];
         // $perVenCre = $_POST['perVenCre'];
         // $perExLim = $_POST['perExLim'];
         // $comisionVenta = $_POST['comisionVenta'];
         // $comisionCobranza = $_POST['comisionCobranza'];
         // $empleado = $_POST['empleado'];
         // $enviosDom = $_POST['enviosDom'];
         // $tipoClas = $_POST['tipoClas'];

         // $banco = $_POST['banco'];
         // $numCuenta = $_POST['numCuenta'];
         // $cuentaCont = $_POST['cuentaCont'];

         // $bandera = $_POST['flag'];


          
            $proveedor = $this->ProveedoresModel->updateProveedorPortal($idProveedor,$calle,$no_ext,$no_int,$cp,$colonia,$selectPais,$selectEstado,$selectMunicipio,$ciudad,$nombre_contacto,$email,$telefono,$web); 
         

        

        echo json_encode($proveedor);
    }

    function prov_prod_reportem(){

    $carac_padre = json_decode($this->ProveedoresModel->carac_padre());
    $carac_hija = json_decode($this->ProveedoresModel->carac_hija());

    if(intval($_POST['tipo_doc']) != 3)
      $resultado = $this->ProveedoresModel->prov_prod_reporte($_POST);
    else
      $resultado = $this->ProveedoresModel->prov_prod_reporte_req($_POST);

    $tabla = '';
    $ImporteProd = $ImporteProv = 0;
    $ImpuestosProd = $ImpuestosProv = 0;
    $TotalProd = $TotalProv = 0;
    $CantidadProd = $CantidadProv = 0;
    $UnidadProd = $UnidadProv = '';
    $UnitarioProd = $UnitarioProv = 0;
    $cont = 0;
    

    while($r = $resultado->fetch_assoc())
    {
      $caracteristicas = '';
      if($r['caracteristica'] != 0)
      {
        $carac = explode(',',$r['caracteristica']);
        for($j = 0; $j <= count($carac)-1; $j++)
        {
          $subcarac = explode('=>',$carac[$j]);
          $caracteristicas .= $carac_padre->{$subcarac[0]}.": ".$carac_hija->{$subcarac[1]}." / ";
        }
      }
      $impuestosTotal = 0;
      if($r['impuestos'])
      {
        $impuestos = explode(',',$r['impuestos']);
        for($i = 0; $i <= count($impuestos)-1; $i++)
        {
          $cant_impuesto = explode('-',$impuestos[$i]);
          $impuestosTotal += floatval($cant_impuesto[2]);
        }
      }
      $muestra = 1;
      if($r['id_proveedor'] != $provAnterior)
      {
        $muestra = 0;
        if($cont)
        {
          $UnitarioProv += $ImporteProd/$CantidadProd;
          $tabla .= "<tr style='font-weight:bold;background-color:white;'><td>Total Producto:</td><td></td><td></td><td>".$CantidadProd."</td><td>".$UnidadProd."</td><td>".number_format(($ImporteProd/$CantidadProd),2)."</td><td>".number_format($ImporteProd,2)."</td><td>".number_format($ImpuestosProd,2)."</td><td>".number_format($TotalProd,2)."</td></tr>";
        }

        $ImporteProd = 0;
        $ImpuestosProd = 0;
        $TotalProd = 0;
        $CantidadProd = 0;
        $UnidadProd = '';
        $UnitarioProd = 0;
        

        //$tabla .= "<tr class='linea_fac'><td>Producto: </td><td colspan='8'>".$r['Producto'].$carac_hija->{1}."</td></tr>";
        if($cont)
          $tabla .= "<tr style='font-weight:bold;background-color:white;'><td>Total Proveedor:</td><td></td><td></td><td>".$CantidadProv."</td><td>".$UnidadProv."</td><td>".number_format($UnitarioProv,2)."</td><td>".number_format($ImporteProv,2)."</td><td>".number_format($ImpuestosProv,2)."</td><td>".number_format($TotalProv,2)."</td></tr>";

        $ImporteProv = 0;
        $ImpuestosProv = 0;
        $TotalProv = 0;
        $CantidadProv = 0;
        $UnidadProv = '';
        $UnitarioProv = 0;

        $tabla .= "<tr class='linea_prov'><td width=250>Proveedor: </td><td colspan='8'>".$r['Proveedor']."</td></tr>";
      }

      if($r['id_producto'] != $prodAnterior)
      {
        if($cont && $muestra)
        {
          $UnitarioProv += $ImporteProd/$CantidadProd;
          $tabla .= "<tr style='font-weight:bold;background-color:white;'><td>Total Producto:</td><td></td><td></td><td>".$CantidadProd."</td><td>".$UnidadProd."</td><td>".number_format(($ImporteProd/$CantidadProd),2)."</td><td>".number_format($ImporteProd,2)."</td><td>".number_format($ImpuestosProd,2)."</td><td>".number_format($TotalProd,2)."</td></tr>";
        }

        $ImporteProd = 0;
        $ImpuestosProd = 0;
        $TotalProd = 0;
        $CantidadProd = 0;
        $UnidadProd = '';
        $UnitarioProd = 0;

        $tabla .= "<tr class='linea_fac'><td>Producto: </td><td colspan='8'>".$r['Producto']."</td></tr>";
      }

      $UnidadBase = explode('*|*',$r['UnidadBase']);

      if(!intval($_POST['imp2']))
        $tabla .= "<tr class='detalle'><td>$caracteristicas</td><td>".$r['fecha']."</td><td>".$r['id_compra']."</td><td>".(floatval($r['cantidad'])/floatval($UnidadBase[1]))."</td><td>".$UnidadBase[0]."</td><td>".$r['costo']."</td><td>".number_format(floatval($r['Importe'])/floatval($UnidadBase[1]),2)."</td><td>".number_format($impuestosTotal,2)."</td><td>".number_format(((floatval($r['Importe']) + $impuestosTotal)/floatval($UnidadBase[1])),2)."</td></tr>";

      $provAnterior = $r['id_proveedor'];
      $prodAnterior = $r['id_producto'];
      $ImporteProv += (floatval($r['Importe'])/floatval($UnidadBase[1]));
      $ImpuestosProv += $impuestosTotal;
      $TotalProv += ((floatval($r['Importe']) + $impuestosTotal)/floatval($UnidadBase[1]));
      $CantidadProv += (floatval($r['cantidad'])/floatval($UnidadBase[1]));
      $UnidadProv = $UnidadBase[0];
      //$UnitarioProv += floatval($r['costo']); 
      $ImporteProd += (floatval($r['Importe'])/floatval($UnidadBase[1]));
      $ImpuestosProd += $impuestosTotal;
      $TotalProd += ((floatval($r['Importe']) + $impuestosTotal)/floatval($UnidadBase[1]));
      $CantidadProd += (floatval($r['cantidad'])/floatval($UnidadBase[1]));
      $UnidadProd = $UnidadBase[0];
      //$UnitarioProd += (floatval($r['Importe'])/floatval($UnidadBase[1])) / (floatval($r['cantidad'])/floatval($UnidadBase[1]));
      $cont++;

    }
    $UnitarioProv += $ImporteProd/$CantidadProd;
    $tabla .= "<tr style='font-weight:bold;background-color:white;'><td>Total Producto:</td><td></td><td></td><td>".$CantidadProd."</td><td>".$UnidadProd."</td><td>".number_format(($ImporteProd/$CantidadProd),2)."</td><td>".number_format($ImporteProd,2)."</td><td>".number_format($ImpuestosProd,2)."</td><td>".number_format($TotalProd,2)."</td></tr>";

    $tabla .= "<tr style='font-weight:bold;background-color:white;'><td>Total Proveedor:</td><td></td><td></td><td>".$CantidadProv."</td><td>".$UnidadProv."</td><td>".number_format($UnitarioProv,2)."</td><td>".number_format($ImporteProv,2)."</td><td>".number_format($ImpuestosProv,2)."</td><td>".number_format($TotalProv,2)."</td></tr>";

    echo $tabla;
    
  }
 
    function index()
    {   
      

        session_start();
        $user= $_SESSION["accelog_login"];
        $expuser= explode('_', $user);
        $idProveedor=$expuser[1];
        
        $paises = $this->ProveedoresModel->paises();
        $estados = $this->ProveedoresModel->estados();
        $municipiosFc = $this->ProveedoresModel->munici();
        $listaPre = $this->ProveedoresModel->listaPrecios();
        $moneda = $this->ProveedoresModel->moneda();
        $tipoCredito = $this->ProveedoresModel->creditos();
        $clasificadores = $this->ProveedoresModel->clasificadoresTipos();
        $empleados = $this->ProveedoresModel->obtenEmple();
        $bancos = $this->ProveedoresModel->bancos();
        $cuentas = $this->ProveedoresModel->cuentas();

        $tipoProveedor = $this->ProveedoresModel->tipoProveedor();
        $cuentap = $this->ProveedoresModel->cuentap();
        $cuentaCliente = $this->ProveedoresModel->cuentaCliente();
        $tipoTercero = $this->ProveedoresModel->tipoTercero();
        $tipoOpercaion = $this->ProveedoresModel->tipoOpercaion();
        $tipoIva = $this->ProveedoresModel->tipoIva();
        $saldoProv = $this->ProveedoresModel->saldoProv($idProveedor);

        $cuentaGastoP = $this->ProveedoresModel->obtener_cuenta_gasto_padre();
        $cuentaGastoP = $cuentaGastoP->fetch_assoc();
        if ($cuentaGastoP !== NULL) {
          $cuentasGastos = $this->ProveedoresModel->obtener_cuentas_gasto($cuentaGastoP['account_code']);
        }
        
        $prepolizas_pago = $this->ProveedoresModel->obtener_prepolizas_pago($cuentaGasto);
        $prepolizas_prov = $this->ProveedoresModel->obtener_prepolizas_provision($cuentaGasto);

        if($idProveedor!=''){
          $datosProveedor = $this->ProveedoresModel->datosProveedor($idProveedor);
          $tasas = $this->ProveedoresModel->tasas($idProveedor, $datosProveedor['basicos'][0]['idTasaPrvasumir']);
          //$datosProveedorFact = $this->ProveedoresModel->datosProveedorFact($idProveedor);
        }
        if($idProveedor!=''){
          $ordenesPartidas = $this->ProveedoresModel->ordenesPartidas($idProveedor);
          //$datosProveedorFact = $this->ProveedoresModel->datosProveedorFact($idProveedor);
          $res2['data'] = $ordenesPartidas['data'];
          $estatus = '';
          $link = '#';
          foreach ($ordenesPartidas['data'] as $key => $value) {
              /*if($value['idCotizacion']==null){
                  $cotizacion='';
              }else{
                  $cotizacion=$value['idCotizacion'];
              } */
                  $value['estatus'] = $value['estatus'] * 1;
                  if($value['estatus'] < 21){
                      $estatus = '<span class="label label-default">No Recibida</span>';
                  }else if($value['estatus'] > 49 && $value['estatus'] < 76){
                      $estatus = '<span class="label label-primary">RECIBIDA</span>';
                  }else if($value['estatus'] == 99){
                      $estatus = '<span class="label label-danger">CANCELADA</span>';
                  }else if($value['estatus'] == 100){
                      $estatus = '<span class="label label-success">PAGADA</span>';
                  }else{
                      $estatus = '<span class="label label-info">PROGRAMADA PARA PAGO</span>';
                  }

                  if($value['fechaConta']!=null){
                    $conta = $value['fechaConta'];
                  }else{
                    $conta = '<input class="checkPro" name="prods" value="'.$value['id_partida'].'" id="check_78-78" onclick="aaa(this);" type="checkbox">';
                  }


              $res2['data'][$key]['idocompra'] = '<a href="'.$link.'">'.$value['idocompra'].'</a>';
              $res2['data'][$key]['codigo'] = '<a href="'.$link.'">'.$value['codigo'].'</a>';
              $res2['data'][$key]['razon_social'] = '<a href="'.$link.'">'.$value['razon_social'].'</a>';
              $res2['data'][$key]['folio'] = '<a href="'.$link.'">'.$value['folio'].'</a>';
              $res2['data'][$key]['uuid'] = '<a href="'.$link.'">'.$value['uuid'].'</a>';
              $res2['data'][$key]['fechaFac'] = '<a href="'.$link.'">'.$value['fechaFac'].'</a>';
              $res2['data'][$key]['ocTotal'] = '<a href="'.$link.'">$'.number_format($value['ocTotal'],2).'</a>';
              $res2['data'][$key]['importe'] = '<a href="'.$link.'">$'.number_format($value['importe'],2).'</a>';
              $res2['data'][$key]['estatus'] = $estatus;
              $res2['data'][$key]['pdf'] = '<a onclick="FunPdf(\''.$value['xmlfile'].'\');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
              $res2['data'][$key]['acci'] = $conta;
              
          }
        }
        //require('views/proveedores/proveedoresForm.php');




        require('views/proveedores/proveedorFormPortal.php');
    }

    function reporte(){
      $proveedores = $this->ProveedoresModel->indexGrid();
      require('views/proveedores/reporte.php');
    }
    function printReport(){
       $desde = $_POST['desde'];
       $hasta = $_POST['hasta'];
       $prove = $_POST['prove'];
       $estatus = $_POST['estatus'];
       $conta = $_POST['conta'];
        //echo $desde.'dedede';
       $filtro = 'where 1 = 1 ';
       if($desde!='' && $hasta!=''){
          $filtro.=" and  fechaFac <= '".$hasta."' and   fechaFac >= '".$desde."' ";
       }
       if($prove!=0 && $prove!=''){
          $filtro .= ' and oc.id_proveedor='.$prove;
       }
       if($estatus!=0 && $estatus!=''){
          if($estatus==1){
            $filtro.=' and p.estatus < 21';
          }
          if($estatus==2){
            $filtro.=' and p.estatus > 49 and p.estatus < 76';
          }
          if($estatus==3){
            $filtro.=' and p.estatus > 76 and p.estatus < 99';
          }
          if($estatus==4){
            $filtro.=' and p.estatus=100';
          }
          if($estatus==5){
            $filtro.=' and p.estatus=99';
          }
         //$filtro .= ' and p.estatus='.$estatus;
       }
       if($conta!=0 && $conta!=''){
          if($conta==1){
            $filtro.=' and p.fechaConta IS NOT NULL';
          }else{
            $filtro.=' and p.fechaConta IS NULL';
          }
          
       }

       $pedidos = $this->ProveedoresModel->printReport($filtro);
        $res2['data'] = $pedidos['data'];
        $estatus = '';
        $link = '#';
        foreach ($pedidos['data'] as $key => $value) {
            /*if($value['idCotizacion']==null){
                $cotizacion='';
            }else{
                $cotizacion=$value['idCotizacion'];
            } */
                $value['estatus'] = $value['estatus'] * 1;
                if($value['estatus'] < 21){
                    $estatus = '<span class="label label-default">No Recibida</span>';
                }else if($value['estatus'] > 49 && $value['estatus'] < 76){
                    $estatus = '<span class="label label-primary">RECIBIDA</span>';
                }else if($value['estatus'] == 99){
                    $estatus = '<span class="label label-danger">CANCELADA</span>';
                }else if($value['estatus'] == 100){
                    $estatus = '<span class="label label-success">PAGADA</span>';
                }else{
                    $estatus = '<span class="label label-info">PROGRAMADA PARA PAGO</span>';
                }

                if($value['fechaConta']!=null){
                  $conta = $value['fechaConta'];
                }else{
                  $conta = '<input class="checkPro" name="prods" value="'.$value['id_partida'].'" id="check_78-78" onclick="aaa(this);" type="checkbox">';
                }
                if($value['complemento'] > 0){
                  $com = 'Si';
                }else{
                  $com = 'No';
                }

            $res2['data'][$key]['idocompra'] = '<a href="'.$link.'">'.$value['idocompra'].'</a>';
            $res2['data'][$key]['codigo'] = '<a href="'.$link.'">'.$value['codigo'].'</a>';
            $res2['data'][$key]['razon_social'] = '<a href="'.$link.'">'.$value['razon_social'].'</a>';
            $res2['data'][$key]['folio'] = '<a href="'.$link.'">'.$value['folio'].'</a>';
            $res2['data'][$key]['uuid'] = '<a href="'.$link.'">'.$value['uuid'].'</a>';
            $res2['data'][$key]['fechaFac'] = '<a href="'.$link.'">'.$value['fechaFac'].'</a>';
            $res2['data'][$key]['ocTotal'] = '<a href="'.$link.'">$'.number_format($value['ocTotal'],2).'</a>';
            $res2['data'][$key]['importe'] = '<a href="'.$link.'">$'.number_format($value['importe'],2).'</a>';
            $res2['data'][$key]['estatus'] = $estatus;
            $res2['data'][$key]['complemento'] = $com;
            $res2['data'][$key]['pdf'] = '<a onclick="FunPdf(\''.$value['xmlfile'].'\');" class="btn btn-default"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
            $res2['data'][$key]['acci'] = $conta;

            
        }
        echo json_encode($res2);




    }
    function contabilizar(){
      $cad = $_POST['cadena'];
      $res = $this->ProveedoresModel->contabilizar($cad);
      echo json_encode($res);
    }
    function guardaPdf(){
      $cad = $_POST['cadena'];
      $folio  = $_POST['folio'];
      $monto  = $_POST['monto'];
      $fecha  = $_POST['fecha'];
      $moneda = $_POST['moneda'];
      $pdfname = $_POST['pdfname'];
      $idOc = $_POST['idOc'];
      $res = $this->ProveedoresModel->guardaPdf($cad,$folio,$monto,$fecha,$moneda,$pdfname,$idOc);
      echo json_encode($res);
    }





}
        // $idCliente = $_GET['idCliente'];

        // $paises = $this->ClienteModel->paises();
        // $estados = $this->ClienteModel->estados();
        // $municipiosFc = $this->ClienteModel->munici();
        // $listaPre = $this->ClienteModel->listaPrecios();
        // $moneda = $this->ClienteModel->moneda();
        // $tipoCredito = $this->ClienteModel->creditos();
        // $clasificadores = $this->ClienteModel->clasificadoresTipos(0);
        // $empleados = $this->ClienteModel->obtenEmple();
        // $bancos = $this->ClienteModel->bancos();
        // $cuentas = $this->ClienteModel->cuentas();
        // $almacenes = $this->ClienteModel->almacenes(); /*
        /*foreach ($proveedores as $key => $value) {
         echo ''.$value['razon_social'].'<br>';
        } 
        if($idCliente!=''){
            $datosCliente = $this->ClienteModel->datosCliente($idCliente);
            $datosClienteFact = $this->ClienteModel->datosClienteFact($idCliente);
            $id_claisf = $datosCliente['basicos'][0]['id_clasificacion'];
            $clasificadores = $this->ClienteModel->clasificadoresTipos($id_claisf);
        } 
        //print_r($datosCliente);
        require('views/cliente/clienteForm.php');
    }

    

}


?>
