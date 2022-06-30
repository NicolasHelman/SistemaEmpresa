<?php

    // Este archivo se usará para la parte de cerrar la sesión (Ajax), pero para iniciar sesión no se usará Ajax

    $peticionAjax=true;

    require_once "../config/APP.php";

    if (isset($_POST['token']) && isset($_POST['usuario'])) {

        /* --------- Instancia al controlador --------- */
        require_once "../controladores/loginControlador.php";
        $instanciaLogin = new loginControlador();

        echo $instanciaLogin -> cerrar_sesion_controlador();

    } else {
        session_start(['name' => 'SPM']); // Se inicia la sesion solo para usar las funciones de vaciar y destruir la sesion para eliminar las variables de sesión.
        session_unset(); // Libera (vacía) todas las variables de sesión
        session_destroy(); // Destruye todos los datos registrados en una sesión
        header('Location: '.SERVERURL.'login/'); // redireccionamos al login
        exit();
    }
    

?>