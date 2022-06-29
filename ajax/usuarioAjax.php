<?php

    $peticionAjax=true;

    require_once "../config/APP.php";

    // detectamos si enviamos los datos del formulario o no
    if (isset($_POST['usuario_dni_reg'])) {

        /* --------- Instancia al controlador --------- */
        require_once "../controladores/usuarioControlador.php";
        $instanciaUsuario = new usuarioControlador();

        /*--------- Agregar un usuario ---------*/
		if(isset($_POST['usuario_dni_reg']) && isset($_POST['usuario_nombre_reg'])){
			echo $instanciaUsuario -> agregar_usuario_controlador();
		}
        

    } else {
        session_start(['name' => 'SPM']); // Se inicia la sesion solo para usar las funciones de vaciar y destruir la sesion para eliminar las variables de sesión.
        session_unset(); // Libera (vacía) todas las variables de sesión
        session_destroy(); // Destruye todos los datos registrados en una sesión
        header('Location: '.SERVERURL.'login/'); // redireccionamos al login
        exit();
    }
    

?>