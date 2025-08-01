<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Confirmar Compra | AgainSafeGas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

  <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIcIVQCXf54_&currency=ARS"></script>

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
      margin-bottom: 2rem;
    }

    #paypal-button-container {
      margin-bottom: 1.5rem;
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
  </style>
</head>

<body>

  <div class="checkout-container">
    <div class="checkout-card">
      <h2>Confirmar Compra</h2>
      <img src="<?= base_url('/imagenes/detector.png'); ?>" alt="Detector ASG">
      <p>Estás a punto de adquirir un dispositivo **AgainSafeGas**. Por favor, procede con el pago seguro a través de PayPal.</p>

      <div id="paypal-button-container"></div>
      <div id="error-message" class="error-message"></div>

      <button class="btn btn-back mt-3" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function showErrorMessage(message) {
      document.getElementById('error-message').innerText = message;
    }

    paypal.Buttons({
      // Se llama para configurar la transacción
      createOrder: (data, actions) => {
        // Llama a tu endpoint de backend para crear la orden de PayPal
        // Es más seguro definir el monto en el backend para evitar manipulaciones del cliente
        return fetch('<?= base_url("paypal/createOrder") ?>', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            // Puedes enviar el monto desde el frontend si lo necesitas,
            // pero es mejor que el backend lo determine para mayor seguridad.
            body: JSON.stringify({
                amount: '95000' // Precio del producto en ARS (si tu moneda es ARS)
            })
        })
        .then(response => {
            if (!response.ok) {
                // Manejar errores de la API de backend
                return response.json().then(err => { throw new Error(err.error || 'Error al crear la orden en el backend'); });
            }
            return response.json();
        })
        .then(order => order.id) // Retorna el ID de la orden de PayPal
        .catch(error => {
            console.error('Error en createOrder:', error);
            showErrorMessage('Error al iniciar el pago: ' + error.message);
            return null; // Evita que se siga con la aprobación si hay un error
        });
      },

      // Se llama cuando el comprador aprueba la transacción
      onApprove: (data, actions) => {
        // Captura la orden en PayPal
        return actions.order.capture().then(details => {
          // Envía los detalles de la captura a tu backend para guardar el pago
          fetch('<?= base_url("paypal/captureOrder") ?>', {
            method: 'post',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              orderID: data.orderID,
              payerID: details.payer.payer_id // El ID del pagador, útil para registros
            })
          })
          .then(response => {
              if (!response.ok) {
                  // Manejar errores de la API de backend
                  return response.json().then(err => { throw new Error(err.error || 'Error al registrar el pago en el backend'); });
              }
              return response.json();
          })
          .then(response => {
            if (response.status === 'COMPLETED') {
              // El pago se completó y se registró correctamente en tu base de datos
              console.log('Pago completado y registrado:', response);
              alert('¡Pago completado con éxito!');
              window.location.href = '<?= base_url("pago-exitoso") ?>'; // Redirige a una página de éxito
            } else {
              // El pago no se completó o no se pudo registrar
              showErrorMessage('El pago no se completó. Estado: ' + response.status + '. Por favor, contacta a soporte.');
              console.error('Pago no completado o error al registrar:', response);
            }
          })
          .catch(error => {
            // Manejar errores durante el envío de la captura al backend
            console.error('Error al enviar la captura al backend:', error);
            showErrorMessage('Error al registrar el pago. Por favor, intenta de nuevo o contacta a soporte.');
          });
        });
      },

      // Se llama cuando el comprador cancela la transacción
      onCancel: () => {
        showErrorMessage('❌ El pago fue cancelado.');
      },

      // Se llama si ocurre un error durante la transacción
      onError: (err) => {
        console.error('Error en la transacción de PayPal:', err);
        showErrorMessage('⚠️ Error al procesar el pago. Intenta de nuevo.');
      }
    }).render('#paypal-button-container'); // Renderiza los botones de PayPal
  </script>

</body>
</html>