function desBoton(div,txt){
  $(div).prop('disabled', true);
  $(div).val(txt);
  $(div).css('cursor','inherit');
}

function habBoton(div,txt){
  $(div).prop('disabled', false);
  $(div).val(txt);
  $(div).css('cursor','pointer');
}

function cmbano(){
  ano = $('#selano').val();
  $('#selsema').html('Cargando...');
  $.ajax({
    url:"semano.php",
    type: 'POST',
    data:{ano:ano},
    success: function(semanas){
      for(x=1; x<=semanas; x++){
        $('#selsema').append('<option value="'+x+'">Semana '+x+'</option>');
      }
    }
  });
}

function llamadaAutorizar(opt){   


    if(opt=='Dest'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_destajistas_bit","Estimacion destajistas realizadas","",2214);
    }
    if(opt=='Subc'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_subcontratistas_bit","Estimacion subcontratistas realizadas","",2215);
    }
    if(opt=='Prov'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_proveedores_bit","Estimacion proveedores realizadas","",2216);
    }
    if(opt=='Clie'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_cliente_bit","Estimaciones al cliente realizadas","",2207);
    }
    if(opt=='Chic'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_cc","Estimacion de caja chica","",1787);
    }
    if(opt=='Indi'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=est_indirectos","Estimacion de indirectos","",1786);
    }
    if(opt=='Requ'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=visualizar_requi","Visualizar requisición","",1776);
    }
    if(opt=='Pedi'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=visualizar_pedi","Visualizar ordenes de compra","",1777);
    }
    if(opt=='Reme'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=remesas","Remesas","",1793);
    }
    if(opt=='Extra'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=extra_aut","Autorizacion de Extraordinarios","",2302);
    }
    if(opt=='Adic'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=adic_aut","Autorizacion de Adicionales","",2305);
    }
    if(opt=='Nocob'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=nocob_aut","Autorizacion de No cobrables","",2304);
    }

    if(opt=='ESTNOMC'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=prenom_ocen","Autorizacion prenomina oficina central","",2276);
    }
    if(opt=='ESTNOMOC'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=prenom_oce","Autorizacion prenomina campo","",2278);
    }
    if(opt=='NOMDEST'){
       window.parent.agregatab("../../modulos/xtructur/index.php?modulo=prenomina_auth","Autorizacion Nomina Obreros","",2268);
    }


}

function uf(num){
  num=num.toString();
  return num.replace(/,/g, "")*1;
}

function funTraspaso(){
  obra_sal = $('#obra_sal').val();
  obra_ent = $('#obra_ent').val();

  if(obra_sal==0){
    alert('Seleccione una obra de salida');
    return false;
  }

  if(obra_ent==0){
    alert('Seleccione una obra de entrada');
    return false;
  }

  if(obra_sal==obra_ent){
    alert('Las obras no pueden ser iguales');
    return false;
  }

  $('#viejaseleccion').css('display','none');
  $('#nuevaseleccion').css('display','block');

  $('#obra_sal').prop('disabled',true);
  $('#obra_ent').prop('disabled',true);

  $.ajax({
      url:"traspasogrid.php",
      type: 'POST',
      data:{obra_sal:obra_sal,obra_ent:obra_ent},
      success: function(r){

        $('#cargagrid').html(r);
      }
  });
}

function autorizarem(opt,idrem){
  $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarem',opt:opt,idrem:idrem},
      success: function(r){
        if(opt==2){
          alert('Remesa autorizada');
        }else{
          alert('Remesa cancelada');
        }
      }
    });

}

//////////////////chais//////////////////////////////////////////////////
function pdf(opt,r){
  if(opt=='des'){
    desBoton('#btnpdfdes','Procesando...');
    id = $('#estimacion_num').val(); /// para tomar el valor del id 
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'pdf',id:id,opt:opt,r:r},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfdes','PDF');
      }
    });
  }
 } 

 function pdfrequisicion(opt){
  if(opt=='req'){
    desBoton('#btnpdfreq','Procesando...');
    id = $('#reqver').val();
    if(id==0) { alert('Selecciona Requisicion'); 
    habBoton('#btnpdfreq','PDF');
    return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'pdfrequisicion',id:id,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfreq','PDF');
      }
    });
  }
 }

 function pdfcompras(opt){
  if(opt=='comp'){
    desBoton('#btnpdfcomp','Procesando...');
    id = $('#compraver').val();
    if(id==0) { alert('Selecciona Compra'); 
    habBoton('#btnpdfcomp','PDF');
    return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'pdfcompras',id:id,opt:opt},
      success: function(resp){
      window.open('pdf/'+resp+'.pdf');
      habBoton('#btnpdfcomp','PDF');
      }
    });
  }
 } 


///funciones para varias salidas pdfsalidas-cmbsal

 function pdfsalidas(opt){
  if(opt=='salida'){
    desBoton('#btnpdfsal','Procesando...');
    id = $('#salver').val();
    id_sal = $('#salida_num').val();
    if(id==0 || id_sal==0) { alert('Selecciona una opcion'); 
    habBoton('#btnpdfsal','PDF');
    return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'pdfsalidas',id:id,id_sal:id_sal,opt:opt},
      success: function(resp){
      window.open('pdf/'+resp+'.pdf');
      habBoton('#btnpdfsal','PDF');
      }
    });
  }
 } 


  function saveConfig(){
    radio=$('input[name=ra]:checked').val();
    time=$('input[name=ti]:checked').val();
    puaut=$('input[name=pu]:checked').val();



    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'saveConfig',radio:radio,time:time,puaut:puaut},
      success: function(resp){
        window.location.reload();
        alert('Cambios guardados con exito');

      }
    });
 } 

 function cmbsal(opt){
  id = $('#salver').val();
  if(opt=='des'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbsal',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la Salida</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">SAL-'+v.id+' / '+v.fecha+' </option>'
          });
          $('#salida_num').html(cad);
        }else{
          $('#salida_num').html('<option selected="selected" value="0">No hay Salidas</option>');
        }
      }
    });
  }
}

 function pdfentradas(opt){
  if(opt=='entradas'){
    desBoton('#btnpdfent','Procesando...');
    id = $('#entver').val();
    id_ent = $('#entrada_num').val();
    if(id==0 || id_ent==0) { alert('Selecciona una opcion'); 
    habBoton('#btnpdfent','PDF');
    return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'pdfentradas',id:id,id_ent:id_ent,opt:opt},
      success: function(resp){
      window.open('pdf/'+resp+'.pdf');
      habBoton('#btnpdfent','PDF');
      }
    });
  }
 } 

function cmbent(opt){
  id = $('#entver').val();
  if(opt=='des'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbent',opt:opt,id:id},
      success: function(r){ 
        console.log(r.success); 
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la Entrada</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">ENT-'+v.id+' / '+v.fecha+' </option>'
          });
          $('#entrada_num').html(cad);
        }else{
          $('#entrada_num').html('<option selected="selected" value="0">No hay Entrada</option>');
        }
      }
    });
  }
}

function pdf_est_sub(opt){
  id_est = $('#estimacion_num').val();
  id_sub = $('#destaver').val();
  if(opt=='sub'){
    desBoton('#btnpdfsub','Procesando...');
    $.ajax({
      url:"ajax.php",
      type: 'POST', 
      data:{opcion:'pdf_est_sub', id_est:id_est,id_sub:id_sub,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfsub','PDF');
      }
    });
  }
 
}

function pdf_est_cli(opt,ob){
  id_cli = $('#idcli').val();
  id_sub = ob;
  if(opt=='cli'){
    desBoton('#buttonpdfcli','Procesando...');
    $.ajax({
      url:"ajax.php",
      type: 'POST', 
      data:{opcion:'pdf_est_cli', id_cli:id_cli,id_sub:id_sub,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#buttonpdfcli','PDF');
      }
    });
  }
 
}

function pdf_est_prov(opt){
  id_est = $('#oc_num2').val();
  if(opt=='prov'){
    desBoton('#btnpdfprov','Procesando...');
    $.ajax({
      url:"ajax.php",
      type: 'POST', 
      data:{opcion:'pdf_est_prov', id_est:id_est,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfprov','PDF');
      }
    });
  }
 
}

function pdf_est_chica(opt){
  id = $('#estimacion_num').val();
  if(opt=='chica'){
    desBoton('#btnpdfchica','Procesando...');
    $.ajax({
      url:"ajax.php",
      type: 'POST', 
      data:{opcion:'pdf_est_chica', id:id,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfchica','PDF');
      }
    });
  }
 
}

function pdf_est_indi(opt){
  id = $('#estimacion_num').val();
  if(opt=='indi'){
    desBoton('#btnpdfindi','Procesando...');
    $.ajax({
      url:"ajax.php",
      type: 'POST', 
      data:{opcion:'pdf_est_indi', id:id,opt:opt},
      success: function(resp){
        window.open('pdf/'+resp+'.pdf');
        habBoton('#btnpdfindi','PDF');
      }
    });
  }
 
}

function graficar_acu(opt,id_obra){
    if(opt == "acumulado"){
        desBoton('#btn_gra_acum','Procesando...');
        $.ajax({
          success: function(response) {
            window.open("grafica_acumulado.php?id_obra="+id_obra+"&opt="+opt)
            habBoton('#btn_gra_acum','Graficar Acumulado');
          }
      });
    }
}
function graficar_ret(opt,id_obra){
  id_des = $('#idcli').val();
  id_sub = $('#id_ret_sub').val();
  if(opt=='des_uno'){
    if(id_des==0) { alert('Selecciona un destajista'); return false; }
    desBoton('#btngraficarDesUno','Procesando...');
      $.ajax({
        success: function(response) {
          $("#message").hide();
          window.open("grafica.php?id_des="+id_des+"&opt="+opt)
          habBoton('#btngraficarDesUno','Graficar Dest');
        }
      }); 
  }

  if(opt=='sub_uno'){
    if(id_sub==0) { alert('Selecciona un subcontratista'); return false; }
    desBoton('#btngraficarSubUno','Procesando...');
    $("#message").show();
    $.ajax({
        success: function(response) {
          $("#message").hide();
          window.open("grafica.php?id_sub="+id_sub+"&opt="+opt)
          habBoton('#btngraficarSubUno','Graficar Sub');
        }
    });
  }
  if(opt=='des_todos'){
    desBoton('#btngraficarDesAll','Procesando...');
    $.ajax({
        success: function(response) {
          $("#message").hide();
          window.open("grafica.php?id_obra="+id_obra+"&opt="+opt)
          habBoton('#btngraficarDesAll','Destajistas');
        }
    });
  }

  if(opt=='sub_todos'){
    desBoton('#btngraficarSubAll','Procesando...');
    $.ajax({
        success: function(response) {
          window.open("grafica.php?id_obra="+id_obra+"&opt="+opt)
          habBoton('#btngraficarSubAll','Subcontratistas');
        }
    });
  }
  if(opt=='est_cliente'){
    desBoton('#btn_est_clie','Procesando...');
        $.ajax({
        success: function(response) {
          window.open("grafica_est_clie.php?id_obra="+id_obra)
          habBoton('#btn_est_clie','Graficar');
        }
    });
  }
  if(opt=='subcontra'){
    desBoton('#btn_gra_sub','Procesando...');
        $.ajax({
        success: function(response) {
          window.open("grafica_subcontra.php?id_obra="+id_obra)
          habBoton('#btn_gra_sub','Graficar');
        }
    });
  }
}// fin de funcion madre
//se crea variable global optgraf
 optgraf='';
 function cmbgra(opt){
  id_des = $('#id_ret_des').val();
  id_sub = $('#id_ret_sub').val();
  optgraf=opt; 
}

