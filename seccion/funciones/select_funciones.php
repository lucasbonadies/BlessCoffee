<?php

function Listar_Articulos($vConexion) {
    $Listado = array();
    $i = 0;

    // Artículos
    $SQL_articulos = "SELECT id_articulo AS id, nombre, precio_unitario AS precio, descripcion, imagen, id_tipo, id_estado, 'articulo' AS tipo_item
                    FROM articulos";

    $rs_articulos = mysqli_query($vConexion, $SQL_articulos);

    while ($data = mysqli_fetch_assoc($rs_articulos)) {
        $Listado[$i] = array(
            'ID_ARTICULO'     => $data['id'],
            'NOMBRE'      => $data['nombre'],
            'PRECIO_UNITARIO'      => $data['precio'],
            'DESCRIPCION' => $data['descripcion'],
            'IMAGEN'      => $data['imagen'],
            'ID_TIPO'     => $data['id_tipo'],
            'ID_ESTADO'   => $data['id_estado'],
            'TIPO_ITEM' => $data['tipo_item']
        );
        $i++;
    }

    // Promociones
    $SQL_promos = "SELECT id_promocion AS id, nombre, precio, descripcion, imagen, id_tipo, id_estado, 'promocion' AS tipo_item
                    FROM promociones";

    $rs_promos = mysqli_query($vConexion, $SQL_promos);

    while ($data = mysqli_fetch_assoc($rs_promos)) {
        $Listado[$i] = array(
            'ID_ARTICULO'     => $data['id'],
            'NOMBRE'      => $data['nombre'],
            'PRECIO_UNITARIO'      => $data['precio'],
            'DESCRIPCION' => $data['descripcion'],
            'IMAGEN'      => $data['imagen'],
            'ID_TIPO'     => $data['id_tipo'],
            'ID_ESTADO'   => $data['id_estado'],
            'TIPO_ITEM' => $data['tipo_item']
        );
        $i++;
    }

    return $Listado;
}


function Listar_Tipo_Articulos($vConexion) {

	//SELECT `id_tipo`, `nombre`, `descripcion` FROM `tipo_articulos`
    $Listado=array();

    //1) genero la consulta que deseo
    $SQL = "SELECT * FROM `tipo_articulos`";

    //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
    $rs = mysqli_query($vConexion, $SQL);
        
    //3) el resultado deberá organizarse en una matriz, entonces lo recorro
    $i=0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_TIPO'] = $data['id_tipo'];
        $Listado[$i]['NOMBRE'] = ucfirst(strtolower($data['nombre']));
        $Listado[$i]['DESCRIPCION'] = $data['descripcion'];
        $i++;
    }

    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
}

function Listar_detalle_Pedidos($vConexion, $vId_persona, $vId_pedido) {

    //`articulos`(`id_articulo`, `nombre`, `precio_unitario`, `descripcion`, `imagen`, `id_tipo`)
    //`pedidos`(`id_pedido`, `fecha_pedido`, `id_estado`, 'id_persona')
	//`detalle_pedidos`(`id_detalle`, `id_pedido`, `id_articulo`, `cantidad`)

    $Listado=array();

   $SQL= "SELECT dp.cantidad,
    a.id_articulo,a.nombre as nombre_articulo, a.imagen, a.precio_unitario,
    (dp.cantidad * a.precio_unitario) AS subtotal
    FROM 
        detalle_pedidos dp
    JOIN 
        articulos a ON dp.id_articulo = a.id_articulo
    JOIN 
        pedidos p ON dp.id_pedido = p.id_pedido
    WHERE 
        p.id_pedido = $vId_pedido 
    AND 
        p.id_persona = $vId_persona" ;

    if(empty($SQL)){
        return $Listado;
    }
 
    //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
    $rs = mysqli_query($vConexion, $SQL);
    
    //3) el resultado deberá organizarse en una matriz, entonces lo recorro
    $i=0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_ARTICULO'] = $data['id_articulo'];
        $Listado[$i]['NOMBRE_ARTICULO'] = $data['nombre_articulo'];
        $Listado[$i]['CANTIDAD'] = $data['cantidad'];
        $Listado[$i]['PRECIO_UNITARIO'] = $data['precio_unitario'];
        $Listado[$i]['IMAGEN'] = $data['imagen'];
        $Listado[$i]['SUBTOTAL'] = $data['subtotal'];
        $i++;
    }
    //devuelvo el listado generado en el array $Listado. (Podra salir vacio o con datos)..
    return $Listado;
    
}

