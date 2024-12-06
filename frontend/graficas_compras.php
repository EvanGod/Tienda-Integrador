<?php
session_start(); // Inicia la sesión
// Verifica si el token existe en la sesión
if (!isset($_SESSION['token'])) {
    header('Location: index.php');  // Redirige al login si no hay token
    exit();
}

$token = $_SESSION['token'];  // Obtén el token desde la sesión
// Decodifica el token para obtener la información del usuario (puedes usar una librería como JWT para decodificarlo)
function decodeToken($token) {
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return null;
    }

    $payload = base64_decode($parts[1]);
    return json_decode($payload, true);
}

$user = decodeToken($token);  // Decodifica el token para obtener el rol del usuario
if (!$user) {
    echo 'Error al decodificar el token';
    exit();
}

$userRole = $user['role'];  // Obtén el rol del usuario desde el token
if ($userRole != 1 && $userRole != 2) {
    header('Location: dashboard.php');  // Redirige al login si no hay token
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Graficas ingresos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container mt-5">
    <div id="header" class="text-center">
      <!-- Aquí se modificará el encabezado según el rol -->
    </div>

    <!-- Sección de iconos -->
<div class="row mt-4 justify-content-center text-center" id="content">
  <!-- Los iconos y textos se generarán dinámicamente -->
</div>


    <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-danger" id="logout-btn">Cerrar sesión</button>
    </div>

    <div class="container mt-5">
  <h3>Gráfica de Ingresos por Productos</h3>
  <!-- Canvas para la gráfica de ventas por productos -->
<canvas id="ingresosPorProductoChart" width="400" height="200"></canvas>

</div>


    <div class="container mt-5">
  <h3>Gráficas de Ingresos</h3>
  <!-- Formulario para seleccionar la fecha -->
  
  <!-- Canvas para la gráfica -->
  <canvas id="ingresosChart" width="400" height="200"></canvas>
</div>



           
       
  


  </div>
 

  <!-- Modal para errores -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="error-message">
          <!-- Mensaje de error será insertado aquí -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
        <div>
           
      </div>
    </div>
  </div>

 





<script>
  // El token se encuentra en PHP y se pasa al frontend
  const token = '<?php echo $token; ?>';  // Obtén el token desde PHP
  const user = <?php echo json_encode($user); ?>;  // Pasa el usuario decodificado al frontend

  const userRole = user.role;

  const header = document.getElementById('header');
  const content = document.getElementById('content');

  // Modificar el encabezado y contenido según el rol
  if (userRole === 1) { // Administrador
    header.innerHTML = '<h3><a href="dashboard.php" style="text-decoration: none; color: black;">Panel de Administrador</a></h3>';
    content.innerHTML = `
      <div class="col-md-3" onclick="window.location.href='graficas_compras.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x" title="Gráficas de Compras"></i>
            <p>Gráficas de Compras</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='personas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Ver Proveedores y Compradores"></i>
            <p>Proveedores y Compradores</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='usuarios.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Agregar usuarios"></i>
            <p>Encargados y Empleados</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='graficas_ventas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x" title="Gráficas de Ventas"></i>
            <p>Gráficas de Ventas</p>
          </div>
        </div>
      </div>`;
  } else if (userRole === 2) { // Encargado
    header.innerHTML = '<h3><a href="dashboard.php" style="text-decoration: none; color: black;">Panel de Encargado</a></h3>';
    content.innerHTML = `
      <div class="col-md-3" onclick="window.location.href='registrar_compras.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-cart-plus fa-3x" title="Registrar Compras"></i>
            <p>Registrar Compras</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='personas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Registrar Proveedores"></i>
            <p>Registrar Proveedores</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='graficas_compras.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-pie fa-3x" title="Gráficas de Compras"></i>
            <p>Gráficas de Compras</p>
          </div>
        </div>
      </div>`;
  } else if (userRole === 3) { // Empleado
    header.innerHTML = '<h3><a href="dashboard.php" style="text-decoration: none; color: black;">Panel de Empleado</a></h3>';
    content.innerHTML = `
      <div class="col-md-3" onclick="window.location.href='registrar_venta.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-credit-card fa-3x" title="Registrar Venta"></i>
            <p>Registrar Venta</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='graficas_ventas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x" title="Gráficas de Ventas"></i>
            <p>Gráficas de Ventas</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='personas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Registrar Compradores"></i>
            <p>Registrar Compradores</p>
          </div>
        </div>
      </div>
      `;
  }
// Función para obtener los ingresos por día desde el backend
async function obtenerIngresosPorDia() {
  try {
    const response = await fetch('http://localhost:5000/api/ingresos/ingresos/dia', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,  // Pasa el token como Bearer en la cabecera
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error al obtener ingresos por día:', error);
  }
}

// Función para obtener los ingresos por producto desde el backend
async function obtenerIngresosPorProducto() {
  try {
    const response = await fetch('http://localhost:5000/api/ingresos/ingresos/producto', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`,  // Pasa el token como Bearer en la cabecera
        'Content-Type': 'application/json'
      }
    });
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error al obtener ingresos por producto:', error);
  }
}

// Función para generar la gráfica de ventas
async function generarGraficaVentas() {
  const ingresosPorDia = await obtenerIngresosPorDia();
  const fechas = ingresosPorDia.map(item => item.fecha);
  const totales = ingresosPorDia.map(item => item.total);

  const ctx = document.getElementById('ingresosChart').getContext('2d');
  const ventasChart = new Chart(ctx, {
    type: 'line', // O 'bar' dependiendo del tipo de gráfica
    data: {
      labels: fechas,
      datasets: [{
        label: 'Ingresos por Día',
        data: totales,
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        fill: false
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Función para generar la gráfica de ingresos por producto
async function generarGraficaIngresosPorProducto() {
  const ingresosPorProducto = await obtenerIngresosPorProducto();
  const nombresProductos = ingresosPorProducto.map(item => item.nombre);
  const cantidades = ingresosPorProducto.map(item => item.cantidad);
  const totales = ingresosPorProducto.map(item => item.total);

  const ctx = document.getElementById('ingresosPorProductoChart').getContext('2d');
  const ventasPorProductoChart = new Chart(ctx, {
    type: 'bar', // O 'pie', dependiendo del tipo de gráfica
    data: {
      labels: nombresProductos,
      datasets: [{
        label: 'Ingresos por Producto',
        data: totales,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Ejecuta las funciones para generar las gráficas cuando se cargue la página
window.onload = () => {
  generarGraficaVentas();
  generarGraficaIngresosPorProducto();
};


  // Cerrar sesión
  document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>