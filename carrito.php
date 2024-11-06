<?php
session_start();
require 'mercado_pago_config.php';
authenticate();

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "punto";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Calcular el total del carrito
$total_cart = 0;
$total_puntos = 0;
$items = [];

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_cart += $item['total_price'] ?? 0;
        $total_puntos += $item['puntos'] ?? 0;

        // Preparar productos para la preferencia
        $items[] = [
            "title" => $item['nombre'],
            "quantity" => 1,
            "unit_price" => $item['total_price'],
            "currency_id" => "ARS"
        ];
    }
} else {
    $cart_empty = true;
}

// Crear preferencia
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

function createPaymentPreference($items) {
    $preferenceClient = new PreferenceClient();
    $payer = [
        "name" => "Nombre del Cliente",
        "email" => "email_cliente@dominio.com"
    ];

    $preferenceData = [
        "items" => $items,
        "payer" => $payer,
        "payment_methods" => [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ],
        "back_urls" => [
            "success" => "http://tusitio.com/success",
            "failure" => "http://tusitio.com/failed"
        ],
        "auto_return" => "approved",
    ];

    try {
        return $preferenceClient->create($preferenceData);
    } catch (MPApiException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

$preference = createPaymentPreference($items);
$checkoutUrl = $preference ? $preference->init_point : "#";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - PUNTO</title>
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

        .logo {
            width: 100px;
            height: 100px;
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

        .cart-container {
            background-color: var(--light-gray);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        .cart-item {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid var(--dark-gray);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item h4 {
            margin: 0;
        }

        .total {
            font-weight: bold;
            font-size: 1.5em;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            background-color: var(--tennis-ball);
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #aacc00; /* Un tono más oscuro para el hover */
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
        <h1 style="color: var(--tennis-ball);">PUNTO</h1>
        <nav>
            <ul>
                <li><a href="/">Inicio</a></li>
                <li><a href="/indumentaria">Indumentaria</a></li>
                <li><a href="/equipamiento">Equipamiento</a></li>
                <li><a href="/contacto">Contacto</a></li>
                <li><a href="/carrito.php">Carrito</a></li> <!-- Enlace al carrito -->
            </ul>
        </nav>
    </header>

    <main>
        <h2>Tu Carrito</h2>

        <div class="cart-container">
            <?php if (isset($cart_empty) && $cart_empty): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <div class="cart-item">
                        <h4><?php echo $item['nombre']; ?></h4>
                        <p>Precio: $<?php echo $item['total_price'] ?? 0; ?></p>
                    </div>
                <?php endforeach; ?>

                <div class="total">
                    <p>Total: $<?php echo $total_cart; ?></p>
                    <p>Puntos acumulados: <?php echo $total_puntos; ?></p>
                </div>
                <a href="<?php echo $checkoutUrl; ?>" class="btn">Realizar Pago</a>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 PUNTO - Todos los derechos reservados</p>
    </footer>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
