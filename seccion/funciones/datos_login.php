<?php

function DatosLogin($vEmail, $vClave, $vConexion){
    //SELECT `usuarios` (`id_usuario`, `clave`, `email`, `id_nivel`, `id_persona`, `id_estado`)
    //SELECT INTO `personas` (`id_persona`, `dni`, `nombre`, `apellido`, `fecha_nacimiento`, 
    //`nro_telefono`, `calle`, `sexo`, `imagen`, `fecha_registro`, `id_pais`, `id_provincia`, `id_localidad`)
    $Usuario = array();

    // Preparar la primera consulta con sentencias preparadas
    $SQL = "SELECT usuarios.*, niveles.*
            FROM usuarios
            INNER JOIN niveles 
            ON usuarios.id_nivel = niveles.id_nivel
            WHERE usuarios.email = ? AND usuarios.clave = ? /*MD5(?)*/";

    if ($stmt = $vConexion->prepare($SQL)) {
        // Bindear los parámetros
        $stmt->bind_param("ss", $vEmail, $vClave);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();
        if ($data_usuario = $result->fetch_assoc()) {
            $Usuario['ID_USUARIO'] = $data_usuario['id_usuario'];
            $Usuario['EMAIL'] = $data_usuario['email'];
            $Usuario['ID_NIVEL'] = $data_usuario['id_nivel'];
            $Usuario['ID_PERSONA'] = $data_usuario['id_persona'];
            $Usuario['ID_ESTADO'] = $data_usuario['id_estado'];
            $Usuario['NOMBRE_NIVEL'] = $data_usuario['nombre_nivel'];
        } else {
            // Si no se encuentra el usuario, devolver un array vacío
            return $Usuario;
        }
        $stmt->close();
    } else {
        // Manejar el error de preparación de la consulta
        die('Error en la consulta SQL: ' . $vConexion->error);
    }

    // Preparar la segunda consulta para obtener los datos de la persona
    $SQL = "SELECT * FROM personas WHERE id_persona = ?";
    if ($stmt = $vConexion->prepare($SQL)) {
        // Bindear el parámetro
        $stmt->bind_param("i", $Usuario['ID_PERSONA']);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();
        if ($data_persona = $result->fetch_assoc()) {
            $Usuario['NOMBRE'] = $data_persona['nombre'];
            $Usuario['APELLIDO'] = $data_persona['apellido'];
            $Usuario['DNI'] = $data_persona['dni'];
            $Usuario['SEXO'] = $data_persona['sexo'];
            $Usuario['FECHA_NACIMIENTO'] = $data_persona['fecha_nacimiento'];
            $Usuario['NRO_TELEFONO'] = $data_persona['nro_telefono'];

            // Verificar si la imagen está vacía
            $Usuario['IMG'] = empty($data_persona['imagen']) ? 'user_icon.jpg' : $data_persona['imagen'];
        }
        $stmt->close();
    } else {
        // Manejar el error de preparación de la consulta
        die('Error en la consulta SQL: ' . $vConexion->error);
    }

    // Devolver los datos del usuario
    return $Usuario;

}

function ObtenerIntentosFallidos($vEmail, $vConexion) {
    $intentos = 0; // Valor por defecto si no se encuentra el usuario
    $SQL = "SELECT intento_inicio_sesion FROM usuarios WHERE email = ?";

    // Preparamos la sentencia
    if ($stmt = $vConexion->prepare($SQL)) {
        // Vinculamos parámetros (s para string)
        $stmt->bind_param("s", $vEmail);

        // Ejecutamos la consulta
        if ($stmt->execute()) {
            // Vinculamos el resultado
            $stmt->bind_result($intentos);

            // Obtenemos el resultado
            $stmt->fetch();
        }

        // Cerramos la sentencia
        $stmt->close();
    } else {
        // Si la consulta falla
        return 0; // Devuelve 0 en caso de error
    }

    return $intentos;
}

function ActualizarIntentosFallidos($vEmail, $vIntentos, $vConexion) {
    $SQL = "UPDATE usuarios SET intento_inicio_sesion = ? WHERE email = ?";
    
    // Preparamos la sentencia
    if ($stmt = $vConexion->prepare($SQL)) {
        $stmt->bind_param("is", $vIntentos, $vEmail);
        // Ejecutamos la consulta y verificamos su éxito
        if (!$stmt->execute()) {
            // Si hay un error, podemos lanzar una excepción o manejarlo
            error_log("Error actualizando intentos fallidos: " . $stmt->error);
        }
        // Cerramos la sentencia
        $stmt->close();
    } else {
        // Si no se pudo preparar la consulta
        error_log("Error preparando consulta de ActualizarIntentosFallidos: " . $vConexion->error);
    }
}

