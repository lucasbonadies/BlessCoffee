<?php

function Validar_Clave($vClave) {  
    
    $_SESSION['Mensaje']= "";

    // Validación de la contraseña
    htmlspecialchars(strip_tags(trim($vClave))); // limpio la clave 

    if (strlen($vClave) < 8) {
        $_SESSION['Mensaje'] .= 'La contraseña debe tener al menos 8 caracteres.<br />';
    }
   
    // Verificar si la contraseña contiene al menos una letra mayúscula
    if (!preg_match('/[A-Z]/', $vClave)) {
        $_SESSION['Mensaje'] .= 'La contraseña debe contener al menos una letra mayúscula.<br />';
    }

    // Verificar si la contraseña contiene al menos una letra minúscula
    if (!preg_match('/[a-z]/', $vClave)) {
        $_SESSION['Mensaje'] .= 'La contraseña debe contener al menos una letra minúscula.<br />';
    }

    // Verificar si la contraseña contiene al menos un número
    if (!preg_match('/\d/', $vClave)) {
        $_SESSION['Mensaje'] .= 'La contraseña debe contener al menos un número.<br />';
    }

    // Verificar si la contraseña contiene al menos un carácter especial
    if (!preg_match('/[\W]/', $vClave)) {  // \W coincide con cualquier carácter que no sea alfanumérico
        $_SESSION['Mensaje'] .= 'La contraseña debe contener al menos un carácter especial (por ejemplo: @, #, $)<br />';
    }

    // Si no hay mensajes de error, todo está bien
    if (empty($_SESSION['Mensaje'])) {
        return true;
    } else {
        $_SESSION['Estilo'] = 'warning';
        return false; // Se detectó algún error
    }
}

function Validar_Email() {

    $_SESSION['Mensaje'] = "";
    
    $Email = isset($_POST['Email']) ? $_POST['Email'] : '';
    // Sanitizar el email
    $Email = htmlspecialchars(strip_tags(trim($Email)));

    // Validación del correo electrónico
    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['Mensaje'] .= 'Correo electrónico inválido.<br />';
    }

    // Si no hay mensajes de error, todo está bien
    if (empty($_SESSION['Mensaje'])) {
        return true;
    } else {
        $_SESSION['Estilo'] = 'warning';
        return false; // Se detectó algún error
    }
}

function Validar_Modificacion_Datos_Personales($vClave) {

    $_SESSION['Mensaje']="";

    htmlspecialchars(strip_tags(trim($vClave))); // limpio la clave 

	// Validacion de registro nuevo usuario o Modificacion de datos
    if (strlen(trim($_POST['Nombre'])) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un nombre con al menos 3 caracteres. <br />';
    }
    if (strlen(trim($_POST['Apellido'])) < 3) {
        $_SESSION['Mensaje'].='Debes ingresar un apellido con al menos 3 caracteres. <br />';
    }
    if (!preg_match("/^\d{8}$/", $_POST['Dni'])) { 
        $_SESSION['Mensaje'].='Debes ingresar un DNI válido de 8 números. <br />';
    }
    if (empty($_POST['Telefono']) ) {
        $_SESSION['Mensaje'].='Debes completar tu telefono. <br />';
    }
    if(!empty($vClave)){
        if ($vClave != $_POST['ReClave']) {
            $_SESSION['Mensaje'].='Las claves ingresadas deben coincidir. <br />';
        }
    }    
    if (empty($_POST['Pais']) ) {
        $_SESSION['Mensaje'].='Debes seleccionar tu Pais. <br />';
    }
    if (empty($_POST['Provincia']) ) {
        $_SESSION['Mensaje'].='Debes seleccionar tu Provincia. <br />';
    }
    if (empty($_POST['Localidad']) ) {
        $_SESSION['Mensaje'].='Debes seleccionar tu Localidad. <br />';
    }
    if (empty($_POST['Domicilio']) ) {
        $_SESSION['Mensaje'].='Debes ingresar tu domicilio. <br />';
    }
    if (empty($_POST['Sexo'])) {
        $_SESSION['Mensaje'].='Debes seleccionar el sexo. <br />';
    }
	
    // Limpieza de las entradas para evitar inyecciones
    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = htmlspecialchars(trim($Valor), ENT_QUOTES, 'UTF-8');
    }

    if(empty($_SESSION['Mensaje'])){
        return true;
    }else{
        return false;
    }
}

function Validar_Registro_Empleado(){
    
    $_SESSION['Mensaje']="";

    if (filter_var($_POST['Sueldo'], FILTER_VALIDATE_FLOAT) === false or $_POST['Sueldo']< 1) {
        $_SESSION['Mensaje']='Valor de Sueldo no válido. <br />';
    }

    // Limpieza de las entradas para evitar inyecciones
    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = htmlspecialchars(trim($Valor), ENT_QUOTES, 'UTF-8');
    }

    if(empty($_SESSION['Mensaje'])){
        return true;
    }else{
        return false;
    }
}

