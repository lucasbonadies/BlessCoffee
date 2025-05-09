<?php
session_start();

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
}

$ID_ARTICULO= htmlspecialchars($_GET['ID_ARTICULO']);
$ID_PEDIDO=htmlspecialchars($_GET['ID_PEDIDO']);
$ID_PERSONA=htmlspecialchars($_GET['ID_PERSONA']);

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

require_once 'funciones/update_funciones.php';

if (Eliminar_Articulo_Pedido($MiConexion, $ID_PEDIDO, $ID_ARTICULO) != false){
        $_SESSION['Mensaje']='Se quitó el artículo del pedido. <br /> ';
        $_SESSION['Estilo']='success';
        header("Location: modificar_pedido.php?ID_PEDIDO=$ID_PEDIDO&ID_PERSONA=$ID_PERSONA");
        exit;
    } else {
		$_SESSION['Mensaje']='No se puede quitar el artículo. <br /> ';
        $_SESSION['Estilo']='danger';
        header("Location: modificar_pedido.php?ID_PEDIDO=$ID_PEDIDO&ID_PERSONA=$ID_PERSONA");
        exit;
    }
?>