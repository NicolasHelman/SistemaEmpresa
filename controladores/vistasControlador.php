<?php
	
	require_once "./modelos/vistasModelo.php";

	class vistasControlador extends vistasModelo {

		/*--------- Controlador obtener plantilla ---------*/
		public function obtener_plantilla_controlador(){
			return require_once "./vistas/plantilla.php"; // require_once -> verificará si el archivo ya ha sido incluido y si es así, no se incluye (require) de nuevo.
		}

		/*--------- Controlador obtener vistas ---------*/
		public function obtener_vistas_controlador(){
            // isset -> comprueba si la variable de tipo GET views(mismo nombre que en htaccess) esta definida
			if(isset($_GET['views'])){
				$ruta=explode("/", $_GET['views']); // explode -> divide un string en varios string separado por /
				$respuesta=vistasModelo::obtener_vistas_modelo($ruta[0]); // con :: accedemos al modelo (vistasModelo)
			}else{
				$respuesta="login";
			}

			return $respuesta;
		}

	}

?>