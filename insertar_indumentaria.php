<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Indumentaria</title>
    <style>
        :root {
            --tennis-ball: #ccff00;
            --dark-gray: #333333;
            --light-gray: #cccccc;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--dark-gray);
            color: white;
        }

        header {
            background: linear-gradient(to bottom, black 50%, var(--dark-gray) 50%);
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 1rem;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        nav ul li a:hover {
            color: var(--tennis-ball);
        }

        main {
            padding: 2rem;
        }

        .hero {
            background: linear-gradient(45deg, var(--tennis-ball), var(--dark-gray));
            color: black;
            padding: 3rem;
            text-align: center;
            margin-bottom: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .form-container {
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        label {
            display: block;
            margin-top: 1rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .preview-container {
            margin-top: 1rem;
        }

        #image-preview {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 0.5rem;
        }

        input[type="submit"] {
            background-color: var(--tennis-ball);
            color: black;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #a8d600;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>PUNTO - Tienda de Deportes</h1>
        <nav>
            <ul>
                <li><a href="/">Inicio</a></li>
                <li><a href="/indumentaria">Indumentaria</a></li>
                <li><a href="/equipamiento">Equipamiento</a></li>
                <li><a href="/contacto">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1>Cargar Indumentaria</h1>
            <div class="form-container">
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" required>

                    <label for="precio">Precio:</label>
                    <input type="number" name="precio" required min="0" step="0.01">

                    <label for="stock">Stock:</label>
                    <input type="number" name="stock" required min="0">

                    <label for="descripcion">Descripción:</label>
                    <textarea name="descripcion" required></textarea>

                    <label for="imagen">Imagen:</label>
                    <input type="file" name="imagen" accept="image/*" required onchange="previewImage(event)">

                    <div class="preview-container" id="preview-container">
                        <img id="image-preview" src="#" alt="Vista previa de la imagen" style="display: none;">
                    </div>

                    <label for="puntos">Puntos:</label>
                    <input type="number" name="puntos" required min="0">

                    <input type="submit" value="Cargar">
                </form>
            </div>
        </section>

        <?php
        // Conectar a la base de datos
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "punto"; 

        // Crear la conexión
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Verificar si el formulario ha sido enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener los valores del formulario
            $nombre = htmlspecialchars($_POST['nombre']);
            $precio = floatval($_POST['precio']);
            $stock = intval($_POST['stock']);
            $descripcion = htmlspecialchars($_POST['descripcion']);
            $puntos = intval($_POST['puntos']); // Captura de puntos
            
            // Manejar la imagen
            $imagen = $_FILES['imagen']['name'];
            $ruta = "uploads/" . basename($imagen);

            // Verificar si el archivo ya existe
            if (file_exists($ruta)) {
                echo "<p style='color: red;'>El archivo ya existe. Por favor, elige otro.</p>";
            } else {
                // Mover la imagen al directorio deseado
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
                    // Insertar los datos en la base de datos usando sentencias preparadas
                    $stmt = $conn->prepare("INSERT INTO items (nombre, precio, stock, descripcion, imagen, puntos, activo) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $activo = 1; // Indica que el artículo está activo
                    $stmt->bind_param("sdisdii", $nombre, $precio, $stock, $descripcion, $ruta, $puntos, $activo);

                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>Indumentaria cargada exitosamente</p>";
                    } else {
                        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>Error al subir la imagen.</p>";
                }
            }
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </main>

    <footer>
        <p>&copy; 2023 PUNTO - Todos los derechos reservados</p>
    </footer>

    <script>
        function previewImage(event) {
            const previewContainer = document.getElementById('preview-container');
            const imagePreview = document.getElementById('image-preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    imagePreview.src = reader.result;
                    imagePreview.style.display = 'block'; // Mostrar la imagen
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '#';
                imagePreview.style.display = 'none'; // Ocultar la imagen si no hay archivo
            }
        }
    </script>
</body>
</html>

