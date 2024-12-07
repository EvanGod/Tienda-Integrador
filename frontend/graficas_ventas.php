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
if ($userRole != 1 && $userRole != 3) {
    header('Location: dashboard.php');  // Redirige al login si no hay token
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grafica de ventas</title>
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
  <h3>Gráfica de Ventas por Productos</h3>
  <!-- Canvas para la gráfica de ventas por productos -->
<canvas id="ventasPorProductoChart" width="400" height="200"></canvas>

</div>


    <div class="container mt-5">
  <h3>Gráficas de Ventas</h3>
 
  
  <!-- Canvas para la gráfica -->
  <canvas id="ventasChart" width="400" height="200"></canvas>
</div>


<div class="container mt-5">
  <h3>Ventas Detalladas</h3>
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>ID Venta</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Total</th>
        <th>Exportar</th>
      </tr>
    </thead>
    <tbody id="ventas-table-body">
      <!-- Las filas se generarán dinámicamente -->
    </tbody>
  </table>
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
// Realizar la solicitud para obtener las ventas por día al cargar la página
window.onload = function() {
  // Enviar la solicitud al backend para obtener las ventas
  fetch('http://localhost:5000/api/ventas/ventas/por-dia', {  // La URL ya no necesita el parámetro de fecha
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token  // Pasa el token de autenticación
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      showError(data.error);
    } else {
      // Actualiza la gráfica con los datos recibidos
      actualizarGrafica(data);
    }
  })
  .catch(error => {
    console.error('Error al obtener las ventas:', error);
    showError('Error al obtener las ventas.');
  });
}

// Función para mostrar un mensaje de error en un modal
function showError(message) {
  const errorMessageElement = document.getElementById('error-message');
  errorMessageElement.textContent = message;

  const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
  errorModal.show();

  // Cerrar el modal después de 2 segundos
  setTimeout(() => {
    errorModal.hide();
  }, 2000);  // 2000 milisegundos = 2 segundos
}

function actualizarGrafica(data) {
  console.log('Datos recibidos:', data);  // Verifica los datos que recibes del backend

  const fechas = data.map(item => item.fecha);
  const totalVentas = data.map(item => item.total_ventas);
  const montoTotal = data.map(item => item.monto_total);

  console.log('Fechas:', fechas);
  console.log('Total Ventas:', totalVentas);
  console.log('Monto Total:', montoTotal);

  // Verifica si las listas no están vacías
  if (fechas.length === 0 || totalVentas.length === 0 || montoTotal.length === 0) {
    showError('No hay datos para mostrar en la gráfica');
    return;
  }

  const ctx = document.getElementById('ventasChart').getContext('2d');

  // Destruir el gráfico anterior si existe
  if (window.ventasChart instanceof Chart) {
    window.ventasChart.destroy();
  }

  // Crear un nuevo gráfico
  window.ventasChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: fechas,
      datasets: [{
        label: 'Ventas Totales',
        data: totalVentas,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }, {
        label: 'Monto Total',
        data: montoTotal,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}


/// Función para obtener ventas por productos
function obtenerVentasPorProducto() {
  fetch('http://localhost:5000/api/ventas/ventas/por-producto', {  // Cambia la ruta si es necesario
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token  // Pasa el token de autenticación
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      showError(data.error);
    } else {
      // Actualiza la gráfica con los datos de ventas por producto
      actualizarGraficaVentasPorProducto(data);
    }
  })
  .catch(error => {
    console.error('Error al obtener las ventas por producto:', error);
    showError('Error al obtener las ventas por producto.');
  });
}

// Función para actualizar la gráfica de ventas por producto
function actualizarGraficaVentasPorProducto(data) {
  const productos = data.map(item => item.producto);
  const totalVendido = data.map(item => item.total_vendido);
  const ingresosGenerados = data.map(item => item.ingresos_generados);

  console.log('Productos:', productos);
  console.log('Total Vendido:', totalVendido);
  console.log('Ingresos Generados:', ingresosGenerados);

  // Verifica si las listas no están vacías
  if (productos.length === 0 || totalVendido.length === 0 || ingresosGenerados.length === 0) {
    showError('No hay datos para mostrar en la gráfica de ventas por productos');
    return;
  }

  const ctx = document.getElementById('ventasPorProductoChart').getContext('2d');

  // Destruir el gráfico anterior si existe
  if (window.ventasPorProductoChart instanceof Chart) {
    window.ventasPorProductoChart.destroy();
  }

  // Crear un nuevo gráfico
  window.ventasPorProductoChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: productos,
      datasets: [{
        label: 'Total Vendido',
        data: totalVendido,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }, {
        label: 'Ingresos Generados',
        data: ingresosGenerados,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
}

// Llamar a la función para cargar la gráfica de ventas por producto
obtenerVentasPorProducto();


// Llamar a la función para obtener las ventas por producto cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
  obtenerVentasPorProducto();  // Llamar la función al cargar la página
});


// Función para cargar las ventas y llenar la tabla
function cargarVentas() {
    fetch('http://localhost:5000/api/ventas/obtener', { // Cambia la URL si es necesario
      method: 'GET',
      headers: {
        'Authorization': 'Bearer ' + token // Incluye el token de autenticación
      }
    })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          showError(data.error);
        } else {
          actualizarTablaVentas(data);
        }
      })
      .catch(error => {
        console.error('Error al cargar las ventas:', error);
        showError('Error al cargar las ventas.');
      });
  }

  // Función para actualizar la tabla con los datos de ventas
  function actualizarTablaVentas(data) {
    const tbody = document.getElementById('ventas-table-body');
    tbody.innerHTML = ''; // Limpia las filas existentes

    data.forEach(venta => {
      const row = document.createElement('tr');

      // Columnas
      row.innerHTML = `
        <td>${venta.idventa}</td>
        <td>${venta.cliente}</td>
        <td>${venta.fecha_hora}</td>
        <td>${venta.total}</td>
        <td>
      <button class="btn btn-primary btn-sm" onclick="exportarTicket(${venta.idventa})">Exportar</button>
    </td>
      `;

      tbody.appendChild(row);
    });
  }

  function exportarTicket(idventa) {
  fetch(`http://localhost:5000/api/ventas/ticket/${idventa}`, { // Incluir idventa en la ruta
      method: 'GET',
      headers: {
        'Authorization': 'Bearer ' + token // Incluye el token de autenticación
      }
    })
    .then(response => response.blob()) // Suponiendo que el backend te devuelve el PDF
    .then(blob => {
      // Crear un enlace para descargar el archivo PDF
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = `ticket_${idventa}.pdf`; // Nombre del archivo PDF
      link.click();
    })
    .catch(error => {
      console.error('Error al exportar el ticket:', error);
    });
}


  // Llamar a la función para cargar las ventas al cargar la página
  document.addEventListener('DOMContentLoaded', function () {
    cargarVentas();
  });


  // Cerrar sesión
  document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
