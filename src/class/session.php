<?php

require_once("classDatabase.php");

class Session{
	
	/**
	* CONSTRUCTOR
	*/
	public function __construct(){

		//sino se ha iniciado session
		if( !isset($_SESSION['cliente']) ){
			session_start();
			//$_SESSION['home'] = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio';
			$_SESSION['home'] = '/Matriz';
			$_SESSION['datos'] = 'matrizescala';
		}

	}
	
	/**
	* DETERMINA SI EL USUARIO ESTA LOGUEADO
	* return true si lo esta sino redirecciona al login.php
	*/
	public function Logueado(){

		if( !isset($_SESSION['cliente']) ){
			$login = $_SESSION['home']."/login.php";

			//redirecciona
			echo '<script type="text/javascript">
			window.location = "'.$login.'"
			</script>';
						
			//header('Location: '.$login);
			exit;
		}else{
			return true;
		}

	}

	/**
	* ACTUALIZA LOS DATOS DE LA SESSION
	* @param $usuarioTipo -> tipo de usuario admin o cliente
	*/
	public function Update($usuarioTipo){
		$base = new Database();
		$datos = $base->Select("SELECT * FROM ".$usuarioTipo." WHERE id = '".$_SESSION['id']."'");

		if(!empty($datos)){
			foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password'){
						//carga los datos en sessiones
						$_SESSION[$campo] = $valor;
					}
				}
			}
			return true;
		}else{
			return false;
		}
	}

	/**
	* SE ENCARGA DE LOGUEAR USUARIO
	*/
	public function LogIn($usuario, $password){
		$base = new Database();

		$usuario = mysql_real_escape_string($usuario);
		$password = mysql_real_escape_string($password);

		$password = $base->Encriptar($password);

		//existe el usuario
		if( $base->Existe("SELECT * FROM clientes WHERE usuario = '".$usuario."' AND contrasena = '".$password."'") ){
			
			if($this->UserIniciarSession($usuario, $password)){
				$_SESSION['cliente'] = true;
				$this->Logueado();
			}

		}else{
			echo 'El usuario o la contraseña es incorrecta';
		}

	}

	/**
	* INICIALIZA LA SESSION DE UN USUARIO
	* @param $usuario -> usuario del admin
	* @param #password -> password encriptado
	*/
	private function UserIniciarSession($usuario, $password){
		$base = new Database();

		$query = "SELECT * FROM clientes WHERE usuario = '".$usuario."' AND contrasena = '".$password."'";

		$datos = $base->Select($query);

		if(!empty($datos)){
			//inicializa variables de session
			foreach ($datos as $fila => $c) {
				foreach ($datos[$fila] as $campo => $valor) {
					if($campo != 'password'){
						$_SESSION['cliente_'.$campo] = $valor;
					}
				}
			}
			$_SESSION['cliente'] = true;
			$_SESSION['cliente_bienvenida'] = false;
			return true;
		}else{
			return false;
		}

	}

	/**
	* LOGOUT 
	*/
	public function LogOut(){
		session_unset($_SESSION['cliente']);
		$_SESSION = array();
		session_destroy ();
	}

}

/**
* CLASE PARA LA SESSION EN EL login.php
*/
class SessionInvitado{
	
	/**
	* CONSTRUCTOR HACE TODO EL TRABAJO
	*/
	public function __construct(){
		session_start();

		//si el usuario no ha iniciado session
		if( isset($_SESSION['cliente']) ){
			//$index = 'http://'.$_SERVER['HTTP_HOST'].'/Consilio/index.php';
			header('Location: '.'index.php');
			exit;
		}

	}

}


?>