function ActualizarEstadoUsuario($vEmail, $vEstado, $vConexion) {

    $SQL = "UPDATE usuarios SET id_estado = ? WHERE email = ?";
    
    // Preparamos la sentencia
    if ($stmt = $vConexion->prepare($SQL)) {
        // Vinculamos los parámetros (is: int para $vEstado, string para $vEmail)
        $stmt->bind_param("is", $vEstado, $vEmail);
        
        // Ejecutamos la consulta y verificamos su éxito
        if (!$stmt->execute()) {
            // Manejo del error
            error_log("Error actualizando estado de usuario: " . $stmt->error);
        }

        // Cerramos la sentencia
        $stmt->close();
    } else {
        // Si no se pudo preparar la consulta
        error_log("Error preparando consulta de ActualizarEstadoUsuario: " . $vConexion->error);
    }
}

function ReiniciarIntentos($vEmail, $vConexion) {
    $SQL = "UPDATE usuarios SET intento_inicio_sesion = 0 WHERE email = ?";
    
    // Preparamos la sentencia
    if ($stmt = $vConexion->prepare($SQL)) {
        // Vinculamos los parámetros (s: string para $vEmail)
        $stmt->bind_param("s", $vEmail);
        
        // Ejecutamos la consulta y verificamos su éxito
        if (!$stmt->execute()) {
            // Manejo del error
            error_log("Error reiniciando intentos de inicio de sesión: " . $stmt->error);
        }

        // Cerramos la sentencia
        $stmt->close();
    } else {
        // Si no se pudo preparar la consulta
        error_log("Error preparando consulta de ReiniciarIntentos: " . $vConexion->error);
    }
}

function Email_Recuperacion($vEmail, $vConexion){
    date_default_timezone_set('America/Argentina/Buenos_Aires'); // zona horaria de Argentina Bs As
     // Buscar al usuario por correo electrónico
     $SQL = "SELECT id_usuario FROM `usuarios` WHERE email = ?";
     $stmt = $vConexion->prepare($SQL);
     $stmt->bind_param("s", $vEmail);
     $stmt->execute();
 
     // Obtener el resultado de la consulta
     $result = $stmt->get_result();
 
     if ($result->num_rows > 0) {
         // Obtener el usuario
         $row = $result->fetch_assoc();
         $user_id = $row['id_usuario'];
 
         // Generar un token único
         $token = bin2hex(random_bytes(16));
 
         // Guardar el token en la base de datos
         $SQL = "INSERT INTO tokens (id_token, token_generado, expiracion, id_usuario) VALUES (NULL, ?, ?, ?)";
         $stmt = $vConexion->prepare($SQL);
         $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Token válido por 15 minutos
         $stmt->bind_param("ssi", $token, $expiracion, $user_id);
         $stmt->execute();
 
         // Enviar correo electrónico con el enlace de recuperación
         $to = $vEmail;
         $subject = "Recuperación de Contraseña";
         $message = "Haga clic en el siguiente enlace para restablecer su contraseña: \n";
         $message .= "http://localhost/ChefSite_v1.0_Prueba/seccion/actualizar_clave.php?token=$token&id_usuario=$user_id";
         $headers = "From: no-reply@ChefSite.com";
        
        // $mail= mail($to, $subject, $message, $headers);
            $_SESSION['Mensaje'] = "Se ha enviado un correo electrónico con instrucciones para recuperar su contraseña.";
            $_SESSION['Estilo']='success';
       /* if ($mail) {
            $_SESSION['Mensaje'] = "Se ha enviado un correo electrónico con instrucciones para recuperar su contraseña.";
        } else {
            // Manejo de error en caso de que mail() falle
            $_SESSION['Mensaje'] = "El correo electrónico no se pudo enviar. Verifica la configuración del servidor de correo.";
        } */

    } else {
        $_SESSION['Estilo']='warning';
        $_SESSION['Mensaje'] = "Correo electrónico incorrecto o sin registrar.";
    }
 
    $vConexion->close();
}

function validar_token($vConexion, $vToken, $vIDusuario){

    $clave_nueva= $_POST['Clave'] ; 

    // Validar el token
    $SQL = "SELECT id_usuario FROM tokens WHERE token_generado = ? AND expiracion > NOW()";
    $stmt = $vConexion->prepare($SQL);
    $stmt->bind_param("s", $vToken);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($vIDusuario);
        $stmt->fetch();
        
        // Actualizar la contraseña del usuario
        //$hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $hashed_password = MD5($clave_nueva);
        $SQL = "UPDATE usuarios SET clave = ? WHERE id_usuario = ?";
        $stmt = $vConexion->prepare($SQL);
        $stmt->bind_param("si", $hashed_password, $vIDusuario);
        $stmt->execute();
        
        // Eliminar el token usado
        $SQL = "DELETE FROM tokens WHERE token_generado = ?";
        $stmt = $vConexion->prepare($SQL);
        $stmt->bind_param("s", $vToken);
        $stmt->execute();
        
       // $vConexion->close();

        return true;
    } else {
       // $vConexion->close();
        return false;
    }
    
}

?>