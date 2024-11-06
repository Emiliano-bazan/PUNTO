<?php
session_start();

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

// Agregar un producto al carrito
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1; // Valor por defecto

    // Consultar detalles del producto
    $sql = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // Calcular el total de la cantidad del producto
        $total_price = $product['precio'] * $quantity;

        // Agregar el producto al carrito o actualizar la cantidad si ya existe
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity; // Sumar la cantidad
            $_SESSION['cart'][$product_id]['total_price'] += $total_price; // Sumar el precio total
        } else {
            $product['quantity'] = $quantity; // Añadir la cantidad al producto
            $product['total_price'] = $total_price; // Guardar el precio total
            $_SESSION['cart'][$product_id] = $product;
        }
    }

    $stmt->close();
}

// Consultar los artículos de indumentaria
$sql = "SELECT id, nombre, precio, stock, descripcion, imagen, puntos FROM items WHERE activo = 1";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indumentaria - PUNTO</title>
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

        header h1 {
            color: var(--tennis-ball); /* Cambiar el color de PUNTO */
        }

        nav ul {
            list-style-type: none;
            display: flex;
            gap: 1rem;
            position: relative;
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

        .cart-icon {
            position: relative;
            cursor: pointer;
        }

        .tooltip {
            position: absolute;
            top: 30px; /* Cambiar para que se despliegue hacia abajo */
            left: 0;
            background: var(--light-gray);
            border-radius: 5px;
            padding: 10px;
            display: none;
            z-index: 1000;
            max-height: 200px; /* Limitar la altura del tooltip */
            overflow-y: auto; /* Hacer scroll si es necesario */
        }

        .tooltip h4 {
            margin: 0;
            color: black;
        }

        .tooltip p {
            margin: 0;
            color: black;
        }

        main {
            padding: 2rem;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .category {
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .category img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .category:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .category:hover img {
            transform: scale(1.1);
        }

        .category h3 {
            color: black;
            margin-bottom: 0.5rem;
            font-size: 1.5em;
            transition: all 0.3s ease;
        }

        .category:hover h3 {
            color: var(--tennis-ball);
        }

        .category p {
            color: #555;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .category:hover p {
            color: black;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
        }

        .add-to-cart {
            background-color: var(--tennis-ball);
            color: black;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background-color: #b3e600;
        }

        .quantity-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .quantity-label {
            margin-right: 10px;
            color: white;
        }

        .quantity-input {
            border-radius: 15px; /* Bordes redondeados */
            padding: 5px;
            width: 60px;
            text-align: center;
            border: none;
        }
    </style>
</head>
<body>
<header>
    <h1>PUNTO</h1>
    <nav>
        <ul>
            <li><a href="/">Inicio</a></li>
            <li><a href="/indumentaria">Indumentaria</a></li>
            <li><a href="/equipamiento">Equipamiento</a></li>
            <li><a href="/contacto">Contacto</a></li>
            <li class="cart-icon" onmouseover="showTooltip()" onmouseout="hideTooltip()">
                <a href="carrito.php">🛒 Carrito</a>
                <div class="tooltip" id="tooltip">
                    <?php
                    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                        $total_cart = 0; // Variable para el total del carrito
                        foreach ($_SESSION['cart'] as $item) {
                            echo '<h4>' . $item['nombre'] . '</h4>';
                            echo '<p>Precio: $' . $item['total_price'] . '</p>'; // Muestra el precio total del artículo
                            $total_cart += $item['total_price']; // Sumar al total del carrito
                        }
                        echo '<hr>';
                        echo '<h4>Total Carrito: $' . $total_cart . '</h4>'; // Mostrar total
                    } else {
                        echo '<p>Carrito vacío</p>';
                    }
                    ?>
                </div>
            </li>
        </ul>
    </nav>
</header>


    <main>
        <h2>Indumentaria Cargada</h2>

        <?php
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Salida de cada fila
            echo '<div class="categories">';
            while ($row = $result->fetch_assoc()) {
                echo '<div class="category">';
                echo '<img src="' . $row['imagen'] . '" alt="' . $row['nombre'] . '">';
                echo '<h3>' . $row['nombre'] . '</h3>';
                echo '<p>Precio: $' . $row['precio'] . '</p>';
                echo '<p>' . $row['descripcion'] . '</p>';
                echo '<p>Puntos: ' . $row['puntos'] . '</p>';
                echo '<div class="quantity-container">';
                echo '<label class="quantity-label">Cantidad:</label>';
                echo '<input type="number" name="quantity" class="quantity-input" value="1" min="1" />';
                echo '</div>';
                echo '<form method="POST" action="">';
                echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="add_to_cart" class="add-to-cart">Añadir al Carrito</button>';
                echo '</form>';
                echo '</div>';
            }
            echo '</div>'; // Cerrar categorías
        } else {
            echo "<p>No hay artículos disponibles.</p>";
        }

        // Cerrar conexión
        $conn->close();
        ?>
    </main>

    <footer>
        <p>&copy; 2024 PUNTO. Todos los derechos reservados.</p>
    </footer>

    <script>
        function showTooltip() {
            document.getElementById('tooltip').style.display = 'block';
        }

        function hideTooltip() {
            document.getElementById('tooltip').style.display = 'none';
        }
    </script>
</body>
</html>






