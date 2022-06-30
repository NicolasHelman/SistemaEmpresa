<script>
    $(document).ready(function(){

        $('.btn-exit-system').on('click', function(e){
            e.preventDefault();

            Swal.fire({
                title: '¿Quieres salir del sistema?',
                text: "La sesion actual se cerrará y saldras del sistema",
                type: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, salir',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.value) {			
                    let url = '<?php echo SERVERURL; ?>ajax/loginAjax.php';
                    let token = '<?php echo $instanciaLoginControlador -> encryption($_SESSION['token_spm']); ?>';
                    let usuario = '<?php echo $instanciaLoginControlador -> encryption($_SESSION['usuario_spm']); ?>';
                    // creamos los datos a partir de las dos variables utilizadas (token y usuario)
                    let datos = new FormData();
                    // agregamos los valores
                    datos.append("token",token);
                    datos.append("usuario",usuario);
                    // con fetch ejecutamos el envio de datos
                    fetch(url,{
                        method: 'POST',
                        body: datos
                    })
                    .then(respuesta => respuesta.json()) 
                    .then(respuesta => {
                        return alertas_ajax(respuesta);
                    });
                    // con then() recibimos una promesa y la parseamos a json para que pueda ser entendida por la funcion alertas_ajax()
                }
            });
        });
    });
</script>