$(document).keypress(function(e) {
    if(e.which == 13) {
      switch (optgraf) {
        case 'sub_uno':  
                        var optgraf1 = optgraf;
                        optgraf='';
                        if(id_sub==0) { return false; }
                          $.ajax({
                              success: function(response) {
                                window.open("grafica.php?id_sub="+id_sub+"&opt="+optgraf1);
                              }
                          });
          break;
        case 'des_uno':
                        var optgraf1 = optgraf;
                        optgraf='';
                        if(id_des==0) { return false; }
                            $.ajax({
                              success: function(response) {
                                window.open("grafica.php?id_des="+id_des+"&opt="+optgraf1);
                              }
                            }); 
        break;
        
          default: 
          break;
      } 
          }
});


function autorizarestAll(opt,id,r){  


  if(opt=='des'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }
  if(opt=='sol'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Solicitud aceptada');
          window.location.reload();
        }else{
          alert('Solicitud rechazada');
          window.location.reload();
        }
      }
    });
  }

  if(opt=='sub'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }

  if(opt=='prov'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }

  if(opt=='cli'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }
   if(opt=='cc'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }
   if(opt=='ind'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }

    
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        if(r==1){
          alert('Estimacion aceptada');
          window.location.reload();
        }else{
          alert('Estimacion rechazada');
          window.location.reload();
        }
      }
    });
  }

     if(opt=='tsal'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }
 if(id==-1){
 var id=$('#ide').val();
     var jus=$('#jus').val();}
    
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r,jus:jus},
      success: function(resp){
        if(r==3){
          alert('Traspaso aceptado');
          window.location.reload();
        }else{
          alert('Traspaso rechazado');
          window.location.reload();
        }
      }
    });
  }

  if(opt=='tent'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    
     if(id==-1){var id=$('#ide').val();
     var jus=$('#jus').val();}

    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r,jus:jus},
      success: function(resp){
        if(r==4){
          alert('Traspaso aceptado');
          window.location.reload();
        }else{
          alert('Traspaso rechazado');
          window.location.reload();
        }
      }
    });
  }

}

 ////////////////chais//////////////////////////////////////////////////
function autorizarest(opt,r){
  id = $('#estimacion_num').val();
  
  if(opt=='des'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }
  if(opt=='sub'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }
  if(opt=='prov'){
    id=$('#oc_num2').val();

    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }
  if(opt=='cc'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }
  if(opt=='ind'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }


    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }

  if(opt=='cli'){    
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }

  if(opt=='nomo'){    
    id = $('#nomi').val();
    if(id==0) { alert('Selecciona algo'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }
  if(opt=='nomot'){    
    id = $('#nomi').val();
    if(id==0) { alert('Selecciona algo'); return false; }
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizarest',id:id,opt:opt,r:r},
      success: function(resp){
        $('.best').remove();
        if(r==1){
          $('#rbest').html('<b>Estimacion aceptada</b>');
        }else{
          $('#rbest').html('<b><font color="#ff0000">Estimacion rechazada</font></b>');
        }
      }
    });
  }


}

function modiFac(id,opt){
   fact = $('#modiFac').val();
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'modiFac',id:id,opt:opt,fact:fact},
      success: function(r){
        alert('Factura modificada con exito');
      }
    });

}

function vercheques(){
   sema = $('#desta2').val();
   
    $.ajax({
      url:"jsest_cheques_view2.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });

    /*
    sema = $('#desta2').val();
    if(sema==0) { alert('Selecciona una remesa'); return false; }
    $.ajax({
      url:"jsest_remesas_view_rep.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
*/
}

function pago(){
tipo=$('#val_fpago').val();
if (tipo==1){
$('#noc').prop('disabled', true);
$('#ban').prop('disabled', true);
$('#val').prop('disabled', true);
$('#estc').prop('disabled', true);
$('#estf').prop('disabled', true);
}

if (tipo==7){
  $('#ban').prop('disabled', false);
$('#info').html('No. de cuenta');
$('#noc').prop('disabled', false);
$('#val').prop('disabled', true);
$('#estc').prop('disabled', true);
$('#estf').prop('disabled', true);
}

  }
function vercheques2(){
   sema = $('#desta2').val();
    
    $.ajax({
      url:"jsest_cheques2_view2.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });

    /*
    sema = $('#desta2').val();
    if(sema==0) { alert('Selecciona una remesa'); return false; }
    $.ajax({
      url:"jsest_remesas_view_rep.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
*/
}


function verremesa(){

   sema = $('#desta2').val();
    if(sema==0) { alert('Selecciona una remesa'); return false; }
    $.ajax({
      url:"jsest_remesas_view_rep.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
}

function verest(opt){
  id = $('#estimacion_num').val();
  
  if(opt=='des'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_destajista_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='cli'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_cliente_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='sub'){
    idsub = $('#destaver').val();

    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_subcontratista_view_rep.php",
      type: 'POST',
      data:{id:id,idsub:idsub},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='ind'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_indirectos_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='cc'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_chica_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='pro'){
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_chica_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='pro2'){
    id = $('#oc_num2').val();
    if(id==0) { alert('Selecciona una estimacion'); return false; }
    $.ajax({
      url:"jsest_prov_view_rep.php",
      type: 'POST',
      data:{id:id},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
  }


}

function cmbdprenom(){
  id = $('#desta').val();
  if(id==0){
    $('#edif').html('<option selected="selected" value="0">No hay edificios</option>');
    return false;
  }
  $('#edif').html('<option selected="selected" value="0">Cargando...</option>');
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbdprenom',id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona un edificio</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.idarea+'">'+v.nombre+'</option>'
          });
          $('#edif').html(cad);
        }else{
          $('#edif').html('<option selected="selected" value="0">No hay edificios</option>');
        }
        
      }
    });
}

function cmbpnom(){
  id = $('#destaver').val();
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbpnom',id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la nomina</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.nomi+'</option>'
          });
          $('#nomi').html(cad);
        }else{
          $('#nomi').html('<option selected="selected" value="0">No hay estimaciones</option>');
        }
        
      }
    });
  }

function cmbest(opt){
  id = $('#destaver').val();
  if(opt=='des'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la estimacion</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.estimacion+'</option>'
          });
          $('#estimacion_num').html(cad);
        }else{
          $('#estimacion_num').html('<option selected="selected" value="0">No hay estimaciones</option>');
        }
        
      }
    });
  }

  if(opt=='sub'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la estimacion</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.estimacion+'</option>'
          });
          $('#estimacion_num').html(cad);
        }else{
          $('#estimacion_num').html('<option selected="selected" value="0">No hay estimaciones</option>');
        }
        
      }
    });
  }

  if(opt=='ind'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la estimacion</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.estimacion+'</option>'
          });
          $('#estimacion_num').html(cad);
        }else{
          $('#estimacion_num').html('<option selected="selected" value="0">No hay estimaciones esta semana</option>');
        }
        
      }
    });
  }

  if(opt=='cc'){
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la estimacion</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.estimacion+'</option>'
          });
          $('#estimacion_num').html(cad);
        }else{
          $('#estimacion_num').html('<option selected="selected" value="0">No hay estimaciones esta semana</option>');
        }
        
      }
    });
  }

  if(opt=='pro'){
    if(id==0){
      $('#oc_num').html('<option selected="selected" value="0">No hay ordenes de compra para este proveedor</option>');
      return false;
    }
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la orden de compra</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.oc+'</option>'
          });
          $('#oc_num').html(cad);
        }else{
          $('#oc_num').html('<option selected="selected" value="0">No hay ordenes de compra para este proveedor</option>');
        }
        
      }
    });
  }

  if(opt=='pro2'){
    id = $('#destaver2').val();
    if(id==0){
      $('#oc_num2').html('<option selected="selected" value="0">No hay oestimaciones para este proveedor</option>');
      return false;
    }
    $.ajax({
      url:"ajax.php",
      dataType:"JSON",
      type: 'POST',
      data:{opcion:'cmbest',opt:opt,id:id},
      success: function(r){
        console.log(r.success);
        if(r.success==1){
          cad='<option selected="selected" value="0">Selecciona la estimacion</option>';
          $.each(r.datos, function( i, v ) {
            cad+='<option value="'+v.id+'">'+v.oc+'</option>'
          });
          $('#oc_num2').html(cad);
        }else{
          $('#oc_num2').html('<option selected="selected" value="0">No hay estimaciones para este proveedor</option>');
        }
        
      }
    });
  }

}

