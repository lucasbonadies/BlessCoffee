<?php

function Listar_Usuarios($vConexion) {

    //SELECT `id_usuario`, `id_estado`, `id_persona`, `id_nivel`, `clave`, `email`, `intento_inicio_sesion` 
    //SELECT `id_nivel`, `nombre_nivel`
    $Listado = array();

    // Consulta de usuarios y niveles de usuarios
    $SQL = "SELECT usuarios.*, niveles.*
            FROM usuarios
            INNER JOIN niveles ON usuarios.id_nivel = niveles.id_nivel";
    
    // Ejecutar la consulta
    $resultado = mysqli_query($vConexion, $SQL);

    // Comprobar si la consulta fue exitosa
    if (!$resultado) {
        die("Error en la consulta SQL: " . mysqli_error($vConexion));
    }

    $i = 0;
    // Recorrer los resultados de la consulta de usuarios
    while ($data_usuario = mysqli_fetch_assoc($resultado)) {
        if (!empty($data_usuario)) {
            $Listado[$i]['ID_USUARIO'] = $data_usuario['id_usuario'];
            $Listado[$i]['EMAIL'] = $data_usuario['email'];
            $Listado[$i]['ID_NIVEL'] = $data_usuario['id_nivel'];
            $Listado[$i]['ID_PERSONA'] = $data_usuario['id_persona'];
            $Listado[$i]['ID_ESTADO'] = $data_usuario['id_estado']; 
            $Listado[$i]['NOMBRE_NIVEL'] = $data_usuario['nombre_nivel'];     
        
            // Consulta segura para obtener los datos de la tabla 'personas'
            $stmt = $vConexion->prepare("SELECT * FROM personas WHERE id_persona = ?");
            $stmt->bind_param("i", $data_usuario['id_persona']);
            $stmt->execute();
            $resultado_persona = $stmt->get_result();

            // Obtener los datos de la persona
            $data_persona = $resultado_persona->fetch_assoc();
            
            if (!empty($data_persona)) {
                $Listado[$i]['NOMBRE'] = $data_persona['nombre'];
                $Listado[$i]['APELLIDO'] = $data_persona['apellido'];
                $Listado[$i]['DNI'] = $data_persona['dni'];
                $Listado[$i]['FECHA_NACIMIENTO'] = $data_persona['fecha_nacimiento'];
                $Listado[$i]['NRO_TELEFONO'] = $data_persona['nro_telefono'];
                
                if (empty($data_persona['imagen'])) {
                    $data_persona['imagen'] = 'user_icon.jpg'; // Imagen por defecto
                }
                
                $Listado[$i]['IMG'] = $data_persona['imagen'];     
            }
            $i++;
        }
    }
    
    return $Listado;
    
}

function EncontrarUsuario($vIDUsuario, $vConexion){
    $Usuario=array();   

    $SQL="SELECT u.id_usuario, u.email, u.id_nivel, u.id_persona, u.id_estado,
            n.nombre_nivel,
            p.id_persona, p.nombre, p.apellido, p.dni, p.fecha_nacimiento, p.nro_telefono, p.imagen
        FROM 
            usuarios u
        JOIN 
            niveles n ON u.id_nivel = n.id_nivel
        JOIN 
            personas p ON u.id_persona = p.id_persona
        WHERE 
            u.id_usuario = ?";

    if ($stmt = $vConexion->prepare($SQL)) {
        // Bindear los parámetros
        $stmt->bind_param("i", $vIDUsuario);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();
        if ($data_usuario = $result->fetch_assoc()) {
            $Usuario['ID_USUARIO'] = $data_usuario['id_usuario'];
            $Usuario['EMAIL'] = $data_usuario['email'];
            $Usuario['ID_NIVEL'] = $data_usuario['id_nivel'];
            $Usuario['ID_PERSONA'] = $data_usuario['id_persona'];
            $Usuario['ID_ESTADO'] = $data_usuario['id_estado'];
            $Usuario['NOMBRE_NIVEL'] = ucfirst(strtolower($data_usuario['nombre_nivel']));
            $Usuario['NOMBRE'] = ucfirst(strtolower($data_usuario['nombre']));
            $Usuario['APELLIDO'] = ucfirst(strtolower($data_usuario['apellido']));
            $Usuario['DNI'] = $data_usuario['dni'];
            $Usuario['FECHA_NACIMIENTO'] = $data_usuario['fecha_nacimiento'];
            $Usuario['NRO_TELEFONO'] = $data_usuario['nro_telefono'];
            $Usuario['IMG'] = empty($data_usuario['imagen']) ? 'user_icon.jpg' : $data_usuario['imagen'];
        } else {
            // Si no se encuentra el usuario, devolver un array vacío
            return $Usuario;
        }
        $stmt->close();
    } else {
        // Manejar el error de preparación de la consulta
        die('Error en la consulta SQL: ' . $vConexion->error);
    }
    return $Usuario; // Devuelve el array con los datos del usuario
}

