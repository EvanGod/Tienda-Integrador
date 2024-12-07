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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

    <?php if ($userRole == 1 || $userRole == 2): ?>
        
        <h4 class="mt-4 text-center">Lista de Proveedores</h4>
        <?php if ($userRole == 2): ?>
            <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-success" id="agregar-proveedores" onclick="window.location.href='agregar_proveedor.php'">Agregar...</button>
            </div>
        <?php endif; ?>
        <div id="proveedores" class="table-responsive mt-3 mx-auto">
        <table class="table table-bordered text-center" style="max-width: 100%;">  <!-- Agregado el max-width -->
            <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Documento</th>
            <th># Documento</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody id="proveedores-tbody">
          <!-- Proveedores cargados dinámicamente aquí -->
        </tbody>
      </table>
        </div>
  
  <?php endif; ?>

  <?php if ($userRole == 1 || $userRole == 3): ?>
        <h4 class="mt-4 text-center">Lista de Clientes</h4>
        <?php if ($userRole == 3): ?>
            <div class="d-flex justify-content-end mt-3">
  <button class="btn btn-success" id="agregar-clientes" onclick="window.location.href='agregar_cliente.php'">Agregar...</button>
</div>
        <?php endif; ?>
        <div id="clientes" class="table-responsive mt-3 mx-auto">
        <table class="table table-bordered text-center" style="max-width: 100%;">  <!-- Agregado el max-width -->
            <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Documento</th>
            <th># Documento</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody id="clientes-tbody">
          <!-- Clientes cargados dinámicamente aquí -->
        </tbody>
      </table>
        </div>
  
  <?php endif; ?>
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

  <?php if ($userRole == 2 || $userRole == 1): ?>
  // Cargar productos
  // Cargar productos
fetch('http://localhost:5000/api/personas/proveedores', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.error) {
    alert('Error al cargar los proveedores: ' + data.error);
    return;
  }

  const proveedoresBody = document.getElementById('proveedores-tbody');
  proveedoresBody.innerHTML = ''; // Limpia la tabla antes de agregar proveedores

  data.forEach(proveedor => {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${proveedor.idpersona}</td>
    <td>${proveedor.nombre}</td>
    <td>${proveedor.tipo_documento}</td>
    <td>${proveedor.num_documento}</td>
    <td>${proveedor.direccion}</td>
    <td>${proveedor.telefono}</td>
    <td>${proveedor.email}</td>
    
  `;


  proveedoresBody.appendChild(row);
});

})
.catch(error => console.error('Error:', error));
<?php endif; ?>
<?php if ($userRole == 3 || $userRole == 1): ?>
fetch('http://localhost:5000/api/personas/compradores', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.error) {
    alert('Error al cargar los clientes: ' + data.error);
    return;
  }

  const clientesBody = document.getElementById('clientes-tbody');
  clientesBody.innerHTML = ''; // Limpia la tabla antes de agregar clientes

  data.forEach(cliente => {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${cliente.idpersona}</td>
    <td>${cliente.nombre}</td>
    <td>${cliente.tipo_documento}</td>
    <td>${cliente.num_documento}</td>
    <td>${cliente.direccion}</td>
    <td>${cliente.telefono}</td>
    <td>${cliente.email}</td>
    
  `;


  clientesBody.appendChild(row);
});

})
.catch(error => console.error('Error:', error));
<?php endif; ?>

  // Cerrar sesión
  document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