function controlind(){
  mes = $('#mes').val();
  if(mes==0){
    alert('Seleccione un mes');
    return false;
  }

    $.ajax({
      url:'jscontrol_indirectos_rep.php',
      type: 'POST',
      data:{mes:mes},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
}

function estadorep(){
  mes = $('#mes').val();
  erep = $('#estadorep').val();
  if(erep==0){
    alert('Seleccione un reporte');
    return false;
  }

  if(mes==0){
    alert('Seleccione un mes');
    return false;
  }

  if(erep==1){
    url='jsestado_rep1.php';
  }
  if(erep==2){
    url='jsestado_rep2.php';
  }
  if(erep==3){
    url='jsestado_rep3.php';
  }
  if(erep==4){
    url='jsestado_rep4.php';
  }


    $.ajax({
      url:url,
      type: 'POST',
      data:{mes:mes},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
}

/*
function verestimaciones(){
  alert('Ver reportes');
  return false;
  id_des = $('#desta').val();
  if(id_des==0){
    alert('Seleccione un destajista');
    return false;
  }
  
    $.ajax({
      url:"jsest_arbol_destajista_view.php",
      type: 'POST',
      data:{id_des:id_des},
      success: function(r){
        $('#estdestajista').html(r);
      }
    });
}
*/
function buscarremesa(){
  sema = $('#desta').val();
  if(sema==0){ alert('Seleccione una semana'); return false; }
    $('#preload').css('display','block');
    $.ajax({
      url:"jsest_remesas_view2.php",
      type: 'POST',
      data:{sema:sema},
      success: function(r){

        $('#estdestajista').html(r);
      }
    });
}

function crearremesa(opt){
  sema = $('#desta').val();
  monto = $('#monto').val();
  if(sema==0){ alert('Seleccione una semana'); return false; }
  if((monto*1)<=0){ alert('Introduzca el monto autorizado para la remesa'); return false; }


    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'saverem',sema:sema,monto:monto},
      success: function(r){
        if(r=='REP'){
          alert('Ya tienes una remesa creada esta semana');
          return false;
        }else{
          $('#preload').css('display','block');
          $.ajax({
            url:"jsest_remesas_view.php",
            type: 'POST',
            data:{sema:sema,id:r},
            success: function(r){

              $('#estdestajista').html(r);
              $('#rea').val(monto);

            }
          });
        }
      }
    });

    
}


function buscanoti(opt){
  fecha1 = $('#jrange input').val();
  fecha2 = $('#jrange2 input').val(); 

          $.ajax({
            url:"buscanoti.php",
            type: 'POST',
            data:{fecha1:fecha1,fecha2:fecha2},
            success: function(r){

              $('#resultados').html(r);

            }
          });

    
}

function crearestimacion(opt){
  
  bloqueo=0;
  id_des = $('#desta').val();

  if(opt=='des'){
    $('#preload').html('Cargando...');
    ag = $('#cargaagr').val();
    ar = $('#cargaesp').val();
    es = $('#cargaare').val();
    pa = $('#cargapart').val();

/*
    if(ag==0 || ar==0 || es==0 || pa==0){
      alert('Selecciona la planeacion');
      return false;
    }
*/
    sema = $('#sema').val();
    if(id_des==0){ alert('Seleccione un destajista'); return false; }
    if(sema==0){ alert('Seleccione una semana'); return false; }
    $('#preload').css('display','block');
    /*$.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:id_des},
      success: function(dr){
        console.log(dr);
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }*/
    $.ajax({
      url:"jsest_destajista_view.php",
      type: 'POST',
      data:{id_des:id_des,sema:sema,ag:ag,ar:ar,es:es,pa:pa},
      success: function(r){
        if(r=='EXIST'){
          
          $('#preload').html('Ya existe una estimacion para este maestro en la semana seleccionada');
          return false;
        }
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='sub'){
    $('#preload').html('Cargando...');
    ag = $('#cargaagr').val();
    ar = $('#cargaesp').val();
    es = $('#cargaare').val();
    pa = $('#cargapart').val();

/*
    if(ag==0 || ar==0 || es==0 || pa==0){
      alert('Selecciona la planeacion');
      return false;
    }
*/
    sema = $('#sema').val();
    if(id_des==0){ alert('Seleccione un subcontratista'); return false; }
    $('#preload').css('display','block');
    /*$.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:id_des},
      success: function(dr){
        console.log(dr);
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }*/
    $.ajax({
      url:"jsest_subcontratista_view.php",
      type: 'POST',
      data:{id_des:id_des,ag:ag,ar:ar,es:es,pa:pa,sema:sema},
      success: function(r){
        if(r=='EXIST'){
          $('#preload').html('Ya existe una estimacion para este subcontratista en la semana seleccionada');
          return false;
        }
        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='cli'){
    ag = $('#cargaagr').val();
    ar = $('#cargaesp').val();

    if(ag==0 || ar==0){
      alert('Selecciona la planeacion');
      return false;
    }
    sema = $('#sema').val();
    if(sema==0){ alert('Seleccione una semana'); return false; }
    $('#preload').css('display','block');
    /*$.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:id_des},
      success: function(dr){
        console.log(dr);
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }*/
    $.ajax({
      url:"jsest_cliente_view.php",
      type: 'POST',
      data:{id_des:id_des,ag:ag,ar:ar,sema:sema},
      success: function(r){

        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='ind'){
    if(id_des==0){ alert('Seleccione una semana'); return false; }
    $('#preload').css('display','block');
    /*$.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:id_des},
      success: function(dr){
        console.log(dr);
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }*/
    $.ajax({
      url:"jsest_indirectos_view.php",
      type: 'POST',
      data:{id_des:id_des},
      success: function(r){

        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='cc'){
    if(id_des==0){ alert('Seleccione una semana'); return false; }
    $('#preload').css('display','block');
    /*
    $.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:id_des},
      success: function(dr){
        console.log(dr);
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }
    */
    $.ajax({
      url:"jsest_chica_view.php",
      type: 'POST',
      data:{id_des:id_des},
      success: function(r){

        $('#estdestajista').html(r);
      }
    });
  }

  if(opt=='pro'){
    $('#preload').html('Cargando...');
    destaver = $('#destaver').val();
    oc_num = $('#oc_num').val();
    estpor= $('input[name=estpor]:checked').val();

    if(destaver==0){ alert('Selecciona un proveedor'); return false; }
    if(oc_num==0){ alert('Selecciona una orden de compra'); return false; }
    if(id_des==0){ alert('Selecciona una semana'); return false; }
    $('#preload').css('display','block');
    
    /*$.ajax({
      async:false,
      url:"ajax.php",
      type: 'POST',
      dataType: 'JSON',
      data:{opcion:'est_pend',opt:opt,id_des:destaver},
      success: function(dr){
        if(dr.success==1){
          bloqueo=1;
        }
      }
    });
    if(bloqueo==1){
      alert('No puedes hacer una estimacion nueva, tienes estimaciones pendientes por autorizar.');
      $('#preload').css('display','none');
      return false;
    }*/
    $.ajax({
      url:"jsest_prov_view.php",
      type: 'POST',
      data:{id_des:id_des,oc_num:oc_num,destaver:destaver,estpor:estpor},
      success: function(r){
        if(r=='EXIST'){
          $('#preload').html('Ya existe una estimacion para este proveedor en la semana seleccionada');
          return false;
        }
        $('#estdestajista').html(r);
      }
    });
  }

}


function filtros(opt,filtro){
  //Caja chica
  filtro_semana=$('#filtro_semana').val();
  filtro_mes=$('#filtro_mes').val();
  filtro_estatus=$('#filtro_estatus').val();
  filtro_proveedor=$('#filtro_proveedor').val();


  if(filtro_mes!=0){
    $("#filtro_semana").val($("#filtro_semana option:first").val());
    $("#filtro_semana").prop('disabled',true);
  }else{
    $("#filtro_semana").prop('disabled',false);
  }
  if(opt=='achica'){
    $("#jq_arbol").jqGrid('setGridParam', { 
          postData: {"filtro_semana":filtro_semana,
                    "filtro_mes":filtro_mes,
                    "filtro_estatus":filtro_estatus,
                    "filtro_proveedor":filtro_proveedor
           }
    }).trigger('reloadGrid'); 
  }
}


function verprenominatecce(){
  id_des = $('#desta').val();
  rango = $('#jrange input').val();
  lala = rango.split(' / ');
  sd=lala[0];
  ed=lala[1];

  if(sd=='' || ed==''){
    alert('Selecciona una semana');
    return false;
  }

  if(id_des==0){
    alert('Seleccione una opcion');
    return false;
  }

    $.ajax({
      url:"jsprenominatec2_view.php",
      type: 'POST',
      data:{id_des:id_des,sd:sd,ed:ed},
      success: function(r){
        $('#vernomina').html(r);
      }
    });
  
}

function verprenominatec(){
  id_des = $('#desta').val();
  sd = $('#startDate').val();
  ed = $('#endDate').val();
  if(sd=='' || ed==''){
    alert('Selecciona una semana');
    return false;
  }

  if(id_des==0){
    alert('Seleccione una opcion');
    return false;
  }

    $.ajax({
      url:"jsprenominatec1_view.php",
      type: 'POST',
      data:{id_des:id_des,sd:sd,ed:ed},
      success: function(r){
        if(r=='EXIST'){
          
          $('#vernomina').html('Ya existe una nomina de tecnicos en la semana seleccionada');
          return false;
        }
        $('#vernomina').html(r);
      }
    });
  
}

function vernomgeneradas(){
  nomi = $('#nomi').val();
  if(nomi==0){
    alert('Seleccione una nomina');
    return false;
  }

    $.ajax({
      url:"jsonomina_view.php",
      type: 'POST',
      data:{nomi:nomi},
      success: function(r){
        $('#vernomina').html(r);

        
      }
    });
}

function vernomgeneradascampo(){
  nomi = $('#nomi').val();
  if(nomi==0){
    alert('Seleccione una nomina');
    return false;
  }

    $.ajax({
      url:"jsonomina_view_campo.php",
      type: 'POST',
      data:{nomi:nomi},
      success: function(r){
        $('#vernomina').html(r);

        
      }
    });
}

function vernomgeneradascentral(){
  nomi = $('#nomi').val();
  if(nomi==0){
    alert('Seleccione una nomina');
    return false;
  }

    $.ajax({
      url:"jsnomina_view_central.php",
      type: 'POST',
      data:{nomi:nomi},
      success: function(r){
        $('#vernomina').html(r);

        
      }
    });
}

function verprenomina(){
  id_des = $('#desta').val();
  //id_edif = $('#edif').val();
  sd = $('#startDate').val();
  ed = $('#endDate').val();
  if(sd=='' || ed==''){
    alert('Selecciona una semana');
    return false;
  }
/*
  if(id_edif==0){
    alert('Seleccione un edificio');
    return false;
  }
*/
  $.ajax({
    async:false,
      url:"ajax.php",
      type: 'POST',
      dataType:'JSON',
      data:{opcion:'nominaest',id_des:id_des,id_edif:0,sd:sd,ed:ed},
      success: function(r){
        $('#infoest').css('display','block');
        if(r.success==1){
          console.log(r);
          $('#idsema').html(r.semana);
          $('#idimp').html(r.datos[0].subtotal1).currency();
          $('#totale').val(r.datos[0].subtotal1);
        }else{
          $('#idsema').html('No hay estimaciones para esta semana');
          $('#idimp').html('$0.00');
        }
      }
    });

    $.ajax({
      url:"jsprenomina_view.php",
      type: 'POST',
      data:{id_des:id_des,id_edif:0,sd:sd,ed:ed},
      success: function(r){
        if(r=='EXIST'){
          
          $('#vernomina').html('Ya existe una nomina para este maestro en la semana seleccionada');
          return false;
        }
        $('#vernomina').html(r);

        
      }
    });
}

function vernominatec(){
  sema=0;
  id_des = $('#desta').val();
  sd = $('#startDate').val();
  ed = $('#endDate').val();
  if(sd=='' || ed==''){
    alert('Selecciona una semana');
    return false;
  }

  if(id_des==0){
    alert('Seleccione una opcion');
    return false;
  }

  if(id_des==1){
    $.ajax({
      url:"jstomaduriatec1_view.php",
      type: 'POST',
      data:{id_des:id_des,sema:sema,sd:sd,ed:ed},
      success: function(r){
        $('#vernomina').html(r);
      }
    });
  }
  if(id_des==2){
    $.ajax({
      url:"jstomaduriatec1_view.php",
      type: 'POST',
      data:{id_des:id_des,sema:sema,sd:sd,ed:ed},
      success: function(r){
        $('#vernomina').html(r);
      }
    });
  }
}

function vernomina(){
  id_des = $('#desta').val();
  sd = $('#startDate').val();
  ed = $('#endDate').val();
  if(sd=='' || ed==''){
    alert('Selecciona una semana');
    return false;
  }

  if(id_des==0){
    alert('Seleccione un destajista');
    return false;
  }
    $.ajax({
      url:"jstomaduria_view.php",
      type: 'POST',
      data:{id_des:id_des,sd:sd,ed:ed},
      success: function(r){
        $('#vernomina').html(r);
      }
    });
}

function savemat(){
  idm = $('#val_fam').val();
  ids = $("#jq_presupuesto").jqGrid('getGridParam','selarrrow');
  selectedRowId = jQuery("#jq_presupuesto").jqGrid('getGridParam','selarrrow');
  if(idm==0){
    alert('Seleccione una familia de materiales');
    return false;
  }
  if(ids!=''){
    if(confirm("Se asignara esta familia a los elementos seleccionados, desea continuar?") == true) {
      $.ajax({
        url:"ajax.php",
        type: 'POST',
        data:{opcion:'savemat',idm:idm,ids:ids},
        success: function(r){
          window.location='index.php?modulo='+modulo;
        }
      });
    }else{

    }
  }else{
    alert('Selecciona un insumo');
  }
}

function savepu(es){
  pre=$('#ingprecio').val();
  ids = jQuery("#jq_asignacion").jqGrid('getGridParam','selarrrow');
  if(ids!=''){
    if(confirm("Se asignara este precio a los elementos seleccionados, desea continuar?") == true) {
      $.ajax({
        url:"ajax.php",
        type: 'POST',
        data:{opcion:'savepu',es:es,ids:ids,pre:pre},
        success: function(r){
          $.each(ids, function( i, v ) {
            if(es==1){
              $('tr#'+v).find('td:eq(9)').text(pre);
              var sum_pu_destajo = $("#jq_asignacion").jqGrid('getCol','pu_destajo',false,'sum');
              $("#jq_asignacion").jqGrid('footerData','set',{pu_destajo:sum_pu_destajo});
            }
            if(es==2){
              $('tr#'+v).find('td:eq(10)').text(pre);
              var sum_pu_subcontrato = $("#jq_asignacion").jqGrid('getCol','pu_subcontrato',false,'sum');
              $("#jq_asignacion").jqGrid('footerData','set',{pu_subcontrato:sum_pu_subcontrato});
            }
          });
        }
      });
    }else{

    }
  }else{
    alert('Selecciona un elemento');
  }
}

function asignpu(es){
  ids = jQuery("#jq_asignacion").jqGrid('getGridParam','selarrrow');
  if(ids!=''){
    if(confirm("Se asignara esta opcion a los elementos seleccionados, desea continuar?") == true) {
      $.ajax({
        url:"ajax.php",
        type: 'POST',
        data:{opcion:'asignpu',es:es,ids:ids},
        success: function(r){
          $.each(ids, function( i, v ) {
            if(es==1){
              $('tr#'+v).find('td:eq(9)').find('input').prop('checked', true);
              $('tr#'+v).find('td:eq(10)').find('input').prop('checked', false);
            }
            if(es==2){
              $('tr#'+v).find('td:eq(9)').find('input').prop('checked', false);
              $('tr#'+v).find('td:eq(10)').find('input').prop('checked', true);
            }
            if(es==3){
              $('tr#'+v).find('td:eq(9)').find('input').prop('checked', true);
              $('tr#'+v).find('td:eq(10)').find('input').prop('checked', true);
            }
            if(es==4){
              $('tr#'+v).find('td:eq(9)').find('input').prop('checked', false);
              $('tr#'+v).find('td:eq(10)').find('input').prop('checked', false);
            }
          });
        }
      });
    }else{

    }
  }else{
    alert('Selecciona un elemento');
  }
}

function autorizaOC(oc){
  if(confirm("Se autorizara la orden de compra seleccionada, desea continuar?") == true) {
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizaOC',oc:oc},
      success: function(r){
        jQuery("#jq_arbol").trigger("reloadGrid");
        window.location='index.php?modulo='+modulo;
      }
    });
  }else{

  }
}

function cancelOC(oc){
  if(confirm("Se cancelara la orden de compra seleccionada, desea continuar?") == true) {
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'cancelOC',oc:oc},
      success: function(r){
        jQuery("#jq_arbol").trigger("reloadGrid");
        window.location='index.php?modulo='+modulo;
      }
    });
  }else{

  }
}

function autorizaReq(req){
  if(confirm("Se autorizara la requisicion seleccionada, desea continuar?") == true) {
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'autorizaReq',req:req},
      success: function(r){
        jQuery("#jq_arbol").trigger("reloadGrid");
        window.location='index.php?modulo='+modulo;
      }
    });
  }else{

  }
}

function cancelReq(req){
  obs=$('#cancelObs').val();
  if(confirm("Se cancelara la requisicion seleccionada, desea continuar?") == true) {
    $.ajax({
      url:"ajax.php",
      type: 'POST',
      data:{opcion:'cancelReq',req:req,obs:obs},
      success: function(r){
        jQuery("#jq_arbol").trigger("reloadGrid");
        window.location='index.php?modulo='+modulo;
      }
    });
  }else{

  }
}

function arbolsin(){
  $('.frurl', window.parent.document).attr('src','../../modulos/xtructur/index.php?modulo=arbols');
}

function arbolcon(){
  $('.frurl', window.parent.document).attr('src','../../modulos/xtructur/index.php?modulo=arbol');
}

function sedai(){

  id_insumo=$('#claveadi').val();

  if(id_insumo>0){


    nomb = $("#claveadi option:selected").text();
    $('#codigo_clave').val(nomb);
    $('#descripcion').val('Cargando...');
    $('#unidtext').val('Cargando...');
    $('#precio_costo').val('Cargando...');

     $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'desc_insumos',id_insumo:id_insumo},
        success: function(r){
          console.log(r);
          if(r.success==1){
            $('#descripcion').prop('disabled', true);
            $('#unidtext').prop('disabled', true);
            $('#precio_costo').prop('disabled', true);

            $('#lainfo_adic_load').remove();
            $('#descripcion').val(r.datos[0].descripcion);
            $('#unidtext').val(r.datos[0].unidtext);
            $('#precio_costo').val(r.datos[0].precio);

          }else{
            $('#lainfo_adic_load').remove();
            $('<tr rowpos="3" class="FormData" id="lainfo_adic_load">\
                          <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                          <td class="DataTD">&nbsp;\
                            No existe el insumo seleccionado\
                          </td>\
                        </tr>').insertAfter('#lainfo_adic');
          }
        }
      });
   }
}

function seda(){
  id_recurso=$('#claveadi').val();

  if(id_recurso>0){


    nomb = $("#claveadi option:selected").text();
    $('#codigo_clave').val(nomb);
    $('#descripcion').val('Cargando...');
    $('#unidtext').val('Cargando...');
    $('#precio_costo').val('Cargando...');

     $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'desc_recursos',id_recurso:id_recurso},
        success: function(r){
          console.log(r);
          if(r.success==1){
            $('#descripcion').prop('disabled', true);
            $('#unidtext').prop('disabled', true);
            $('#precio_costo').prop('disabled', true);

            $('#lainfo_adic_load').remove();
            $('#descripcion').val(r.datos[0].descripcion);
            $('#unidtext').val(r.datos[0].unidtext);
            $('#precio_costo').val(r.datos[0].precio_costo);

          }else{
            $('#lainfo_adic_load').remove();
            $('<tr rowpos="3" class="FormData" id="lainfo_adic_load">\
                          <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                          <td class="DataTD">&nbsp;\
                            No existe el insumo seleccionado\
                          </td>\
                        </tr>').insertAfter('#lainfo_adic');
          }
        }
      });
   }
}



