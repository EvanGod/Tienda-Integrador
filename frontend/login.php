<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Aquí debes realizar la autenticación del usuario
    $url = 'http://localhost:5000/api/auth/login'; // Cambia esto a tu URL de backend
    $data = array('email' => $email, 'password' => $password);

    // Inicializa cURL
    $ch = curl_init();

    // Configura las opciones de cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Convierte los datos a JSON
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));

    // Ejecuta la solicitud
    $response = curl_exec($ch);

    // Verifica si ocurrió un error con la conexión cURL
    if ($response === FALSE) {
        $_SESSION['error_message'] = 'Error al conectar con el backend: ' . curl_error($ch);
        header('Location: index.php');
        exit();
    }

    // Cierra la conexión cURL
    curl_close($ch);

    // Decodifica la respuesta JSON
    $responseData = json_decode($response, true);

    if (isset($responseData['token'])) {
        $_SESSION['token'] = $responseData['token']; // Guarda el token en la sesión
        header('Location: dashboard.php'); // Redirige al dashboard
        exit();
    } else {
        $_SESSION['error_message'] = $responseData['message'] ?? 'Credenciales incorrectas';
        header('Location: index.php');
        exit();
    }
}
?>