function Existe_Email($vConexion){
    //Esta funcion sirve para verificar que no exista un mail igual en la base de datos.
    $_SESSION['Mensaje']='';

    $Email = isset($_POST['Email']) ? $_POST['Email'] : '';
    // Sanitizar el email
    $Email = htmlspecialchars(strip_tags(trim($Email)));

    // Preparar la consulta
    $SQL = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $vConexion->prepare($SQL);

    // Enlazar el parámetro y ejecutar la consulta
    $stmt->bind_param("s", $Email);
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        $_SESSION['Mensaje']='El mail ya se encuentra registrado, pruebe otro';
        $_SESSION['Estilo']='warning';
        return true; // se encontraron coincidencias con el email
    } else {
        return false;
    } 
}

function Existe_Email_Login($vConexion){
    //Esta funcion sirve para verificar que no exista un mail igual en la base de datos.
    $_SESSION['Mensaje']='';

    $Email = isset($_POST['Email']) ? $_POST['Email'] : '';
    // Sanitizar el email
    $Email = htmlspecialchars(strip_tags(trim($Email)));

    // Preparar la consulta
    $SQL = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $vConexion->prepare($SQL);

    // Enlazar el parámetro y ejecutar la consulta
    $stmt->bind_param("s", $Email);
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        return true; // se encontraron coincidencias con el email
    } else {
        $_SESSION['Mensaje']='El correo electrónico no se encuentra registrado';
        $_SESSION['Estilo']='warning';
        return false;
    } 
}

function Existe_Clave($vClave, $vEmail, $vConexion){
    // Sanitizar valores por parametro
    $Clave= htmlspecialchars(strip_tags(trim($vClave)));
    $Email = htmlspecialchars(strip_tags(trim($vEmail)));

    //Esta funcion sirve para verificar que la contraseña que se ingresa, sea la misma que ya tiene en la base de datos.
    $SQL = "SELECT * FROM usuarios WHERE email = ? && clave = MD5( ? ) ";
    $stmt = $vConexion->prepare($SQL);

    // Enlazar el parámetro y ejecutar la consulta
    $stmt->bind_param("ss", $Email, $Clave);
    $stmt->execute();

    // Obtener el resultado
    $result = $stmt->get_result();

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        return true; // se encontraron coincidencias con la contraseña
    } else {
        $_SESSION['Mensaje']='Clave incorrecta';
        $_SESSION['Estilo']='warning';
        return false;
    } 
}

function Validar_Pedido($vCantidadElementosPost) {

    $_SESSION['Mensaje'] = '';
    $_SESSION['Estilo'] = 'warning';
    $algunoSeleccionado = false;

    // Iterar sobre los elementos del formulario
    for ($i = 0; $i < $vCantidadElementosPost; $i++) {
        // Verifica si el artículo está seleccionado
        if (!empty($_POST["Articulo$i"])) {
            $algunoSeleccionado = true;
            // Verifica la cantidad si el artículo está seleccionado
            $cantidad = $_POST["Cantidad$i"] ?? 0;
            if ($cantidad <= 0 || $cantidad > 20) {
                $_SESSION['Mensaje'] .= 'Debes seleccionar una cantidad válida entre 1 y 20 para el artículo "'. $_POST["ArticuloNombre$i"].'." <br />';
            }
        }
    }

    // Limpiar espacios y caracteres no deseados
    array_walk($_POST, function(&$valor) {
        $valor = strip_tags(trim($valor));
    });

    if (!empty($_SESSION['Mensaje'])) {
        return false;
    }

    if ($algunoSeleccionado==false) {
        $_SESSION['Mensaje'] .= 'Debes elegir al menos un producto para hacer el pedido. <br />';
        return false;
    }else{
        return true;
    }

}

function Validar_Articulo(){
    $_SESSION['Mensaje'] = '';
    $_SESSION['Estilo'] = '';

    // Array para almacenar los mensajes de error
    $errores = [];

    // Validación de Nombre
    $nombre = trim($_POST['Nombre']);
    if (strlen($nombre) < 3) {
        $errores[] = 'Debes ingresar un nombre con al menos 3 caracteres.';
    }

    // Validación de Precio
    $precio = $_POST['Precio_unitario'];
    if (!filter_var($precio, FILTER_VALIDATE_FLOAT) || $precio < 1) {
        $errores[] = 'Valor de precio no válido.';
    }

    // Validación de Descripción
    $descripcion = trim($_POST['Descripcion']);
    if (empty($descripcion)) {
        $_POST['Descripcion'] = "";  // Si está vacío, se asigna NULL
    }

    // Validación de Tipo
    $tipo = trim($_POST['Tipo']);
    if (empty($tipo)) {
        $errores[] = 'Debes seleccionar el tipo de Articulo al que corresponde.';
    }

    // Si hay errores, los almacenas en la sesión y retornas false
    if (!empty($errores)) {
        $_SESSION['Mensaje'] = implode('<br />', $errores);
        $_SESSION['Estilo'] = "warning";
        return false;
    }

    return true;
}

