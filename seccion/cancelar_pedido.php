<?php
session_start();

if(empty($_SESSION['id_usuario']) || empty($_SESSION['id_nivel'])){
    header('Location: cerrarsesion.php');
    exit;
 } 

$ID_PEDIDO= htmlspecialchars($_GET['ID_PEDIDO']);

require_once 'funciones/conexion.php';
$MiConexion = ConexionBD();

require_once 'funciones/update_funciones.php';

if (Cancelar_Pedido($MiConexion, $ID_PEDIDO) != false){
        $_SESSION['Mensaje']='Pedido Cancelado. <br /> ';
        $_SESSION['Estilo']='success';
        header('Location: mis_pedidos.php');
        exit;
    } else {
		$_SESSION['Mensaje']='No se pudo cancelar el pedido. <br /> ';
        $_SESSION['Estilo']='danger';
        header('Location: mis_pedidos.php');
        exit;
    }
?>