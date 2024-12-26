$(function()
 {
     $("#resultados").hide();
     $("#clientes,#formasPago").select2();
     fechasPredefinidas(0);
 });

function generar()
{
    $.post('ajax.php?c=reportes&f=gastoDatos',
    {
        cliente :$("#clientes").val(),
        rango   :$("#fechas").val(),
        /*forma   :$("#formasPago").val(),*/
        no_vuelo:$("#no_vuelo").val(),
        matricula:$("#matricula").val()
    },
    function(data)
    {
        $("#datos").html(data);
        $("#resultados").show();            
    });
    
}





function fechasPredefinidas(num)
{
    var start = moment();
    var end = moment();
    var fechas;
    var lado;
    if(!num)
    {
        fechas = ".fechas_izq";
        lado = "left";
    }

        $(fechas).daterangepicker({
            locale: {
                        format: 'YYYY-MM-DD',
                        separator: ' / ',
                        "applyLabel": "OK",
                        "cancelLabel": "Cancelar",
                        "fromLabel": "Desde",
                        "toLabel": "Hasta",
                        "customRangeLabel": "Rango de fechas",
                        "weekLabel": "S",
                        "daysOfWeek": [
                            "Do",
                            "Lu",
                            "Ma",
                            "Mi",
                            "Ju",
                            "Vi",
                            "Sa"
                        ],
                        "monthNames": [
                            "Enero",
                            "Febrero",
                            "Marzo",
                            "Abril",
                            "Mayo",
                            "Junio",
                            "Julio",
                            "Agosto",
                            "Septiembre",
                            "Octubre",
                            "Noviembre",
                            "Deciembre"
                        ],
                    },
            startDate: start,
            endDate: end,
            "opens": lado,
            ranges: {
               'Hoy': [moment(), moment()],
               'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Esta semana': [moment().startOf('week'), moment().endOf('week')],
               '1 Semana atrás': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
               '2 Semanas atrás': [moment().subtract(2, 'week').startOf('week'), moment().subtract(2, 'week').endOf('week')],
               '3 Semanas atrás': [moment().subtract(3, 'week').startOf('week'), moment().subtract(3, 'week').endOf('week')],
               'Este mes': [moment().startOf('month'), moment().endOf('month')],
               'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
               'Dos meses atrás': [moment().subtract(2, 'month').startOf('month'), moment().subtract(2, 'month').endOf('month')]
            }
        });    
}