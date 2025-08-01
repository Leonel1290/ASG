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

  <!-- PayPal SDK - IMPORTANTE: Verifica tu client-id -->
  <script src="https://www.paypal.com/sdk/js?client-id=Aaf4oThh4f97w4hkRqUL7QgtSSHKTpruCpklUqcwWhotqUyLbCMnGXQgwqNEvv-LZ9TnVHTdIH5FECk0&currency=USD&components=buttons"></script>

  <!-- PWA -->
  <link rel="manifest" href="<?= base_url('manifest.json') ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ASG">

  <!-- Estilos personalizados -->
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
      margin-bottom: 2rem;
    }

    #paypal-button-container {
      margin-bottom: 1.5rem;
      min-height: 45px; /* Asegura espacio para el botón */
    }

    .paypal-button {
      border-radius: 4px !important;
    }

    .error-message {
      color: #f87171;
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

    /* Modal personalizado */
    .modal-content {
      background-color: #1c2128;
      color: #c9d1d9;
    }

    .modal-title {
      color: #2ea043;
    }

    .btn-primary {
      background-color: #238636;
      border-color: #238636;
    }

    .btn-primary:hover {
      background-color: #2ea043;
      border-color: #2ea043;
    }
  </style>
</head>

<body>

  <div class="checkout-container">
    <div class="checkout-card">
      <h2>Confirmar Compra</h2>
      <img src="<?= base_url('/imagenes/detector.png'); ?>" alt="Detector ASG">
      <p>Estás a punto de adquirir un dispositivo <strong>AgainSafeGas</strong>. Por favor, procede con el pago seguro a través de PayPal.</p>
      
      <!-- Formulario para dirección de envío -->
      <div class="mb-3">
        <label for="direccion" class="form-label">Dirección de envío</label>
        <textarea class="form-control bg-dark text-light" id="direccion" rows="3" required></textarea>
      </div>
      
      <div id="paypal-button-container"></div>
      <div id="error-message" class="error-message"></div>

      <button class="btn btn-back mt-3" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>
  </div>

  <!-- Modal Éxito -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content border-0">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">¡Compra Exitosa!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          Tu pago fue procesado correctamente. ¡Gracias por confiar en AgainSafeGas!
        </div>
        <div class="modal-footer">
          <a href="<?= base_url('mis-compras') ?>" class="btn btn-primary">Ver mis compras</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
      keyboard: false
    });

    function showErrorMessage(message) {
      const errorDiv = document.getElementById('error-message');
      errorDiv.innerText = message;
      setTimeout(() => errorDiv.innerText = '', 5000);
    }

    // Verificar si PayPal SDK se cargó correctamente
    if (typeof paypal === 'undefined') {
      showErrorMessage('Error al cargar PayPal. Por favor recarga la página.');
    } else {
      paypal.Buttons({
        style: {
          layout: 'vertical',
          color: 'gold',
          shape: 'rect',
          label: 'paypal'
        },
        createOrder: function(data, actions) {
          // Validar dirección antes de proceder
          const direccion = document.getElementById('direccion').value.trim();
          if (!direccion) {
            showErrorMessage('Por favor ingresa tu dirección de envío');
            return false;
          }

          return actions.order.create({
            purchase_units: [{
              amount: {
                value: '100.00',
                currency_code: 'USD'
              },
              description: 'Dispositivo AgainSafeGas'
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(async function(details) {
            // Obtener datos del usuario y dirección
            const direccion = document.getElementById('direccion').value.trim();
            const userId = '<?= session()->get('user_id') ?? 0 ?>'; // Asegúrate de tener el ID de usuario
            
            try {
              // Enviar datos al servidor para guardar la compra
              const response = await fetch('<?= base_url('guardar_compra') ?>', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                  id_usuario: userId,
                  monto: '100.00',
                  moneda: 'USD',
                  direccion: direccion,
                  paypal_order_id: details.id,
                  estado_pago: 'completado'
                })
              });

              const result = await response.json();

              if (result.success) {
                successModal.show();
              } else {
                showErrorMessage('Error al registrar la compra: ' + (result.message || 'Error desconocido'));
              }
            } catch (error) {
              console.error('Error:', error);
              showErrorMessage('Error al comunicarse con el servidor');
            }
          });
        },
        onCancel: function(data) {
          showErrorMessage('❌ El pago fue cancelado.');
        },
        onError: function(err) {
          console.error('Error en PayPal:', err);
          showErrorMessage('⚠️ Error al procesar el pago. Intenta de nuevo.');
        }
      }).render('#paypal-button-container');
    }

    // Service Worker para PWA
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
          .then(reg => console.log('ServiceWorker registrado:', reg.scope))
          .catch(err => console.error('Fallo en el registro del ServiceWorker:', err));
      });
    }
  </script>
</body>
</html>