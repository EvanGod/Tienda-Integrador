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

if ($userRole != 3) { // Solo los empleados tienen acceso
    header('Location: dashboard.php');  
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrar Venta</title>
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

    

    
    <div class="mt-4">
      <h2>Registrar Venta</h2>
      <form id="venta-form">
        <div class="mb-3">
          <label for="idcliente" class="form-label">Cliente</label>
          <select class="form-control" id="idcliente" required>
            <!-- Opciones cargadas dinámicamente -->
          </select>
        </div>
        <div class="mb-3">
          <label for="tipo_comprobante" class="form-label">Tipo de Comprobante</label>
          <select class="form-control" id="tipo_comprobante" required>
            <option value="Factura">Factura</option>
            <option value="Recibo">Recibo</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="serie_comprobante" class="form-label">Serie de Comprobante</label>
          <input type="text" class="form-control" id="serie_comprobante" required>
        </div>
        <div class="mb-3">
          <label for="num_comprobante" class="form-label">Número de Comprobante</label>
          <input type="text" class="form-control" id="num_comprobante" required>
        </div>
        <h4>Detalles de la Venta</h4>
        <table class="table" id="detalle-venta-table">
          <thead>
            <tr>
              <th>Artículo</th>
              <th>Precio</th>
              <th>Cantidad</th>
              <th>Descuento</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <!-- Filas dinámicas aquí -->
          </tbody>
        </table>
        <button type="button" class="btn btn-primary mt-3" id="add-product-btn">Agregar Producto</button>
        <button type="submit" class="btn btn-success mt-3">Registrar Venta</button>
      </form>

      <!-- Total de la Venta -->
      <div class="mt-3">
        <h4>Total: <span id="total-venta">0.00</span></h4>
      </div>
    </div>
  </div>


  <!-- Modal de error -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel"><i class="fas fa-exclamation-triangle"></i> Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="errorMessage" class="text-center"></p>
      </div>
      <div class="modal-footer d-flex justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


  <script>

    // El token se encuentra en PHP y se pasa al frontend
  const token = '<?php echo $token; ?>';  // Obtén el token desde PHP
  const user = <?php echo json_encode($user); ?>;  // Pasa el usuario decodificado al frontend

  const userRole = user.role;
  const userId = user.id; 

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
  let productos = [];

// Mostrar errores en el modal
function showErrorModal(message) {
  const errorMessageElement = document.getElementById('errorMessage');
  errorMessageElement.textContent = message;

  const errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {
    keyboard: true
  });

  errorModal.show();

  // Cerrar el modal automáticamente después de 3 segundos
  setTimeout(() => {
    errorModal.hide();
  }, 3000);
}

// Cargar clientes
fetch('http://localhost:5000/api/personas/compradores', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
  .then(response => response.json())
  .then(clientes => {
    const idClienteSelect = document.getElementById('idcliente');
    idClienteSelect.innerHTML = clientes.map(cliente => `<option value="${cliente.idpersona}">${cliente.nombre}</option>`).join('');
  })
  .catch(error => {
    console.error('Error al cargar clientes:', error);
    showErrorModal('Error al cargar la lista de clientes. Intenta nuevamente.');
  });

// Cargar productos
fetch('http://localhost:5000/api/productos/prod', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  }
})
  .then(response => response.json())
  .then(data => {
    productos = data; // Guarda los productos en el array
    console.log('Productos cargados:', productos);
  })
  .catch(error => {
    console.error('Error al cargar productos:', error);
    showErrorModal('Error al cargar la lista de productos. Intenta nuevamente.');
  });

  document.getElementById('add-product-btn').addEventListener('click', () => {
  if (productos.length === 0) {
    console.error('No hay productos disponibles');
    showErrorModal('No hay productos disponibles para agregar.');
    return;
  }

  const tableBody = document.querySelector('#detalle-venta-table tbody');
  const row = document.createElement('tr');
  row.innerHTML = `
    <td>
      <select class="form-control select-articulo" name="articulo">
        ${productos.map(producto => `<option value="${producto.idarticulo}">${producto.nombre}</option>`).join('')}
      </select>
    </td>
    <td>
    <input 
      type="number" 
      class="form-control precio" 
      readonly 
      step="0.01" 
      min="0" ">
  </td>
    <td>
      <input 
        type="number" 
        class="form-control cantidad" 
        placeholder="Cantidad" 
        required 
        name="cantidad">
    </td>
    <td>
      <input 
        type="number" 
        class="form-control descuento" 
        placeholder="Descuento" 
        required 
        name="descuento">
    </td>
    <td>
      <button type="button" class="btn btn-danger remove-row-btn">Eliminar</button>
    </td>
  `;

  
  
  const selectArticulo = row.querySelector('.select-articulo');
    const inputPrecio = row.querySelector('.precio');

    // Actualizar el precio cuando se seleccione un producto
    selectArticulo.addEventListener('change', () => {
      const articulo = productos.find(p => p.idarticulo == selectArticulo.value);
      if (articulo) {
        inputPrecio.value = articulo.precio_venta;
      }
    });

    // Inicializar el precio del producto seleccionado por defecto
    const initialArticulo = productos.find(p => p.idarticulo == selectArticulo.value);
    if (initialArticulo) {
      inputPrecio.value = initialArticulo.precio_venta;
    }




  tableBody.appendChild(row);
  updateTotal();
});



