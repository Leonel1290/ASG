<?php
// Configuración de la Base de Datos
// *** REEMPLAZA ESTOS VALORES CON LOS DETALLES DE TU BASE DE DATOS CLEVER CLOUD ***
$servername = "beqt3q0gyevl7xonxald-mysql.services.clever-cloud.com"; 
$username = "ujlzjpclm80hxedm";
$password = "2OJyrxmcM62meKPHo7mu";
$dbname = "beqt3qoyevl7xonxald";
$port = 3306;

// Clave API secreta: Se obtiene de las variables de entorno de Render
// Asegúrate de que la variable de entorno 'API_KEY_SECRET' esté configurada en Render.
$api_key_expected = 'API_KEY_SECRET'; 

// ----------------------------------------------------
// CÓDIGOS DE RESPUESTA PARA EL ESP32 (Errores internos)
// ----------------------------------------------------
// -1: Dispositivo MAC no encontrado en la base de datos
// -2: Clave API inválida (Fallo de seguridad/autenticación)
// -3: Error de conexión a la base de datos MySQL (Fallo del servidor)
// -4: Parámetros MAC o API Key faltantes en la solicitud
// ----------------------------------------------------

/**
 * Función para sanear la entrada (seguridad básica)
 * @param string $data La cadena de entrada.
 * @return string La cadena saneada.
 */
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    // Nota: El uso de sentencias preparadas de MySQLi proporciona la mejor defensa contra inyección SQL.
    return $data;
}

// 1. Verificar que los parámetros necesarios estén presentes en la URL
if (!isset($_GET['mac']) ||!isset($_GET['api_key'])) {
    echo "-4"; // Parámetros MAC o API Key faltantes
    exit();
}

$mac_address = test_input($_GET['mac']);
$api_key_received = test_input($_GET['api_key']);

// 2. Verificar la Clave API
if (!$api_key_expected |

| $api_key_received!== $api_key_expected) {
    // Si la clave API no está definida en el entorno O no coincide
    error_log("Alerta de seguridad: Intento de acceso con API Key inválida.");
    echo "-2"; 
    exit();
}

// 3. Establecer Conexión con la Base de Datos
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Verificar conexión
if ($conn->connect_error) {
    error_log("Error de conexión a la base de datos: ". $conn->connect_error);
    echo "-3"; // Problema de conexión a DB
    exit();
}

// 4. Preparar y Ejecutar la Consulta SQL
// Se usa una sentencia preparada para prevenir la inyección SQL.
$stmt = null;
try {
    $stmt = $conn->prepare("SELECT estado_valvula FROM dispositivos WHERE MAC =?");
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: ". $conn->error);
    }
    
    // El parámetro "s" indica que el valor de $mac_address es una string
    $stmt->bind_param("s", $mac_address);
    $stmt->execute();
    $result = $stmt->get_result();

    // 5. Devolver el Resultado
    if ($result->num_rows > 0) {
        // Dispositivo encontrado, devolver el estado de la válvula
        $row = $result->fetch_assoc();
        echo $row['estado_valvula'];
    } else {
        // No se encontró ningún dispositivo con la MAC dada
        echo "-1";
    }

} catch (Exception $e) {
    error_log("Error de ejecución de consulta: ". $e->getMessage());
    echo "-3"; // Error interno del servidor al procesar la consulta
} finally {
    // 6. Cerrar Conexiones
    if ($stmt) {
        $stmt->close();
    }
    $conn->close();
}
?>