function Modificar_Acceso_Usuario($vIdUsuario, $vidEstado, $vConexion){
    
    // Validar que vIdUsuario y vidEstado sean enteros
    if (!filter_var($vIdUsuario, FILTER_VALIDATE_INT) || !filter_var($vidEstado, FILTER_VALIDATE_INT)) {
        return false;
    }

    if($_SESSION['id_nivel']==5){
        // Alternar el estado para activar o desactivar una cuenta si soy administrador
        $vidEstado = $vidEstado == 1 ? 2 : 1;
    }else{
        //si soy cliente que quiere dar de baja la cuenta solo asigno el 2=inactivo
        $vidEstado = 2;
    }

    // Preparar la consulta
    $SQL = "UPDATE usuarios SET id_estado = ? WHERE id_usuario = ?";

    // Usar una consulta preparada para evitar inyección SQL
    if ($stmt = mysqli_prepare($vConexion, $SQL)) {
        // Vincular parámetros (s: string, i: int, d: double, b: blob)
        mysqli_stmt_bind_param($stmt, "ii", $vidEstado, $vIdUsuario);

        // Ejecutar la consulta
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    } else {
        return false;
    }
}

function Update_Usuario($vConexion, $vIDUsuario) {

    if(!empty($_POST['ClaveNueva'])){
            
        // Construimos la consulta 
        $SQL= "UPDATE usuarios SET clave = MD5(?) WHERE id_usuario = ?";

        // Preparamos la consulta
        $stmtUsuarios = $vConexion->prepare($SQL);

        // Asignamos los parámetros
        $stmtUsuarios->bind_param("si", $_POST['ClaveNueva'], $vIDUsuario);

        // Ejecutamos la consulta para "usuarios"
        if (!$stmtUsuarios->execute()) {
            return false; // Error en la consulta
        }
        $stmtUsuarios->close();

        return true; // Actualización de Clave exitosa
    }    
    
    return true; // No Hubo cambios en la Clave.
}

function Update_Persona($vConexion, $vIDPersona){

    //actualizamos la tabla "personas"
    $SQLPersonas = "UPDATE personas SET nombre = ?, apellido = ?, fecha_nacimiento = ?, nro_telefono = ?, 
        calle = ?, sexo = ?, id_pais = ?, id_provincia = ?, id_localidad = ?
        WHERE id_persona = ?";

    // Preparamos la consulta para "personas"
    $stmtPersonas = $vConexion->prepare($SQLPersonas);
    $stmtPersonas->bind_param("ssssssiiii",
        $_POST['Nombre'], $_POST['Apellido'], $_POST['FechaNacimiento'], $_POST['Telefono'],
        $_POST['Domicilio'], $_POST['Sexo'], $_POST['Pais'],
        $_POST['Provincia'], $_POST['Localidad'], $vIDPersona );

    // Ejecutamos la consulta para "personas"
    if (!$stmtPersonas->execute()) {
        return false; // Error en la consulta
    }
    $stmtPersonas->close();

    // Actualizar las variables de sesión si no es el nivel 5
    if ($_SESSION['id_nivel'] != 5) {
        $_SESSION['nombre'] = $_POST['Nombre'] ?? $_SESSION['nombre'];
        $_SESSION['apellido'] = $_POST['Apellido'] ?? $_SESSION['apellido'];
        $_SESSION['sexo'] = $_POST['Sexo'] ?? $_SESSION['sexo'];
        $_SESSION['fecha_nacimiento'] = $_POST['FechaNacimiento'] ?? $_SESSION['fecha_nacimiento'];
        $_SESSION['nro_telefono'] = $_POST['Telefono'] ?? $_SESSION['nro_telefono'];
    }
    return true;
}