function Listar_Pedidos($vConexion, $vId_persona, $vId_estado) {

    //`pedidos`(`id_pedido`, `fecha_pedido`, `id_estado`, 'id_persona')
    //`estados`(`id_estado`, `nombre`, `entidad`, `descripcion`)

    $Listado = array();
    
    // Validar que $vId_persona sea un número entero
    if (!is_int($vId_persona)) {
        return $Listado; // Retorna vacío si no es un entero
    }

    if($_SESSION['id_nivel']==1){
        // Preparar la consulta
        $SQL = "SELECT p.id_pedido, p.fecha_pedido, p.id_estado, p.id_persona,
        e.nombre AS nombre_estado
        FROM pedidos p 
        JOIN estados e ON p.id_estado = e.id_estado
        WHERE id_persona = ? 
        AND p.id_estado = ? 
        ORDER BY p.fecha_pedido DESC ";

        $stmt = mysqli_prepare($vConexion, $SQL);

    }else if($_SESSION['id_nivel']==5){
        // Preparar la consulta
        $SQL = "SELECT p.id_pedido, p.fecha_pedido, p.id_estado, p.id_persona,
        e.nombre AS nombre_estado
        FROM pedidos p
        JOIN estados e ON p.id_estado = e.id_estado
        WHERE p.id_estado = ? 
        ORDER BY p.fecha_pedido DESC ";

        $stmt = mysqli_prepare($vConexion, $SQL);
    }

    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        return $Listado; // Retorna vacío si hay un error en la preparación
    }

    if($_SESSION['id_nivel']==1){
        // Vincular parámetros
        mysqli_stmt_bind_param($stmt, 'ii', $vId_persona, $vId_estado);
    }else if($_SESSION['id_nivel']==5){
        mysqli_stmt_bind_param($stmt, 'i', $vId_estado);
    }

    // Ejecutar la consulta
    mysqli_stmt_execute($stmt);
    
    // Obtener el resultado
    $result = mysqli_stmt_get_result($stmt);
    
    // Recoger los datos
    while ($data = mysqli_fetch_assoc($result)) {
        $Listado[] = array(
            'ID_PEDIDO' => $data['id_pedido'],
            'FECHA_PEDIDO' => $data['fecha_pedido'],
            'ID_ESTADO' => $data['id_estado'],
            'NOMBRE_ESTADO' => $data['nombre_estado'],
            'ID_PERSONA' => $data['id_persona']
        );
    }

    // Cerrar la declaración
    mysqli_stmt_close($stmt);

    return $Listado;
}

function asignarClaseEstado($estado) {
    // Función para asignar clases (COLOR) según el estado del pedido
    switch ($estado) {
        case 8:
            return 'success';
        case 6:
            return 'warning';
        default:
            return 'danger';
    }
}

function Listar_Estados_Pedidos($vConexion){

    //`estados`(`id_estado`, `nombre`, `entidad`, `descripcion`)
    $Listado=array();
    //1) genero la consulta que deseo
    $SQL = "SELECT id_estado, nombre FROM estados WHERE entidad= 'pedido' ORDER BY nombre DESC";

    //2) a la conexion actual le brindo mi consulta, y el resultado lo entrego a variable $rs
    $rs = mysqli_query($vConexion, $SQL);
    
    //3) el resultado deberá organizarse en una matriz, entonces lo recorro
    $i=0;
    while ($data = mysqli_fetch_array($rs)) {
        $Listado[$i]['ID_ESTADO'] = $data['id_estado'];
        $Listado[$i]['NOMBRE_ESTADO'] = ucfirst(strtolower($data['nombre']));
        $i++;
    }

    return $Listado;

}

function Seleccionar_Pedidos_Por_Fecha($vConexion, $vDesde, $vHasta, $vIdEstado) {

    //`pedidos`(`id_pedido`, `fecha_pedido`, `id_estado`, 'id_persona')
    $Listado = [];

    $SQL = "SELECT p.id_pedido, p.fecha_pedido, p.id_estado, p.id_persona, e.nombre as nombre_estado 
    FROM pedidos p 
    LEFT JOIN estados e ON p.id_estado = e.id_estado
    WHERE DATE(fecha_pedido) >= DATE(?) AND DATE(fecha_pedido) <= DATE(?)" . (empty($vIdEstado) ? "" : " AND p.id_estado = ?");

    $stmt = $vConexion->prepare($SQL);
    if (empty($vIdEstado)) {
        $stmt->bind_param("ss", $vDesde, $vHasta);
    } else {
        $stmt->bind_param("ssi", $vDesde, $vHasta, $vIdEstado);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $Listado = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $Listado;
}

function Separar_Articulos_Por_Categoria($vListadoPedidos, $vCategoria) {

    $ListadoFiltrado = [];
    
    foreach ($vListadoPedidos as $listado) {
        if ($listado['ID_TIPO'] == $vCategoria) {
            $ListadoFiltrado[] = $listado; // Agregar artículo coincidente al nuevo listado
        }
    }

    return $ListadoFiltrado;
}


?>