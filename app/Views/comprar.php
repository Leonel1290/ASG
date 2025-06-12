<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar Dispositivo - ASG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos personalizados */
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #1a202c; /* Fondo oscuro principal */
            color: #cbd5e0; /* Texto claro */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: #2d3748 !important;
        }

        .navbar-brand {
            color: #48bb78 !important;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: #cbd5e0 !important;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #48bb78 !important;
        }

        .container {
            flex: 1; /* Permite que el contenedor se expanda y ocupe el espacio disponible */
            padding-top: 56px; /* Espacio para la navbar fija */
            padding-bottom: 2rem;
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -5px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #48bb78;
            border-color: #48bb78;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #38a169;
            border-color: #38a169;
        }

        .form-group label {
            color: #a0aec0;
            font-weight: bold;
        }

        .form-control {
            background-color: #4a5568;
            border: 1px solid #2d3748;
            color: #e2e8f0;
        }

        .form-control:focus {
            background-color: #4a5568;
            color: #e2e8f0;
            border-color: #48bb78;
            box-shadow: 0 0 0 0.25rem rgba(72, 187, 120, 0.25);
        }

        .invalid-feedback {
            color: #fc8181;
        }

        .alert {
            border-radius: 0.3rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #1a202c;
            border-color: #a7f3d0;
        }

        .alert-danger {
            background-color: #fed7d7;
            color: #1a202c;
            border-color: #fbcbcb;
        }

        .paypal-button-container {
            margin-top: 20px;
        }

        /* Footer */
        .footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 1rem 0;
            text-align: center;
            margin-top: auto; /* Empuja el footer hacia abajo */
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/inicio') ?>">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/enlace') ?>">Enlazar MAC</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="<?= base_url('/comprar') ?>">Comprar</a>
                        </li>
                        <li class="nav-item">
                            <form action="<?= base_url('/logout') ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="nav-link btn btn-link text-decoration-none" style="border: none; background: none;">Cerrar Sesión</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/login') ?>">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/register') ?>">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Comprar Dispositivo ASG</h4>

                        <?php if (session('success')): ?>
                            <div class="alert alert-success mt-3"><i class="fas fa-check-circle me-2"></i> <?= session('success') ?></div>
                        <?php endif; ?>
                        <?php if (session('error')): ?>
                            <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-triangle me-2"></i> <?= session('error') ?></div>
                        <?php endif; ?>
                        <?php if (session('info')): ?>
                            <div class="alert alert-info mt-3"><i class="fas fa-info-circle me-2"></i> <?= session('info') ?></div>
                        <?php endif; ?>

                        <p class="text-center">
                            Adquiere tu dispositivo ASG para monitorear tu hogar.
                            <br>
                            **Precio: $19.99 USD**
                        </p>

                        <div class="mb-3">
                            <label for="mac_input" class="form-label">Ingresa la MAC de tu Dispositivo:</label>
                            <input type="text" class="form-control" id="mac_input" placeholder="Ej: AA:BB:CC:DD:EE:FF">
                            <small class="form-text text-muted">Asegúrate de ingresar la MAC correcta de tu dispositivo ASG.</small>
                        </div>
                        <div id="mac-error" class="text-danger mb-3" style="display: none;"></div>

                        <div class="paypal-button-container">
                            <div id="paypal-button-container"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <span>© <?= date('Y') ?> ASG. Todos los derechos reservados.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://www.paypal.com/sdk/js?client-id=TU_PAYPAL_CLIENT_ID&currency=USD"></script>

    <script>
        // Reemplaza 'TU_PAYPAL_CLIENT_ID' con tu Client ID real de PayPal
        // Este ID puede ser de Sandbox para pruebas o de Live para producción.
        // Lo ideal es cargar este ID de forma segura (ej. desde una variable de entorno en tu servidor).
        const PAYPAL_CLIENT_ID = "TU_PAYPAL_CLIENT_ID"; // <--- ¡CAMBIA ESTO!

        // URL base de tu aplicación CodeIgniter
        const BASE_URL = "<?= base_url() ?>";

        document.addEventListener('DOMContentLoaded', function() {
            // Validar la MAC antes de inicializar PayPal
            const macInput = document.getElementById('mac_input');
            const macErrorDiv = document.getElementById('mac-error');
            let validatedMac = null;

            function validateMac(mac) {
                const macRegex = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
                return macRegex.test(mac);
            }

            macInput.addEventListener('input', function() {
                const rawMac = macInput.value;
                if (!validateMac(rawMac)) {
                    macErrorDiv.textContent = 'Formato de MAC inválido. Ej: AA:BB:CC:DD:EE:FF';
                    macErrorDiv.style.display = 'block';
                    validatedMac = null;
                } else {
                    macErrorDiv.style.display = 'none';
                    validatedMac = rawMac.toUpperCase().replace(/[:-]/g, ''); // Limpiar la MAC
                }
            });

            // Renderizar los botones de PayPal
            paypal.Buttons({
                style: {
                    layout: 'vertical', // o 'horizontal'
                    color:  'gold',     // 'gold', 'blue', 'silver', 'white', 'black'
                    shape:  'rect',     // 'rect', 'pill'
                    label:  'paypal'    // 'paypal', 'checkout', 'pay', 'buynow', 'installment'
                },
                // Función que se llama cuando se crea una orden en PayPal
                createOrder: function(data, actions) {
                    if (!validatedMac) {
                        macErrorDiv.textContent = 'Por favor, ingresa una MAC válida para continuar.';
                        macErrorDiv.style.display = 'block';
                        return actions.reject('MAC inválida.'); // Detiene el proceso de PayPal
                    }

                    // Llama a tu endpoint del servidor para crear la orden de PayPal
                    return fetch(BASE_URL + '/paypal/create-order', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest', // Opcional, para identificar la solicitud como AJAX
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>' // Añadir token CSRF
                        },
                        body: JSON.stringify({
                            mac_dispositivo: validatedMac // Envía la MAC validada al servidor
                        })
                    })
                    .then(response => response.json())
                    .then(order => {
                        if (order.id) {
                            return order.id; // Retorna el ID de la orden de PayPal
                        } else {
                            // Manejar errores del servidor
                            console.error('Error al crear la orden:', order.message || 'Error desconocido');
                            alert('Hubo un error al preparar el pago. Inténtalo de nuevo.');
                            return actions.reject('Error en el servidor al crear la orden.'); // Detiene el proceso de PayPal
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('Error de red o servidor al crear la orden. Inténtalo de nuevo.');
                        return actions.reject('Error de red al crear la orden.'); // Detiene el proceso de PayPal
                    });
                },
                // Función que se llama cuando el usuario aprueba el pago en PayPal
                onApprove: function(data, actions) {
                    // Llama a tu endpoint del servidor para capturar la orden
                    return fetch(BASE_URL + '/paypal/capture-order', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_hash() ?>' // Añadir token CSRF
                        },
                        body: JSON.stringify({
                            orderID: data.orderID
                        })
                    })
                    .then(response => response.json())
                    .then(captureData => {
                        if (captureData.status === 'success') {
                            // Pago exitoso, redirigir a la página de éxito
                            window.location.href = BASE_URL + '/paypal/success';
                        } else {
                            // Pago no completado o error, redirigir a la página de cancelación/error
                            console.error('Error al capturar el pago:', captureData.message || 'Error desconocido');
                            alert('Hubo un error al procesar el pago: ' + (captureData.message || 'Transacción no completada.'));
                            window.location.href = BASE_URL + '/paypal/cancel';
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        alert('Error de red o servidor al capturar el pago. Inténtalo de nuevo.');
                        window.location.href = BASE_URL + '/paypal/cancel';
                    });
                },
                // Función que se llama si el usuario cancela la transacción
                onCancel: function(data) {
                    // Redirigir a la página de cancelación
                    console.log('Pago cancelado por el usuario.');
                    window.location.href = BASE_URL + '/paypal/cancel';
                },
                // Función que se llama si ocurre un error
                onError: function(err) {
                    console.error('Ocurrió un error en PayPal:', err);
                    alert('Ocurrió un error con PayPal. Por favor, inténtalo de nuevo.');
                    window.location.href = BASE_URL + '/paypal/cancel';
                }
            }).render('#paypal-button-container'); // Renderiza el botón en el div
        });
    </script>
</body>
</html>
