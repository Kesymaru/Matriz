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

					$lista .= '<img src="'.$_SESSION['datos'].$datosCategoria[0]['imagen'].'" /><p>'.$datosCategoria[0]['nombre'].'</p>';

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

	if(!empty($hijos)){ //tiene hijos
		echo '<div class="categoria" id="Padre'.$padre.'">';

		$id = 0;
		$nombre = "";

		echo '<ul>';
		
		foreach ($hijos as $f => $hijo) {

			//carga hijos de la categoria
			echo '<li id="'.$hijo['id'].'" onClick="Hijos('.$hijo['id'].', '.$proyecto.')">'.$hijo['nombre'].'</li>';
		}

		echo '</ul>';
		echo '</div>';

		//tiene hijos por lo tanto no es hoja
		//echo '<script>NormasCategoria('.$padre.');</script>';

	}else{
		//no tiene hijos es una hoja
		if( !$registros->EsRoot($padre) ){
			echo '<script>DatosCategoria('.$padre.');</script>';
		}
	}
}


?>