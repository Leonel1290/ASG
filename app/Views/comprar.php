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
            color: #48bb78;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background-color: #2d3748;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .card-title {
            color: #48bb78;
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #a0aec0;
        }

        .form-control {
            background-color: #4a5568;
            color: #fff;
            border: 1px solid #6b7280;
        }

        .form-control:focus {
            background-color: #4a5568;
            color: #fff;
            border-color: #48bb78;
            box-shadow: 0 0 0 0.25rem rgba(72, 187, 120, 0.25);
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

        .alert-info {
            background-color: #2a69ac;
            color: #fff;
            border-color: #2a69ac;
        }
        .alert-success {
            background-color: #48bb78;
            color: #fff;
            border-color: #48bb78;
        }
        .alert-danger {
            background-color: #e53e3e;
            color: #fff;
            border-color: #e53e3e;
        }

        /* Footer styles */
        .footer {
            background-color: #2d3748;
            color: #cbd5e0;
            padding: 1rem 0;
            text-align: center;
            margin-top: auto; /* Pushes footer to the bottom */
        }
    </style>
    <script src="https://www.paypal.com/sdk/js?client-id=<?= getenv('PAYPAL_CLIENT_ID') ?>&currency=USD"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG" width="40" height="40" class="d-inline-block align-top">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>"><i class="fas fa-home me-1"></i> Inicio</a>
                    </li>
                    <?php if (session()->get('logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/perfil') ?>"><i class="fas fa-user me-1"></i> Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="<?= base_url('/comprar') ?>"><i class="fas fa-shopping-cart me-1"></i> Comprar</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/logout') ?>"><i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/loginobtener') ?>"><i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/register') ?>"><i class="fas fa-user-plus me-1"></i> Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="card">
            <h2 class="card-title">Comprar Dispositivo ASG</h2>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('info')): ?>
                <div class="alert alert-info">
                    <?= session()->getFlashdata('info') ?>
                </div>
            <?php endif; ?>

            <p>
                El precio del dispositivo es de **USD 10.00**.
            </p>
            <p>
                Al completar la compra, se te asignará automáticamente un dispositivo disponible y se vinculará a tu cuenta.
            </p>

            <div id="paypal-button-container"></div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> ASG. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const BASE_URL = '<?= base_url() ?>';
            const price = 10.00; // Define el precio del dispositivo

            paypal.Buttons({
                // Configuración del estilo del botón
                style: {
                    layout: 'vertical', // 'vertical' o 'horizontal'
                    color:  'gold',     // 'gold', 'blue', 'silver', 'white', 'black'
                    shape:  'rect',     // 'rect' o 'pill'
                    label:  'paypal',   // 'paypal', 'checkout', 'pay', 'buynow', 'installment'
                },

                // Función que se llama para crear la orden de pago
                createOrder: function(data, actions) {
                    return fetch(BASE_URL + '/paypal/create-order', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest', // Importante para CI4 CSRF si lo tienes activado
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // Añadir token CSRF
                        },
                        body: JSON.stringify({
                             // No necesitamos enviar MAC aquí, la MAC ya está en la sesión
                             // o se asignará en el backend.
                        })
                    }).then(function(response) {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                console.error('Error en createOrder:', errorData);
                                throw new Error(errorData.message || 'Error al crear la orden de PayPal.');
                            });
                        }
                        return response.json();
                    }).then(function(orderData) {
                        return orderData.id; // Retorna el ID de la orden de PayPal
                    }).catch(error => {
                        console.error('Fetch error during createOrder:', error);
                        alert('Error al crear la orden de pago: ' + error.message);
                        return null; // Devuelve null para evitar que el proceso de PayPal continúe
                    });
                },

                // Función que se llama cuando el usuario aprueba el pago
                onApprove: function(data, actions) {
                    return fetch(BASE_URL + '/paypal/capture-order/' + data.orderID, {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest', // Importante para CI4 CSRF
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // Añadir token CSRF
                        },
                        body: JSON.stringify({}) // Cuerpo vacío o datos adicionales si los necesitas
                    })
                    .then(response => response.json())
                    .then(captureData => {
                        if (captureData.status === 'success') {
                            alert('¡Pago completado! Se ha asignado y vinculado un dispositivo a tu cuenta.');
                            window.location.href = BASE_URL + '/paypal/success'; // Redirigir a una página de éxito
                        } else {
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