function sedadi(){
  id_recurso=$('#claveadi').val();

  if(id_recurso>0){

    $('#Descripcion').val('Cargando...');
    $('#Unidad').val('Cargando...');
    $('#precio').val('Cargando...');
    $('#Codigo').val($('#claveadi option:selected').text());
     $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'desc_recursos',id_recurso:id_recurso},
        success: function(r){
          console.log(r);
          if(r.success==1){
            $('#Descripcion').prop('disabled', true);
            $('#Unidad').prop('disabled', true);
            $('#precio').prop('disabled', true);
            $('#Descripcion').val(r.datos[0].descripcion);
            $('#Unidad').val(r.datos[0].unidtext);
            $('#precio').val(r.datos[0].precio_costo);

          }else{
            $('#lainfo_adic_load').remove();
            $('<tr rowpos="3" class="FormData" id="lainfo_adic_load">\
                          <td class="CaptionTD"><font color="#cecece">&nbsp;</font></td>\
                          <td class="DataTD">&nbsp;\
                            No existe el insumo seleccionado\
                          </td>\
                        </tr>').insertAfter('#lainfo_adic');
          }
        }
      });
   }
}


function guardarvt(){
salir=0;
nomenor=0;
  entrada = $('.quis_rec_').map(function() {
      rcant =$(this).attr('rcant');
      rant =$(this).attr('rant');
      if(this.value*1>rcant*1){
        salir=1;
      }
      if(this.value*1>rant){
        return this.name+'='+this.id+'='+this.value; //area=insumo=vol
      }
      if(this.value*1==rant){

      }
      if(this.value*1<rant){
        /*
        $(this).val(rant);
        nomenor=1;
        */
        return this.name+'='+this.id+'='+this.value; //area=insumo=vol
      }
  }).get().join(', ');

  if(nomenor==1){
    alert('Solo puedes modificar un volumen tope aumentando su cantidad, no disminuyendola.');
    return false;
  }
/*
  if(salir==1){
    alert('Las cantidades de volumen tope sobrepasan a la cantidad de contrato, verificar vol tope');
    return false;
  }
*/
  if(entrada==''){
    alert('No se detectaron cambios');
    return false;
  }

  

  

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    data: {opcion:'save_topes',entrada:entrada},
    success: function(r){
        alert('Volumenes tope guardados con exito');
       // window.location='index.php?modulo='+modulo;
    }
  });

}

