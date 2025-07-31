<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar Compra | AgainSafeGas</title>

  <!-- Bootstrap y Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

  <!-- SDK de PayPal -->
  <script src="https://www.paypal.com/sdk/js?client-id=Aaf4oThh4f97w4hkRqUL7QgtSSHKTpruCpklUqcwWhotqUyLbCMnGXQgwqNEvv-LZ9TnVHTdIH5FECk0&currency=USD"></script>

  <style>
    /* Mantén tus estilos CSS actuales */
    body {
      background-color: #0d1117;
      font-family: 'Segoe UI', sans-serif;
      color: #e6edf3;
      margin: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    /* ... resto de tus estilos ... */
  </style>
</head>

<body>
  <div class="checkout-container">
    <div class="checkout-card">
      <h2>Confirmar Compra</h2>
      <img src="<?= base_url('/imagenes/detector.png'); ?>" alt="Detector ASG">
      <p>Estás a punto de adquirir un dispositivo <strong>AgainSafeGas</strong>. Por favor, procede con el pago seguro a través de PayPal.</p>
      
      <div id="paypal-button-container"></div>
      <div id="error-message" class="error-message"></div>

      <button class="btn btn-back mt-3" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>
  </div>

  <!-- Modal de éxito (mantenerlo) -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <!-- ... contenido del modal ... -->
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
      keyboard: false
    });

    function showErrorMessage(message) {
      const errorDiv = document.getElementById('error-message');
      errorDiv.innerText = message;
    }

    paypal.Buttons({
      createOrder: (data, actions) => {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '100.00'
            }
          }]
        });
      },
      onApprove: (data, actions) => {
        return actions.order.capture().then((details) => {
          fetch('<?= base_url("guardar_compra") ?>', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              monto: details.purchase_units[0].amount.value,
              moneda: details.purchase_units[0].amount.currency_code,
              paypal_order_id: details.id,
              estado_pago: details.status === 'COMPLETED' ? 'completado' : 'fallido'
            })
          })
          .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
          })
          .then(serverData => {
            if (serverData.success) {
              successModal.show();
              setTimeout(() => {
                window.location.href = '<?= base_url("loginobtener") ?>';
              }, 3000);
            } else {
              showErrorMessage(serverData.message || 'Error al procesar la compra');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showErrorMessage('Error al conectar con el servidor');
          });
        });
      },
      onCancel: () => showErrorMessage('❌ El pago fue cancelado.'),
      onError: (err) => {
        console.error('Error en PayPal:', err);
        showErrorMessage('⚠️ Error al procesar el pago');
      }
    }).render('#paypal-button-container');
  </script>
</body>
</html>