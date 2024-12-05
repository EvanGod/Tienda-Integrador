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
  <title>Dashboard</title>
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

    

    <h3 class="mt-4 text-center">Lista de Productos</h3>
    <!-- Botón "Agregar..." solo visible para Admin y Encargado -->
<?php if ($userRole == 1 || $userRole == 2): ?>
  <div class="d-flex justify-content-end mt-3">
    <button class="btn btn-success" id="agregar-btn">Agregar...</button>
  </div>
<?php endif; ?>
  <div id="productos" class="table-responsive mt-3 mx-auto">
        <table class="table table-bordered text-center" style="max-width: 100%;">  <!-- Agregado el max-width -->
            <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Categoría</th>
            <th>Nombre</th>
            <th>Código</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Descripción</th>
            <th>Estado</th>
            <?php if ($userRole == 1 || $userRole == 2): // Administrador o Encargado ?>
              <th>Acciones</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody id="productos-tbody">
          <!-- Productos cargados dinámicamente aquí -->
        </tbody>
      </table>
        </div>
  </div>

  <!-- Modal de confirmación -->
<div id="modalConfirmacion" class="modal" style="display:none;">
  <div class="modal-content">
    <span id="btnCancelar" class="close">&times;</span>
    <h3>¿Estás seguro de que deseas eliminar este producto?</h3>
    <button id="btnConfirmar">Sí, eliminar</button>
  </div>
</div>
  <!-- Tabla de productos -->
  

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
    header.innerHTML = '<h3>Panel de Administrador</h3>';
    content.innerHTML = `
      <div class="col-md-3" onclick="window.location.href='graficas.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x" title="Gráficas de Compras"></i>
            <p>Gráficas de Compras</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='proveedores.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Ver Proveedores y Compradores"></i>
            <p>Proveedores y Compradores</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='gestion_productos.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-chart-line fa-3x" title="Gráficas de Ventas"></i>
            <p>Gráficas de Ventas</p>
          </div>
        </div>
      </div>`;
  } else if (userRole === 2) { // Encargado
    header.innerHTML = '<h3>Panel de Encargado</h3>';
    content.innerHTML = `
      <div class="col-md-3" onclick="window.location.href='registrar_compras.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-cart-plus fa-3x" title="Registrar Compras"></i>
            <p>Registrar Compras</p>
          </div>
        </div>
      </div>
      <div class="col-md-3" onclick="window.location.href='registrar_proveedores.php'">
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
    header.innerHTML = '<h3>Panel de Empleado</h3>';
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
      <div class="col-md-3" onclick="window.location.href='registrar_compradores.php'">
        <div class="card">
          <div class="card-body">
            <i class="fas fa-users fa-3x" title="Registrar Compradores"></i>
            <p>Registrar Compradores</p>
          </div>
        </div>
      </div>
      `;
  }

  // Cargar productos
  // Cargar productos
fetch('http://localhost:5000/api/productos/productos', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  if (data.error) {
    alert('Error al cargar los productos: ' + data.error);
    return;
  }

  const productosBody = document.getElementById('productos-tbody');
  productosBody.innerHTML = ''; // Limpia la tabla antes de agregar productos

  data.forEach(producto => {
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>${producto.idarticulo}</td>
    <td>${producto.categoria}</td>
    <td>${producto.nombre}</td>
    <td>${producto.codigo || 'N/A'}</td>
    <td>${producto.precio_venta}</td>
    <td>${producto.stock}</td>
    <td>${producto.descripcion || 'Sin descripción'}</td>
    <td>${producto.estado.data[0] === 1 ? 'Activo' : 'Eliminado'}</td>
    <?php if ($userRole == 1 || $userRole == 2): // Administrador o Encargado ?>
    <td class="d-flex justify-content-center">
  <button class="btn btn-warning btn-sm me-2" onclick="editarProducto(${producto.idarticulo})">Editar</button>
  <button class="btn btn-danger btn-sm btn-eliminar" data-id="${producto.idarticulo}">Eliminar</button>
</td>


    <?php endif; ?>
    
  `;

  // Aquí verificamos si el estado es 0 (eliminado) y luego ocultamos el botón de eliminar
  if (producto.estado.data[0] === 0) {
    // Accedemos al botón de eliminar y lo ocultamos
    const btnEliminar = row.querySelector('.btn-eliminar');
    if (btnEliminar) {
      btnEliminar.style.display = 'none';
    }
  }

  productosBody.appendChild(row);
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