function generaSal(id){
  desBoton('#btngenl','Procesando...');

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();

  ccosto=$('#ccosto').val();

  id_oc = $('.ccbox:checked').map(function() {
    return this.value;
  }).get().join(', ');

  if(id_oc==""){
    alert('Selecciona una orden de compra');
    habBoton('#btngenl','Generar vale de salida');
    return false;
  }
  noceros=0;
noagotada=0;
  salir=0;
  entrada = $('.quis_'+id_oc+'_').map(function() {
    rcant =$(this).attr('rcant');
    noceros+=this.value*1
    if(this.value*1>rcant*1){
      salir=1;
    }
    if(this.value*1!=rcant*1){
      noagotada++;
    }
    return this.name+'='+this.id+'='+this.value; //req=insumo=entrada
  }).get().join(', ');

  if(noceros==0){
    alert('No puedes realizar salidas en ceros');
    habBoton('#btngenl','Generar vale de salida');
    return false;
  }

  if(salir==1){
    alert('Las cantidades de salida sobrepasan a lo que esta en almacen, verificar salidas');
    habBoton('#btngenl','Generar vale de salida');
    return false;
  }

  recibio=$('#val_recibio').val();
  entrego=$('#val_entrego').val();
  autorizo=$('#val_autorizo').val();

  iduserlog=$('#iduserlog').val();
  if(iduserlog==0){
    alert('No hay datos de usuario logueado');
    return false;
  }
/*
  if(autorizo==0){
    alert('Seleccione un almacenista para poder continuar');
    habBoton('#btngenl','Generar vale de salida');
    return false;
  }
*/
  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0 || ccosto==0){
    alert('Selecciona la planeacion y una cuenta de costo');
    habBoton('#btngenl','Generar vale de salida');
    return false;
  }
  fecente=$('#fecente').val();
  obs=$('#obs').val();

  btnval = $('#btngenl').val();
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    data: {opcion:'save_sali',id:id,autorizo:autorizo,fecente:fecente,obs:obs,id_oc:id_oc,entrada:entrada,recibio:recibio,entrego:entrego,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,ccosto:ccosto,noagotada:noagotada,iduserlog:iduserlog},
    success: function(r){
        alert('Salida de almacen generada con exito');
        window.location='index.php?modulo='+modulo;
    }
  });
}

function generaRem(ses,sema,idrem){
    desBoton('#btngenrem','Procesando...');

    sema = $('#desta').val();
    monto = $('#montah').val();

    x=0;
    tp=uf($('#ttp').val());
    ra=uf($('#montah').val());
    solicito=$('#val_solicito').val();

    iduserlog=$('#iduserlog').val();

    if(iduserlog==0){
      alert('Selecciona un tecnico.');
      habBoton('#btngenrem','Generar Remesa');
      return false;

    }

    if(sema==0){
      alert('Selecciona una semana.');
      habBoton('#btngenrem','Generar Remesa');
      return false;

    }

    if(monto==0){
      alert('El monto no puede ser 0.');
      habBoton('#btngenrem','Generar Remesa');
      return false;

    }

    if(ra==''){
      alert('La remesa autorizada no puede estar vacia.');
      habBoton('#btngenrem','Generar Remesa');
      return false;

    }

      xnx = $('.quis__').map(function() {

         fp = $(this).parent().nextAll("td").find('select').val();

        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }
        return this.id+'='+this.name+'='+uf(this.value)+'='+$(this).attr('est')+'='+fp; //id_ps,id_esti,imp_sem,proviene 
      }).get().join(', ');


    $.ajax({
      url:'ajax.php',
      type: 'POST',
      data: {opcion:'save_remesa',xnx:xnx,sema:sema,tp:tp,ra:ra,solicito:solicito,idrem:idrem,monto:monto,iduserlog:iduserlog},
      success: function(r){
        if(r=='REP'){
          alert('Ya tienes una remesa creada esta semana');
          return false;
        }else if(r==1){
          alert('Remesa generada con exito');
          window.location='index.php?modulo='+modulo;
        }
      }
    });
}

function generaCob(ses,sema,idrem){
    desBoton('#btngenrem','Procesando...');

    sema = $('#desta').val();
    monto = $('#montah').val();

    x=0;
    tp=uf($('#ttp').val());
    total=$('#ttp').val();
    ra=uf($('#montah').val());
    solicito=$('#val_solicito').val();

    iduserlog=$('#iduserlog').val();

    if(iduserlog==0){
      alert('Selecciona un tecnico.');
      habBoton('#btngenrem','Cobrar');
      return false;

    }

    if(sema==0){
      alert('Selecciona una semana.');
      habBoton('#btngenrem','Cobrar');
      return false;

    }

    if(monto==0){
      alert('El monto no puede ser 0.');
      habBoton('#btngenrem','Cobrar');
      return false;

    }

    if(monto>tp){
      alert('El monto no puede ser mayor al total.'+monto);
      habBoton('#btngenrem','Cobrar');
      return false;

    }

    if(ra==''){
      alert('El cobro autorizado no puede estar vacio.');
      habBoton('#btngenrem','Cobrar');
      return false;

    }

      xnx = $('.quis__').map(function() {

        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }
        return this.id+'='+this.name+'='+uf(this.value)+'='+$(this).attr('est'); //id_ps,id_esti,imp_sem,proviene 
      }).get().join(', ');

tp=tp-monto;

    $.ajax({
      url:'ajax.php',
      type: 'POST',
      data: {opcion:'save_cobro',xnx:xnx,sema:sema,tp:tp,ra:ra,solicito:solicito,idrem:idrem,monto:monto,iduserlog:iduserlog},
      success: function(r){
        if(r=='REP'){
          alert('Ya tienes un cobro creado esta semana');
          return false;
        }else if(r==1){

          alert('Cobro generado con exito');
          window.location='index.php?modulo='+modulo;
        }
      }
    });
}

function actualizarRemesa(){
  window.location='index.php?modulo='+modulo;
}


function cambiaMatOC2(){
  id_insumo = $('#clavematerial').val();
    //$('.FormData#lainfo_adic').remove();
    if(id_insumo>0){
      //$('#clavematerial').html('<option value="0">Cargando...</option>');

      $.ajax({
          url:'ajax.php',
          type: 'POST',
          dataType: 'JSON',
          data: {opcion:'desc_insumos',id_insumo:id_insumo},
          success: function(r){
            console.log(r.datos);
            if(r.success==1){
              formulario='<div class="row">\
                <div class="col-sm-3">\
                    Unidad\
                </div>\
                <div class="col-sm-9">\
                     <input id="volant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].unidtext+'">\
                </div>\
              </div>\
              <div class="row">\
                <div class="col-sm-3">\
                    Vol. Anterior\
                </div>\
                <div class="col-sm-9">\
                     <input id="volant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos.vol_anterior+'">\
                </div>\
              </div>\
              <div class="row">\
                <div class="col-sm-3">\
                    Cantidad\
                </div>\
                <div class="col-sm-9">\
                     <input id="canti" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="">\
                </div>\
              </div>\
              <div class="row">\
                <div class="col-sm-3">\
                    Maxima Cantidad\
                </div>\
                <div class="col-sm-9">\
                     <input id="totcant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos.totcant.totcant+'">\
                </div>\
              </div>\
              <div class="row">\
                <div class="col-sm-3">\
                    Descripcion\
                </div>\
                <div class="col-sm-9">\
                     <input id="volant" disabled="disabled" type="text" role="textbox" class="FormElement ui-widget-content ui-corner-all" style="width: 278px;" value="'+r.datos[0].descripcion+'">\
                </div>\
              </div>';

              $('#agregaDatosOC').html(formulario);
            }
          }
      });
      /*
      $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'desc_insumos_mat',id_clave:id_clave},
        success: function(r){
          if(r.success==1){
            $('#clavematerial').html('<option value="0">Seleccione</option>');
            $.each(r.datos, function( k, v ) {
              $('#clavematerial').append('<option value="'+v.id+'">'+v.clave+'</option>');
            });
          }else{
            $('#clavematerial').html('<option value="0">No hay insumos</option>');
          }
        }
      });
      */
    }
}

function cambiaMatOC(){
  id_material = $('#fammaterial').val();
    //$('.FormData#lainfo_adic').remove();
    if(id_material>0 || id_material=='t'){
      $('#clavematerial').html('<option value="0">Cargando...</option>');
      $.ajax({
        url:'ajax.php',
        type: 'POST',
        dataType: 'JSON',
        data: {opcion:'desc_insumos_mat',id_material:id_material},
        success: function(r){
          if(r.success==1){
            $('#clavematerial').html('<option value="0">Seleccione</option>');
            $.each(r.datos, function( k, v ) {
              $('#clavematerial').append('<option value="'+v.id+'">'+v.clave+'</option>');
            });
          }else{
            $('#clavematerial').html('<option value="0">No hay insumos</option>');
          }
        }
      });
    }
}

function guardaDesglo(impdesglo){

       xnx = $('.cquis__').map(function() {
        por=$(this).val();

        id=$(this).attr('id');
        idsplit=id.split('_');
        id=idsplit[1];
        console.log(id);
        return id+'='+por;
        //if(this.value==''){ ocero=0; }else{ ocero=this.value; }
        //cnue=$('#c_'+idtemp+'_'+this.id).val();
        //return idtemp+'>'+this.name+'='+this.id+'='+ocero+'='+cnue;
      }).get().join(',');
         


      $.ajax({
        url:'ajax.php',
        type: 'POST',
        data: {opcion:'save_desglo',xnx:xnx,impdesglo:impdesglo},
        success: function(r){

            alert('Desgloce de indirectos guardado con exito');
            window.location='index.php?modulo='+modulo;

        }
      });


  }

