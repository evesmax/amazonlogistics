$(function(){
	
$("#preloader").hide();
	
});
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
function EliminaProductos(id)
{
	var r = confirm("Estas seguro que deseas eliminar este registro?");
	if (r == true)
	  {
	  x = "You pressed OK!";
	  }
	else
	  {
	  x = "You pressed Cancel!";
	  }
}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	

function paginacionGridProductos($pagina,$filtro,$elimina)
{
		if($pagina==0)
		{
			$pagina=$("#irpagina").val();
		}
		
		$("#preloader").show();
		$.ajax({
		type: 'POST',
		url:'grid.php',
		data:{tabla:"mrp_productos",filtro:$filtro,page:$pagina,paginacion:$("#irpaginacion").val(),elimina:$elimina},
		success: function(resp){  
		
		$("#grid_producto").html(resp);
		$("#preloader").hide();
		
		 }});//end ajax	
		
}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function busquedas(evt,val,campo)
	{
				var key = evt.keyCode;
				if(key==13)
				{
					var filtro=1;
	
						if(val!="")
						{
								switch(campo)
								{
									case 'id': filtro+=" and idProducto='"+val+"' and estatus=1"; break;		
									case 'codigo': filtro+=" and p.codigo like '%"+val+"%' and estatus=1"; break;
									case 'nombre': filtro+=" and p.nombre like '%"+val+"%' and estatus=1"; break;
									case 'departamento': filtro+=" and d.nombre like '%"+val+"%' and estatus=1"; break;
									case 'familia': filtro+=" and f.nombre like '%"+val+"%' and estatus=1"; break;
									case 'linea': filtro+=" and l.nombre like '%"+val+"%' and estatus=1"; break;
									case 'precio': filtro+=" and p.precioventa='"+val+"' and estatus=1"; break;
								}	
						}
					paginacionGridProductos(1,filtro);
					}
			}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
						
