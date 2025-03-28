<!doctype html>
<html lang="es">
  <head>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <!-- Meta tags requeridos -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
      /* Estilos personalizados */
      body {
        background-color: #f4f6f9;
        color: #333;
        font-family: Arial, sans-serif;
      }

      .navbar {
        margin-bottom: 0;
        background-color: #001f3f; /* Azul marino */
      }

      .navbar-brand {
        font-weight: bold;
        color: #fff;
      }

      .navbar-brand:hover {
        color: #ddd;
      }

      .hero {
        background: linear-gradient(to right, #001f3f, #3aafa9); /* Degradado azul marino a azul verdoso */
        color: white;
        padding: 80px 0;
        text-align: center;
      }

      .hero h1 {
        font-size: 3em;
        margin-bottom: 20px;
      }

      .hero p {
        font-size: 1.5em;
        margin-bottom: 30px;
      }

      .hero .btn-primary {
        background-color: #f39c12; /* Naranja ámbar */
        border-color: #f39c12;
        padding: 10px 30px;
        font-size: 1.2em;
      }

      .hero .btn-primary:hover {
        background-color: #e67e22; /* Un tono más oscuro para el hover */
        border-color: #e67e22;
      }

      .feature-icons {
        padding: 60px 0;
        text-align: center;
      }

      .feature-icons .icon {
        font-size: 3em;
        color: #001f3f; /* Azul marino */
        margin-bottom: 20px;
      }

      .feature-icons h3 {
        font-size: 1.5em;
        margin-bottom: 15px;
      }

      .feature-icons p {
        font-size: 1.1em;
        color: #555;
      }

      footer {
        background-color: #343a40;
        color: white;
        padding: 20px;
        margin-top: 40px;
        text-align: center;
      }

      /* Estilo personalizado para el cuadro de información de la empresa */
      .company-info {
        background-color: #001f3f; /* Azul marino */
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-top: 40px;
        text-align: center;
      }

      .company-info a {
        color: white;
        text-decoration: underline;
      }

      .company-info a:hover {
        color: #ccc;
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

    <title>ASG - Seguridad en tu Hogar</title>
  </head>
  <body>
   <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <a class="navbar-brand" href="#">ASG</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <center><a class="nav-link" href="#companyInfo">¿Cómo contactarnos?</a></center>
      </li>
      <li class="nav-item">
        <a class="nav-link btn btn-warning text-dark px-3 ml-2" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
      </li>
    </ul>
  </div>
</nav>


    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <h1>Protege lo que más importa</h1>
        <p>Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
        <a href="<?= base_url('/loginobtener') ?>" class="btn btn-light ml-3">Inicia Sesión</a>
      </div>
    </section>

    <!-- Feature Icons Section -->
    <section id="features" class="feature-icons">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <div class="icon">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h3>Seguridad Total</h3>
            <p>Sistema avanzado de cierre automático de válvulas para garantizar tu seguridad.</p>
          </div>
          <div class="col-md-4">
            <div class="icon">
              <i class="fas fa-mobile-alt"></i>
            </div>
            <h3>Monitoreo Remoto</h3>
            <p>Controla y monitorea las fugas desde tu dispositivo móvil con nuestra app.</p>
          </div>
          <div class="col-md-4">
            <div class="icon">
              <i class="fas fa-bell"></i>
            </div>
            <h3>Alertas en Tiempo Real</h3>
            <p>Recibe notificaciones instantáneas cuando detectemos cualquier fuga de gas.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Información de la empresa -->
    <div class="container mt-5" id="companyInfo">
      <div class="company-info mt-5">
        <h2>Sobre Nosotros</h2>
        <p><strong>Empresa:</strong> ASG</p>
        <p><strong>Dirección:</strong> Río Tercero</p>
        <p><strong>Teléfono:</strong> 3571-623889</p>
        <p><strong>Email:</strong> againsafegas.ascii@gmail.com</p>
        <p><strong>Sitio Web:</strong> <a href="https://www.gassafe.com">www.Againsafegas.com</a></p>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>&copy; 2024 AgainSafeGas Solutions | Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para scroll suave -->
    <script>
      $(document).ready(function(){
        // Scroll suave al hacer clic en los enlaces de navegación
        $('a[href^="#"]').on('click', function(e) {
          e.preventDefault();

          $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
          }, 500);
        });
      });
    </script>

  </body>
</html>
