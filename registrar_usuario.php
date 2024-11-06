<?php
// Datos de conexión a la base de datos
$host = "localhost";
$user = "root"; // Cambia esto si usas otro usuario
$password = ""; // Cambia esto si tu base de datos tiene contraseña
$database = "punto"; // Cambia esto si tu base de datos se llama de otra forma

// Conexión a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Verificar si hay errores de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recoger los datos enviados por el formulario
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$password = $_POST['password'];

// Validación simple (asegurarse de que los campos no estén vacíos)
if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
    echo "Todos los campos requeridos deben estar completos.";
    exit;
}

// Encriptar la contraseña antes de almacenarla en la base de datos
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Consulta SQL para insertar los datos del nuevo usuario
$sql = "INSERT INTO usuarios (nombre, apellido, email, telefono, password) 
        VALUES ('$nombre', '$apellido', '$email', '$telefono', '$passwordHash')";

if ($conn->query($sql) === TRUE) {
    // Redirigir a index.php si el registro es exitoso
    header("Location: indumentaria.html");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>


