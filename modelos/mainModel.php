<?php

    // si la peticionAjax que esta en la plantilla es true
    if ($peticionAjax) {
        // incluimos los archivos de configuracion del servidor con ..
        require_once "../config/SERVER.php";
    } else {
        // incluimos los archivos de configuracion del servidor con .
        require_once "./config/SERVER.php";
    }

    class mainModel {

        /*--------- Funcion conectar a DB ---------*/
        protected static function conectar() {
            $conexion = new PDO(SGBD,USER,PASS);
            $conexion -> exec("SET CHARACTER SET utf8"); // exec() -> ejecuta un programa externo

            return $conexion;
        }

        /*--------- Funcion ejecutar consultas simples ---------*/
        protected static function ejecutar_consulta_simple($consulta) {
            // self -> hacemos referencia a un metodo de la misma clase (por ej this)
            $sql = self::conectar() -> prepare($consulta);
            $sql -> execute();

            return $sql;
        }

        /*--------- Funciones manejo de los valores(id) en la url para poder pasarlos al controlador ---------*/

        /*--------- Funcion encryption -> procesa por el hash cualquier texto plano (encripta cadenas) ---------*/
        public function encryption($string){
			$output=FALSE;
			$key=hash('sha256', SECRET_KEY);
			$iv=substr(hash('sha256', SECRET_IV), 0, 16);
			$output=openssl_encrypt($string, METHOD, $key, 0, $iv);
			$output=base64_encode($output);
			return $output;
		}

        /*--------- Funcion decryption -> a partir del hash vuelve al estado original el valor (desencripta cadenas) ---------*/
        protected static function decryption($string){
			$key=hash('sha256', SECRET_KEY);
			$iv=substr(hash('sha256', SECRET_IV), 0, 16);
			$output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
			return $output;
		}

        /*--------- Funcion generar codigos aleatorios ---------*/
        protected static function generar_codigo_aleatorio($letra,$longitud,$numero) {
            for ($i=1; $i<=$longitud ; $i++) { 
                $aleatorio = rand(0,9);
                $letra.=$aleatorio;
            }

            return $letra."-".$numero;
        }

        /*--------- Funcion limpiar cadenas de texto ---------*/
        protected static function limpiar_cadena($cadena){
            // trim -> para eliminar espacios en blanco
            $cadena=trim($cadena);
            // stripslashes -> para quitar las barras de un string
            $cadena=stripslashes($cadena);
            // str_ireplace -> para reemplazar un texto mediante una busqueda
            $cadena=str_ireplace("<script>", "", $cadena);
            $cadena=str_ireplace("</script>", "", $cadena);
            $cadena=str_ireplace("<script src", "", $cadena);
            $cadena=str_ireplace("<script type=", "", $cadena);
            $cadena=str_ireplace("SELECT * FROM", "", $cadena);
            $cadena=str_ireplace("DELETE FROM", "", $cadena);
            $cadena=str_ireplace("INSERT INTO", "", $cadena);
            $cadena=str_ireplace("DROP TABLE", "", $cadena);
            $cadena=str_ireplace("DROP DATABASE", "", $cadena);
            $cadena=str_ireplace("TRUNCATE TABLE", "", $cadena);
            $cadena=str_ireplace("SHOW TABLES;", "", $cadena);
            $cadena=str_ireplace("SHOW DATABASES;", "", $cadena);
            $cadena=str_ireplace("<?php", "", $cadena);
            $cadena=str_ireplace("?>", "", $cadena);
            $cadena=str_ireplace("--", "", $cadena);
            $cadena=str_ireplace("^", "", $cadena);
            $cadena=str_ireplace("<", "", $cadena);
            $cadena=str_ireplace("[", "", $cadena);
            $cadena=str_ireplace("]", "", $cadena);
            $cadena=str_ireplace("==", "", $cadena);
            $cadena=str_ireplace(";", "", $cadena);
            $cadena=str_ireplace("::", "", $cadena);
            // limpiamos nuevamente las cadenas de texto evitando la inyeccion sql
            $cadena=trim($cadena);
            $cadena=stripslashes($cadena);
    
            return $cadena;
        }

        /*--------- Funcion validar datos (pattern del input) ---------*/
        protected static function verificar_datos($filtro,$cadena) {
            // preg_match recibe dos parametros (una expresion regular y la cadena que queremos verificar)
            if(preg_match("/^".$filtro."$/", $cadena)) {
                return false;
            }else{
                return true;
            }
        }

        /*--------- Funcion validar fechas (pattern del input) ---------*/
        protected static function verificar_fecha($fecha) {
            $valores=explode('-',$fecha);
            if (count($valores) == 3 && checkdate($valores[1],$valores[2],$valores[0])) {
                return false;
            } else {
                return true;
            }
        }

        // paginador de tablas
        protected static function paginador_tablas($pagina,$nroPaginas,$url,$botones){

            // inicio del nav
            $tabla='<nav aria-label="Page navigation example"><ul class="pagination justify-content-center">';
            
            // verificacion primer pagina
            if($pagina==1){
                $tabla.='<li class="page-item disabled"><a class="page-link"><i class="fas fa-angle-double-left"></i></a></li>';
            }else{
                $tabla.='<li class="page-item"><a class="page-link" href="'.$url.'1/"><i class="fas fa-angle-double-left"></i></a></li>
                <li class="page-item"><a class="page-link" href="'.$url.($pagina-1).'/">Anterior</a></li>';
            }

            // recorremos la cantidad de registros existentes para ver cuantos botones tenemos en el paginador
            $cont=0;
            for($i=$pagina; $i<=$nroPaginas; $i++){
                if($cont>=$botones){
                    break;
                }

                if($pagina==$i){
                    $tabla.='<li class="page-item"><a class="page-link active" href="'.$url.$i.'/">'.$i.'</a></li>';
                }else{
                    $tabla.='<li class="page-item"><a class="page-link" href="'.$url.$i.'/">'.$i.'</a></li>';
                }

                $cont++;
            }

            // verificacion ultima pagina
            if($pagina==$nroPaginas){
                $tabla.='<li class="page-item disabled"><a class="page-link"><i class="fas fa-angle-double-right"></i></a></li>';
            }else{
                $tabla.='<li class="page-item"><a class="page-link" href="'.$url.($pagina+1).'/">Siguiente</a></li>
                <li class="page-item"><a class="page-link" href="'.$url.$nroPaginas.'/"><i class="fas fa-angle-double-right"></i></a></li>
                ';
            }
            
            // cierre del nav
            $tabla.='</ul></nav>';

            return $tabla;
        }

    }
    
?>