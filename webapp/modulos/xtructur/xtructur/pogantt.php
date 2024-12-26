<link rel="stylesheet" type="text/css" href="dhtmlxgantt.css"   title="estilo"  />
<script src="dhtmlxgantt.js" type="text/javascript"></script>
<script src="gant.espanol.js" type="text/javascript"></script>
<script src="dhtmlxgantt_tooltip.js"></script>

<style type="text/css">
	.grid_h0{ background-color: #c3c3c3 !important; border: 1px solid #c3c3c3;}
	.grid_h1{ background-color: #d3d3d3 !important;}
	.grid_h2{ background-color: #e3e3e3 !important;}
	.grid_h3{ background-color: #f3f3f3 !important;}

	.colores{ background-color: #f3f3f3 !important; color:#ff0000;}


#lc_chat_layout{
	display:none;
}

</style>

<div class="col-md-12" style="padding:0px; width: 100%; margin-left:0%;">
 <div id="gantt_here" style="width: 100%; height: 100%;"></div>
</div>

<div class="row">
&nbsp;
</div>
<div class="row" style="margin:0px;">
<button class="btn btn-primary btn-xs" onclick="guardarGantt();"> Guardar cambios</button>
<div class="col-md-12" style="padding:0px;height:200px;">
</div>
</div>



 <script>
$(function() {

	verGantt('<?php echo $idses_obra;?>',1);

});


function verGantt(idobra,por){

	var demo_tasks='';
	$.ajax({
        url:"ajax.php",
        type: 'POST',
        async: false,
        data:{opcion:'data_gantt',por:por},
        success: function(r){
          	demo_tasks=r;            
        }
    });

	gantt.config.lightbox.sections = [
		{name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
		{name: "time", height: 72, type: "duration", map_to: "auto"}
	];

	gantt.config.scale_unit = "month";
	gantt.config.date_scale = "%F, %Y";

	gantt.config.task_height = 15;
	gantt.config.row_height = 25;

	gantt.config.min_column_width = 50;
	gantt.config.scale_height = 60;

	gantt.config.subscales = [
		{unit:"day", step:1, date:"%j, %D" }
	];

	gantt.config.columns = [
	    {name:"text",       label:"Concepto",  width:"200", tree:true, template:myFunc },
	    {name:"start_date", label:"Inicio", align: "center" },
	    {name:"duration",   label:"Duracion",   align: "center" }
	];

	gantt.init("gantt_here");
	gantt.clearAll();

	function myFunc(task){

	    return "<div class='important' style='font-size:12px;'>"+task.text+"</div>";
	  
	};

	gantt.templates.grid_row_class = function( start, end, task ){
	    if ( task.$level == 0 ){ return "grid_h0"; }
	    if ( task.$level == 1 ){ return "grid_h1"; }
	    if ( task.$level == 2 ){ return "grid_h2"; }
	    if ( task.$level == 3 ){ return "grid_h3"; }

	};

	gantt.templates.tooltip_text = function(start,end,task){
		/*
		desc='';
		$.ajax({
	        url:"ajax.php",
	        type: 'POST',
	        async: false,
	        data:{opcion:'desc_gantt',codigo:task.text},
	        success: function(r){
	          	desc=r;
	        }
	    });
	    */
	    return "<b>Descripcion:</b> "+task.text+"<br/><b>Duracion:</b> " + task.duration;
	};

	gantt.templates.task_text=function(start,end,task){
		corte = task.text.split(' |  | ');
		return corte[0];
	    //return "<b>Text:</b> "+task.text+",<b> Holders:</b> "+task.users;
	};

	gantt.config.drag_resize = false;
	gantt.config.drag_move = false;
	gantt.config.drag_progress = false;

/*
	gantt.templates.task_row_class  = function(start, end, task){
	    if(task.$level<4){
	          return 'meeting_task';
	    }
	};
*/
		
	gantt.parse(demo_tasks);
}

function guardarGantt(){

	var json = gantt.serialize();

	if(confirm("Se van a realizar cambios en el gantt, desea continuar?") == true) {
	$.ajax({
        url:"ajax.php",
        type: 'POST',
        async: false,
        data:{opcion:'save_gantt',links:json.links},
        success: function(r){
        	alert('Cambios realizados con exito');
        	window.location.reload();
        }
    });
	}
}
</script>

