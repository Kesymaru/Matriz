<?php

/**
* CLASE MASTER METODOS PARA EL INDEX.PHP
*/

require_once('class/session.php');
require_once('class/classDatabase.php');
require_once('class/usuarios.php');
require_once('class/proyectos.php');

class Master{

	/**
	* AL SER DECLARADO DETERMINA SI EL USUARIO ESTA LOGUEADO
	*/
	public function __construct(){
		$session = new Session();
		//seguridad de que el usuario este logueado
		$session->Logueado();
	}

	//busca en proyectos, presenta solo los del cliente logueado
	private function BuscarProyectos($busqueda){
		$contador = 0;
		$resultado = '';
		$resultadoTemp = '';

		$query = "SELECT * FROM proyectos WHERE nombre LIKE '%".$busqueda."%' LIMIT 0, 30";
		$base = new Database();
		
		$datos = $base->Select($query);

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				$resultadoTemp .= '<div class="resultado"><ul class="etiqueta"><li><a href="#">Proyecto';
				$resultadoTemp .= '</a></li></ul>'.$datos[$fila]['nombre'].'</div>';
				$contador++;
			}
			$resultado .= $this->Plural($contador, "Proyecto");
			$resultado .= $resultadoTemp."</div>";
			return $resultado;
		}else{
			return '';
		}
	}


/******************************** MENUS ********************/

	/**
	* DATOS PARA EL MENU DEL ADMIN
	*/
	public function MenuCliente(){
		$admin = new Admin();

		echo '<li onClick="editar();"><img src="';
		echo $admin->getAdminDato("imagen");
		echo '" /></li>';

		echo '<li><button onClick="EditarAdmin();">Editar</button>';
		echo '<button onClick="LogOut();">Salir</button></li>';

	}

	/**
	* HERRAMIENTA PARA LA BUSQUEDA PONE PLURALES Y LA CANTIDAD DE RESULTADOS
	*/
	private function Plural($contador, $titulo){
		$plural = '';
		if($contador > 0){
			$plural .= '<div class="resultados">
							<div class="titulo">'.$contador.' Resultado';
			if($contador > 1){
				$plural .='s'; //plural para resultado(s)
			}

			$plural .= ' para '.$titulo;
			if($contador > 1){
				$plural .='s'; //plural para Categoria(s)
			}
			$plural .= "</div>";

			return $plural;
		}
	}

	/**
	* IMPRIME LOS DATOS DE LA SESSION
	*/
	public function ImprimirSession(){
		echo $_SESSION['nombre'];
		echo $_SESSION['id'];
	}

	/**
	 * MENU DE PROYECTOS
	 */
	public function MenuProyectos(){
		echo '<li onClick="Proyectos()">Proyectos</li>';
	}

/************************ PROYECTOS *************/
	
	/**
	* MUESTRA LA LISTA DE PROYECTOS DEL USUARIO
	*/
	public function Proyectos(){
		$proyectos = new Proyectos();
	}

}

?>