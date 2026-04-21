<?php
session_start();

// 🔒 PROTECCIÓN: Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    header('Location: login_restaurante.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];
$resultado = null;

// Procesar el cálculo de descuento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calcular'])) {
    $consumo = floatval($_POST['consumo'] ?? 0);
    
    if ($consumo < 0) {
        $resultado = ['error' => 'El consumo no puede ser negativo.'];
    } else {
        // PASO 1: Calcular descuento estándar según consumo
        if ($consumo <= 100) {
            $descuento_estandar = 10; // 10%
        } else {
            $descuento_estandar = 20; // 20%
        }
        
        $monto_descuento_estandar = $consumo * ($descuento_estandar / 100);
        $subtotal = $consumo - $monto_descuento_estandar;
        
        // PASO 2: Aplicar descuento adicional según rol
        $descuento_adicional = 0;
        $monto_descuento_adicional = 0;
        
        if ($rol === 'vip') {
            $descuento_adicional = 5; // 5% adicional
            $monto_descuento_adicional = $subtotal * ($descuento_adicional / 100);
        }
        
        // PASO 3: Calcular total final
        $total_final = $subtotal - $monto_descuento_adicional;
        
        $resultado = [
            'consumo' => $consumo,
            'descuento_estandar_porcentaje' => $descuento_estandar,
            'monto_descuento_estandar' => $monto_descuento_estandar,
            'subtotal' => $subtotal,
            'descuento_adicional_porcentaje' => $descuento_adicional,
            'monto_descuento_adicional' => $monto_descuento_adicional,
            'total_final' => $total_final,
            'rol' => $rol
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulador de Descuentos - Restaurante</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5e6d3; }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .bienvenida {
            font-size: 1.1em;
        }
        .rol-cliente { color: #e67e22; }
        .rol-vip { color: #c0392b; font-weight: bold; }
        .logout-btn {
            background-color: #e74c3c;
            padding: 8px 15px;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            font-size: 14px;
        }
        .logout-btn:hover { background-color: #c0392b; }
        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover { background-color: #219a52; }
        .resultado {
            margin-top: 25px;
            padding: 20px;
            background-color: #e8f8f5;
            border-radius: 8px;
            border-left: 5px solid #27ae60;
        }
        .resultado h3 { margin: 0 0 15px 0; color: #1e8449; }
        .linea {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .total {
            font-size: 1.3em;
            font-weight: bold;
            color: #1e8449;
            border-bottom: 2px solid #27ae60;
            margin-top: 10px;
            padding-top: 10px;
        }
        .vip-badge {
            background-color: #f1c40f;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background-color: #fadbd8;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="bienvenida">
            👋 <strong><?= htmlspecialchars($usuario) ?></strong><br>
            <?php if ($rol === 'vip'): ?>
                <span class="rol-vip">✨ Socio VIP ✨</span> <span class="vip-badge">+5% adicional</span>
            <?php else: ?>
                <span class="rol-cliente">🍕 Cliente estándar</span>
            <?php endif; ?>
        </div>
        <a href="logout_restaurante.php" class="logout-btn">🚪 Cerrar sesión</a>
    </div>
    
    <h2>💰 Calculadora de Descuentos</h2>
    <p>Ingresa el monto total de tu consumo:</p>
    
    <form method="post">
        <label>💵 Consumo total ($):</label>
        <input type="number" step="0.01" name="consumo" placeholder="Ej: 150.00" required>
        <button type="submit" name="calcular">🔢 Calcular descuento</button>
    </form>
    
    <?php if (isset($resultado) && isset($resultado['error'])): ?>
        <div class="error"><?= $resultado['error'] ?></div>
    <?php endif; ?>
    
    <?php if (isset($resultado) && !isset($resultado['error'])): ?>
        <div class="resultado">
            <h3>📋 Factura detallada</h3>
            
            <div class="linea">
                <span>💰 Consumo bruto:</span>
                <strong>$<?= number_format($resultado['consumo'], 2) ?></strong>
            </div>
            
            <div class="linea">
                <span>📉 Descuento estándar (<?= $resultado['descuento_estandar_porcentaje'] ?>%):</span>
                <span>-$<?= number_format($resultado['monto_descuento_estandar'], 2) ?></span>
            </div>
            
            <div class="linea">
                <span>🔄 Subtotal después de desc. estándar:</span>
                <strong>$<?= number_format($resultado['subtotal'], 2) ?></strong>
            </div>
            
            <?php if ($resultado['rol'] === 'vip'): ?>
                <div class="linea" style="background-color: #fff9e6;">
                    <span>✨ Descuento VIP adicional (<?= $resultado['descuento_adicional_porcentaje'] ?>%):</span>
                    <span>-$<?= number_format($resultado['monto_descuento_adicional'], 2) ?></span>
                </div>
            <?php endif; ?>
            
            <div class="linea total">
                <span>💵 TOTAL A PAGAR:</span>
                <span>$<?= number_format($resultado['total_final'], 2) ?></span>
            </div>
            
            <?php if ($resultado['rol'] === 'vip'): ?>
                <p style="margin-top: 15px; font-size: 12px; color: #c0392b; text-align: center;">
                    ✨ Beneficio VIP: 5% adicional aplicado correctamente ✨
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <p style="font-size: 12px; text-align: center; margin-top: 20px; color: #777;">
        🍽️ Descuento estándar: 10% para consumos ≤ $100 | 20% para consumos > $100
    </p>
</div>
</body>
</html>
