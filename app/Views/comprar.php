<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar con PayPal</title>
    <style>
        /* Estilos básicos para la página */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Realizar Compra</h1>
        <p>El total a pagar es de $10.00 ARS. Haga clic en el botón de PayPal para continuar.</p>
        <div id="paypal-button-container"></div>
    </div>

    <script src="https://www.paypal.com/sdk/js?client-id=AcPUPMO4o6DTBBdmCmosS-e1fFHHyY3umWiNLu0T0b0RCQsdKW7mEJt3c3WaZ2VBZdSZHIgIVQCXf54_&currency=ARS"></script>

    <script>
        paypal.Buttons({
            // Configurar la transacción
            createOrder: function(data, actions) {
                return fetch('/api/paypal/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        // El valor debe coincidir con el del controlador para evitar errores
                        // Tu controlador actualmente usa USD, pero tu vista indica ARS.
                        // Asegúrate de que ambas partes usen la misma moneda.
                        amount: '10.00'
                    })
                }).then(function(response) {
                    return response.json();
                }).then(function(order) {
                    return order.id;
                });
            },

            // Capturar la transacción cuando se aprueba
            onApprove: function(data, actions) {
                return fetch('/api/paypal/capture-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        orderID: data.orderID
                    })
                }).then(function(response) {
                    return response.json();
                }).then(function(details) {
                    // Muestra un mensaje de éxito al usuario
                    if (details.status === 'COMPLETED') {
                        alert('¡Transacción completada! ID de pago: ' + details.id);
                        // Aquí puedes redirigir al usuario o actualizar la página
                        // window.location.href = '/success_page';
                    } else {
                        alert('La transacción no se pudo completar. Estado: ' + details.status);
                    }
                }).catch(function(error) {
                    console.error('Error al capturar la orden:', error);
                    alert('Hubo un error al procesar el pago.');
                });
            },

            // Manejar errores
            onError: function(err) {
                console.error('Error en el pago de PayPal:', err);
                alert('Ocurrió un error con el pago de PayPal.');
            },

            // Cancelar la transacción
            onCancel: function(data) {
                alert('Transacción cancelada por el usuario.');
            }
        }).render('#paypal-button-container'); // Renderiza el botón de PayPal en el contenedor
    </script>
</body>
</html>