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
                    <a href="<?= base_url('loginobtener') ?>" class="btn btn-primary">Continuar al Login</a>
                </div>
            </div>
        </div>
    </div>

    <!-- PayPal SDK con el nuevo Client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=USD"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        console.log("Script principal de compra iniciado.");

        const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
            keyboard: false
        });

        function showErrorMessage(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.innerText = message;
        }

        // Se verifica que el objeto de PayPal exista antes de intentar usarlo.
        if (typeof paypal === 'undefined') {
            showErrorMessage("⚠️ Error: El SDK de PayPal no se ha cargado correctamente. Revisa tu conexión y el Client ID.");
            console.error("Error: Objeto 'paypal' no encontrado. El script del SDK no se cargó.");
        } else {
            console.log("SDK de PayPal cargado exitosamente. Renderizando botones...");

            paypal.Buttons({
                createOrder: (data, actions) => {
                    console.log("Creando orden de PayPal...");
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '100.00'
                            }
                        }]
                    });
                },
                onApprove: (data, actions) => {
                    console.log("Pago aprobado. Capturando transacción...");
                    return actions.order.capture().then((details) => {
                        fetch('<?= base_url("home/guardar_compra") ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                orderID: data.orderID,
                                payerID: data.payerID,
                                paymentID: details.id,
                                status: details.status,
                            })
                        })
                        .then(response => response.json())
                        .then(serverData => {
                            if (serverData.status === 'success') {
                                successModal.show();
                                setTimeout(() => {
                                    window.location.href = '<?= base_url("loginobtener") ?>';
                                }, 3000);
                            } else {
                                showErrorMessage('⚠️ Error al guardar la compra en la base de datos.');
                                console.error('Error del servidor:', serverData.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error en la llamada al servidor:', error);
                            showErrorMessage('⚠️ Error de conexión. El pago se realizó, pero no pudimos registrarlo.');
                        });
                    });
                },
                onCancel: () => {
                    showErrorMessage('❌ El pago fue cancelado.');
                    console.log("Pago cancelado por el usuario.");
                },
                onError: (err) => {
                    console.error('Error en la transacción:', err);
                    showErrorMessage('⚠️ Error al procesar el pago. Intenta de nuevo.');
                }
            }).render('#paypal-button-container')
            .catch(err => {
                console.error("Error al renderizar el botón de PayPal:", err);
                showErrorMessage('⚠️ Error al intentar mostrar el botón de PayPal. Revisa la consola para más detalles.');
            });
        }
    </script>

    <script>
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
