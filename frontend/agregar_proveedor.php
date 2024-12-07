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

if ($userRole != 2) {
    header('Location: dashboard.php');  // Redirige al login si no hay token
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proveedores</title>
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
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Mensaje</h5>
      </div>
      <div class="modal-body" id="messageModalBody">
        Aquí va el mensaje.
      </div>
    </div>
  </div>
</div>


    <div class="d-flex justify-content-end mt-3">
      <button class="btn btn-danger" id="logout-btn">Cerrar sesión</button>
    </div>
  </div>

  <div class="container mt-5">
    <h3 class="text-center">Registrar Proveedor</h3>
    <form id="form-proveedor" class="mt-4">
      <input type="hidden" name="tipo_persona" id="tipo_persona" value="Proveedor">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="tipo_documento" class="form-label">Tipo de Documento</label>
          <select class="form-select" id="tipo_documento" name="tipo_documento">
            <option value="DNI">DNI</option>
            <option value="RUC">RUC</option>
            <option value="Pasaporte">Pasaporte</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="num_documento" class="form-label">Número de Documento</label>
          <input type="text" class="form-control" id="num_documento" name="num_documento">
        </div>
        <div class="col-md-6 mb-3">
          <label for="direccion" class="form-label">Dirección</label>
          <input type="text" class="form-control" id="direccion" name="direccion">
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="telefono" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Correo Electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
      </div>

      <div class="d-flex justify-content-center">
      <button type="button" class="btn btn-primary" id="submitButton">Aceptar</button>

      </div>
    </form>
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

  document.addEventListener('DOMContentLoaded', function() {
  const submitButton = document.getElementById('submitButton');
  
  // Verifica si el botón existe en el DOM
  if (submitButton) {
    submitButton.addEventListener('click', () => {
      // Capturar valores del formulario
      const tipo_persona = document.getElementById('tipo_persona').value.trim();
      const nombre = document.getElementById('nombre').value.trim();
      const tipo_documento = document.getElementById('tipo_documento').value.trim();
      const num_documento = document.getElementById('num_documento').value.trim();
      const direccion = document.getElementById('direccion').value.trim();
      const telefono = document.getElementById('telefono').value.trim();
      const email = document.getElementById('email').value.trim();

      // Validar campos requeridos
      if (!nombre || !email) {
        showMessageModal('Por favor, complete todos los campos obligatorios.');
        return;
      }

      // Crear el objeto de datos
      const formData = {
        tipo_persona,
        nombre,
        tipo_documento,
        num_documento,
        direccion,
        telefono,
        email,
      };

      // Enviar datos al servidor usando Fetch API
      fetch('http://localhost:5000/api/personas/insertar-proveedor', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(formData) // Enviar los datos del formulario al servidor
})
.then(response => response.json())  // Procesar la respuesta en formato JSON
.then(data => {
    if (data.success) {
        setTimeout(() => {
            // Cerrar el modal con el método de Bootstrap
            const modal = new bootstrap.Modal(document.getElementById('messageModal'));
            modal.hide();
            
            // Redirigir a personas.php después de cerrar el modal
            window.location.href = 'personas.php';
          }, 2000);
    } else {
        // Si hay un error, muestra un mensaje de error que viene del backend
        const errorMessage = data.message || 'Hubo un error al registrar el proveedor. Intenta nuevamente.';
        showMessageModal(errorMessage);
    }
})
.catch(error => {
    // Manejo de errores de la petición Fetch
    console.error('Error:', error);
    showMessageModal('Error al enviar los datos. Intenta nuevamente.');
});
    });
  }

  // Función para mostrar el modal con el mensaje
  function showMessageModal(message) {
    const modalBody = document.getElementById('messageModalBody');
    modalBody.innerHTML = message;
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    messageModal.show();

    // Asegurarse de que el modal se cierre después de unos segundos
    setTimeout(() => {
      messageModal.hide();
    }, 3000); // Cierra el modal después de 3 segundos
  }
});

document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });




</script>

<!-- JavaScript necesario para el funcionamiento de Bootstrap (modal, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
