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

// Obtener los puntos del usuario
$usuario_id = 1; // Cambia esto para obtener el ID del usuario logueado
$result = $conn->query("SELECT puntos FROM usuarios WHERE id = $usuario_id");
$usuario = $result->fetch_assoc();
$userPoints = $usuario['puntos'] ?? 0; // Establece a 0 si no hay puntos

// Función para calcular puntos del carrito
function calculateTotalPoints($conn, $usuario_id) {
    // Aquí debes reemplazar con la lógica adecuada para obtener los puntos del carrito
    // Ya que parece que no estás usando una tabla 'carrito', simplemente 
    // devuelve 0 si no tienes un sistema de carrito.
    return 0; // Cambia esto si tienes otra lógica para sumar puntos del carrito
}

// Sumar puntos del carrito a los puntos totales
$totalPointsFromCart = calculateTotalPoints($conn, $usuario_id);
$userPoints += $totalPointsFromCart;

// Verifica si se está haciendo un canje
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redeemItem'])) {
    $item_id = intval($_POST['item_id']);
    $item_nombre = $_POST['item_nombre'];
    $puntos_requeridos = intval($_POST['puntos_requeridos']);

    if ($userPoints >= $puntos_requeridos) {
        // Actualizar los puntos del usuario
        $conn->query("UPDATE usuarios SET puntos = puntos - $puntos_requeridos WHERE id = $usuario_id");

        // Registrar el canjeo en el historial
        $conn->query("INSERT INTO historial (usuario_id, tipo, descripcion, puntos) VALUES ($usuario_id, 'Canje', '$item_nombre', -$puntos_requeridos)");
        
        // Actualiza los puntos del usuario
        $userPoints -= $puntos_requeridos;
        echo "Canje realizado con éxito.";
    } else {
        echo "No tienes suficientes puntos para este canje.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programa de Fidelización Punto</title>
    <link rel="stylesheet" href="css/fidelizacion.css" />
    <style>
        /* CSS interno */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .points-section, .items-section {
            margin-bottom: 20px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background: #f8f8f8;
        }
    </style>
</head>
<body>
<h1>Programa de Fidelización Punto</h1>

<div class="container">
    <div class="points-section">
        <h2>Tus Puntos</h2>
        <div class="points-graph-container">
            <svg width="200" height="200" viewBox="0 0 200 200">
                <circle class="points-graph-circle" cx="100" cy="100" r="90"/>
                <circle class="points-graph-fill" cx="100" cy="100" r="90" id="pointsGraphFill"/>
            </svg>
            <div class="points-graph-text">
                <span id="userPoints"><?php echo number_format($userPoints); ?></span><br>puntos
            </div>
        </div>
    </div>
    
    <div class="items-section">
        <h2>Canjea tus Puntos</h2>
        <div class="item">
            <span class="item-name">Descuento de $10</span>
            <span class="item-points">1,000 puntos</span>
            <form method="post">
                <input type="hidden" name="item_id" value="1">
                <input type="hidden" name="item_nombre" value="Descuento de $10">
                <input type="hidden" name="puntos_requeridos" value="1000">
                <button type="submit" name="redeemItem">Canjear</button>
            </form>
        </div>
        <div class="item">
            <span class="item-name">Producto Gratis</span>
            <span class="item-points">2,500 puntos</span>
            <form method="post">
                <input type="hidden" name="item_id" value="2">
                <input type="hidden" name="item_nombre" value="Producto Gratis">
                <input type="hidden" name="puntos_requeridos" value="2500">
                <button type="submit" name="redeemItem">Canjear</button>
            </form>
        </div>
        <div class="item">
            <span class="item-name">Envío Gratis</span>
            <span class="item-points">500 puntos</span>
            <form method="post">
                <input type="hidden" name="item_id" value="3">
                <input type="hidden" name="item_nombre" value="Envío Gratis">
                <input type="hidden" name="puntos_requeridos" value="500">
                <button type="submit" name="redeemItem">Canjear</button>
            </form>
        </div>
        <div class="item">
            <span class="item-name">Tarjeta Regalo de $50</span>
            <span class="item-points">5,000 puntos</span>
            <form method="post">
                <input type="hidden" name="item_id" value="4">
                <input type="hidden" name="item_nombre" value="Tarjeta Regalo de $50">
                <input type="hidden" name="puntos_requeridos" value="5000">
                <button type="submit" name="redeemItem">Canjear</button>
            </form>
        </div>
    </div>
</div>

<h2>Historial de Canjes y Compras</h2>
<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Descripción</th>
            <th>Puntos</th>
            <th>Gasto ($)</th>
        </tr>
    </thead>
    <tbody id="historyTable">
        <!-- Aquí se puede llenar el historial desde la base de datos -->
    </tbody>
</table>

<script>
    let userPoints = <?php echo $userPoints; ?>; // Recupera puntos del PHP
    const maxPoints = 10000; // Punto máximo para el gráfico

    function updatePoints() {
        document.getElementById('userPoints').textContent = userPoints.toLocaleString();
        updatePointsGraph();
    }

    function updatePointsGraph() {
        const graphFill = document.getElementById('pointsGraphFill');
        const circumference = 2 * Math.PI * 90; // 2πr
        const fillPercentage = (userPoints / maxPoints);
        const dashArray = circumference * fillPercentage;
        graphFill.style.strokeDasharray = `${dashArray} ${circumference}`;
        graphFill.style.strokeDashoffset = 0;
    }

    updatePoints();
</script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>
