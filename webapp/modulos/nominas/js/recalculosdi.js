 $(document).ready(function(){


	 $('#tablasdi').DataTable( {
	 	"destroy": true,
	    "language": {
	      "url": "js/Spanish.json"
	    },
	    "lengthMenu": [ 5,10, 25, 50, 75, 100 ],
	    "info": true
	  });

   
  


	});

 function cargarConceptos(idEmpleado){

  $('#myModal').modal('show');


    // var table = $('#tableconceptos').DataTable(
    // {
    //     "destroy": true,
    //     "paging": false,
    //     "info": false,
    //     "language": {
    //     "url": "js/Spanish.json"
    //     }
    // });
    // table.clear().draw();

   $.ajax({
      url:"ajax.php?c=Sobrerecibo&f=cargarconceptossdi",
      type: 'POST',
      dataType:'json',
      data:{ idEmpleado:idEmpleado 
      },
      success: function(r){
          // var datax=JSON.stringify(r);
          // console.log(datax);


     
    if(r.success==1){



    // var table = $('#tableconceptos').DataTable(
    // {
    //   "destroy": true,
    //     "paging": false,
    //     "info": false,
    //     "language": {
    //     "url": "js/Spanish.json"
    //     }
    // });
   



      $('#tableconceptos').DataTable( {
        "destroy": true,
        "paging": false,
        "info": false,
        "language": {
        "url": "js/Spanish.json"
        },
        
      //   "ajax": {
      //       "url": "ajax.php?c=Sobrerecibo&f=cargarconceptossdi",
      //       "data":{ idEmpleado:idEmpleado 
      // }
      //   },
        "data": r.data,
        "columns": [

            { "data":"descripcion"}, 
            { "data": "gravado"}
             ]
    });


       }
       else{

        var table=$('#tableconceptos').DataTable( {
        "destroy": true,
        "paging": false,
        "info": false,
        "language": {
        "url": "js/Spanish.json"
        },
     
      "columns": [
    null,
    null]
    });
        table.clear().draw();
        
      }
}
  });



  


 }





$(function() {

$('#existeSdiBimestral').on('click', function(evt) { 
   
        $.ajax({
            url:"ajax.php?c=Sobrerecibo&f=existeSDIbimestral",
            type: 'POST',
            dataType:'json',
            success: function(r){
                var existeptu = r[0].existeptu;  
                var prenominaautorizados = r[0].prenominaautorizados;

                if (existeptu ==0){
                    existeSdiBimestral();
                }
                else {

                    if(confirm("Ya tiene un cálculo de Bimestre,¿Desea reemplazarlo?")){
                        if (prenominaautorizados >0){
                            //alert ("Existen recibos PTU del ejercicio autorizados, no puede reemplazar la informacion."); 
                        }
                        else
                            existeSdiBimestral();
                    } 
                }
            },
            error: function(r){
                alert(r);
            }
        });
    return 0;
});

existeSdiBimestral = function(){

  $.ajax({
    url:"ajax.php?c=Sobrerecibo&f=guardarSDIbimestral",
    type: 'POST',
    dataType:'json',
    data:
    {   
    // montoRepartir: $("#montoRepartir").val(),
    // descontarincidencias:  $("#descontarincidencias").val(),
    // ejercicio:$("#ejercicio").val(),
    // ptu:$("#ptu").val()

},
success: function(r){

  if(r==1){
    alert("Guardado.");
    
    var answer = confirm ("Recuerde calcular su nomina para que considere su nuevo SDI en las cuotas del IMSS.")
    if (answer){
       window.parent.preguntar=false;
       window.parent.quitartab("tb2282",2282,"Calculo de Prenomina");
       window.parent.agregatab('../../modulos/nominas/index.php?c=Prenomina&f=vistaPrenomina','Calculo de Prenomina','',2282);
       window.parent.preguntar=true;
    }else{}

  }else if (r=2) {
    alert("No tiene registrado ningun periodo Extraordinario.");

  }else{
    alert("Error.");
  } 
},
error: function(e){
  alert("Error.");
}
});
}


$('#cargarSDI').on('click', function() { 

    var btnguardar = $(this);
    btnguardar.button("loading");
    var status = true;

     $.ajax({
      url:"ajax.php?c=Sobrerecibo&f=cargarecalculosdi",
      type: 'POST',
      dataType:'json',
      data:{},
      success: function(r){
    
		if(r.success==1 ){

       	$('#tablasdi').DataTable( {
	      "destroy": true,
        //"bProcessing": true,
	      "language": {
        "url": "js/Spanish.json"
        },
        // "ajax": {
        //     "url": "ajax.php?c=Sobrerecibo&f=cargarecalculosdi",
        //     // "dataSrc": ""
        // },
        "data": r.data,
        "columns": [
            { "data":"nombreEmpleado"}, 
            { "data": "sdiactivo"},
            { "data": "diasbimestre"},
            { "data": "incapacidades"},
            { "data": "faltasPermiCast"},
            { "data": "totaldiasrecalculo"},
            { "data": "sumaingresosvariables"},
            { "data": "partevariable"},
            { "data": "SDInuevo"},
            { "data": "inivigencia"},
            { "data": "finalvigencia"},
            { "data": "idEmpleado",
            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            $(nTd).html("<button type='button' class='btn btn-block btn-primary' onclick=cargarConceptos('"+oData.idEmpleado+"');>ver</a>");}}
        	]
    });
         btnguardar.button('reset');

       }
       
       else{}
}
  });
   });
//fin de funcion de  almacenhoras
//






});

