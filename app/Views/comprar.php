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
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            padding: 2rem;
        }

        .checkout-card {
            background-color: #2d3748; /* Card background */
            color: #e2e8f0; /* Card text color */
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 500px; /* Max width for the card */
            width: 100%; /* Make card responsive */
            text-align: center; /* Center text inside card */
        }

        .checkout-card h2 {
            color: #4299e1; /* Heading color */
            margin-bottom: 1.5rem;
        }

        .checkout-card p {
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        #paypal-button-container {
            margin-top: 1.5rem;
        }

        /* Estilos para mensajes de error */
        .error-message {
            color: #f56565; /* Red color for errors */
            margin-top: 1rem;
            font-weight: bold;
        }

        /* Estilos para el modal de éxito */
        .modal-content {
            background-color: #2d3748; /* Fondo del modal */
            color: #e2e8f0; /* Color del texto del modal */
        }

        .modal-header {
            border-bottom-color: #4a5568; /* Color del borde del header del modal */
        }

        .modal-footer {
            border-top-color: #4a5568; /* Color del borde del footer del modal */
        }

        .modal-title {
            color: #48bb78; /* Color verde para el título del modal */
        }

        .btn-secondary {
            background-color: #6b7280;
            border-color: #6b7280;
            transition: background-color 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
            border-color: #4b5563;
        }
        /* New style for the image */
        .checkout-card img {
            max-width: 150px; /* Adjust as needed */
            height: auto;
            margin-bottom: 1rem; /* Space below the image */
            display: block; /* Make it a block element to center with margin auto */
            margin-left: auto;
            margin-right: auto;
        }


    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">

</head>
<body>

    <div class="checkout-container">
        <div class="checkout-card">
            <h2>Confirmar Compra</h2>
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="AgainSafeGas Logo">
            <p>Estás a punto de adquirir un dispositivo AgainSafeGas. Por favor, procede con el pago a través de PayPal.</p>

            <div id="paypal-button-container"></div>

            <div id="error-message" class="error-message"></div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">¡Compra Exitosa!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Tu pago ha sido procesado exitosamente. ¡Gracias por tu compra!
                </div>
                <div class="modal-footer">
                    <a href="<?= base_url('loginobtener') ?>" class="btn btn-primary">Continuar al Login</a>
                    </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Inicializar el modal de éxito
    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
        keyboard: false // Evitar cerrar con la tecla Esc
    });

    // Función para mostrar mensajes de error
    function showErrorMessage(message) {
        const errorMessageDiv = document.getElementById('error-message');
        if (errorMessageDiv) {
            errorMessageDiv.innerText = message;
        }
    }

    // Configurar los botones de PayPal
    paypal.Buttons({
        createOrder: function (data, actions) {
            // Set up the transaction
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '100.00' // Replace with dynamic price if needed
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            // Capture the funds from the transaction
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
            showErrorMessage('❌ El pago fue cancelado.');
        },
        onError: function (err) {
            // Handle errors
            console.error('An error occurred during the transaction', err);
            showErrorMessage('⚠️ Error al procesar el pago. Por favor, intente de nuevo.');
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