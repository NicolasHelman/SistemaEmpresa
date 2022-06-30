<?php

    if ($peticionAjax) {
        require_once "../modelos/loginModelo.php";
    } else {
        require_once "./modelos/loginModelo.php";
    }
    
    class loginControlador extends loginModelo {

        /* --------- Metodo Iniciar Sesion --------- */
        public function iniciar_sesion_controlador() {

            // guardamos en variables todos los datos que enviamos por el formulario
            // evitamos inyeccion sql usando la funcion limpiar_cadena
            $usuario = mainModel::limpiar_cadena($_POST['usuario_log']);
			$clave = mainModel::limpiar_cadena($_POST['clave_log']);

            /* ===== Comprobar campos vacios ===== */
			if ($usuario=="" || $clave=="") {

                // generamos la alerta utilizando js
                echo '
                <script>
                    Swal.fire({
                        title: "Ocurrió un error inesperado",
                        text: "No has llenado todos los campos requeridos",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';

                exit();
			}

            /* ===== Verificando integridad de los datos ===== */
			if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)) {

                // generamos la alerta utilizando js
                echo '
                <script>
                    Swal.fire({
                        title: "Ocurrió un error inesperado",
                        text: "El NOMBRE DE USUARIO no coincide con el formato solicitado",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';

                exit();
            }

            if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave)) {

                // generamos la alerta utilizando js
                echo '
                <script>
                    Swal.fire({
                        title: "Ocurrió un error inesperado",
                        text: "La CLAVE no coincide con el formato solicitado",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';

                exit();
            }

            // Para poder enviar la clave al modelo la debemos encriptar para que sea igual a la DB
            $clave = mainModel::encryption($clave);

            // Creamos el array de datos que le vamos a enviar al modelo
            $datos_login=[
                "Usuario" => $usuario,
                "Clave" => $clave
            ];

            // guardamos en una variable la consulta que vamos a realizar con la DB
            $datos_cuenta = loginModelo::iniciar_sesion_modelo($datos_login);
            // si existe un registro con esos datos
            if ($datos_cuenta->rowCount() == 1) {
                // creamos un array row para almacenar todos los datos del usuario que estan en la DB. 
                // con fetch() convertimos los datos en un array de datos
                $row = $datos_cuenta->fetch();
                // Se inicia la sesion
                session_start(['name' => 'SPM']);
                // creamos las variables de sesion que se van a utilizar en el sistema
                $_SESSION['id_spm'] = $row['usuario_id']; // a la variable de session predeterminada (id_spm) le asignamos el id del usuario logeado (usuario_id)
                $_SESSION['nombre_spm'] = $row['usuario_nombre'];
                $_SESSION['apellido_spm'] = $row['usuario_apellido'];
                $_SESSION['usuario_spm'] = $row['usuario_usuario'];
                $_SESSION['privilegio_spm'] = $row['usuario_privilegio'];
                $_SESSION['token_spm'] = md5(uniqid(mt_rand(),true)); // esta variable se va a utilizar para cerrar la sesion de una forma segura con un token (le asignamos un id unico para cada sesion, para evitar que otro usuario no cierre la sesion desde otra pc)

                // verificamos si se estan enviando encabezados por php con headers_sent()
                if (headers_sent()) {
                    echo "<script> window.location.href='".SERVERURL."home/';</script>";
                } else {
                    return header('Location: '.SERVERURL.'home/'); // redireccionamos al home
                }
            } else {
                echo '
                <script>
                    Swal.fire({
                        title: "Ocurrió un error inesperado",
                        text: "El NOMBRE DE USUARIO o CLAVE son incorrectos",
                        type: "error",
                        confirmButtonText: "Aceptar"
                    });
                </script>
                ';
            }

        }

        /* --------- Metodo Forzar Cierre Sesion --------- */
        public function forzar_cierre_sesion_controlador() {
            session_unset(); // Libera (vacía) todas las variables de sesión
            session_destroy(); // Destruye todos los datos registrados en una sesión

            // verificamos si se estan enviando encabezados por php con headers_sent()
            if (headers_sent()) {
                echo "<script> window.location.href='".SERVERURL."login/';</script>";
            } else {
                return header('Location: '.SERVERURL.'login/'); // redireccionamos al login
            }

        }

        /* --------- Metodo Cerrar Sesion --------- */
        public function cerrar_sesion_controlador() {
            // Iniciamos la sesion SPM
            session_start(['name' => 'SPM']);
            // desencriptamos el token y el usuario con la funcion decryption
            $token = mainModel::decryption($_POST['token']);
            $usuario = mainModel::decryption($_POST['usuario']);

            // comprobamos si las dos variables son las mismas que las que iniciaron sesion
            if ($token == $_SESSION['token_spm'] && $usuario == $_SESSION['usuario_spm']) {
                session_unset(); // Libera (vacía) todas las variables de sesión
                session_destroy(); // Destruye todos los datos registrados en una sesión
                // redireccionamos al usuario al login
                $alerta=[
                    "Alerta"=>"redireccionar",
                    "URL"=>SERVERURL."login/"
                ];
            } else {
                $alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No se pudo cerrar la sesión en el sistema",
					"Tipo"=>"error"
				];

            }
            
            echo json_encode($alerta);
            
        }
    }

?>