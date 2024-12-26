<?php 
$semana = strftime('%V');
week_bounds(date('Y-m-d'), $start, $end);

$SQL = "SELECT a.*, concat('DEST-',b.id,' -  ',b.nombre,' ',b.paterno,' ',b.materno) nombre FROM constru_altas a inner join constru_info_tdo b on b.id_alta=a.id where a.id_obra='$idses_obra' and a.borrado=0 AND a.id_tipo_alta=2;";
  $result = $mysqli->query($SQL);
  if($result->num_rows>0) {
    while($row = $result->fetch_array() ) {
      $maestros[]=$row;
    }
  }else{
    $maestros=0;
  }
  ?>
<div class="row">&nbsp;</div>
    <div class="panel panel-default" >
      <!-- Panel Heading -->
      <div class="panel-heading">
      <div class="panel-title">Elaboracion Nomina Tec-Admon Oficina Campo</div>
      </div><!-- End panel heading -->

      <!-- Panel body -->
      <div class="panel-body" >
          <div class="row">
  <div class="col-md-7">

    <div class="row">
      <div class="col-md-6">
        <label>Periodo:</label><br>
        <?php echo $start; ?> al <?php echo $end; ?> | <?php echo 'Semana: '.$semana; ?>
      </div>
      <div class="col-md-6">
        <label>Tecnico:</label>
        <select class="form-control" id="desta">
          <option selected="selected" value="2">Tecnicos oficina campo</option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6" style="padding-top: 8px;">
        <label>Selecciona semana:</label><br>
        <div class="week-picker" style="width:100% !important;"></div>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-6">
            <label>Inicio:</label>
            <input class="form-control" id="startDate">
          </div>
          <div class="col-md-6">
            <label>Fin:</label>
            <input class="form-control" id="endDate">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-md-offset-6" style="padding-top: 10px;">
           <button class="btn btn-primary btn-xm pull-right" onclick="verprenominatec();" > Ver nomina</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
          
      </div><!-- ENd panel body -->
    </div>



<div class="row">
  <div class="col-sm-12" id="vernomina">
    
  </div>
</div>
<script>     
    $(function() {
    var startDate;
    var endDate;
    
    var selectCurrentWeek = function() {
        window.setTimeout(function () {
            $('.week-picker').find('.ui-datepicker-current-day a').addClass('ui-state-active')
        }, 1);
    }
$('.week-picker').datepicker( {
        minDate: '<?php echo $obra_ini; ?>',
        maxDate: '<?php echo $obra_fin; ?>',
        showOtherMonths: true,
        selectOtherMonths: true,
        firstDay: 1,
        dateFormat: "yy-mm-dd",
        onSelect: function(dateText, inst) { 
            var myGrid = $('#row_proforma'),
            id = myGrid.jqGrid ('getGridParam', 'selrow');

            var date = $(this).datepicker('getDate');
          //  console.log(date.getDate()+0);
            
            if(date.getDay()==0){
              gd=7;
            }else{
              gd=date.getDay();
            }
           // console.log(gd);
            startDate = new Date(date.getFullYear(), date.getMonth(), (date.getDate()+0) - gd+1);
           // console.log(startDate);
            endDate = new Date(date.getFullYear(), date.getMonth(), (date.getDate()+0) - gd+7);
            var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;

            st=$.datepicker.formatDate( dateFormat, startDate, inst.settings );
            fn=$.datepicker.formatDate( dateFormat, endDate, inst.settings );
            $('#startDate').val(st);
            $('#endDate').val(fn);

            selectCurrentWeek();
        },
        beforeShowDay: function(date) {
            var cssClass = '';
          /*  console.log(date);
            console.log(startDate);
            console.log(endDate);
*/
            if(date >= startDate && date <= endDate)
                cssClass = 'ui-datepicker-current-day';

            return [true, cssClass];
        },
        onChangeMonthYear: function(year, month, inst) {
            selectCurrentWeek();
        }
        /*,
        beforeShowDay: function(date) {
      //    alert(8);
var day = date.getDay();
return [day != 0,''];
}
*/
  });
});
  </script> 

