<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Confirmar Compra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #1E3D59;
      font-family: 'Segoe UI', sans-serif;
      color: #333;
      margin: 0;
      padding: 0;
    }

    .checkout-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .product-card {
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      padding: 30px;
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .product-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .product-card h2 {
      margin-bottom: 10px;
      color: #007bff;
    }

    .product-card p {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .price {
      font-size: 24px;
      color: #28a745;
      margin-bottom: 20px;
    }

    #paypal-button-container {
      margin-top: 20px;
    }

    .footer {
      margin-top: 50px;
      text-align: center;
      color: #aaa;
      font-size: 14px;
    }

    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.6);
      display: flex;
      justify-content: center;
      align-items: center;
      visibility: hidden;
      opacity: 0;
      transition: opacity 0.4s ease, visibility 0.4s;
    }

    .modal.show {
      visibility: visible;
      opacity: 1;
    }

    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      text-align: center;
      width: 300px;
      color: #333;
    }

    .modal-content h3 {
      margin: 0;
      color: #28a745;
    }

    .modal-content button {
      margin-top: 20px;
      background: #007bff;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
 <!-- Botón volver -->
<button class="btn btn-outline-light position-absolute top-0 end-0 m-3" data-bs-toggle="modal" data-bs-target="#logoutModal">
  <i class="fas fa-sign-out-alt"></i> Volver al Inicio
</button>

<!-- Modal de Confirmación -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="logoutModalLabel">¿Deseas volver?</h5>
        <a href="<?= base_url('logout') ?>" class="btn btn-danger">Volver</a>
      </div>
    </div>
  </div>
</div>

<!-- FontAwesome & Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

  <div class="checkout-container">
    <div class="product-card">
      <img src="https://www.rosarioseguridad.com.ar/admin/productos/3fbd1467fa1e6f1747aa1651f7545fc0.jpg" alt="Producto">
      <h2>Detector de Gas</h2>
      <p>Dispositivo inteligente para monitoreo de gas en tiempo real.</p>
      <div class="price">$100.00 USD</div>

      <div id="paypal-button-container"></div>
    </div>

    <div class="footer">
      &copy; 2024 AgainSafeGas Solutions | Todos los derechos reservados.
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="modalExito">
    <div class="modal-content">
      <h3>✅ ¡Pago realizado con éxito!</h3>
      <button id="cerrarModal">Aceptar</button>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalExito');
    const cerrarModal = document.getElementById('cerrarModal');

    paypal.Buttons({
      createOrder: function (data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '100.00'
            }
          }]
        });
      },
      onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
          modal.classList.add('show');
        });
      },
      onCancel: function (data) {
        alert('❌ Pago cancelado');
      },
      onError: function (err) {
        alert('⚠️ Error al procesar el pago');
        console.error(err);
      }
    }).render('#paypal-button-container');

    cerrarModal.addEventListener('click', () => {
      modal.classList.remove('show');
      // Aquí podrías redirigir al usuario si quieres
      // window.location.href = 'tu-pagina-principal';
    });
  </script>
</body>
</html>
