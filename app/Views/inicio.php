<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASG - Seguridad en tu Hogar</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0D1B2A, #1B263B);
            font-family: 'Poppins', sans-serif;
            color: #E0E1DD;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(13, 27, 42, 0.9);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand, .nav-link {
            color: #FFFFFF;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #778DA9;
        }

        .btn-custom {
            background-color: #778DA9;
            border: none;
            color: #0D1B2A;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(119, 141, 169, 0.2);
        }

        .btn-custom:hover {
            background-color: #5F738A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(119, 141, 169, 0.3);
        }

        .hero {
            padding: 4rem 0;
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-size: 2.8rem;
            color: #FFFFFF;
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-line {
            width: 70px;
            height: 4px;
            background-color: #778DA9;
            margin: 1.25rem auto 1.75rem;
            border-radius: 2px;
        }

        .hero-img {
            max-width: 90%;
            height: auto;
            margin-top: 2rem;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .features {
            padding: 4rem 0;
            background-color: #415A77;
            border-radius: 20px;
            margin: 3rem auto;
            max-width: 95%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .features h2 {
            color: #FFFFFF;
            font-weight: 700;
            margin-bottom: 3rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .feature-card {
            background-color: #0D1B2A;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
        }

        .features i {
            font-size: 3rem;
            color: #778DA9;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px rgba(119, 141, 169, 0.5);
        }

        .features h3 {
            color: #FFFFFF;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .features p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .company-info {
            background-color: #415A77;
            border-radius: 20px;
            padding: 3rem;
            color: #E0E1DD;
            margin: 3rem auto;
            max-width: 95%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .company-info h2 {
            color: #FFFFFF;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .company-info p {
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
        }

        .company-info strong {
            color: #778DA9;
        }

        .company-info a {
            color: #778DA9;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .company-info a:hover {
            color: #FFFFFF;
        }

        footer {
            background-color: #0D1B2A;
            text-align: center;
            padding: 2rem;
            font-size: 0.9rem;
            color: #E0E1DD;
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28224, 225, 221, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem #778DA9;
            border-color: #778DA9;
        }

        @media (min-width: 768px) {
            .hero h1 {
                font-size: 3.5rem;
            }
            .hero-img {
                max-width: 100%;
            }
            .features, .company-info {
                max-width: 80%;
                margin: 5rem auto;
            }
            .feature-card {
                padding: 2.5rem;
            }
            .company-info {
                padding: 3.5rem;
            }
        }
    </style>
</head>
<body>

<div id="explosionOverlay" class="explosion-animation-overlay">
    <div class="explosion-image-container"></div>
</div>

<header>
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero d-flex align-items-center" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <h1>Protege lo que más importa</h1>
                    <div class="hero-line"></div>
                    <p class="lead">Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
                    <a href="<?= base_url('/loginobtener') ?>" class="btn btn-custom mt-4">Inicia Sesión</a>
                </div>
                <div class="col-md-6 text-center mt-5 mt-md-0">
                    <img src="https://cdn3d.iconscout.com/3d/premium/thumb/fuga-de-gas-8440307-6706766.png?f=webp"
                         alt="Ilustración de fuga de gas"
                         class="hero-img img-fluid"
                         loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <section class="features py-5 text-center">
        <div class="container">
            <h2>Características Principales</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4 col-sm-6">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Seguridad Total</h3>
                        <p>Sistema de cierre automático de válvulas para una protección eficaz.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Monitoreo Remoto</h3>
                        <p>Control desde tu celular a través de nuestra app segura.</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="feature-card">
                        <i class="fas fa-bell"></i>
                        <h3>Alertas en Tiempo Real</h3>
                        <p>Notificaciones inmediatas ante cualquier fuga detectada.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="company">
        <div class="container">
            <div class="company-info">
                <h2>Sobre Nosotros</h2>
                <address class="mb-0">
                    <p><strong>Empresa:</strong> AgainSafeGas</p>
                    <p><strong>Dirección:</strong> Río Tercero, Córdoba</p>
                    <p><strong>Teléfono:</strong> <a href="tel:+543571623889">3571-623889</a></p>
                    <p><strong>Email:</strong> <a href="mailto:againsafegas.ascii@gmail.com">againsafegas.ascii@gmail.com</a></p>
                    <p><strong>Sitio Web:</strong> <a href="https://www.gassafe.com" target="_blank">www.AgainSafeGas.com</a></p>
                </address>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 AgainSafeGas Solutions | Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){
        // Smooth scroll para enlaces internos
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if(target.length) {
                const offset = $('.navbar').outerHeight() + 10;
                $('html, body').animate({ scrollTop: target.offset().top - offset }, 500);
            }
        });

        // Ocultar overlay de animación al cargar la página (si lo mantienes)
        $(window).on('load', function() {
            // Elimina la clase o aplica un fade-out si quieres una animación al inicio
            $('#explosionOverlay').addClass('fade-out');
            // O simplemente ocultarlo si no quieres animación
            // $('#explosionOverlay').hide();
        });
    });
</script>
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            // Asegúrate de que base_url('service-worker.js') apunte a la ruta correcta
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