const formularios_ajax = document.querySelectorAll(".FormularioAjax");

function enviar_formulario_ajax(evento) {
    // prevenimos el evento por defecto cuando se envia el formulario
    evento.preventDefault();

    let data = new FormData(this); // obtenemos los datos de todos los input del formulario con FormData
	let method = this.getAttribute("method"); // obtenemos el valor del atributo method del formulario
	let action = this.getAttribute("action"); // obtenemos el valor del atributo action del formulario
	let tipo = this.getAttribute("data-form"); // obtenemos el valor del atributo data-form del formulario

	let encabezados = new Headers();

	let config = {
		method: method,
		headers: encabezados,
		mode: 'cors',
		cache: 'no-cache',
		body: data
	}

	let texto_alerta;

	if (tipo==="save"){
		texto_alerta="Los datos quedaran guardados en el sistema";
	} else if (tipo==="delete"){
		texto_alerta="Los datos serán eliminados completamente del sistema";
	} else if (tipo==="update"){
		texto_alerta="Los datos del sistema serán actualizados";
	} else if (tipo==="search"){
		texto_alerta="Se eliminará el término de búsqueda y tendrás que escribir uno nuevo";
	} else if (tipo==="loans"){
		texto_alerta="Desea remover los datos seleccionados para préstamos o reservaciones";
	} else {
		texto_alerta="Quieres realizar la operación solicitada";
	}

	Swal.fire({
		title: '¿Estás seguro?',
		text: texto_alerta,
		type: 'question',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar'
	}).then((result) => {
		if(result.value){
			fetch(action,config) // con fetch ejecutamos el envio de datos
			.then(respuesta => respuesta.json())
			.then(respuesta => {
				return alertas_ajax(respuesta);
			});
		}
	});
}

formularios_ajax.forEach(formularios => {
    formularios.addEventListener("submit",enviar_formulario_ajax);
});

function alertas_ajax(alerta) {
    if (alerta.Alerta === "simple") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        });
    } else if (alerta.Alerta === "recargar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload(); // recargamos la pagina con reload()
            }
        });
    } else if (alerta.Alerta === "limpiar") {
        Swal.fire({
            title: alerta.Titulo,
            text: alerta.Texto,
            type: alerta.Tipo,
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('.FormularioAjax').reset(); // reseteamos los campos
            }
        });
    } else if (alerta.Alerta === "redireccionar") {
        window.location.href = alerta.URL; 
    }
}