function Crear_Empleado($vConexion, $vIDUsuario){
    
    //`usuarios` (`id_usuario`, `clave`, `email`, `id_nivel`, `id_persona`, `id_estado`)
    $SQL_Update= "UPDATE usuarios SET id_nivel = ? WHERE id_usuario = ?";

    // Preparamos la consulta
    $stmtUsuarios = $vConexion->prepare($SQL_Update);

    // Asignamos los parámetros
    $stmtUsuarios->bind_param("ii", $_POST['Id_nivel'], $vIDUsuario);

    // Ejecutamos la consulta para "usuarios"
    if (!$stmtUsuarios->execute()) {
        return false; // Error en la consulta
    }
    $stmtUsuarios->close();

    $existeEmpleado= 0;
    // Verificamos si el empleado ya existe en la tabla empleados
    $SQL_Verificar = "SELECT COUNT(*) FROM empleados WHERE id_persona = ?";
    $stmtVerificar = $vConexion->prepare($SQL_Verificar);
    $stmtVerificar->bind_param("i", $_POST['Id_persona']);
    $stmtVerificar->execute();
    $stmtVerificar->bind_result($existeEmpleado);
    $stmtVerificar->fetch();
    $stmtVerificar->close();

    if ($existeEmpleado > 0) {
        // El empleado ya existe
        return false; 
    }


    $id_ocupacion= $_POST['Id_nivel'];

   // INSERT INTO `empleados` (`id_empleado`, `sueldo`, `fecha_alta`, `id_persona`, `id_ocupacion`)
    $SQL_Insert="INSERT INTO empleados (`id_empleado`, `sueldo`, `fecha_alta`, `id_persona`, `id_ocupacion`)
    VALUES ( NULL , '".$_POST['Sueldo']."' , NOW() , '".$_POST['Id_persona']."' , $id_ocupacion)";

    if (!mysqli_query($vConexion, $SQL_Insert)) {
        //si surge un error, finalizo la ejecucion del script con un mensaje
        echo "Error: " .$SQL_Insert. "<br>" . mysqli_error($vConexion);
        die('<h4>Error al intentar insertar el registro.</h4>');
    }

    return true;

}

function Update_Imagen_Perfil($vConexion, $vIDPersona){

    if(!empty($_FILES['Imagen_Perfil']["name"])){

        $SQL= "UPDATE personas SET imagen = ? WHERE id_persona = ?";
        // Preparamos la consulta para "personas"
        $stmtPersona = $vConexion->prepare($SQL);
        $stmtPersona->bind_param("si", $_FILES['Imagen_Perfil']["name"], $vIDPersona);

        // Ejecutamos la consulta
        if (!$stmtPersona->execute()) {
            return false; // Error en la consulta
        }
        $stmtPersona->close();

        // Se cambia la imagen de perfil para los usuarios
        if($_SESSION['id_nivel']!=5){
            $_SESSION['imagen'] = $_FILES['Imagen_Perfil']["name"];
        }
        $_SESSION['Mensaje'].= "Imagen actualizada de forma exitosa.";
        return true;
    }
    $_SESSION['Mensaje'].= "No hubo actualización para la imagen.";
    return true;
}
?>