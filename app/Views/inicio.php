<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
      /* Estilos personalizados */
      body {
        background-color: #f4f6f9;
        color: #333;
      }

      .navbar {
        margin-bottom: 20px;
        background-color: #343a40;
      }

      .navbar-brand {
        font-weight: bold;
        color: #fff;
      }

      .navbar-brand:hover {
        color: #ddd;
      }

      .dropdown-item:hover {
        background-color: #007bff;
        color: white;
      }

      .container {
        max-width: 1200px;
      }

      h1, h2 {
        color: #007bff;
        font-weight: bold;
        margin-top: 20px;
      }

      p.lead {
        font-size: 1.2em;
        margin-bottom: 20px;
      }

      .feature-list {
        margin: 30px 0;
      }

      .feature-list li {
        margin-bottom: 10px;
      }

      .company-info {
        background-color: #007bff;
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-top: 40px;
      }

      .company-info a {
        color: white;
        text-decoration: underline;
      }

      .company-info a:hover {
        color: #ccc;
      }

      footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
        margin-top: 40px;
        text-align: center;
      }

      /* Estilo personalizado para el botón de cerrar sesión */
      .btn-logout {
        color: #007bff; /* Color por defecto */
      }

      .btn-logout:hover {
        background-color: red; /* Color al pasar el cursor */
        color: white; /* Cambiar el color del texto al pasar el cursor */
      }
    </style>

    <title>Información del Dispositivo</title>
  </head>
  <body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
      <a class="navbar-brand" href="#">GasSafe</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/inicio') ?>">Inicio</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Usuario
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenu">
              <a class="dropdown-item" href="<?= base_url('/perfilobtener') ?>">Perfil</a>
              <a class="dropdown-item btn-logout" href="<?= base_url('/logout') ?>">Cerrar Sesión</a> <!-- Cambiado aquí -->
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Información del dispositivo -->
    <div class="container">
      <div class="row">
        <div class="col-md-8">
          <h1>Detector de Gas Inteligente</h1>
          <p class="lead">Protege tu hogar con nuestro avanzado sistema de detección de fugas de gas. Equipado con sensores de alta precisión y un sistema de respuesta automática, este dispositivo es esencial para la seguridad de tu hogar.</p>

          <h2>Características Destacadas</h2>
          <ul class="feature-list">
            <li>Sensores de última tecnología para detectar múltiples gases inflamables.</li>
            <li>Notificaciones en tiempo real en tu dispositivo móvil.</li>
            <li>Sistema de cierre automático de válvulas para mayor seguridad.</li>
            <li>Integración con una aplicación móvil para monitoreo y control remoto.</li>
            <li>Fácil instalación en cualquier tipo de vivienda.</li>
          </ul>

        </div>
        <div class="col-md-4">
          <img src="https://www.abelson.com.ar/11196-thickbox_default/detector-de-gas-profesional-intelligent-gas.jpg" class="img-fluid" alt="Imagen del dispositivo de gas">
        </div>
      </div>

      <!-- Información de la empresa -->
      <div class="company-info mt-5">
        <h2>Sobre Nosotros</h2>
        <p><strong>Empresa:</strong> ASG</p>
        <p><strong>Dirección:</strong> Calle 123, Ciudad, País</p>
        <p><strong>Teléfono:</strong> +123 456 7890</p>
        <p><strong>Email:</strong> contacto@gassafe.com</p>
        <p><strong>Sitio Web:</strong> <a href="https://www.gassafe.com">www.Againsafegas.com</a></p>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>&copy; 2024 GasSafe Solutions. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
