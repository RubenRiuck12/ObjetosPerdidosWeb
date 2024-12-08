<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'u793886059_semillon';
$username = 'u793886059_root';
$password = '1234567Ya$';
$port = '3306';

try {
    // Crear conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4;port=$port", $username, $password,);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si la conexión falla, retornar error
    http_response_code(500);
    echo json_encode(['error' => 'Error al conectar a la base de datos']);
    exit;
}

// Leer los datos enviados por POST en formato JSON
$data = json_decode(file_get_contents('php://input'), true);

// Validar que los datos requeridos están presentes
if (
    empty($data['idusuario']) ||
    empty($data['nombre']) ||
    empty($data['curp']) ||
    empty($data['telefono']) ||
    empty($data['email']) ||
    empty($data['password']) ||
    !isset($data['rol']) // Aunque el rol siempre será 1, validamos su existencia
) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

// Sanitizar y asignar valores
$idusuario = intval($data['idusuario']);
$nombre = htmlspecialchars($data['nombre']);
$curp = htmlspecialchars($data['curp']);
$telefono = htmlspecialchars($data['telefono']);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$password = htmlspecialchars($data['password']); // Almacena la contraseña directamente
$rol = intval($data['rol']); // Este será 1 por defecto

try {
    // Preparar la consulta SQL para insertar datos
    $stmt = $pdo->prepare("
        INSERT INTO Usuarios (IDUsuario, Nombre, CURP, NumTelefono, Email, Rol, Contraseña)
        VALUES (:idusuario, :nombre, :curp, :telefono, :email, :rol, :password)
    ");
    // Ejecutar la consulta con los datos
    $stmt->execute([
        ':idusuario' => $idusuario,
        ':nombre' => $nombre,
        ':curp' => $curp,
        ':telefono' => $telefono,
        ':email' => $email,
        ':rol' => $rol,
        ':password' => $password,
    ]);

    // Respuesta exitosa
    http_response_code(201);
    echo json_encode(['success' => 'Usuario registrado exitosamente']);
} catch (PDOException $e) {
    // Manejo de errores de la base de datos
    if ($e->getCode() == 23000) { // Código 23000 para errores de clave duplicada
        http_response_code(409);
        echo json_encode(['error' => 'El IDUsuario ya existe']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al registrar el usuario']);
    }
}
?>
