<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprar dispositivo ASG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=ARS"></script>
</head>
<body class="bg-light">

    <div class="container mt-5 text-center">
        <h1 class="mb-4">🛒 Comprar dispositivo ASG</h1>

        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h5 class="card-title">Detector de Gas ASG</h5>
                <p class="card-text">Dispositivo inteligente para detectar fugas de gas y alertar automáticamente.</p>
                <p><strong>Precio:</strong> $95.000 ARS</p>

                <!-- Botón de PayPal -->
                <div id="paypal-button-container" class="mt-4"></div>
            </div>
        </div>

        <a href="/inicio" class="btn btn-link mt-3">⬅ Volver al inicio</a>
    </div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '95000'  // Precio exacto
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Redirigir a tu backend para registrar el pago
                    const params = new URLSearchParams({
                        paymentId: data.orderID,
                        PayerID: details.payer.payer_id
                    });

                    window.location.href = '/registrar-pago-paypal?' + params.toString();
                });
            },
            onCancel: function(data) {
                alert('El pago fue cancelado.');
            },
            onError: function(err) {
                console.error('Ocurrió un error con PayPal:', err);
                alert('Ocurrió un error al procesar el pago.');
            }
        }).render('#paypal-button-container');
    </script>

</body>
</html>
