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
  <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=ARS"></script>

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
      <p>Estás a punto de adquirir un dispositivo <strong>AgainSafeGas</strong>. Por favor, procede con el pago seguro a través de PayPal.</p>
      
      <div id="paypal-button-container"></div>
      <div id="error-message" class="error-message"></div>

      <button class="btn btn-back mt-3" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i> Volver
      </button>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function showErrorMessage(message) {
      document.getElementById('error-message').innerText = message;
    }

    paypal.Buttons({
      createOrder: (data, actions) => {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '95000' // Precio en ARS
            }
          }]
        });
      },
      onApprove: (data, actions) => {
        return actions.order.capture().then(details => {
          const params = new URLSearchParams({
            paymentId: data.orderID,
            PayerID: details.payer.payer_id
          });
          window.location.href = '<?= base_url("registrar-pago-paypal") ?>?' + params.toString();
        });
      },
      onCancel: () => {
        showErrorMessage('❌ El pago fue cancelado.');
      },
      onError: (err) => {
        console.error('Error en la transacción:', err);
        showErrorMessage('⚠️ Error al procesar el pago. Intenta de nuevo.');
      }
    }).render('#paypal-button-container');
  </script>

</body>
</html>
