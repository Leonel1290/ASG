<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Compra - AgainSafeGas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <script src="https://www.paypal.com/sdk/js?client-id=Aaf4oThh4f97w4hkRqUL7QgtSSHKTpruCpklUqcwWhotqUyLbCMnGXQgwqNEvv-LZ9TnVHTdIH5FECk0&currency=USD"></script>

    <style>
        body {
            background-color: #1a202c; /* Dark mode background */
            font-family: 'Segoe UI', sans-serif;
            color: #e2e8f0; /* Light text for dark background */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .checkout-container {
            flex: 1; /* Allow container to grow */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background-color: #2d3748; /* Slightly lighter dark for cards */
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }

        h2 {
            color: #4CAF50; /* Green for headings */
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .product-details {
            margin-bottom: 25px;
            border-bottom: 1px solid #4a5568; /* Subtle separator */
            padding-bottom: 20px;
        }

        .product-details p {
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .product-details strong {
            color: #fff;
        }

        .total-price {
            font-size: 1.5em;
            font-weight: bold;
            color: #fff;
            text-align: right;
            margin-bottom: 30px;
        }

        #paypal-button-container {
            margin-top: 20px;
        }

        /* Message styling */
        .message {
            margin-top: 20px;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #c6f6d5;
            color: #2f855a;
        }

        .message.error {
            background-color: #fed7d7;
            color: #c53030;
        }

        /* Modal styling */
        .modal-content {
            background-color: #2d3748;
            color: #fff;
        }

        .modal-header {
            border-bottom-color: #4a5568;
        }

        .modal-footer {
            border-top-color: #4a5568;
        }

        .btn-success {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }

        .btn-success:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>
    <div class="checkout-container">
        <div class="card">
            <h2>Confirmar Compra</h2>

            <div class="product-details">
                <p><strong>Producto:</strong> Sensor de Gas Detector ESP32</p>
                <p><strong>Descripción:</strong> Un dispositivo inteligente para la detección de fugas de gas en tu hogar.</p>
                <p><strong>Cantidad:</strong> 1</p>
            </div>

            <div class="total-price">
                Total: $100.00 USD
            </div>

            <div id="paypal-button-container"></div>
            <div id="responseMessage" class="message" style="display: none;"></div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">¡Pago Confirmado!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Tu compra ha sido realizada con éxito. Ahora puedes añadir el dispositivo a tu perfil.</p>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <a href="<?= base_url('/perfil') ?>" class="btn btn-success">Ir a Mi Perfil</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function showMessage(message, type) {
        const msgElement = document.getElementById("responseMessage");
        msgElement.innerText = message;
        msgElement.className = `message ${type}`;
        msgElement.style.display = "block";

        setTimeout(() => {
            msgElement.style.display = "none";
        }, 5000);
    }

    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
        backdrop: 'static', // Prevent closing by clicking outside
        keyboard: false // Prevent closing with keyboard
    });

    paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '100.00' // Monto del producto
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                // Show the success modal
                successModal.show();

                // Optional: Set a timeout for automatic redirection if the user doesn't click the button
                // --- CORRECCIÓN: Cambiar la URL de redirección a loginobtener ---
                setTimeout(() => {
                     // Verificar si el modal todavía está abierto antes de redirigir
                     const modalElement = document.getElementById('successModal');
                     const isModalOpen = modalElement && modalElement.classList.contains('show');

                     if (isModalOpen) {
                         window.location.href = '<?= base_url("loginobtener") ?>';
                     }
                }, 3000); // Redirigir después de 3 segundos si el modal sigue abierto
                // --- FIN CORRECCIÓN ---

            });
        },
        onCancel: function (data) {
            // Handle cancelation
            console.log('Payment cancelled', data);
            showMessage('❌ El pago fue cancelado.', 'error');
        },
        onError: function (err) {
            // Handle errors
            console.error('An error occurred during the transaction', err);
            showMessage('⚠️ Error al procesar el pago. Por favor, intente de nuevo.', 'error');
        }
    }).render('#paypal-button-container'); // Render the PayPal button into the container
</script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker:', error);
                    });
            });
        }
    </script>

</body>
</html>
