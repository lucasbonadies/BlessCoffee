<?php

// Verificar si una sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para obtener el consumo total de la "mesa común"
function ObtenerConsumoTotal($id_mesa, $conexion) {
    // Consulta SQL para obtener el consumo total
    $consulta = "SELECT SUM(a.precio_unitario * dp.cantidad) AS consumo_total 
                 FROM detalle_pedidos dp
                 INNER JOIN articulos a ON dp.id_articulo = a.id_articulo";

    // Ejecuta la consulta
    $resultado = mysqli_query($conexion, $consulta);

    // Verifica si se ejecutó correctamente la consulta
    if ($resultado) {
        // Obtiene el resultado de la consulta como un array asociativo
        $fila = mysqli_fetch_assoc($resultado);

        // Verifica si se obtuvieron resultados
        if ($fila && $fila['consumo_total'] != null) {
            // Retorna los datos del consumo
            return [
                'consumo_total' => $fila['consumo_total']
            ];
        } else {
            // Si no hay resultados o el consumo total es NULL, retorna cero
            return [
                'consumo_total' => 0
            ];
        }
    } else {
        // Si hay un error en la consulta, muestra el mensaje de error
        die('Error en la consulta: ' . mysqli_error($conexion));
    }
}
?>