function updateTotal() {
  let totalVenta = 0;
  const rows = document.querySelectorAll('#detalle-venta-table tbody tr');

  rows.forEach(row => {
    const precio = parseFloat(row.querySelector('.precio').value) || 0;
    const cantidad = parseInt(row.querySelector('.cantidad').value) || 0;
    const descuento = parseFloat(row.querySelector('.descuento').value) || 0;

    const totalProducto = (precio * cantidad) - descuento;
    totalVenta += totalProducto;
  });

  document.getElementById('total-venta').textContent = totalVenta.toFixed(2);
}

// Escucha los cambios en los productos seleccionados, cantidades y descuentos
document.addEventListener('change', (event) => {
  const target = event.target;

  // Actualizar precio según el artículo seleccionado
  if (target.matches('.select-articulo')) {
    const row = target.closest('tr');
    const inputPrecio = row.querySelector('.precio');

    // Buscar el artículo seleccionado en el array `productos`
    const articuloSeleccionado = productos.find(producto => producto.idarticulo == target.value);

    if (articuloSeleccionado) {
      inputPrecio.value = articuloSeleccionado.precio_venta.toFixed(2); // Asignar el precio con formato
    } else {
      console.error('No se encontró el artículo seleccionado en la lista de productos.');
      inputPrecio.value = "0.00"; // Fallback en caso de error
    }

    updateTotal(); // Actualizar el total después de cambiar el precio
  }

  // Actualizar el total cuando cambian cantidad o descuento
  if (target.matches('.cantidad') || target.matches('.descuento')) {
    updateTotal();
  }
});
;



// Eliminar fila (usando delegación de eventos)
document.querySelector('#detalle-venta-table').addEventListener('click', (e) => {
  if (e.target.classList.contains('remove-row-btn')) {
    e.target.closest('tr').remove();
    //updateTotal();
  }
});

// Registrar venta
document.getElementById('venta-form').addEventListener('submit', async (e) => {
  e.preventDefault();

  const detalles = [];
  document.querySelectorAll('#detalle-venta-table tbody tr').forEach(row => {
    const idarticulo = parseInt(row.querySelector('.select-articulo').value);
    const precio = parseFloat(row.querySelector('.precio').value);
    const cantidad = parseFloat(row.querySelector('.cantidad').value);
    const descuento = parseFloat(row.querySelector('.descuento').value);

    if (!idarticulo || isNaN(precio) || isNaN(cantidad) || isNaN(descuento)) {
      showErrorModal('Por favor completa todos los campos correctamente en los detalles de la venta.');
      return;
    }

    detalles.push({ idarticulo, precio, cantidad, descuento });
  });

  const data = {
    idusuario: userId,
    idcliente: parseInt(document.getElementById('idcliente').value, 10),
    tipo_comprobante: document.getElementById('tipo_comprobante').value,
    serie_comprobante: document.getElementById('serie_comprobante').value,
    num_comprobante: document.getElementById('num_comprobante').value,
    detalles
  };

  try {
    const response = await fetch('http://localhost:5000/api/ventas/ventas', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    if (response.ok) {
      const result = await response.json();
      showErrorModal(`Venta registrada con éxito. ID Venta: ${result.idventa}`);
      window.location.href = 'dashboard.php';
    } else {
      const errorResult = await response.json();
      showErrorModal(`Error al registrar la venta: ${errorResult.error || 'Error desconocido'}`);
    }
  } catch (error) {
    console.error('Error al realizar la solicitud:', error);
    showErrorModal('Error al registrar la venta. Revisa la consola para más detalles.');
  }
});

// Cerrar sesión
document.getElementById('logout-btn').addEventListener('click', () => {
  window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
});
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
