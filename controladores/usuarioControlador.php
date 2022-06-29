<?php

    if ($peticionAjax) {
        require_once "../modelos/usuarioModelo.php";
    } else {
        require_once "./modelos/usuarioModelo.php";
    }
    
    class usuarioControlador extends usuarioModelo {

        /* --------- Metodo Agregar Usuario --------- */
        public function agregar_usuario_controlador() {

            // guardamos en variables todos los datos que enviamos por el formulario
            $dni = mainModel::limpiar_cadena($_POST['usuario_dni_reg']);
			$nombre = mainModel::limpiar_cadena($_POST['usuario_nombre_reg']);
			$apellido = mainModel::limpiar_cadena($_POST['usuario_apellido_reg']);
			$telefono = mainModel::limpiar_cadena($_POST['usuario_telefono_reg']);
			$direccion = mainModel::limpiar_cadena($_POST['usuario_direccion_reg']);
			$usuario = mainModel::limpiar_cadena($_POST['usuario_usuario_reg']);
			$email = mainModel::limpiar_cadena($_POST['usuario_email_reg']);
			$clave1 = mainModel::limpiar_cadena($_POST['usuario_clave_1_reg']);
			$clave2 = mainModel::limpiar_cadena($_POST['usuario_clave_2_reg']);
			$privilegio = mainModel::limpiar_cadena($_POST['usuario_privilegio_reg']);

			/* ===== Comprobar campos vacios ===== */
			if ($dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2=="") {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No has llenado todos los campos que son obligatorios",
					"Tipo"=>"error"
				];

				echo json_encode($alerta); // json_encode -> Transformamos el array $alerta a JSON

				exit();
			}

			/* ===== Verificando integridad de los datos ===== */
			if (mainModel::verificar_datos("[0-9-]{10,20}",$dni)) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DNI no coincide con el formato solicitado",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$nombre)) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El NOMBRE no coincide con el formato solicitado",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,35}",$apellido)) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El APELLIDO no coincide con el formato solicitado",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			if ($telefono != ""){
				if(mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)) {
					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"El TELEFONO no coincide con el formato solicitado",
						"Tipo"=>"error"
					];

					echo json_encode($alerta);

					exit();
				}
			}

			if ($direccion != ""){
				if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}",$direccion)) {

					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"La DIRECCION no coincide con el formato solicitado",
						"Tipo"=>"error"
					];

					echo json_encode($alerta);

					exit();
				}
			}

			if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El NOMBRE DE USUARIO no coincide con el formato solicitado",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave2)) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Las CLAVES no coinciden con el formato solicitado",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			/* ===== Comprobando DNI ===== */
			$check_dni = mainModel::ejecutar_consulta_simple("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
            // Si existe un registro
			if ($check_dni->rowCount() > 0) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El DNI ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			/* ===== Comprobando usuario ===== */
			$check_user = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'");
            // Si existe un registro
			if($check_user->rowCount() > 0) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El NOMBRE DE USUARIO ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

			/* ===== Comprobando email ===== */
			if ($email != "") {
                // filter_var() -> Filtra una variable con un filtro especificado
				if (filter_var($email,FILTER_VALIDATE_EMAIL)) {

					$check_email=mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email='$email'");
                    // Si existe un registro
					if($check_email->rowCount()>0){

						$alerta=[
							"Alerta"=>"simple",
							"Titulo"=>"Ocurrió un error inesperado",
							"Texto"=>"El EMAIL ingresado ya se encuentra registrado en el sistema",
							"Tipo"=>"error"
						];

						echo json_encode($alerta);

						exit();
					}
				} else {

					$alerta=[
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrió un error inesperado",
						"Texto"=>"Ha ingresado un correo no valido",
						"Tipo"=>"error"
					];

					echo json_encode($alerta);

					exit();
				}
			}


			/* ===== Comprobando claves ===== */
			if ($clave1 != $clave2) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"Las claves que acaba de ingresar no coinciden",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			} else {
                // procesamos la $clave1 encriptada(encryption) y la guardamos en la variable $clave
				$clave=mainModel::encryption($clave1);
			}

			/* ===== Comprobando privilegios ===== */
			if ($privilegio < 1 || $privilegio > 3) {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"El privilegio seleccionado no es valido",
					"Tipo"=>"error"
				];

				echo json_encode($alerta);

				exit();
			}

            // creamos un array de datos
			$datos_usuario_reg=[
				"Dni" => $dni,
				"Nombre" => $nombre,
				"Apellido" => $apellido,
				"Telefono" => $telefono,
				"Direccion" => $direccion,
				"Email" => $email,
				"Usuario" => $usuario,
				"Clave" => $clave,
				"Estado" => "Activa",
				"Privilegio" => $privilegio
			];

            // creamos una variable donde vamos a almacenar lo que nos devuelva la funcion agregar_usuario_modelo()
			$agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);

			if ($agregar_usuario->rowCount() == 1) {

				$alerta=[
					"Alerta"=>"limpiar",
					"Titulo"=>"usuario registrado",
					"Texto"=>"Los datos del usuario han sido registrados con exito",
					"Tipo"=>"success"
				];

			} else {

				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrió un error inesperado",
					"Texto"=>"No hemos podido registrar el usuario",
					"Tipo"=>"error"
				];
			}

			echo json_encode($alerta);
        }

    }

?>