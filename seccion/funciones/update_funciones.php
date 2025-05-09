<?php

function Cancelar_Pedido($vConexion, $vIdPedido) {
    // Consulta SQL
    $SQL = "UPDATE pedidos SET id_estado = 9 WHERE id_pedido = ?";

    // Preparar la declaración
    $stmt = $vConexion->prepare($SQL);
    if ($stmt === false) {
        // Error al preparar la consulta
        return false;
    }

    // Vincular los parámetros
    $stmt->bind_param("i", $vIdPedido);

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        // Error al ejecutar la consulta
        return false;
    }

    // Cerrar la declaración
    $stmt->close();

    // Retornar true si la consulta fue exitosa
    return true;
}

function Eliminar_Articulo_Pedido($vConexion, $vIdPedido, $vIdArticulo) {

    // Validar que los IDs sean enteros
    if (!filter_var($vIdPedido, FILTER_VALIDATE_INT) || !filter_var($vIdArticulo, FILTER_VALIDATE_INT)) {
        return false;
    }

    // SQL: Eliminar el artículo del pedido
    $SQL = "DELETE FROM detalle_pedidos WHERE id_pedido = ? AND id_articulo = ?";

    // Preparar la consulta
    if ($stmt = $vConexion->prepare($SQL)) {
        
        // Enlazar los parámetros
        $stmt->bind_param("ii", $vIdPedido, $vIdArticulo);
        
        // Ejecutar la consulta
        if ($stmt->execute()) {
            $stmt->close(); // Cerrar el statement
            return true; // Eliminación exitosa
        } else {
            $stmt->close(); // Cerrar el statement en caso de error
            return false; // Error en la ejecución
        }
        
    } else {
        // Error en la preparación de la consulta
        return false;
    }
}

function Update_Pedido($vConexion, $vIdPedido, $articulos) {

    foreach ($articulos as $vIdArticulo => $cantidad) {

        //`detalle_pedidos`(`id_detalle`, `id_pedido`, `id_articulo`, `cantidad`)

        // Construimos la consulta 
        $SQL = "UPDATE detalle_pedidos SET cantidad = ? WHERE id_pedido = ? AND id_articulo = ?";

        // Preparamos la consulta
        $stmtUsuarios = $vConexion->prepare($SQL);

        if ($stmtUsuarios === false) {
            return false; // Error en la preparación de la consulta
        }

        // Asignar los parámetros
        $stmtUsuarios->bind_param("iii", $cantidad, $vIdPedido, $vIdArticulo);

        // Ejecutamos la consulta para cada artículo
        if (!$stmtUsuarios->execute()) {
            $stmtUsuarios->close();
            return false; // Error en la consulta
        }

        // Cerramos la declaración preparada después de ejecutarla
        $stmtUsuarios->close();
    }

    return true; // Retornamos true solo cuando todos los artículos se hayan actualizado correctamente
}

function ActualizarArticulosYPromociones($vConexion, $articulos) {
    $resultados = [];

    foreach ($articulos as $id => $articulo) {
        // 1. Leer datos
        $nombre = trim($articulo['nombre']);
        $precio = floatval($articulo['precio']);
        $estado = intval($articulo['estado']);
        $tipo = intval($articulo['tipo']);
        $imagen = !empty($articulo['imagen']['name']) ? $articulo['imagen']['name'] : null;

        // 2. Validaciones básicas
        if (empty($nombre)) {
            $resultados[$id] = 'Error: El nombre no puede estar vacío.';
            continue;
        }

        if ($precio < 0) {
            $resultados[$id] = 'Error: El precio no puede ser negativo.';
            continue;
        }

        if (!in_array($estado, [1, 2])) {
            $resultados[$id] = 'Error: Estado inválido.';
            continue;
        }

        // 3. Identificar si es Promoción o Artículo
        $esPromocion = ($tipo == 8);
        $idReal = intval($articulo['id']); // <== CORRECTO, este es el que se guarda en la DB

        if ($esPromocion) {
            if ($imagen) {
                $sql = "UPDATE promociones SET nombre = ?, precio = ?, id_estado = ?, imagen = ? WHERE id_promocion = ?";
                $stmt = $vConexion->prepare($sql);
                $stmt->bind_param("sdisi", $nombre, $precio, $estado, $imagen, $idReal);
            } else {
                $sql = "UPDATE promociones SET nombre = ?, precio = ?, id_estado = ? WHERE id_promocion = ?";
                $stmt = $vConexion->prepare($sql);
                $stmt->bind_param("sdii", $nombre, $precio, $estado, $idReal);
            }
        } else {
            if ($imagen) {
                $sql = "UPDATE articulos SET nombre = ?, precio_unitario = ?, id_estado = ?, imagen = ?, id_tipo = ? WHERE id_articulo = ?";
                $stmt = $vConexion->prepare($sql);
                $stmt->bind_param("sdisii", $nombre, $precio, $estado, $imagen, $tipo, $idReal);
            } else {
                $sql = "UPDATE articulos SET nombre = ?, precio_unitario = ?, id_estado = ?, id_tipo = ? WHERE id_articulo = ?";
                $stmt = $vConexion->prepare($sql);
                $stmt->bind_param("sdiii", $nombre, $precio, $estado, $tipo, $idReal);
            }
        }

        if (!$stmt) {
            $resultados[$id] = 'Error al preparar la consulta.';
            continue;
        }

        if (!$stmt->execute()) {
            $resultados[$id] = 'Error al ejecutar: ' . $stmt->error;
        } else {
            $resultados[$id] = 'Actualización exitosa.';
        }

        $stmt->close();
    }

    return $resultados;
}


?>