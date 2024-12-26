<?php 
	require_once("conexiondb.php");
	$id_obra =$_GET['id_obra'];
    //$id =$_GET['id'];
    $obra="SELECT obra FROM constru_generales where id='$id_obra';";
    $result = $mysqli->query($obra);
    $row = $result->fetch_array();
    $obra = $row['obra'];

    //$categorias="SELECT b.semana from constru_estimaciones_bit_cliente b inner join constru_estimaciones_cliente a on a.id_bit_cliente=b.id left join constru_recurso c on c.id=a.id_insumo WHERE a.id_obra='$id_obra' order by b.semana asc;";
    //$series="SELECT a.sestmp from constru_estimaciones_bit_cliente b inner join constru_estimaciones_cliente a on a.id_bit_cliente=b.id left join constru_recurso c on c.id=a.id_insumo WHERE a.id_obra='$id_obra' order by b.semana asc;";
    $categorias="SELECT a.id, a.imp_estimacion, a.xxano as semana FROM constru_estimaciones_bit_cliente a where a.id_obra='$id_obra' and a.borrado=0  order by a.semana asc";
    $series="SELECT a.id, a.imp_estimacion as sestmp, a.semana FROM constru_estimaciones_bit_cliente a where a.id_obra='$id_obra' and a.borrado=0  order by a.semana asc";
    
    $name="Semana";
    $name1="Destajisas";
    $serie1 = "";
    $filename = "gra_estcliet".$id_obra;

 ?>

 <!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Estimaciones al Cliente</title>

        <script src="js/jquery-1.10.2.min.js"></script>
        <style type="text/css">
${demo.css}
        </style>
        <script type="text/javascript">
$(function () {
    $('#container').highcharts({
        
        title: {
            text: 'Estimaciones al Cliente de '+'<?php echo $obra; ?>',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },

        xAxis: {
            title: {
                text: '<?php echo $serie1;?>'
            },
            categories: [
            <?php
            $sql = $categorias;
            $result = $mysqli->query($sql);
            while($row = $result->fetch_array()) 
            {
             ?>
                '<?php echo $row["semana"] ?>',
             <?php 
            }
              ?>
            ]
        },
        yAxis: {
            title: {
                text: 'Retencion'
            },
            labels: {
                formatter: function () {
                    //return this.value / 1000 + 'k'; 
                    
                    return '$' + this.value;
                    
                }
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: 'MXN'
        },
        legend: {
            layout: 'vertical',
            align: 'center',
            verticalAlign: 'bottom',
            borderWidth: 0
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '${point.y:,.2f}'
                }
            }
        },

        series: [{
            name: '<?php echo $name?>',
            data: [
            <?php
            $sql = $series;
            $result = $mysqli->query($sql);
            while($row = $result->fetch_array()) 
            {
                ?>
                    <?php //echo $row["retencion"]

                            $estimacion = $row["sestmp"]; // solo se quita la sumatoria
                            echo $estimacion;
                                          
                     ?>,      
                <?php
            }
            ?>
            ]

            },
           
            ],

            exporting: {
                sourceWidth: 1200,
                sourceHeight: 500,
                filename: '<?php echo $filename ?>',
            },

    });
});
        </script>
    </head>
    <body>
<script src="highcharts/js/highcharts.js"></script>
<script src="highcharts/js/modules/exporting.js"></script>

<div class="row">
    <div class="col-xs-12 tablaResponsiva">
      <div class="table-responsive" id="container" style="height: 500px; margin: 0 auto">
          
      </div>
    </div>
</div> 

    </body>
</html>