function Validar_Promocion($nombre, $precio, $articulos) {

    //`promociones`(`id_promocion`, `id_tipo`, `nombre`, `imagen`, `precio`, `descripcion`, `id_estado`)
    if (empty($nombre) || empty($precio) || !is_numeric($precio) || $precio <= 0) {
        $_SESSION['Mensaje'] .= 'El nombre o el precio de la promoción son inválidos. <br />';
        $_SESSION['Estilo'] = 'warning';      
        return false;
    }
    if (empty($articulos) || !is_array($articulos)) {
        $_SESSION['Mensaje'] .= 'No se han seleccionado artículos para la promoción. <br />';
        $_SESSION['Estilo'] = 'warning';
        return false;
    }
    foreach ($articulos as $articulo) {
        if (empty($articulo['id']) || empty($articulo['cantidad']) || $articulo['cantidad'] <= 0) {
            $_SESSION['Mensaje'] .= 'Los artículos seleccionados no son válidos. <br />';
            $_SESSION['Estilo'] = 'warning';
            return false;
        }
    }
    return true;
}

function Validar_Update_Pedido() {

    foreach ($_POST as $key => $value) {
        // Validamos solo los campos que corresponden a cantidades
        if (strpos($key, 'Cantidad') === 0) {
            $cantidad = filter_var($value, FILTER_VALIDATE_INT);
            
            // Verificar si la cantidad es válida
            if ($cantidad === false || $cantidad <= 0 || $cantidad > 20) {
                $_SESSION['Mensaje'] .= 'Debes seleccionar una cantidad válida para cada artículo. <br />';
                return false;
            }
        }
    }

    // Limpiamos los valores de caracteres no deseados
    foreach ($_POST as $Id => $Valor) {
        $_POST[$Id] = trim($Valor);
        $_POST[$Id] = strip_tags($Valor);
    }

    return true;
}

function Validar_Reporte_Pedidos($vDesde, $vHasta, $vIdEstado){

    // Validar que los parámetros de fecha y estado sean correctos
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $vDesde) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $vHasta)) {
        $_SESSION['Mensaje'].='La Fecha ingresada es invalida </br>';
        return false; // Retorna false si las fechas no son válidas
    }
    // Convertir las fechas a objetos DateTime
    $fechaDesde = DateTime::createFromFormat('Y-m-d', $vDesde);
    $fechaHasta = DateTime::createFromFormat('Y-m-d', $vHasta);

    // Validar si las conversiones fueron exitosas
    if (!$fechaDesde || !$fechaHasta) {
        $_SESSION['Mensaje'] .= 'La Fecha ingresada es inválida </br>';
        return false;
    }

    // Comparar las fechas
    if ($fechaDesde > $fechaHasta) {
        $_SESSION['Mensaje'] .= 'La fecha desde no puede ser superior a la fecha hasta </br>';
        return false;
    }
    if(!empty($vIdEstado)){
        if (!filter_var($vIdEstado, FILTER_VALIDATE_INT)) {
            $_SESSION['Mensaje'].='El Estado seleccionado es Invalido </br>';
            return false; // Retorna false si el ID de estado no es un número entero
        }
    }

    return true;

}

function Validar_Update_Articulo($vListaDeArticulos) {

    $_SESSION['Mensaje'] = '';
    $_SESSION['Estilo'] = '';
    
    // Array para almacenar los mensajes de error
    $errores = [];
    
    foreach ($vListaDeArticulos as $id => $articulo) {
        // Validación de Nombre
        $nombre = trim($articulo['nombre']);
        if (strlen($nombre) < 3) {
            $errores[] = "El nombre del artículo con ID $id debe tener al menos 3 caracteres.";
        }

        // Validación de Precio
        $precio = $articulo['precio'];
        if (!filter_var($precio, FILTER_VALIDATE_FLOAT) || $precio < 1) {
            $errores[] = "El precio del artículo con ID $id no es válido.";
        }

        // Validación de Tipo
        $tipo = trim($articulo['tipo']);
        if (empty($tipo)) {
            $errores[] = "Debes seleccionar el tipo del artículo con ID $id.";
        }

        // Validación de Imagen
        $imagen = 'imagen' . $id; // Aquí generamos el nombre dinámico del campo
        if (!empty($_FILES[$imagen]['name'])) {
            if (SubirArchivo($imagen) === false) {
                $errores[] = $_SESSION['Mensaje'];
            }
        }
    }
    
    // Si hay errores, los almacenas en la sesión y retornas false
    if (!empty($errores)) {
        $_SESSION['Mensaje'] = implode('<br />', $errores);
        $_SESSION['Estilo'] = "warning";
        return false;
    }

    return true;
}

?>