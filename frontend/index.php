<?php
session_start();

// Verificar si hay un mensaje de error en la sesión
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']); // Limpiar el mensaje después de mostrarlo
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">

  <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
      <div class="card-body">
        <h3 class="card-title text-center mb-4">Bienvenido</h3>
        <form action="login.php" method="POST">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Iniciar sesión</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal de error -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="error-message">
          <?php
            // Si hay un error, lo mostramos en el modal
            if ($error_message) {
              echo $error_message;
            }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Verifica si hay un mensaje de error y muestra el modal
    window.onload = function() {
      const errorMessage = document.getElementById('error-message').innerText.trim();
      if (errorMessage) {
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
      }
    };
  </script>

</body>
</html>
