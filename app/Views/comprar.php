<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar Compra | AgainSafeGas</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Icono -->
  <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

  <!-- PayPal SDK con SANDBOX -->
  <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=USD"></script>

  <!-- PWA -->
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ASG">

  <style>
    body {
      background-color: #0d1117;
      font-family: 'Segoe UI', sans-serif;
      color: #e6edf3;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .checkout-container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .checkout-card {
      background-color: #161b22;
      border-radius: 0.75rem;
      padding: 2.5rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      max-width: 500px;
      width: 100%;
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .checkout-card h2 {
      color: #58a6ff;
      font-weight: bold;
      margin-bottom: 1.5rem;
    }

    .checkout-card img {
      max-width: 140px;
      margin-bottom: 1.5rem;
    }

    .checkout-card p {
      font-size: 1.05rem;
      line-height: 1.6;
      margin-bottom: 1rem;
    }

    .device-info {
      background-color: #2d333b;
      border-radius: 0.5rem;
      padding: 1rem;
      margin-bottom: 1.5rem;
      text-align: left;
    }

    .device-info p {
      margin-bottom: 0.5rem;
    }

    #paypal-button-container {
      margin-bottom: 1.5rem;
    }

    .error-message {
      color: #f87171;
      font-weight: bold;
      margin-top: 1rem;
    }

    .success-message {
      color: #4ade80;
      font-weight: bold;
      margin-top: 1rem;
    }

    .btn-back {
      background-color: #2d333b;
      color: #e6edf3;
      border: none;
      border-radius: 0.375rem;
      padding: 0.6rem 1.25rem;
      transition: all 0.3s ease;
    }

    .btn-back:hover {
      background-color: #21262d;
      color: #fff;
    }

    .loading-spinner {
      display: none;
      margin: 1rem auto;
      border: 4px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      border-top: 4px solid #58a6ff;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>

  <div class="checkout-container">
    <div class="checkout-card">
      <h2>Confirmar Compra</h2>
      <img src="<?= base_url('/imagenes/detector.png'); ?>" alt="Detector ASG">
      <p>Estás a punto de adquirir un dispositivo <strong>AgainSafeGas</strong>. Por favor, revisa los detalles y procede con el pago seguro.</p>
      
      <!-- Información del dispositivo -->
      <div class="device-info">
        <p><strong>Dispositivo:</strong> <span id="device-name"><?= $dispositivo['nombre'] ?></span></p>
        <p><strong>Ubicación:</strong> <span id="device-location"><?= $dispositivo['ubicacion'] ?></span></p>
        <p><strong>Precio:</strong> $<span id="device-price">99.99</span> USD</p>
        <input type="hidden" id="device-id" value="<?= $dispositivo['id'] ?>">
        <input type="hidden" id="user-id" value="<?= $usuario_id ?>">
      </div>
      
      <div id="paypal-button-container"></div>
      <div id="loading-spinner" class="loading-spinner"></div>
      <div id="error-message" class="error-message"></div>
      <div id="success-message" class="success-message"></div>

      <button class="btn btn-back mt-3" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Mostrar mensaje de error
    function showErrorMessage(message) {
      document.getElementById('error-message').innerText = message;
      document.getElementById('success-message').innerText = '';
    }

    // Mostrar mensaje de éxito
    function showSuccessMessage(message) {
      document.getElementById('success-message').innerText = message;
      document.getElementById('error-message').innerText = '';
    }

    // Mostrar spinner de carga
    function showLoading(show) {
      document.getElementById('loading-spinner').style.display = show ? 'block' : 'none';
    }

    paypal.Buttons({
      createOrder: async (data, actions) => {
        try {
          showLoading(true);
          
          const deviceId = document.getElementById('device-id').value;
          const userId = document.getElementById('user-id').value;
          const price = document.getElementById('device-price').textContent;
          
          const response = await fetch('<?= base_url("paypal/createOrder") ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              amount: price,
              dispositivo_id: deviceId,
              usuario_id: userId
            }),
          });
          
          const orderData = await response.json();
          
          if (orderData.error) {
            showErrorMessage(orderData.error);
            showLoading(false);
            return;
          }
          
          return orderData.id;
          
        } catch (error) {
          console.error('Error al crear orden:', error);
          showErrorMessage('Error al crear la orden de pago');
          showLoading(false);
        }
      },
      
      onApprove: async (data, actions) => {
        try {
          showLoading(true);
          
          const response = await fetch('<?= base_url("paypal/captureOrder") ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              orderID: data.orderID
            }),
          });
          
          const captureData = await response.json();
          
          if (captureData.error) {
            showErrorMessage(captureData.error);
            showLoading(false);
            return;
          }
          
          if (captureData.status === 'COMPLETED') {
            showSuccessMessage('✅ Pago completado con éxito. Redirigiendo...');
            
            // Redirigir después de 2 segundos
            setTimeout(() => {
              window.location.href = '<?= base_url("mis-dispositivos") ?>';
            }, 2000);
          } else {
            showErrorMessage('El pago no se completó correctamente');
          }
          
        } catch (error) {
          console.error('Error al capturar pago:', error);
          showErrorMessage('Error al procesar el pago');
        } finally {
          showLoading(false);
        }
      },
      
      onCancel: () => {
        showErrorMessage('❌ El pago fue cancelado.');
      },
      
      onError: (err) => {
        console.error('Error en la transacción:', err);
        showErrorMessage('⚠️ Error al procesar el pago. Intenta de nuevo.');
        showLoading(false);
      }
      
    }).render('#paypal-button-container');
  </script>

</body>
</html>