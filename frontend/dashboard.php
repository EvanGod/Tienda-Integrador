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
  <button type="button" class="btn btn-primary" onclick="agregarProducto()">
  Agregar Producto
</button>

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


<!-- Modal de edición de producto -->
<div class="modal" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
        <button type="button" class="btn-close" id="btnCerrarModal" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editProductForm">
          <h4>Campos no editables:</h4>
          <p><strong>ID:</strong> <span id="product-id"></span></p>
          <div class="mb-3">
            <label for="product-code" class="form-label">Código:</label>
            <input type="text" id="product-code" class="form-control">
          </div>
          <p><strong>Categoría:</strong> <span id="product-cate"></span></p>
          <div class="mb-3">
          <p><strong>Nombre:</strong> <span id="product-name"></span></p>
          </div>
          <div class="mb-3">
          <p><strong>Precio:</strong> <span id="product-price"></span></p>
          </div>
          <div class="mb-3">
          <p><strong>Stock:</strong> <span id="product-stock"></span></p>
          </div>
          <div class="mb-3">
            <label for="product-description" class="form-label">Descripción:</label>
            <textarea id="product-description" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label for="product-state" class="form-label">Estado:</label>
            <select id="product-state" class="form-control">
              <option value=1>Activo</option>
              <option value=0>Eliminado</option>
            </select>
          </div>
          <button type="button" class="btn btn-secondary" id="btnCancelarEdicion">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Modal de crear producto -->
<div class="modal" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="crearModalLabel">Crear Producto</h5>
      </div>
      <div class="modal-body">
        <form id="formAgregarProducto">
          <div class="mb-3">
            <label for="idcategoria" class="form-label">Categoría</label>
            <select class="form-control" id="idcategoria" name="idcategoria" required>
              <option value="">Seleccione una categoría</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="codigo" class="form-label">Código</label>
            <input type="text" class="form-control" id="codigo" name="codigo" required>
          </div>
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
          </div>
          <button type="button" class="btn btn-secondary" id="btnCancelarCrear">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
        </form>
      </div>
    </div>
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
// Aquí agregamos el evento después de cargar los productos
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    console.log('Botón de eliminar encontrado');
    btn.addEventListener('click', function() {
      const idProducto = btn.getAttribute('data-id');
      console.log(`ID Producto: ${idProducto}`);
      mostrarModalConfirmacion(idProducto); // Mostrar el modal con el ID del producto
    });
  });

})
.catch(error => console.error('Error:', error));

// Función para mostrar el modal de confirmación
function mostrarModalConfirmacion(id) {
  const modal = document.getElementById('modalConfirmacion');
  const btnConfirmar = document.getElementById('btnConfirmar');
  const btnCancelar = document.getElementById('btnCancelar');

  // Mostrar el modal
  modal.style.display = 'block';

  // Acción cuando se confirma la eliminación
  btnConfirmar.onclick = function() {
    eliminarProducto(id); // Llama a la función de eliminación
    modal.style.display = 'none'; // Cierra el modal
  };

  // Acción cuando se cancela
  btnCancelar.onclick = function() {
    modal.style.display = 'none'; // Cierra el modal
  };
}

// Función para eliminar un producto
// Función para eliminar un producto
function eliminarProducto(idProducto) {
  console.log(`Eliminando producto con ID: ${idProducto}`);

  fetch(`http://localhost:5000/api/productos/productos/${idProducto}`, {
  method: 'DELETE',
  headers: {
    'Authorization': 'Bearer <?php echo $_SESSION['token']; ?>',
    'Content-Type': 'application/json'
  }
})
.then(response => {
  console.log('Código de estado:', response.status); // Verifica el código de estado HTTP
  if (!response.ok) {
    throw new Error('Error al eliminar el producto');
  }
  return response.json();
})
.then(data => {
  console.log('Datos recibidos del servidor:', data);
  
  // Verifica si el mensaje es el esperado
  if (data.message && data.message === 'Producto eliminado') {
    console.log('Producto eliminado con éxito');
    document.querySelector(`button[data-id='${idProducto}']`).closest('tr').remove();
    location.reload(); // Recarga la página para actualizar el dashboard
  } else {
    console.log('Error al eliminar el producto:', data.error);
  }
})


.catch(error => {
  console.error('Error en la solicitud de eliminación:', error);
});

}




// Cerrar la modal cuando el usuario haga clic fuera de ella
window.onclick = function(event) {
  const modal = document.getElementById('modalConfirmacion');
  if (event.target === modal) {
    modal.style.display = 'none'; // Cierra el modal
  }
};

