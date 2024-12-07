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
if ($userRole != 1) {
    header('Location: dashboard.php');  // Redirige al login si no hay token
    exit();
}
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

           
        <h4 class="mt-4 text-center">Lista de Usuarios</h4>
        
            <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-success" id="agregar-proveedores" onclick="window.location.href='agregar_usuario.php'">Agregar...</button>
            </div>
        <div id="usuarios" class="table-responsive mt-3 mx-auto">
        <table class="table table-bordered text-center" style="max-width: 100%;">  <!-- Agregado el max-width -->
            <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Rol</th>
            <th>Nombre</th>
            <th>Documento</th>
            <th># Documento</th>
            <th>Direccion</th>
            <th>Telefono</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody id="usuarios-tbody">
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

  
fetch('http://localhost:5000/api/auth/usuarios', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.error) {
    alert('Error al cargar los usuarios: ' + data.error);
    return;
  }

  const usuariosBody = document.getElementById('usuarios-tbody');
  usuariosBody.innerHTML = ''; // Limpia la tabla antes de agregar usuarios

  data.forEach(usuario => {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${usuario.idusuario}</td>
    <td>
          ${usuario.idrol === 2 ? 'Encargado' : usuario.idrol === 3 ? 'Empleado' : 'Otro rol'}
        </td>
    <td>${usuario.nombre}</td>
    <td>${usuario.tipo_documento === null ? '' : usuario.tipo_documento}</td>
    <td>${usuario.num_documento === null ? '' : usuario.num_documento }</td>
    <td>${usuario.direccion== null ? '' : usuario.direccion}</td>
    <td>${usuario.telefono== null ? '' : usuario.telefono}</td>
    <td>${usuario.email}</td>
    
  `;


  usuariosBody.appendChild(row);
});

})
.catch(error => console.error('Error:', error));


  // Cerrar sesión
  document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
