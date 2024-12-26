  $(document).ready(function(){
    $("#fechaNacimientoPiloto").datepicker({dateFormat:'yy-mm-dd'});
  });

  //$("#nombrePiloto").select2();

  function modalAgregarPiloto(){
    $("#modalAgregarPiloto").modal("show");
  }
  function guardarPiloto(){
    var idPiloto                = $("#idPiloto").val();
    var nombrePiloto            = $("#nombrePiloto").val();
    var apellidosPiloto         = $("#apellidosPiloto").val();
    var fechaNacimientoPiloto   = $("#fechaNacimientoPiloto").val();
    var numeroCertificadoPiloto = $("#numeroCertificadoPiloto").val();
    var idEstatusPiloto         = $("#idEstatusPiloto").val();

    var flag = false;

    if(nombrePiloto == ""){
      $("#lblNombrePiloto").html("Campo vacio...");
      flag = true;
    }else{
      $("#lblNombrePiloto").html("");
    }
    if(apellidosPiloto == ""){
      $("#lblApellidosPiloto").html("Campo vacio...");
      flag = true;
    }else{
      $("#lblApellidosPiloto").html("");
    }
    if(fechaNacimientoPiloto == ""){
      $("#lblFechaNacimientoPiloto").html("Campo vacio...");
      flag = true;
    }else{
      $("#lblFechaNacimientoPiloto").html("");
    }
    if(numeroCertificadoPiloto == ""){
      $("#lblNumeroCertificadoPiloto").html("Campo vacio...");
      flag = true;
    }else{
      $("#lblNumeroCertificadoPiloto").html("");
    }

    $.ajax({
      url: "ajax.php?c=orden_servicio&f=agregarPiloto",
      type:"POST",
      data:{
        "idPiloto":idPiloto,
        "nombrePiloto":nombrePiloto,
        "apellidosPiloto":apellidosPiloto,
        "fechaNacimientoPiloto":fechaNacimientoPiloto,
        "numeroCertificadoPiloto":numeroCertificadoPiloto,
        "idEstatusPiloto":idEstatusPiloto
      },
      beforeSend: function(e){
        $("#btnAgregarPiloto").attr("disabled", true);
  			$("#btnAgregarPiloto").html("<i class=\"fas fa-sync fa-spin\"></i>");
      },
      success: function(e){
        console.log(e);
        var json = $.parseJSON(e);
        if(json.code == 200){

          alertify.notify('Piloto agregado con éxito', 'success', 5, function(){  console.log('dismissed'); });
          $("#modalAgregarPiloto").modal("hide");
          limpiarCamposPiloto();
          $("#idPiloto").append(json.body);
          $("#btnAgregarPiloto").attr("disabled", false);
          $("#btnAgregarPiloto").html('<i class="fa fa-plus" aria-hidden="true"></i> Agregar');
          $("#lblPiloto").html("Agregago con exito");
          $("#lblPiloto").show("fadeIn");;
          setTimeout(function(){
            $("#lblPiloto").hide("fadeOut");
            $("#lblPiloto").html("");
          }, 4000);


          var dt  = $('#dtPilotos').DataTable();
          dt.row.add($(json.row),0).draw();

          if(idPiloto != 0){
            $("#rowtr"+idPiloto).remove();
          }
        }else{
          alertify.notify('Algo salio mal', 'error', 5, function(){  console.log('dismissed'); });
        }


      },
      error: function(e){

        $("#btnAgregarPiloto").attr("disabled", false);
        $("#btnAgregarPiloto").html('<i class="fa fa-plus" aria-hidden="true"></i> Agregar');
      }
    });
  }


  function limpiarCamposPiloto(){
    $("#nombrePiloto").val("");
    $("#lblNombrePiloto").html("");
    $("#apellidosPiloto").val("");
    $("#lblApellidosPiloto").html("");
    $("#fechaNacimientoPiloto").val("");
    $("#lblFechaNacimientoPiloto").html("");
    $("#numeroCertificadoPiloto").val("");
    $("#lblNumeroCertificadoPiloto").html("");
    $("#idEstatusPiloto").val(0);
  }

  function desactivar(e){
    var idPiloto = $(e).attr("data-id");
    var bActivo  = $(e).attr("data-bactivo");
    $.ajax({
        url: "ajax.php?c=orden_servicio&f=desactivarPiloto",
        type:"POST",
        data:{
          "idPiloto":idPiloto,
          "bActivo":bActivo
        },
        beforeSend: function(e){
          $("#btn"+idPiloto).attr("disabled", true);
    			$("#btn"+idPiloto).html("<i class=\"fas fa-sync fa-spin\"></i>");
        },
        success: function(e){
          if(e == 1){
            if(bActivo == 1){
              alertify.success('Desactivado con exito');
              $("#btn"+idPiloto).removeClass("btn btn-danger");
              $("#btn"+idPiloto).addClass("btn btn-success");
              $("#btn"+idPiloto).attr("disabled", false);
              $("#btn"+idPiloto).html("<em class=\"fas fa-check-circle\"></em>");
              $("#check"+idPiloto).prop("checked",false);
              $("#btn"+idPiloto).attr("data-bactivo",0);
            }else{
              alertify.success('Activado con exito');
              $("#btn"+idPiloto).removeClass("btn btn-success");
              $("#btn"+idPiloto).addClass("btn btn-danger");
              $("#btn"+idPiloto).attr("disabled", false);
              $("#btn"+idPiloto).html("<em class=\"fas fa-trash-alt\"></em>");
              $("#check"+idPiloto).prop("checked",true);
              $("#btn"+idPiloto).attr("data-bactivo",1);
            }
          }else if(e == 0){
            alertify.error('Algo salio mal');
          }

        },
        error: function(e){
          $("#btn"+id).addClass("btn btn-danger");
          $("#btn"+id).removeClass("btn btn-success");
          $("#btn"+id).attr("disabled", false);
          $("#btn"+id).html("<i class=\"fa fa-trash\"></i>");
        }
      });
  }
  function editar(e){
    var idPiloto = $(e).attr("data-id");
    $("#idPiloto").val(idPiloto);
    $.ajax({
        url: "ajax.php?c=orden_servicio&f=getPilotoById",
        type:"POST",
        data:{
          "idPiloto":idPiloto,

        },
        beforeSend: function(e){
          $("#btnedit"+idPiloto).attr("disabled", true);
    			$("#btnedit"+idPiloto).html("<i class=\"fas fa-sync fa-spin\"></i>");
        },
        success: function(e){
          console.log(e);
          var json = $.parseJSON(e);
          $("#nombrePiloto").val(json.vNombre);
          $("#apellidosPiloto").val(json.vApellidos);
          $("#fechaNacimientoPiloto").val(json.dFechaNacimiento);
          $("#numeroCertificadoPiloto").val(json.vNumeroCertificado);
          $("#idEstatusPiloto").val(json.bActivo);
          $("#btnedit"+idPiloto).attr("disabled", false);
          $("#btnedit"+idPiloto).html("<i class=\"fas fa-user-edit\"></i>");
          $("#modalAgregarPiloto").modal("show");

        },
        error: function(e){
            $("#idPiloto").val(0);
        }
      });

  }