function generaPed2(id){
    x=0;
    //desBoton('#btngenped','Procesando...');
    lalala = $('.ccbox:checked').map(function() {
      idtemp = this.value;
      t=0;
       xnx = $('.quis_'+idtemp+'_').map(function() {
        vasel = $('.selopp_'+idtemp+':eq('+t+')').val();


        if(this.value==''){ ocero=0; }else{ ocero=this.value; }
        cnue=$('#c_'+idtemp+'_'+this.id).val();
        t++;
        return idtemp+'>'+this.name+'='+this.id+'='+ocero+'='+cnue+'='+vasel;
      }).get().join(', ');
             x++;
      return xnx;


    }).get().join('/');
if(x==1){
   // lalala = lalala+'/';
}

    pro=0;
    condpago=$('#condpago').val();
    solicito=$('#val_solicito').val();
    fpago=$('#val_fpago').val();
    obsgen=$('#obsgen').val();

    iduserlog=$('#iduserlog').val();
    if(iduserlog==0){
      alert('Seleccione un tecnico para poder continuar');
      habBoton('#btngenped','Generar Pedido');
      return false;
    }
    if(fpago==0){
      alert('Seleccione una forma de pago');
      habBoton('#btngenped','Generar Pedido');
      return false;
    }
    fecente=$('#fecente').val();
    if(fecente==0){
      alert('Seleccione la Fecha');
      return false;
     }
     
    $.ajax({
      url:'ajax.php',
      type: 'POST',
      dataType: 'JSON',
      data: {opcion:'save_pedi',id:id,solicito:solicito,pro:pro,fecente:fecente,lalala:lalala,fpago:fpago,obsgen:obsgen,condpago:condpago,iduserlog:iduserlog},
      success: function(r){
        if(r.success==1){
          alert('Pedido generado con exito');
          window.location='index.php?modulo='+modulo;
        }
      }
    });
}

function guardacheque(){
  salir=0;
  id_oc = $('.ccbox:checked').map(function() {
    return this.value;
  }).get().join(', ');

  if(id_oc==""){
    alert('Selecciona una estimacion');
    return false;
  }

noc= $('#noc').val();
val= $('#val').val();
ban= $('#ban').val();
fee= $('#fee').val();
estc= $('#estc').val();
estf= $('#estf').val();
remesa= $('#noremesah').val();
  
  if(remesa=="0"){
    alert('Seleccione una estimacion');
    return false;
  }

  if(estc=="0"){
    alert('Seleccione un estatus del cheque');
    return false;
  }

  if(estf=="0"){
    alert('Seleccione un estatus de la factura');
    return false;
  }

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    data: {opcion:'save_cheque',id_est:id_oc,noc:noc,val:val,ban:ban,fee:fee,estc:estc,estf:estf,remesa:remesa},
    success: function(r){
        alert('Cheque guardado con exito');
        window.location='index.php?modulo='+modulo;
    }
  });

}

function guardacheque2(){
  salir=0;
  id_oc = $('.ccbox:checked').map(function() {
    return this.value;
  }).get().join(', ');

  if(id_oc==""){
    alert('Selecciona una estimacion');
    return false;
  }

noc= $('#noc').val();
val= $('#val').val();
ban= $('#ban').val();
fee= $('#fee').val();
estc= $('#estc').val();
estf= $('#estf').val();
remesa= $('#noremesah').val();
valpago= $('#val_fpago').val();
  
  if(remesa=="0"){
    alert('Seleccione una estimacion');
    return false;
  }

  

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    data: {opcion:'save_cheque2',id_est:id_oc,noc:noc,val:val,ban:ban,fee:fee,estc:estc,estf:estf,remesa:remesa,valpago:valpago},
    success: function(r){
        alert('Cobro guardado con exito');
        window.location='index.php?modulo='+modulo;
    }
  });

}

function generaEnt(id){
  
  desBoton('#btngenl','Procesando...');
  salir=0;
  id_oc = $('.ccbox:checked').map(function() {
    return this.value;
  }).get().join(', ');

  if(id_oc==""){
    alert('Selecciona una orden de compra');
    habBoton('#btngenl','Generar Entrada');
    return false;
  }

  noagotada=0;
  entrada = $('.quis_'+id_oc+'_').map(function() {
    rcant =$(this).attr('rcant');
    if(this.value*1>rcant*1){
      salir=1;
    }
    if(this.value*1!=rcant*1){
      noagotada++;
    }
    return this.name+'='+this.id+'='+this.value; //req=insumo=entrada
  }).get().join(', ');

  if(salir==1){
    var r = confirm("Las cantidades de entrada sobrepasan a lo pedido en la orden de compra, ¿Desea continuar?");
    if (r == true) {
       
    } else {
        habBoton('#btngenl','Generar Entrada');
      return false;
    }
    //alert('Las cantidades de entrada sobrepasan a lo pedido en la orden de compra, verificar entradas');
  }

  solicito=$('#val_solicito').val();

  iduserlog=$('#iduserlog').val();

  if(iduserlog==0){
    alert('Seleccione un almacenista para poder continuar');
    habBoton('#btngenl','Generar Entrada');
    return false;
  }

  fecente=$('#fecente').val();
  obs=$('#obs').val();

  btnval = $('#btngenl').val();
  $('#btngenl').prop('disabled', true);
  $('#btngenl').val('Procesando...');
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    data: {opcion:'save_entri',id:id,solicito:solicito,fecente:fecente,obs:obs,id_oc:id_oc,entrada:entrada,noagotada:noagotada,iduserlog:iduserlog},
    success: function(r){
        alert('Entrada de almacen generada con exito');
        window.location='index.php?modulo='+modulo;
    }
  });
}

function agotarEntradas(idEntrada){
  var r = confirm("Al finalizar esta entrada ya no se podra recibir mas material, ¿Desea continuar?");
  if (r == true) {
    $.ajax({
      url:'ajax.php',
      type: 'POST',
      data: {opcion:'agotarEntrada',idEntrada:idEntrada},
      success: function(r){
          alert('Entrada agotada con exito');
          window.location='index.php?modulo='+modulo;
      }
    });
  }else{
    
  }
}

function agotarSalidas(idSalida){
  var r = confirm("Al finalizar ya no podra salir mas material, ¿Desea continuar?");
  if (r == true) {
    $.ajax({
      url:'ajax.php',
      type: 'POST',
      data: {opcion:'agotarSalida',idSalida:idSalida},
      success: function(r){
          alert('Salida agotada con exito');
          window.location='index.php?modulo='+modulo;
      }
    });
  }else{
    
  }
}

