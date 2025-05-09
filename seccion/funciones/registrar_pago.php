<?php
// Función actualizada para obtener el consumo total de cada id_pedido por separado
function ObtenerConsumoTotal($conexion) {
    // Consulta SQL para obtener el consumo total agrupado por id_pedido
    $consulta = "SELECT dp.id_pedido, SUM(a.precio_unitario * dp.cantidad) AS suma_total 
                 FROM detalle_pedidos dp
                 INNER JOIN articulos a ON dp.id_articulo = a.id_articulo
                 GROUP BY dp.id_pedido";

    // Ejecuta la consulta
    $resultado = mysqli_query($conexion, $consulta);
    $consumo_total = [];

    // Verifica si se ejecutó correctamente la consulta
    if ($resultado) {
        // Recorre los resultados de la consulta
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $consumo_total[$fila['id_pedido']] = $fila['suma_total'];
        }
    } else {
        // Si hay un error en la consulta, muestra el mensaje de error
        die('Error en la consulta: ' . mysqli_error($conexion));
    }

    return $consumo_total;
}

// Define la función RegistrarPago que toma cinco parámetros: $monto, $metodo_pago, $conexion, $id_pedido, $id_estado
function RegistrarPago($monto, $metodo_pago, $conexion, $id_pedido, $id_estado) {
    // Define la consulta SQL para insertar un nuevo pago en la tabla pagos, incluyendo el estado
    $sql = "INSERT INTO pagos (monto, metodo_pago, id_estado) VALUES (?, ?, ?)";
    // Prepara la consulta SQL para ejecución
    $stmt = $conexion->prepare($sql);
    // Vincula los parámetros a la consulta preparada: $monto como double (d), $metodo_pago como string (s), $id_estado como entero (i)
    $stmt->bind_param("dsi", $monto, $metodo_pago, $id_estado);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        // Si la ejecución es exitosa, obtiene el id del pago insertado
        $id_pago = $stmt->insert_id;

        // Llama a la función CambiarEstadoPedido para actualizar el estado del pedido correspondiente
        if (CambiarEstadoPedido($id_pedido, $conexion) && EliminarDetallePedido($id_pedido, $conexion)) {
            // Si la actualización del estado del pedido y la eliminación del detalle del pedido son exitosas, retorna true
            return true;
        } else {
            // Si hay un error, actualiza el estado del pago a "Rechazado" (14)
            ActualizarEstadoPago($conexion, $id_pago, 14);
            return false;
        }
    } else {
        // Si hay un error al ejecutar la consulta, retorna false
        return false;
    }
}

// Define la función CambiarEstadoPedido que toma dos parámetros: $id_pedido y $conexion
function CambiarEstadoPedido($id_pedido, $conexion) {
    // Define la consulta SQL para actualizar el estado del pedido a 8
    $sql = "UPDATE pedidos SET id_estado = 8 WHERE id_pedido = ?";
    // Prepara la consulta SQL para ejecución
    $stmt = $conexion->prepare($sql);
    // Vincula el parámetro a la consulta preparada: $id_pedido como entero (i)
    $stmt->bind_param("i", $id_pedido);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        // Si la ejecución es exitosa, retorna true
        return true;
    } else {
        // Si hay un error al ejecutar la consulta, imprime el error para depuración
        error_log("Error al actualizar el estado del pedido: " . $stmt->error);
        return false;
    }
}

// Define la función EliminarDetallePedido que toma dos parámetros: $id_pedido y $conexion
function EliminarDetallePedido($id_pedido, $conexion) {
    // Define la consulta SQL para eliminar los detalles del pedido de la tabla detalle_pedidos
    $sql = "DELETE FROM detalle_pedidos WHERE id_pedido = ?";
    // Prepara la consulta SQL para ejecución
    $stmt = $conexion->prepare($sql);
    // Vincula el parámetro a la consulta preparada: $id_pedido como entero (i)
    $stmt->bind_param("i", $id_pedido);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        // Si la ejecución es exitosa, retorna true
        return true;
    } else {
        // Si hay un error al ejecutar la consulta, imprime el error para depuración
        error_log("Error al eliminar el detalle del pedido: " . $stmt->error);
        return false;
    }
}

// Define la función ActualizarEstadoPago que toma tres parámetros: $conexion, $id_pago y $id_estado
function ActualizarEstadoPago($conexion, $id_pago, $id_estado) {
    // Define la consulta SQL para actualizar el estado del pago basado en el id_pago
    $sql = "UPDATE pagos SET id_estado = ? WHERE id_pago = ?";
    // Prepara la consulta SQL para ejecución
    $stmt = $conexion->prepare($sql);
    // Vincula los parámetros a la consulta preparada: $id_estado como entero (i) y $id_pago como entero (i)
    $stmt->bind_param("ii", $id_estado, $id_pago);

    // Ejecuta la consulta preparada
    if ($stmt->execute()) {
        // Si la ejecución es exitosa, retorna true
        return true;
    } else {
        // Si hay un error al ejecutar la consulta, imprime el error para depuración
        error_log("Error al actualizar el estado del pago: " . $stmt->error);
        return false;
    }
}
?>
