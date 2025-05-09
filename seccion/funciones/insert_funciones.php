<?php
//Funciones para la insercion de platos, bebidas o postres Nivel 3 (CHEF) y Nivel 5 (Admin)

function InsertarArticulo($vConexion){

    //INSERT INTO `articulos`(`id_articulo`, `nombre`, `precio_unitario`, `descripcion`, `imagen`, `id_tipo`, `id_estado`)
    // Primero validamos si los datos están correctamente establecidos
    if (!isset($_POST['Nombre'], $_POST['Precio_unitario'], $_POST['Tipo'])) {
        die('Faltan datos en el formulario.');
    }

    // Validación y limpieza de datos
    $nombre = mysqli_real_escape_string($vConexion, $_POST['Nombre']);
    $precio = mysqli_real_escape_string($vConexion, $_POST['Precio_unitario']);
    $descripcion = isset($_POST['Descripcion']) ? mysqli_real_escape_string($vConexion, $_POST['Descripcion']) : NULL;
    $tipo = mysqli_real_escape_string($vConexion, $_POST['Tipo']);
    if(empty($_FILES['Imagen_Menu']['name'])){
        $ruta_imagen = 'sin_imagen.jpg';
    }else{
        $ruta_imagen = $_FILES['Imagen_Menu']['name'];
    }    
    $valor_estado= 1;

    // Consulta preparada
    $SQL_Insert = $vConexion->prepare("INSERT INTO articulos (id_articulo, nombre, precio_unitario, descripcion, imagen, id_tipo, id_estado)
                                        VALUES (NULL, ?, ?, ?, ?, ?, ?)");
    $SQL_Insert->bind_param("sssssi", $nombre, $precio, $descripcion, $ruta_imagen, $tipo, $valor_estado);

    // Ejecutamos la consulta
    if (!$SQL_Insert->execute()) {
        echo "Error: " . $SQL_Insert->error;
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    $vConexion->close();
    
    return true;
}

function InsertarPromocion($vConexion, $nombre, $precio, $descripcion, $tipo, $imagen, $articulos) {

    // Insertar la promoción en la tabla `promociones`
    $descripcion = isset($_POST['Descripcion_Promocion']) ? mysqli_real_escape_string($vConexion, $_POST['Descripcion_Promocion']) : NULL;
    $id_tipo = $tipo;
    $id_estado = 1; // Estado activo por defecto

    //`promociones`(`id_promocion`, `id_tipo`, `nombre`, `imagen`, `precio`, `descripcion`, `id_estado`)
    $queryPromocion = "INSERT INTO promociones (id_promocion, id_tipo, nombre, imagen, precio, descripcion, id_estado) 
                       VALUES (NULL, ?, ?, ?, ?, ?, ?)";
    $stmtPromocion = $vConexion->prepare($queryPromocion);
    $stmtPromocion->bind_param("issdsi", $id_tipo, $nombre, $imagen, $precio, $descripcion, $id_estado);

    if (!$stmtPromocion->execute()) {
       /* $_SESSION['Mensaje'] = 'Error al insertar la promoción: ' . $stmtPromocion->error;
        $_SESSION['Estilo'] = 'danger';*/
        return false;
    }

    $idPromocion = $vConexion->insert_id; // Obtener el ID de la promoción recién insertada

    // Insertar los detalles de la promoción en la tabla `promocion_detalle`
    //`promocion_detalle`(`id_detalle`, `id_promocion`, `id_articulo`, `cantidad`)
    $queryDetalle = "INSERT INTO promocion_detalle (id_detalle, id_promocion, id_articulo, cantidad) 
                     VALUES (NULL, ?, ?, ?)";
    $stmtDetalle = $vConexion->prepare($queryDetalle);

    foreach ($articulos as $articulo) {
        $idArticulo = $articulo['id'];
        $cantidad = $articulo['cantidad'];
        $stmtDetalle->bind_param("iii", $idPromocion, $idArticulo, $cantidad);

        if (!$stmtDetalle->execute()) {
            $_SESSION['Mensaje'] = 'Error al insertar el detalle de la promoción: ' . $stmtDetalle->error;
            $_SESSION['Estilo'] = 'danger';
            return false;
        }
    }
    
    return true;
}

function InsertarPedido($vConexion, $vIdPersona) {
	
    //INSERT INTO `pedidos`(`id_pedido`, `fecha_pedido`, `id_estado`, `id_persona`)
	$SQL_Insert="INSERT INTO pedidos (id_pedido, fecha_pedido, id_estado, id_persona)
    VALUES ( NULL , NOW() , 3, $vIdPersona)";

    // NOW() sirve para estampar la fecha en que se creo el registro actual

    if (!mysqli_query($vConexion, $SQL_Insert)) {
        //si surge un error, finalizo la ejecucion del script con un mensaje
        echo "Error: " .$SQL_Insert. "<br>" . mysqli_error($vConexion);
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    return true;
}

function InsertarDetalle($vConexion, $productos_seleccionados) {

    // Obtener el ID del último pedido insertado
    $id_pedido = $vConexion->insert_id;
    $insercionesExitosas = 0;


    //`detalle_pedidos`(`id_detalle`, `id_articulo`, `id_promocion`, `id_pedido`, `cantidad`, `notas`)
    // Preparamos cada tipo de consulta.
    $stmtArticulo = $vConexion->prepare(
        "INSERT INTO detalle_pedidos (id_articulo, id_promocion, id_pedido, cantidad, notas)
         VALUES (?, NULL, ?, ?, NULL)"
    );

    $stmtPromocion = $vConexion->prepare(
        "INSERT INTO detalle_pedidos (id_articulo, id_promocion, id_pedido, cantidad, notas)
         VALUES (NULL, ?, ?, ?, NULL)"
    );

    // Verificar que ambas sentencias fueron preparadas correctamente
    if (!$stmtArticulo || !$stmtPromocion) {
        echo "Error al preparar las sentencias: " . $vConexion->error;
        return false;
    }

    // Según el tipo, insertamos en la columna correspondiente
    // Recorrer los productos seleccionados
    foreach ($productos_seleccionados as $producto) {

        $id = $producto['id'];
        $cantidad = $producto['cantidad'];
        $tipo = $producto['tipo'];

        if ($tipo === 'articulo') {
            $stmtArticulo->bind_param("iii", $id, $id_pedido, $cantidad);
            if ($stmtArticulo->execute()) {
                $insercionesExitosas++;
            } else {
                echo "Error al insertar artículo: " . $stmtArticulo->error;
                return false;
            }
        } elseif ($tipo === 'promocion') {
            $stmtPromocion->bind_param("iii", $id, $id_pedido, $cantidad);
            if ($stmtPromocion->execute()) {
                $insercionesExitosas++;
            } else {
                echo "Error al insertar promoción: " . $stmtPromocion->error;
                return false;
            }
        }
    }

    // Cerrar las sentencias
    $stmtArticulo->close();
    $stmtPromocion->close();

     if($insercionesExitosas>0){
        return true;
    }else{
        return false;
    }
}


?>