function generaNominaTe(id_des,sd,ed){
  //
  total=$('#total').val();
  ccosto=$('#ccosto').val();
  solicito=$('#val_solicito').val();
  var ids = $('#row_proforma').jqGrid('getDataIDs');
  var dts = $("#row_proforma").jqGrid("getCol", "diast");

  var he = $("#row_proforma").jqGrid("getCol", "hre");
  var idt = $("#row_proforma").jqGrid("getCol", "importedt");
  var ihe = $("#row_proforma").jqGrid("getCol", "importehr");
  var desci = $("#row_proforma").jqGrid("getCol", "descinf");
  var finis = $("#row_proforma").jqGrid("getCol", "fini");
  var subt = $("#row_proforma").jqGrid("getCol", "subtotal1");
  var totallist = $("#row_proforma").jqGrid("getCol", "totalpago");

  if(solicito==0){
    alert('Seleccione un tecnico para poder continuar');
    return false;
  }
  if(ccosto==0){
    alert('Seleccione una cuenta de costo');
    return false;
  }

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_nominaca',id_des:id_des,solicito:solicito,ccosto:ccosto,total:total,sd:sd,ed:ed,ids:ids,dts:dts,he:he,idt:idt,ihe:ihe,desci:desci,finis:finis,subt:subt,totallist:totallist},
    success: function(r){
      if(r.success==1){
        alert('Nomina generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });

}

function generaNomina(id_des,sd,ed,id_edif){
  total=$('#total').val();
  totale=$('#totale').val();
  ccosto=$('#ccosto').val();
  solicito=$('#val_solicito').val();


  var ids = $('#row_proforma').jqGrid('getDataIDs');
  var dts = $("#row_proforma").jqGrid("getCol", "diast");

  var he = $("#row_proforma").jqGrid("getCol", "hre");
  var df = $("#row_proforma").jqGrid("getCol", "diasf");
  var idt = $("#row_proforma").jqGrid("getCol", "importedt");
  var ihe = $("#row_proforma").jqGrid("getCol", "importehr");
  var idf = $("#row_proforma").jqGrid("getCol", "impdf");
  var desci = $("#row_proforma").jqGrid("getCol", "descinf");
  var finis = $("#row_proforma").jqGrid("getCol", "fini");
  var subt = $("#row_proforma").jqGrid("getCol", "subtotal1");
  var difd = $("#row_proforma").jqGrid("getCol", "difd");
  var totallist = $("#row_proforma").jqGrid("getCol", "totalpago");

  var xtotal = 0;
    for (var i = 0; i < totallist.length; i++) {
    xtotal += totallist[i] << 0;
  }


  if(solicito==0){
    alert('Seleccione un tecnico para poder continuar');
    return false;
  }

  if(ccosto==0){
    alert('Seleccione una cuenta de costo');
    return false;
  }

  

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_nominades',id_des:id_des,solicito:solicito,ccosto:ccosto,id_edif:id_edif,total:total,totale:totale,sd:sd,ed:ed,ids:ids,dts:dts,he:he,df:df,idt:idt,ihe:ihe,idf:idf,desci:desci,finis:finis,subt:subt,difd:difd,totallist:totallist},
    success: function(r){
      if(r.success==1){
        alert('Nomina generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaPed(id){
  pro=$('#val_pro').val();
  solicito=$('#val_solicito').val();
  if(solicito==0){
    alert('Seleccione un tecnico para poder continuar');
    return false;
  }
  fecente=$('#fecente').val();
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_pedi',id:id,solicito:solicito,pro:pro,fecente:fecente},
    success: function(r){
      if(r.success==1){
        alert('Pedido generado con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEstCli(id,id_des,sema){
  imp_con=uf($('#imp_con').val());
  ade1=uf($('#ade1').val());
  ade2=uf($('#ade2').val());
  ade3=uf($('#ade3').val());
  imp_cont=uf($('#imp_cont').val());
  anti=uf($('#anti').val());
  iaa=uf($('#iaa').val());
  iae=uf($('#iae').val());
  tota=uf($('#tota').val());
  poramo=uf($('#poramo').val());

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();

  cargaagr=0;
  cargaesp=0;
  cargaare=0;
  cargapart=0;

  id_aut=$('#val_solicito').val();
  
  imp_est=uf($('#imp_est').val());
  fgarantia=uf($('#fgarantia').val());
  subt1=uf($('#subt1').val());
  subt2=uf($('#subt2').val());
  iva=uf($('#iva').val());
  retencion=uf($('#retencion').val());
  cargos=uf($('#cargos').val());
  total=uf($('#total').val());

  fgp=uf($('#fgp').val());
  rep=uf($('#rep').val());

  modiFac=$('#modiFac').val();

  if((imp_est*1)==0 ){
    alert('El importe de estimacion no puede ser 0.00');
    return false;
  }

  
  /*
  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion y una cuenta de costo');
    return false;
  }
  */
  
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_cliente',id_des:id_des,id:id,subt1:subt1,retencion:retencion,cargos:cargos,total:total,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,id_aut:id_aut,imp_con:imp_con,imp_cont:imp_cont,ade1:ade1,ade2:ade2,ade3:ade3,anti:anti,iaa:iaa,iae:iae,tota:tota,poramo:poramo,imp_est:imp_est,fgarantia:fgarantia,subt2:subt2,iva:iva,fgp:fgp,rep:rep,sema:sema,modiFac:modiFac},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEstSub(id,id_des,sema){
  desBoton('#btngenestsub','Procesando...');
  imp_con=uf($('#imp_con').val());
  ade1=uf($('#ade1').val());
  ade2=uf($('#ade2').val());
  ade3=uf($('#ade3').val());
  imp_cont=uf($('#imp_cont').val());
  anti=uf($('#anti').val());
  iaa=uf($('#iaa').val());
  iae=uf($('#iae').val());
  tota=uf($('#tota').val());
  poramo=uf($('#poramo').val());

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();
  ccosto=$('#ccosto').val();
  id_aut=$('#val_solicito').val();
  
  imp_est=uf($('#imp_est').val());
  fgarantia=uf($('#fgarantia').val());
  subt1=uf($('#subt1').val());
  subt2=uf($('#subt2').val());
  iva=uf($('#iva').val());
  retencion=uf($('#retencion').val());
  cargos=uf($('#cargos').val());
  total=uf($('#total').val());

  fgp=uf($('#fgp').val());
  rep=uf($('#rep').val());

  fact=$('#fact').val();
  
  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0 || ccosto==0){
    alert('Selecciona la planeacion y una cuenta de costo');
    habBoton('#btngenestsub','Generar Estimacion');
    return false;
  }

  if( (imp_est*1)==0){
    alert('La estimacion no puede estar en 0.00');
    return false;
  }
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_subcontratista',id_des:id_des,id:id,subt1:subt1,retencion:retencion,cargos:cargos,total:total,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,id_aut:id_aut,imp_con:imp_con,imp_cont:imp_cont,ade1:ade1,ade2:ade2,ade3:ade3,anti:anti,iaa:iaa,iae:iae,tota:tota,poramo:poramo,ccosto:ccosto,imp_est:imp_est,fgarantia:fgarantia,subt2:subt2,iva:iva,fgp:fgp,rep:rep,fact:fact,sema:sema},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEst_cc(id,id_des){
  desBoton('#btngenestcc','Procesando...');
  id_aut=$('#val_solicito').val();
  //id_cc=$('#id_cc').val();
  subt1=uf($('#subt1').val());
  //iva=uf($('#iva').val());
  imp_est=uf($('#imp_est').val());
  total=uf($('#total').val());

  if((imp_est*1)==0){
    alert('La estimacion no puede estar en 0.00');
    return false;
  }

  if(id_aut==0){
    alert('Selecciona quien autoriza y una cuenta de costo');
    habBoton('#btngenestcc','Generar Estimacion');
    return false;
  }


  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_chica',id_des:id_des,id:id,subt1:subt1,total:total,id_aut:id_aut,imp_est:imp_est,iva:0},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEst_Pro(id,id_des,id_prov,id_oc){
  desBoton('#btngenestpro','Procesando...');
  id_aut=$('#val_solicito').val();
  id_cc=$('#id_cc').val();
  subt1=uf($('#subt1').val());
  iva=uf($('#iva').val());
  imp_est=uf($('#imp_est').val());
  total=uf($('#total').val());
  fact=$('#fact').val();

  if(imp_est==0){
    alert('Esta estimacion esta en $0.00, verificar datos');
    habBoton('#btngenestpro','Generar Estimacion');
    return false;
  }

  if(id_aut==0 || id_cc==0){
    habBoton('#btngenestpro','Generar Estimacion');
    alert('Selecciona quien autoriza y una cuenta de costo');
    return false;
  }

  if(fact==''){
    habBoton('#btngenestpro','Generar Estimacion');
    alert('Falta el campo factura');
    return false;
  }

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_prov',id_des:id_des,id:id,subt1:subt1,total:total,id_aut:id_aut,imp_est:imp_est,id_cc:id_cc,iva:iva,fact:fact,id_prov:id_prov,id_oc:id_oc},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEst_Ind(id,id_des){
  desBoton('#btngenestind','Procesando...');
  id_aut=$('#val_solicito').val();
  id_cc=$('#ccosto').val();
  subt1=uf($('#subt1').val());
  iva=uf($('#iva').val());
  imp_est=uf($('#imp_est').val());
  total=uf($('#total').val());
  fact=$('#fact').val();
  id_prov=$('#val_pro').val();


  if(id_aut==0 || id_cc==0){
    alert('Selecciona quien autoriza y una cuenta de costo');
    habBoton('#btngenestind','Generar Estimacion');
    return false;
  }

  if(fact==''){
    alert('Falta el campo factura');
    habBoton('#btngenestind','Generar Estimacion');
    return false;
  }

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_indirectos',id_des:id_des,id:id,subt1:subt1,total:total,id_aut:id_aut,imp_est:imp_est,id_cc:id_cc,iva:iva,fact:fact,id_prov:id_prov},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaEst(id,id_des,sema){
  desBoton('#btngenest','Procesando...');
  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();
  id_aut=$('#val_solicito').val();

  ccosto=0;

 /* if(ccosto==0){
    alert('Selecciona una cuenta de cargo');
    return false;
  }
*/
  subt1=uf($('#subt1').val());
  retencion=uf($('#retencion').val());
  cargos=uf($('#cargos').val());
  total=uf($('#total').val());
  rep=uf($('#rep').val());

/*
  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion');
    habBoton('#btngenest','Generar Estimacion');
    return false;
  }
  */

  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_est_destajista',id_des:id_des,id:id,subt1:subt1,retencion:retencion,cargos:cargos,total:total,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,id_aut:id_aut,sema:sema,ccosto:ccosto,rep:rep},
    success: function(r){
      if(r.success==1){
        alert('Estimacion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function guardaTraspaso(id,obrasal,obraent){
  ids = $('#jq_requisiciones').jqGrid('getDataIDs');
  desBoton('#btnGenReq','Procesando...');
  if(ids==''){
    alert('No tienes ningun material agregado en el traspaso');
    habBoton('#btnGenReq','Guardar traspaso');
    return false;
  }

  solicito=$('#val_solicito').val();
  iduserlog=$('#iduserlog').val();
   fentrada=$('#fenvio').val();
    resalida=$('#resalida').val();
     rentrada=$('#reentrada').val();


  if(resalida==0 || reentrada==0){
    alert('Selecciona quien recibe traspaso de entrada y salida ');
    habBoton('#btnGenReq','Guardar traspaso');
    return false;
  }

  if(fenvio==''){
    alert('Selecciona una fecha de envio');
    habBoton('#btnGenReq','Guardar traspaso');
    return false;
  }




  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_traspaso',id:id,solicito:iduserlog,iduserlog:iduserlog,obrasal:obrasal,obraent:obraent,resalida:resalida,rentrada:rentrada,fentrada:fentrada},
    success: function(r){
      if(r.success==1){
      
        alert('Traspaso generado con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });

}

function cambiarObras(){
  window.location='index.php?modulo='+modulo;
}

function generaReq(id){

  ids = $('#jq_requisiciones').jqGrid('getDataIDs');
  desBoton('#btnGenReq','Procesando...');
  if(ids==''){
    alert('No tienes ningun material agregado en la requisicion');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }


fecente = $('#fecente').val();
  if(fecente==0){
    alert('Seleccione la Fecha');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  solicito=$('#val_solicito').val();
  iduserlog=$('#iduserlog').val();

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();



  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  if(iduserlog==0){
    alert('Seleccione un tecnico para poder continuar');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }


  //fecente=0;//$('#fecente').val();
  
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_requi',id:id,solicito:solicito,fecente:fecente,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,iduserlog:iduserlog},
    success: function(r){
      if(r.success==1){
        alert('Requisicion generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaext(id){

  ids = $('#jq_requisiciones').jqGrid('getDataIDs');
  desBoton('#btnGenReq','Procesando...');
  if(ids==''){
    alert('No tienes ningun material agregado en la requisicion');
    habBoton('#btnGenReq','Generar solicitud');
    return false;
  }


fecente=$('#fecha').val();
  if(fecente==0){
    alert('Seleccione la Fecha');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  total=$('#total').val();


  solicito=$('#val_solicito').val();
  iduserlog=$('#iduserlog').val();

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();



  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  if(iduserlog==0){
    alert('Seleccione un tecnico para poder continuar');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }


  //fecente=0;//$('#fecente').val();
  
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_extra',id:id,solicito:solicito,fecente:fecente,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,iduserlog:iduserlog,total:total},
    success: function(r){
      if(r.success==1){
        alert('Soicitud generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generaadi(id){

  ids = $('#jq_requisiciones').jqGrid('getDataIDs');
  desBoton('#btnGenReq','Procesando...');
  if(ids==''){
    alert('No tienes ningun material agregado en la requisicion');
    habBoton('#btnGenReq','Generar solicitud');
    return false;
  }


fecente=$('#fecha').val();
  if(fecente==0){
    alert('Seleccione la Fecha');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  total=$('#total').val();


  solicito=$('#val_solicito').val();
  iduserlog=$('#iduserlog').val();

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();



  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  if(iduserlog==0){
    alert('Seleccione un tecnico para poder continuar');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }


  //fecente=0;//$('#fecente').val();
  
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_adi',id:id,solicito:solicito,fecente:fecente,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,iduserlog:iduserlog,total:total},
    success: function(r){
      if(r.success==1){
        alert('Soicitud generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function generanocob(id){

  ids = $('#jq_requisiciones').jqGrid('getDataIDs');
  desBoton('#btnGenReq','Procesando...');
  if(ids==''){
    alert('No tienes ningun material agregado en la requisicion');
    habBoton('#btnGenReq','Generar solicitud');
    return false;
  }


fecente=$('#fecha').val();
  if(fecente==0){
    alert('Seleccione la Fecha');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  total=$('#total').val();


  solicito=$('#val_solicito').val();
  iduserlog=$('#iduserlog').val();

  cargaagr=$('#cargaagr').val();
  cargaesp=$('#cargaesp').val();
  cargaare=$('#cargaare').val();
  cargapart=$('#cargapart').val();



  if(cargaagr==0 || cargaesp==0 || cargaare==0 || cargapart==0){
    alert('Selecciona la planeacion');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }

  if(iduserlog==0){
    alert('Seleccione un tecnico para poder continuar');
    habBoton('#btnGenReq','Generar requisicion');
    return false;
  }


  //fecente=0;//$('#fecente').val();
  
  $.ajax({
    url:'ajax.php',
    type: 'POST',
    dataType: 'JSON',
    data: {opcion:'save_nocob',id:id,solicito:solicito,fecente:fecente,ag:cargaagr,ar:cargaesp,es:cargaare,pa:cargapart,iduserlog:iduserlog,total:total},
    success: function(r){
      if(r.success==1){
        alert('Soicitud generada con exito');
        window.location='index.php?modulo='+modulo;
      }
    }
  });
}

function cambio(opcion,id){
    if(opcion==0){
      id=0;
      url='jsagrupador.php';
    }else if(opcion==1){
      url='jsarea.php';
    }else if(opcion==2){
      url='jsespecialidad.php';
    }else if(opcion==3){
      url='jspartida.php';
    }else if(opcion==4){
      url='jsrecurso.php';
    }
    $.ajax({
      url:url,
      type: 'POST',
      //dataType: 'JSON',
      data: {id:id},
      success: function(r){
        $('#contjs').html(r);
      }
    });
} 

function eliminar_pres(id_pres){
    if (confirm("Se eliminara el presupuesto y los recursos relacionados, desea continuar?") == true) {
        $.ajax({
            url:"elimina_presu.php",
            type: 'POST',
            data:{id_pres:id_pres},
            success: function(r){
                    window.location='index.php?modulo='+modulo;
            }
        });
    }else{

    }
}

function back(opcion){
    if(opcion==0){
      window.location="importar_xls.php";
    }
    if(opcion==1){
      window.location="tab2.php?id_obra="+id;
    }
  
}
function obra_acceder(modulo){
	id_obra = $('#selobra').val();
    $.ajax({
        url:"start_session.php",
        type: 'POST',
        dataType:'JSON',
        data:{id_obra:id_obra,modulo:modulo},
        success: function(r){
            if(r.success==1){
                window.location='index.php?modulo='+modulo;
            }else{
                alert('Error al seleccionar esta obra');
            }
            
            //
        }
    });
}

function cerrar_session(modulo){
    $.ajax({
        url:"close_session.php",
        type: 'POST',
        data:{},
        success: function(r){
            window.location='index.php?modulo='+modulo;
        }
    });
}

function quitarasign(id,ido){
  var r = confirm("Esta seguro de quitar esta asignacion?");
  if(r==true) {
    $.ajax({
        url:"ajax.php",
        type: 'POST',
        data:{opcion:'quitarasign',id:id},
        success: function(r){
            $("#as_"+id+"_as").remove();
            u = $('#va_'+ido+'_va').text()*1;
            d=u-1;
            $('#va_'+ido+'_va').text(d)*1;
        }
    });
  }
}

function verasignados(id){
  $.ajax({
    url:"ajax.php",
    type: 'POST',
    dataType:'JSON',
    data:{opcion:'verasignados',id_recurso:id},
    success: function(r){
      $("#dialog-confirm").html('');
      if(r.success==1){
        cad='<table style="width:500px;font-size:11px;">\
              <tr>\
                <!--<td width="80"><b>Agrupador</b></td>-->\
                <td width="120"><b>Area</b></td>\
                <td width="110"><b>Especialidad</b></td>\
                <td width="200"><b>Partida</b></td>\
                <td width="40">&nbsp;</td>\
              </tr>';

        $.each( r.datos, function( i, v ) {
          cad+='<tr id="as_'+v.id+'_as">\
                  <!--<td>'+v.agrupador+'</td>-->\
                  <td>'+v.area+'</td>\
                  <td>'+v.especialidad+'</td>\
                  <td>'+v.partida+'</td>\
                  <td><input class="btn btn-danger btnMenu" type="button" style="cursor:pointer; width:60px;" onclick="quitarasign('+v.id+','+id+');" value="Quitar"></td>\
                </tr>';
        });
        cad+='</table>';
        $("#dialog-confirm").html(cad);
      }else{
        $("#dialog-confirm").html('Este recurso no esta asignado a ninguna partida');
      }
      $("#dialog-confirm").dialog({
          width:620,
          height:300,
          modal:true,
          dialogClass:"ui-jqdialog",
          closeOnEscape: false,
          buttons:{
              'Aceptar': function(){
                  //jQuery("#jq_asignacion").trigger("reloadGrid");
                  $(this).dialog('close');
              }
          }
      });
    }
  });
}

function chcc(){
      $('#chcosto').css('visibility','hidden');
      $('#ll2').css('display','block');
      cmbcc=$('#cmbcc').val();

      if(cmbcc>0){
        $.ajax({
            url:"ajax.php",
            type: 'POST',
            //async: false,
            dataType:'JSON',
            data:{opcion:'cmbcc',cmbcc:cmbcc},
            success: function(resp){

              $('#ll2').css('display','none');
              $('#chcosto').prop("disabled", false); // Element(s) are now enabled.
              $('#chcosto').css('visibility','visible');

              if(resp.success==1){
                $('#chcosto').html('<option value="0" selected="selected">Selecciona una cuenta de costo</option>');
                $.each(resp.datos, function (index, data) {
                  $('#chcosto').append('<option value="'+data.id+'">'+data.costo+'</option>');
                })
              }else{
                 $('#chcosto').html('<option value="0" selected="selected">No hay cuentas de costo</option>');
              }
            }
        });
      }
    }

function chcosto1(){
      $('#ccosto').css('visibility','hidden');
      $('#ll2').css('display','block');
      chcosto=$('#chcosto').val();

      if(chcosto>0){
        $.ajax({
            url:"ajax.php",
            type: 'POST',
            //async: false,
            dataType:'JSON',
            data:{opcion:'chcosto',chcosto:chcosto},
            success: function(resp){

              $('#ll2').css('display','none');
              $('#ccosto').prop("disabled", false); // Element(s) are now enabled.
              $('#ccosto').css('visibility','visible');

              if(resp.success==1){
                $('#ccosto').html('<option value="0" selected="selected">Selecciona una cuenta de cargo</option>');
                $.each(resp.datos, function (index, data) {
                  $('#ccosto').append('<option value="'+data.id+'">'+data.cargo+'</option>');
                })
              }else{
                 $('#ccosto').html('<option value="0" selected="selected">No hay cuentas de cargo</option>');
              }
            }
        });
      }
    }

function chagru2(){
      $('#cargaesp').css('visibility','hidden');
      $('#ll2').css('display','block');
      idagru=$('#cargaagr').val();

      if(idagru>0){
        $.ajax({
            url:"ajax.php",
            type: 'POST',
            //async: false,
            dataType:'JSON',
            data:{opcion:'chagru',idagru:idagru},
            success: function(resp){

              $('#ll2').css('display','none');
              $('#cargaesp').prop("disabled", false); // Element(s) are now enabled.
              $('#cargaesp').css('visibility','visible');

              if(resp.success==1){
                $('#cargaesp').html('<option value="0" selected="selected">Selecciona un area</option>');
                $.each(resp.datos, function (index, data) {
                  $('#cargaesp').append('<option value="'+data.id+'">'+data.nombre+'</option>');
                })
              }else{
                 $('#cargaesp').html('<option value="0" selected="selected">No hay areas</option>');
              }
            }
        });
      }
    }

function chesp2(){
      ids = jQuery("#jq_asignacion").jqGrid('getGridParam','selarrrow');
      $('#cargaare').css('visibility','hidden');
      $('#ll3').css('display','block');
      idesp=$('#cargaesp').val();
      if(idagru>0 ){
        $.ajax({
            url:"ajax.php",
            type: 'POST',
            //async: false,
            dataType:'JSON',
            data:{opcion:'chesp',idesp:idesp},
            success: function(resp){
              $('#ll3').css('display','none');
              $('#cargaare').prop("disabled", false); // Element(s) are now enabled.
              $('#cargaare').css('visibility','visible');
              
              if(resp.success==1){
                $('#cargaare').html('<option value="0" selected="selected">Selecciona una especialidad</option>');
                $.each(resp.datos, function (index, data) {
                  $('#cargaare').append('<option value="'+data.id+'">'+data.nombre+'</option>');
                })
              }else{
                 $('#cargaare').html('<option value="0" selected="selected">No hay especialidades</option>');
              }

            }
        });
      }
    }
function charea2(){
      $('#cargapart').css('visibility','hidden');
      $('#ll4').css('display','block');
      idarea=$('#cargaare').val();
      if(idarea>0 ){
        $.ajax({
            url:"ajax.php",
            type: 'POST',
            dataType:'JSON',
            data:{opcion:'charea',idarea:idarea},
            success: function(resp){

              $('#ll4').css('display','none');
              $('#cargapart').prop("disabled", false); // Element(s) are now enabled.
              $('#cargapart').css('visibility','visible');

              if(resp.success==1){
                $('#cargapart').html('<option value="0" selected="selected">Selecciona</option>');
                $.each(resp.datos, function (index, data) {
                  $('#cargapart').append('<option value="'+data.id+'">'+data.nombre+'</option>');
                })
              }else{
                 $('#cargapart').html('<option value="0" selected="selected">No hay partidas</option>');
              }
            }
        });
      }
    }