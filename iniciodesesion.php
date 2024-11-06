<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "punto";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $contrasena = $_POST['password']; // Asegúrate de que el nombre del input coincida

    // Asegúrate de que el nombre de la columna aquí coincida con el de tu base de datos
    $sql = "SELECT password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($contrasena, $hashed_password)) {
            // Inicio de sesión exitoso, redirigir a indumentaria2.php
            header("Location: indumentaria2.php");
            exit(); // Asegúrate de usar exit después de la redirección
        } else {
            // Contraseña incorrecta
            $message = 'La contraseña es incorrecta.';
        }
    } else {
        // Correo electrónico no encontrado
        $message = 'El correo electrónico no está registrado.';
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto - Inicio de Sesión</title>
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
            max-width: 350px;
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
        .links {
            text-align: center;
            margin-top: 1rem;
        }
        .links a {
            color: #ccff00;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión en Punto</h1>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <input type="submit" value="Iniciar Sesión">
        </form>
        <div class="links">
            <a href="https://punto.com/forgot-password">¿Olvidaste tu contraseña?</a>
            <br>
            <a href="registro.html">¿No tienes una cuenta? Regístrate</a>
        </div>
    </div>
</body>
</html>




