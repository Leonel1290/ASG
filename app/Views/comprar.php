<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmar Compra | AgainSafeGas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
        
        /* Contenedor principal para la vista de producto */
        .product-checkout-view {
            background-color: #161b22;
            border-radius: 0.75rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            padding: 2.5rem;
            max-width: 900px; /* Aumentar el ancho para acomodar más contenido */
            width: 100%;
            display: flex; /* Usar flexbox para el layout de dos columnas */
            gap: 2rem;
            animation: fadeIn 1s ease-in-out;
            flex-wrap: wrap; /* Permitir que los elementos se envuelvan en pantallas pequeñas */
        }
        
        .product-image-section {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }
        
        .product-image-section img {
            width: 100%;
            height: auto;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        
        .product-details-section {
            flex: 1.5; /* Darle más espacio a la sección de detalles */
            min-width: 300px;
        }
        
        .product-details-section h2 {
            color: #58a6ff;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2ea043; /* Color verde para el precio, destaca la oferta */
            margin-bottom: 1.5rem;
        }
        
        .product-features {
            list-style: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .product-features li {
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }

        .product-features i {
            color: #2ea043;
            margin-right: 10px;
        }
        
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .trust-badges .badge-item {
            text-align: center;
            font-size: 0.85rem;
        }
        
        .trust-badges i {
            color: #58a6ff;
            font-size: 2rem;
        }
        
        .trust-badges p {
            margin-top: 0.5rem;
            line-height: 1.2;
            font-size: 0.9rem;
        }

        /* Estilos de botones, modales, etc. se mantienen igual */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .product-checkout-view {
                flex-direction: column;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="checkout-container">
        <div class="product-checkout-view">
            <div class="product-image-section">
                <img src="/imagenes/Sentinel.png" alt="Detector ASG">
            </div>

            <div class="product-details-section">
                <h2>Confirmar Compra</h2>
                <h1 class="mb-2">AgainSafeGas Sentinel</h1>
                <p class="product-price">
                    $29.99 USD
                </p>
                <p>Estás a punto de adquirir el innovador dispositivo <strong>AgainSafeGas Sentinel</strong>, un detector de gas de última generación que garantiza la seguridad de tu hogar y de tu familia.</p>
                
                <hr style="border-color: #2d333b;">

                <h4>Características Clave:</h4>
                <ul class="product-features">
                    <li><i class="fas fa-check-circle"></i> Detección ultra-sensible de fugas de gas.</li>
                    <li><i class="fas fa-check-circle"></i> Alertas instantáneas en tu smartphone.</li>
                    <li><i class="fas fa-check-circle"></i> Batería de larga duración (hasta 6 meses).</li>
                    <li><i class="fas fa-check-circle"></i> Diseño compacto y fácil instalación.</li>
                </ul>

                <hr style="border-color: #2d333b;">
                
                <div class="trust-badges">
                    <div class="badge-item">
                        <i class="fas fa-shield-alt"></i>
                        <p><strong>Pago Seguro</strong><br>Cifrado SSL</p>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-truck"></i>
                        <p><strong>Envío Rápido</strong><br>En 2-5 días hábiles</p>
                    </div>
                    <div class="badge-item">
                        <i class="fas fa-star-half-alt"></i>
                        <p><strong>Garantía de Satisfacción</strong><br>100% confiable</p>
                    </div>
                </div>

                <div id="paypal-button-container"></div>
                <div id="error-message" class="error-message"></div>

                <button class="btn btn-back mt-3" onclick="window.history.back();">
                    <i class="fas fa-arrow-left"></i> Volver
                </button>
            </div>
        </div>
    </div>

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
                    <a href="/" class="btn btn-primary">Continuar al Inicio</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="processingModalLabel">Procesando compra</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="loading-spinner"></div> 
                    <span>Estamos guardando los detalles de tu compra...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=USD"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log("Script principal de compra iniciado.");

        const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
            keyboard: false
        });
        
        const processingModal = new bootstrap.Modal(document.getElementById('processingModal'), {
            keyboard: false,
            backdrop: 'static'
        });

        function showErrorMessage(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.innerText = message;
        }

        if (typeof paypal === 'undefined') {
            showErrorMessage("⚠️ Error: El SDK de PayPal no se ha cargado correctamente.");
            console.error("Error: Objeto 'paypal' no encontrado.");
        } else {
            console.log("SDK de PayPal cargado exitosamente. Renderizando botones...");

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return fetch('/paypal/create-order', { 
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.text().then(text => { throw new Error(text) });
                        }
                        return res.json();
                    })
                    .then(order => {
                        console.log("Orden creada en backend:", order);
                        if (order.error) {
                            showErrorMessage("Error: " + order.error);
                            return;
                        }
                        return order.id;
                    })
                    .catch(err => {
                        console.error("Error al crear la orden:", err);
                        showErrorMessage("⚠️ Error al crear la orden de pago: " + err.message);
                    });
                },
                onApprove: function(data, actions) {
                    processingModal.show();
                    return fetch(`/paypal/capture-order/${data.orderID}`, { 
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.text().then(text => { throw new Error(text) });
                        }
                        return res.json();
                    })
                    .then(details => {
                        console.log("Orden capturada:", details);
                        processingModal.hide();
                        
                        if (details.status === "COMPLETED") {
                            successModal.show();
                        } else {
                            showErrorMessage("⚠️ Hubo un problema al procesar el pago.");
                        }
                    })
                    .catch(err => {
                        console.error("Error al capturar la orden:", err);
                        processingModal.hide();
                        successModal.show();
                        console.log("Pago exitoso pero posible error al guardar en BD");
                    });
                },
                onCancel: () => {
                    showErrorMessage('❌ El pago fue cancelado.');
                    console.log("Pago cancelado por el usuario.");
                },
                onError: (err) => {
                    console.error('Error en la transacción:', err);
                    showErrorMessage('⚠️ Error al procesar el pago.');
                }
            }).render('#paypal-button-container');
        }
    </script>
</body>
</html>