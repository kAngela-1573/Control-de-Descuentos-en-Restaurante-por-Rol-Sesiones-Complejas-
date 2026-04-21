<?php
session_start();

// Si ya está logueado, redirigir al simulador
if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
    header('Location: restaurante.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $rol = $_POST['rol'] ?? '';
    
    if (empty($nombre)) {
        $error = '⚠️ Por favor, ingresa tu nombre.';
    } elseif (!in_array($rol, ['cliente', 'vip'])) {
        $error = '⚠️ Por favor, selecciona un tipo de usuario válido.';
    } else {
        // Guardar datos en sesión
        $_SESSION['usuario'] = $nombre;
        $_SESSION['rol'] = $rol;
        
        header('Location: restaurante.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Restaurante</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5e6d3; }
        .container {
            max-width: 450px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #c0392b; }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover { background-color: #d35400; }
        .error { color: red; text-align: center; margin-top: 10px; }
        .info {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🍽️ Restaurante Gourmet</h2>
    <p style="text-align: center;">Inicia sesión para calcular tu descuento</p>
    
    <form method="post">
        <label>👤 Nombre:</label>
        <input type="text" name="nombre" placeholder="Ej: Juan Pérez" required>
        
        <label>👑 Tipo de usuario:</label>
        <select name="rol" required>
            <option value="">Seleccione...</option>
            <option value="cliente">🍕 Cliente (10% o 20% estándar)</option>
            <option value="vip">✨ Socio VIP (estándar + 5% adicional)</option>
        </select>
        
        <button type="submit">🔓 Ingresar al sistema</button>
    </form>
    
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="info">
        💡 Los Socios VIP obtienen un 5% EXTRA sobre el total ya descontado.
    </div>
</div>
</body>
</html>
