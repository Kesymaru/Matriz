/**
* JAVASCRIPT PARA LOS PROYECTOS
*/
	
/**
* MUESTRA PRPOYECTO
* @param id -> id del proyecto
*/
function Proyecto(id){
	notifica("mostrando proyecto "+id);
		
	if(!$("#menu").is(":visible")){
		ActivaMenu()
	}

	if($("#content").html() !== ""){
		LimpiarContent();
	}

	CategoriasRoot(id);
}

/**
* CATEGORIAS ROOT
* @param proyecto -> id del proyecto
*/
function CategoriasRoot(proyecto){
	var queryParams = {"func" : "CategoriasRoot", "proyecto" : proyecto};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			
			if(response.length > 0){
				$("#menu").html(response);
			}else{
				notificaError("Error: "+response);
			}

		},
		fail: function(response){
			notificaError("Error: "+response);
		}
	});
}


/**
* SELECCIONA UNA CATEGORIA PADRE Y CARGA SUS HIJOS
*/
function PadreHijos(padre, proyecto){
	if(!$("#menu2").is(":visible")){
		Menu2();
	}

	$("#categorias li").hasClass("root-selected");

	$("#"+padre).addClass("root-selected");

	$("#menu2").html('<div id="subcategorias" class="subcategorias" /></div>');

	Hijos(padre, proyecto);

}	


/**
* CARGA LOS HIJOS DE UN PADRE SELECCIONADO
*/
function Hijos(padre, proyecto){

	//LimpiarHermanos(padre, proyecto);		

	var queryParams = {'func' : "Hijos", "padre" : padre, "proyecto" : proyecto};

	//carga hijos
	$.ajax({
		data: queryParams,
		type: "post",
		async: false,
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){

				$("#subcategorias").append(response);


				var totalWidth = 0;

				$("#subcategorias li").each(function(index){
					totalWidth += parseInt($(this).width(), 10);
				});
				totalWidth += $("#Padre"+padre).width();

				$("#subcategorias").animate({
					"width" : totalWidth
				},700, function(){
					$("#subcategorias").css('width', totalWidth);
				});

			}else{
				notificaError("Error: "+response);
			}
		},
		fail: function(){
			notificaError("Error: ocurrio un error :(<br/>Codigo: ajaxEdicion 001.");
		}
	});
}

/**
* LIMPIA EL CAMINO DEL ARBOL DE CATEGORIAS
* @param padre -> id del padre
*/
function LimpiarCamino(padre, proyecto){

	//BORRA HIJOS
	if( $("#Padre"+padre).length ){
		
		$("#Padre"+padre).fadeOut(500, function(){
			$("#Padre"+padre).remove();
		});
		
		//obtiene los hijos del padre seleccionado
		var queryParams = {'func' : 'GetHijos', 'padre' : padre};
		$.ajax({
			data: queryParams,
			type: "post",
			url: "src/ajaxProyectos.php",
			beforeSend: function(){
			},
			success: function(response){
				if(response.length > 0){
					var hijos = $.parseJSON(response); 
					
					//alert(response);
					$.each(hijos, function(f,c, proyecto){
						LimpiarCamino(c, proyecto);
					});

				}else{
					//no hay hijos que borrar
				}
			},
			fail: function(){
				notificaError("Error: ocurrio un error.<br/>Codigo: ajaxEdicion 001.");
			}
		});
	}

}

/**
* BORRAR LOS HERMANOS DE UN NODO
* @param padre
*/
function LimpiarHermanos(padre, proyecto){
	//BORRA HERMANOS ASINCRONAMENTE
	var queryParams = {'func' : 'GetHermanos', 'padre' : padre, 'proyecto' : proyecto};
	
	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforeSend: function(){
		},
		success: function(response){
			if(response.length > 0){
				
				var hermanos = $.parseJSON(response); 
				
				$.each(hermanos, function(f,c){
					if($("#Padre"+c).length ){
						LimpiarCamino(c);						
					}
				});
			}
		},
		fail: function(){
			notificaError("Error: AJAX fail.<br/>"+response);
		}
	}).done(function ( data ) {
		  LimpiarCamino(padre);
	});

}

/**
* PONE ESTILO PARA CARGAR HIJO COMO SELECCIONADO
* @param hijo -> id hijo seleccionado
*/
function SeleccionaHijo(hijo){

	var padre = $("#"+hijo).closest("div").attr('id');

	$("#"+padre+' li').removeClass('seleccionada');
	$("#"+hijo).addClass('seleccionada');

	var padre = $('#'+hijo).closest('div').attr('id');
	
	if( padre == '0'){
		//si es supercategoria el menu varia
		//ContextMenuSuperCategoria(hijo);
	}else{
		//ContextMenuCategoria(hijo);
	}
}

/**
* CARGA LAS NORMAS DE LA CATEGORIA
* @param $id -> id categoria
*/
function Categoria(id){
	$("#content").html("cargando datos de "+id);

	var queryParams = {"func" : "Categoria", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			notifica(response);
			if(response.length > 0){
				$("#content").html(response);
			}else{
				notificaError("Error: "+response);
			}
			
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Categoria()<br/>"+response);
		}
	});
}

/**
 * SELECCIONA UNA NORMA
 */
function SelectNorma(id){
	/*$(".datos li").removeClass("seleccionada");
	$("#"+id).addClass("seleccionada");

	//doble click para mostrar la norma
	$("#"+id).dblclick(function(){
		Norma(id);
		return;
	});
*/
}

/**
 * MUESTRA DATOS DE UNA NORMA
 * @param $id -> id norma
 */
function Norma(id){

	var queryParams = {"func" : "Norma", "id" : id};

	$.ajax({
		data: queryParams,
		type: "post",
		url: "src/ajaxProyectos.php",
		beforesend: function(){
		},
		success: function(response){
			notifica(response);
			if(response.length > 0){
				$("#contenido").html(response);
			}else{
				notificaError("Error: "+response);
			}
			
		},
		fail: function(response){
			notificaError("Error: AJAX fail Proyectos.js Norma()<br/>"+response);
		}
	});
}