// Función para editar un producto
function editarProducto(idProducto) {
  // Llama a la API para obtener los detalles del producto
  fetch(`http://localhost:5000/api/productos/productos/${idProducto}`, {
    method: 'GET',
    headers: {
      'Authorization': `Bearer <?php echo $_SESSION['token']; ?>`,
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(producto => {

    console.log(producto)
    // Carga los datos del producto en el formulario
    document.getElementById('product-id').textContent = producto.idarticulo;
    document.getElementById('product-code').value = producto.codigo || 'N/A';
    document.getElementById('product-cate').textContent = producto.categoria_nombre || 'N/A';
    document.getElementById('product-name').textContent = producto.articulo_nombre;
    document.getElementById('product-price').textContent = producto.precio_venta;
    document.getElementById('product-stock').textContent = producto.stock;
    document.getElementById('product-description').value = producto.descripcion || '';
    document.getElementById('product-state').value = producto.estado.data[0];

    // Muestra el modal
    document.getElementById('editModal').style.display = 'block';
  })
  .catch(error => console.error('Error:', error));
}

// Función para cerrar el modal
document.getElementById('btnCerrarModal').addEventListener('click', () => {
  document.getElementById('editModal').style.display = 'none';
});

// Función para cancelar la edición
document.getElementById('btnCancelarEdicion').addEventListener('click', () => {
  document.getElementById('editModal').style.display = 'none';
});

// Manejar la actualización de producto
document.getElementById('editProductForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada


  const idProducto = Number(document.getElementById('product-id').textContent);
  const codigo = document.getElementById('product-code').value;
  const descripcion = document.getElementById('product-description').value;
  const estado = parseInt(document.getElementById('product-state').value, 10);


  console.log(idProducto)
  // Realizar la actualización del producto
  const productoActualizado = {
    codigo,
    descripcion,
    estado
  };

  console.log(productoActualizado)

  fetch(`http://localhost:5000/api/productos/productos/${idProducto}`, {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer <?php echo $_SESSION['token']; ?>`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(productoActualizado)
  })
  .then(response => response.json())
  .then(data => {
    if (data.message === 'Producto actualizado correctamente') {
      location.reload(); // Recargar la página para reflejar los cambios
    } else {
      alert('El codigo debe de ser diferente');
    }
  })
  .catch(error => console.error('Error:', error));
});

function agregarProducto() {
  document.getElementById('crearModal').style.display = 'block';

  // Cargar categorías desde el backend
  fetch('http://localhost:5000/api/categorias/', {
    method: 'GET',
    headers: {
      'Authorization': `Bearer <?php echo $_SESSION['token']; ?>`,
      'Content-Type': 'application/json'
    }
  })
    .then(response => response.json())
    .then(data => {
    // Asegúrate de que el select esté vacío antes de llenarlo
    const selectCategoria = document.getElementById('idcategoria');
    selectCategoria.innerHTML = ''; // Limpiar el select antes de agregar las opciones

    // Agregar la opción por defecto
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Seleccione una categoría';
    selectCategoria.appendChild(defaultOption);

    // Agregar las opciones de las categorías
    data.forEach(categoria => {
      const option = document.createElement('option');
      option.value = categoria.idcategoria;
      option.textContent = categoria.nombre;
      selectCategoria.appendChild(option);
    });
  })
    .catch(error => console.error('Error al cargar las categorías:', error));

  // Función para cerrar el modal
  document.getElementById('btnCerrarModal').addEventListener('click', () => {
    document.getElementById('crearModal').style.display = 'none';
  });

  // Función para cancelar la edición
  document.getElementById('btnCancelarCrear').addEventListener('click', () => {
    document.getElementById('crearModal').style.display = 'none';
  });

  // Manejo del formulario
  document.getElementById('formAgregarProducto').addEventListener('submit', function(event) {
    event.preventDefault(); // Evitar que el formulario se envíe de forma predeterminada

    const idcategoria = parseInt(document.getElementById('idcategoria').value, 10);
    const codigo = document.getElementById('codigo').value;
    const descripcion = document.getElementById('descripcion').value;
    const nombre = document.getElementById('nombre').value;

    const productoActualizado = {
      idcategoria,
      nombre,
      codigo,
      descripcion
    };

    console.log(productoActualizado)

    fetch('http://localhost:5000/api/productos/productos', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer <?php echo $_SESSION['token']; ?>`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(productoActualizado)
    })
    .then(response => response.json())
    .then(data => {
      if (data.message === 'Producto creado') {
        location.reload(); // Recargar la página para reflejar los cambios
      } else {
        alert(data.message);
      }
    })
    .catch(error => console.error('Error:', error));
  });
}

  // Cerrar sesión
  document.getElementById('logout-btn').addEventListener('click', () => {
    window.location.href = 'logout.php';  // Redirige a logout.php para cerrar la sesión
  });
</script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
