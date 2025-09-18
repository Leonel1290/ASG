<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmar Compra | AgainSafeGas</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        :root {
            --bg-color-main: #0d1117;
            --bg-color-card: #161b22;
            --font-color-main: #e6edf3;
            --accent-color-blue: #58a6ff;
            --accent-color-green: #2ea043;
            --accent-color-red: #f87171;
            --border-color-dark: #2d333b;
            --shadow-light: rgba(0,0,0,0.2);
            --shadow-dark: rgba(0,0,0,0.4);
        }

        body {
            background-color: var(--bg-color-main);
            background-image: radial-gradient(circle, #1a222e 0%, var(--bg-color-main) 100%);
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            color: var(--font-color-main);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ------------------- HEADER Y BOTÓN DE PERFIL ------------------- */
        .header {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .profile-btn {
            background-color: var(--bg-color-card);
            color: var(--font-color-main);
            border: 1px solid var(--border-color-dark);
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 10px var(--shadow-dark);
        }

        .profile-btn:hover {
            background-color: var(--border-color-dark);
            transform: scale(1.05);
            box-shadow: 0 6px 12px var(--shadow-dark);
        }

        /* ------------------- CONTENEDOR PRINCIPAL ------------------- */
        .checkout-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .product-checkout-view {
            background-color: var(--bg-color-card);
            border-radius: 1rem;
            box-shadow: 0 0 30px var(--shadow-dark);
            padding: 3rem;
            max-width: 900px;
            width: 100%;
            display: flex;
            gap: 2rem;
            animation: fadeIn 1s ease-in-out;
            flex-wrap: wrap;
        }

        /* ------------------- SECCIONES DEL PRODUCTO ------------------- */
        .product-image-section {
            flex: 1;
            min-width: 300px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* MODIFICACIÓN: Imagen más grande */
        .product-image-section img {
            width: 100%;
            max-width: 450px; /* Aumentado el tamaño máximo */
            height: auto;
            border-radius: 0.5rem;
            box-shadow: 0 8px 25px var(--shadow-dark);
            transition: transform 0.4s ease-in-out;
            margin: 0 auto; /* Centrar la imagen */
        }

        .product-image-section img:hover {
            transform: translateY(-10px) scale(1.05); /* Efecto de zoom al hacer hover */
        }
        
        .product-details-section {
            flex: 1.5;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-details-section h2 {
            color: var(--accent-color-blue);
            font-weight: bold;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .product-details-section h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent-color-green);
            margin-bottom: 1.5rem;
            animation: pulse 1.5s infinite;
        }

        .product-description {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .product-features {
            list-style: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .product-features li {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .product-features i {
            color: var(--accent-color-green);
            margin-right: 10px;
        }
        
        .trust-badges {
            display: flex;
            justify-content: flex-start;
            gap: 1.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .trust-badges .badge-item {
            text-align: center;
            font-size: 0.85rem;
            color: #99aab5;
        }
        
        .trust-badges i {
            color: var(--accent-color-blue);
            font-size: 2rem;
        }
        
        .trust-badges p {
            margin-top: 0.5rem;
            line-height: 1.2;
            font-size: 0.9rem;
        }

        /* ------------------- OTROS ESTILOS ------------------- */
        .error-message {
            color: var(--accent-color-red);
            font-weight: bold;
            margin-top: 1rem;
        }

        .btn-back {
            background-color: var(--border-color-dark);
            color: var(--font-color-main);
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
            color: var(--accent-color-green);
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
        
        /* ------------------- ANIMACIONES ------------------- */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ------------------- RESPONSIVIDAD ------------------- */
        @media (max-width: 768px) {
            .product-checkout-view {
                flex-direction: column;
                padding: 1.5rem;
            }
            .trust-badges {
                justify-content: center;
            }
            
            /* Ajuste para móviles: imagen más pequeña */
            .product-image-section img {
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <button class="profile-btn" aria-label="Abrir perfil">
            <i class="fas fa-user"></i>
        </button>
    </header>

    <div class="checkout-container">
        <div class="product-checkout-view">
            <div class="product-details-section">
                <h2>Confirmar Compra</h2>
                <h1 class="mb-2">AgainSafeGas Sentinel</h1>
                <p class="product-price">
                    $100 USD
                </p>
                <p class="product-description">Estás a punto de adquirir el innovador dispositivo <strong>AgainSafeGas Sentinel</strong>, un detector de gas de última generación que garantiza la seguridad de tu hogar y de tu familia.</p>
                
                <hr style="border-color: var(--border-color-dark);">

                <h4>Características Clave:</h4>
                <ul class="product-features">
                    <li><i class="fas fa-check-circle"></i> Detección ultra-sensible de fugas de gas.</li>
                    <li><i class="fas fa-check-circle"></i> Alertas instantáneas en tu smartphone.</li>
                    <li><i class="fas fa-check-circle"></i> Batería de larga duración (hasta 6 meses).</li>
                    <li><i class="fas fa-check-circle"></i> Diseño compacto y fácil instalación.</li>
                </ul>

                <hr style="border-color: var(--border-color-dark);">
                
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
            
            <div class="product-image-section">
                <img src="/imagenes/Sentinel.png" alt="Detector ASG">
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