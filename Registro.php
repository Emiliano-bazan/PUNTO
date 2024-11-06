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

// Procesar el formulario al enviar (cuando el método es POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recoger los datos enviados por el formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    // Validación simple (asegurarse de que los campos no estén vacíos)
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        echo "<script>alert('Todos los campos requeridos deben estar completos.');</script>";
    } else {
        // Encriptar la contraseña antes de almacenarla en la base de datos
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Usar sentencias preparadas para evitar inyección SQL
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nombre, $apellido, $email, $telefono, $passwordHash);

        if ($stmt->execute()) {
            // Redirigir a index.php si el registro es exitoso
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Cerrar la conexión
        $stmt->close();
    }
}

// Cerrar la conexión al finalizar el procesamiento
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="registro.css" />
  <title>Punto - Formulario de Registro</title>

  <!-- Estilos en el mismo archivo -->
  <style>
body {
  font-family: Arial, sans-serif;
  background-color: #1a1a1a;
  color: #e0e0e0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  margin: 0;
}

.container {
  background-color: #333333;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0,0,0,0.3);
  max-width: 400px;
  width: 100%;
}

h1 {
  text-align: center;
  color: #ccff00; /* Color de pelota de tenis */
  margin-bottom: 1.5rem;
}

form {
  display: flex;
  flex-direction: column;
}

label {
  margin-bottom: 0.5rem;
}

input {
  padding: 0.5rem;
  margin-bottom: 1rem;
  border: none;
  border-radius: 5px;
  background-color: #4a4a4a;
  color: #e0e0e0;
}

input[type="submit"] {
  background-color: #ccff00;
  color: #1a1a1a;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
  background-color: #b3e600;
}




  </style>
</head>

<body>
  <div class="container">
    <h1>Registro en Punto</h1>
    <form method="POST" action="">
      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required>
      
      <label for="apellido">Apellido:</label>
      <input type="text" id="apellido" name="apellido" required>
      
      <label for="email">Correo electrónico:</label>
      <input type="email" id="email" name="email" required>
      
      <label for="telefono">Teléfono:</label>
      <input type="tel" id="telefono" name="telefono">
      
      <label for="password">Contraseña:</label>
      <input type="password" id="password" name="password" required>
      
      <label for="confirm-password">Confirmar contraseña:</label>
      <input type="password" id="confirm-password" name="confirm-password" required>
      
      <input type="submit" value="Registrarse">
    </form>
  </div>

  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm-password').value;
      
      if (password !== confirmPassword) {
        alert('Las contraseñas no coinciden. Por favor, inténtalo de nuevo.');
        e.preventDefault(); // Evitar que el formulario se envíe si las contraseñas no coinciden.
      }
    });
  </script>
</body>
</html>
