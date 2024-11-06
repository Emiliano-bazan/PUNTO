<?php
// Incluir el archivo de conexión
include 'conexion.php';

// Resto del código
$sql = "SELECT * FROM items WHERE activo = 1 and stock > 0";
$items = $conn->query($sql);
?>
<html><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PUNTO - Indumentaria</title>
<link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div id="loader">
    <div id="tennis-ball-loader"></div>
  </div>

  <header>
    <svg class="logo" viewBox="0 0 100 100">
      <defs>
        <linearGradient id="headerGradient" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="50%" style="stop-color:black;stop-opacity:1" />
          <stop offset="50%" style="stop-color:#333333;stop-opacity:1" />
        </linearGradient>
      </defs>
      <rect width="100" height="100" fill="url(#headerGradient)"/>
      <text x="50" y="55" font-size="60" text-anchor="middle" fill="var(--tennis-ball)" font-weight="bold" font-family="Arial, sans-serif" style="font-variant: small-caps;">P</text>
      <circle cx="50" cy="80" r="5" fill="var(--tennis-ball)"/>
    </svg>
    <nav>
      <ul>
        <li><a href="/">Inicio</a></li>
        <li><a href="/indumentaria">Indumentaria</a></li>
        <li><a href="insertar_indumentaria.php">Administrador</a></li>
        <li><a href="/menu">Menú</a></li>
        <li><a href="contacto.html">Contacto</a></li>
      </ul>
    </nav>
  </header>

    <main>
        <h1>INDUMENTARIA PUNTO</h1>
        <section class="menu-items">
            <?php
            if ($items->num_rows > 0) {
                // Iterar sobre cada fila de resultados
                while($row = $items->fetch_assoc()) {
                    echo '<div class="menu-item">';
                    echo '<img src="' . $row["imagen"] . '" alt="' . $row["nombre"] . '" width="300" height="250">';
                    echo '<div class="menu-info">';
                    echo '<h3>' . $row["nombre"] . '</h3>';
                    echo '<p>' . $row["descripcion"] . '</p>';
                    echo '<p>Apurate que solo quedan ' . $row["stock"] . '</p>';
                    echo '<span class="price">$' . number_format($row["precio"], 2) . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No hay ítems disponibles en este momento.</p>';
            }
            ?>
        </section>
  </main>

  <div class="nutritional-info">
    <button class="close-btn">&times;</button>
    <h4>Información Nutricional</h4>
    <div id="nutrition-content"></div>
  </div>

  <footer>
    <p>&copy; 2024 PUNTO - Todos los derechos reservados</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
      }, 1500);

      const menuLinks = document.querySelectorAll('nav ul li a');
      menuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          document.getElementById('loader').style.display = 'flex';
          setTimeout(() => {
            window.location.href = link.href;
          }, 1500);
        });
      });

      const menuItems = document.querySelectorAll('.menu-item');
      const nutritionalInfo = document.querySelector('.nutritional-info');
      const nutritionContent = document.getElementById('nutrition-content');
      const closeBtn = document.querySelector('.close-btn');

      menuItems.forEach(item => {
        item.addEventListener('click', () => {
          nutritionContent.innerHTML = item.dataset.nutrition;
          nutritionalInfo.style.display = 'block';
        });
      });

      closeBtn.addEventListener('click', () => {
        nutritionalInfo.style.display = 'none';
      });

      window.addEventListener('click', (e) => {
        if (e.target === nutritionalInfo) {
          nutritionalInfo.style.display = 'none';
        }
      });

      menuItems.forEach(item => {
        item.addEventListener('mousemove', (e) => {
          const rect = item.getBoundingClientRect();
          const x = e.clientX - rect.left;
          const y = e.clientY - rect.top;
          
          item.style.setProperty('--mouse-x', `${x}px`);
          item.style.setProperty('--mouse-y', `${y}px`);
        });
      });
    });
  </script>
</body>
</html>

<?php
// Cerrar la conexión
$conn->close();
?>