<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/idumentaria.css" />
  <title>PUNTO - Indumentaria</title>
</head>

<body>
  <header>
    <div class="logo-container">
      <img src="img/punto.png" alt="Logo" class="logo">
    </div>
    <nav>
      <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="indumentaria.php">Indumentaria</a></li>
        <li><a href="contacto.html">Contacto</a></li>
        <li><a href="insertar_indumentaria.php">Admin</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h1>Lista de Indumentaria</h1>
    <div class="product-container">
      <?php
      // Conectar a la base de datos
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "tienda_punto";

      // Crear la conexión
      $conn = new mysqli($servername, $username, $password, $dbname);

      // Verificar la conexión
      if ($conn->connect_error) {
          die("Conexión fallida: " . $conn->connect_error);
      }

      // Obtener los productos desde la base de datos
      $sql = "SELECT nombre, precio, descripcion, imagen FROM items WHERE activo = 1";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
          // Mostrar los productos
          while($row = $result->fetch_assoc()) {
              echo '<div class="product">';
              echo '<img src="' . $row["imagen"] . '" alt="' . $row["nombre"] . '">';
              echo '<h2>' . $row["nombre"] . '</h2>';
              echo '<p>Precio: $' . $row["precio"] . '</p>';
              echo '<p>' . $row["descripcion"] . '</p>';
              echo '</div>';
          }
      } else {
          echo "<p>No se encontraron productos.</p>";
      }

      // Cerrar la conexión
      $conn->close();
      ?>
    </div>
  </main>

  <footer>
    <p>&copy; 2024 PUNTO - Todos los derechos reservados</p>
  </footer>
</body>
</html>
