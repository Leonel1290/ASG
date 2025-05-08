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
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .product-card {
            background-color: #2d3748; /* Dark card background */
            color: #e2e8f0; /* Light text */
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); /* Stronger shadow */
            padding: 30px;
            max-width: 450px; /* Slightly wider card */
            width: 100%;
            text-align: center;
             /* Subtle hover effect */
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease;
        }

         .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }


        .product-card img {
            width: 100%;
            max-width: 300px; /* Limit image size */
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
             object-fit: cover; /* Ensure image covers area without distortion */
        }

        .product-card h2 {
            margin-bottom: 10px;
            color: #63b3ed; /* Lighter blue for dark mode */
            font-size: 1.8rem; /* Slightly larger title */
        }

        .product-card p {
            font-size: 1rem; /* Standard paragraph size */
            margin-bottom: 20px;
            color: #a0aec0; /* Muted text color */
        }

        .price {
            font-size: 2rem; /* Prominent price */
            color: #48bb78; /* Green for price */
            margin-bottom: 25px; /* More space below price */
            font-weight: bold;
        }

        #paypal-button-container {
            margin-top: 20px;
        }

        .footer {
            margin-top: auto; /* Push footer to the bottom */
            padding: 20px;
            text-align: center;
            color: #718096; /* Muted color */
            font-size: 0.9rem; /* Slightly smaller text */
        }

        /* Custom styles for Bootstrap Modals in dark mode */
        .modal-content {
            background-color: #2d3748; /* Dark background for modal */
            color: #e2e8f0; /* Light text for modal */
            border: none; /* Remove default border */
        }

        .modal-header {
            border-bottom-color: #4a5568; /* Darker border */
        }

        .modal-footer {
            border-top-color: #4a5568; /* Darker border */
        }

        .modal-title {
            color: #f7fafc; /* Light color for title */
        }

        .btn-close {
            filter: invert(1); /* Make close button visible on dark header */
        }

        /* Style for success modal header */
        .modal-header.bg-success-dark {
            background-color: #38a169 !important; /* Darker green */
            color: #fff;
        }

         /* Style for danger modal header */
        .modal-header.bg-danger-dark {
            background-color: #c53030 !important; /* Darker red */
            color: #fff;
        }
    </style>
</head>
<body>

<a href="<?= base_url('/') ?>" class="btn btn-outline-secondary position-absolute top-0 start-0 m-3">
     <i class="fas fa-arrow-left me-2"></i> Volver
</a>


<main class="checkout-container">
    <div class="product-card">
        <img src="imagenes/detector.png" alt="Detector de Gas">
        <h2>Detector de Gas</h2>
        <p>Dispositivo inteligente para monitoreo de gas en tiempo real.</p>
        <div class="price">$100.00 USD</div>

        <div id="paypal-button-container"></div>
    </div>
</main>

<footer class="footer">
    &copy; 2024 AgainSafeGas Solutions | Todos los derechos reservados.
</footer>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success-dark">
        <h5 class="modal-title" id="successModalLabel"><i class="fas fa-check-circle me-2"></i> ¡Pago Exitoso!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p>✅ Su compra se ha completado con éxito.</p>
        <p>Será redirigido en breve.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-primary" id="redirectAfterSuccess">Continuar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger-dark">
        <h5 class="modal-title" id="errorModalLabel"><i class="fas fa-times-circle me-2"></i> Error en el Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <p id="errorModalBodyText">Ha ocurrido un error durante el proceso de pago.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Get Bootstrap modal instances
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
    const errorModalBodyText = document.getElementById('errorModalBodyText');
    const redirectAfterSuccessBtn = document.getElementById('redirectAfterSuccess');

    // Function to show error modal
    function showErrorMessage(message) {
        errorModalBodyText.textContent = message;
        errorModal.show();
    }

    // Handle redirect after clicking "Continuar" in success modal
    redirectAfterSuccessBtn.addEventListener('click', () => {
        // Redirect to the login page as requested
        window.location.href = '<?= base_url("login") ?>'; // Make sure base_url("login") outputs the correct URL
    });

    // PayPal Buttons integration
    paypal.Buttons({
        style: {
            layout: 'vertical', // 'vertical' or 'horizontal'
            color: 'gold',      // 'gold', 'blue', 'silver', 'black'
            shape: 'rect',      // 'rect' or 'pill'
            label: 'paypal'     // 'paypal', 'checkout', 'buynow', 'pay'
        },
        createOrder: function (data, actions) {
            // Set up the order
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
                setTimeout(() => {
                     if (successModal._isShown) { // Check if modal is still open
                         window.location.href = '<?= base_url("login") ?>';
                     }
                }, 3000); // Redirect after 3 seconds if modal is still open

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

</body>
</html>