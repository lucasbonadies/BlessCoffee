/****************************************************** OCULTA O MOSTRAR CONTRASEÑA **************************************************************/
function mostrarPassword(){
	var cambio = document.getElementById("txtPassword");
	if(cambio.type == "password"){
		cambio.type = "text";
		$('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
	}else{
		cambio.type = "password";
		$('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
	}
}

/****************************************************** OCULTA O MOSTRAR DETALLE REPORTE PEDIDOS **************************************************************/
function toggleDetalles(idEstado) {  
	var detalles = document.getElementById('detallePedidos'+idEstado);
	if (detalles.style.display === "none") {
		detalles.style.display = "block";
	} else {
		detalles.style.display = "none";
	}
}
/****************************************************** PANEL MENU MOSTRAR POR CATEGORIAS **************************************************************/
function mostrarCategoria(categoria) {
	// Oculta todas las categorías
	document.querySelectorAll('.categoria').forEach(function(div) {
		div.style.display = 'none';
	});
	// Muestra solo la categoría seleccionada
	document.querySelectorAll('.categoria[data-categoria="' + categoria + '"]').forEach(function(div) {
		div.style.display = 'block';
	});
}

// Muestra todas las categorías al cargar la página
document.addEventListener("DOMContentLoaded", function() {
	document.querySelectorAll('.categoria').forEach(function(div) {
		div.style.display = 'block'; // Muestra todas al inicio
	});
});

/******************************************************BOTON PARA VOLVER PARA ARRIBA************************************************************/
const backToTopButton = document.getElementById('back-to-top');
window.onscroll = function() {
	if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
		backToTopButton.style.display = "block";
	} else {
		backToTopButton.style.display = "none";
	}
};

backToTopButton.onclick = function() {
	document.body.scrollTop = 0; // Para Safari
	document.documentElement.scrollTop = 0; // Para Chrome, Firefox, IE y Opera
};

/******************************************************NO PERMITIR QUE SE ACEPTEN VALORES POR PRECIONAR ENTER************************************************************/
document.getElementById('menuForm').addEventListener('keydown', function(event) {
	if (event.key === 'Enter') {
		event.preventDefault();
	}
});
