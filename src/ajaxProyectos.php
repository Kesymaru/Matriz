<?php
/**
* AJAX PARA PROYECTOS EN CLIENTES
*/

require_once("class/proyectos.php");
require_once("class/registros.php");

if(isset($_POST['func'])){
	switch ($_POST['func']) {

		//CARGA LAS CATEGORIAS ROOT DE UN PROYECTO
		case 'CategoriasRoot':
			if(isset($_POST['proyecto'])){
				CategoriasRoot($_POST['proyecto']);
			}

		//OBTIENE LOS HIJOS DE UN PADRE SELECCIONADO
		case 'Hijos':
			if(isset($_POST['padre']) && isset($_POST['proyecto'])){
				Hijos( $_POST['padre'], $_POST['proyecto'] );
			}
			break;

		//OBTIENE LOS IDS DE LOS HIJOS DE UN PADRE
		case 'GetHijos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHijos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//OBTIENE LOS IDS DE LOS HERMANOS DE UN PADRE
		case 'GetHermanos':
			if( isset($_POST['padre']) ){
				$registros = new Registros();
				//el id de todos los hijos
				$hijos = $registros->getTodosHermanos($_POST['padre']);
				echo json_encode($hijos);
			}
			break;

		//DATOS DE UNA CATEGORIA
		case 'Categoria':
			if(isset($_POST['id'])){
				Categoria($_POST['id']);
			}
			break;

		//CARGA NORMA
		case 'Norma':
			if(isset($_POST['id'])){
				Norma($_POST['id']);
			}
			break;
	}

}

/**
* CATEGORIAS ROOT DE UN PROYECTO
*/
function CategoriasRoot($proyecto){
	$registros = new Registros();

	$datos = $registros->getRegistros($proyecto);

	$lista = '';

	if(!empty($datos)){
		$categorias = unserialize($datos[0]['registro']);

		$lista .= '<ul class="categorias">';

		if(!empty($categorias)){
			foreach ($categorias as $key => $categoria) {
				
				$datosCategoria = $registros->getCategoriaDatos($categoria);

				if($datosCategoria[0]['padre'] == 0){
					
					$lista .= '<li class="" id="'.$categoria.'" onClick="PadreHijos('.$categoria.','.$proyecto.')">';

					$lista .= '<img title="'.$datosCategoria[0]['nombre'].'" src="'.$_SESSION['datos'].$datosCategoria[0]['imagen'].'" /><p>'.$datosCategoria[0]['nombre'].'</p>';

					$lista .= '</li>';
				}
			}
		}else{
			$lista .= '<li>No hay datos</li>';
		}

		$lista .= '</ul>';
	}else{

	}

	echo $lista;
}

/**
* CARGA CATEGORIAS HIJAS DE UN PADRE
* @param $padre -> id del padre
*/
function Hijos($padre, $proyecto){
	$registros = new Registros();
	$hijos = $registros->getHijos($_POST['padre']);

	$lista = "";

	if(!empty($hijos)){ //tiene hijos
		$datos = $registros->getRegistros($proyecto);
		$disponibles = unserialize($datos[0]['registro']);

		$lista .= '<div class="categoria" id="Padre'.$padre.'">';

		$lista .= '<ul>';
		
		foreach ($hijos as $f => $hijo) {

			foreach ($disponibles as $s => $incluida) {
				if($incluida == $hijo['id']){
					$lista .= '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
				}else{
					continue;
				}
			}
			//carga hijos de la categoria
			//$lista .= '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
		}

		$lista .= '</ul>';
		$lista .= '</div>';

		//tiene hijos por lo tanto no es hoja
		//echo '<script>NormasCategoria('.$padre.');</script>';

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			$lista .= '<script>Categoria('.$padre.','.$proyecto.');</script>';
		}
	}

	echo $lista;
}

/**
 * CARGA LAS NORMAS DE LA CATEGORIA
 * @param $id -> id de la categoria
 */
function Categoria($id){
	$registros = new Registros();
	$datos = $registros->getCategoriaDatos($id);

	if(!empty($datos)){
		$normas = unserialize($datos[0]['normas']);
		
		$lista = '<div>
					<input type="hidden" id="categoria" value="'.$id.'" />
				  </div>
				  <div id="contenido">
				  <div class="titulo" id="normas" >
					'.$datos[0]['nombre'].'
					<hr>
					</div>
					<div class="datos">
				<uL>';

		foreach ($normas as $key => $norma) {
			$datosNormas = $registros->getDatosNorma($norma);

			$lista .= '<li id="'.$datosNormas[0]['id'].'" onClick="SelectNorma('.$datosNormas[0]['id'].')">'.$datosNormas[0]['nombre'].'</li>';

		}

		$lista .= '</ul>
					</div>
					</div>';
	}
	echo $lista;
}

/**
 * MUESTRA DATOS DE UNA NORMA
 */
function Norma($id){
	$registros = new Registros();
	$datos = $registros->getDatosNorma($id);

	$lista = '';

	if(!empty($datos)){
		$lista .= '<div class="titulo">
					'.$datos[0]['nombre'].'
				<hr>
				</div>
				<div class="datos">';

		foreach ($datos as $fila => $norma) {
			foreach ($norma as $titulo => $valor) {
				$lista .= '<div class="box">
								<div class="titulo">
									'.$titulo.'
								</div>
								'.$valor.'
							</div>';
			}
		}

		echo $lista;
	}
}


?>