<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Seguridad en tu Hogar</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0D1F23, #132E35);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            margin: 0;
            /* overflow: hidden; */ /* Comentamos overflow: hidden en el body para evitar problemas de scroll */
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(13, 31, 35, 0.8);
            position: fixed; /* Mantenemos la posición fija */
            top: 0; /* La fija en la parte superior */
            width: 100%; /* Ocupa todo el ancho */
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
            padding: 1rem 0; /* Ajusta el padding para que no sea demasiado pequeño */
        }

        .navbar-brand {
            color: #4CAF50 !important; /* Verde vibrante para el logo */
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
            text-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .navbar-nav .nav-link {
            color: #AFB3B7 !important;
            font-weight: 600;
            margin: 0 15px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #fff !important;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
        }

        .hero-section {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: url('<?= base_url('/css/90514.jpg'); ?>') no-repeat center center/cover;
            color: #fff;
            position: relative;
            z-index: 1; /* Asegura que esté sobre la explosión si hay un overlay */
            padding-top: 80px; /* Ajuste para el navbar fijo */
        }

        .hero-content {
            max-width: 800px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.4); /* Overlay oscuro para mejor legibilidad */
            border-radius: 10px;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }

        .hero-section p {
            font-size: 1.25rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-custom {
            background-color: #4CAF50;
            border-color: #4CAF50;
            color: #fff;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #45a049;
            border-color: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .content-section {
            padding: 80px 0;
            text-align: center;
        }

        .content-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #4CAF50;
            margin-bottom: 50px;
            position: relative;
        }

        .content-section h2::after {
            content: '';
            width: 80px;
            height: 4px;
            background-color: #4CAF50;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -15px;
            border-radius: 2px;
        }

        .feature-icon {
            font-size: 3.5rem;
            color: #4CAF50;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .feature-item:hover .feature-icon {
            transform: scale(1.1);
        }

        .feature-item h3 {
            font-size: 1.7rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 15px;
        }

        .feature-item p {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #AFB3B7;
        }

        .about-us-section {
            background: url('<?= base_url('/css/91639.jpg'); ?>') no-repeat center center/cover;
            color: #fff;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .about-us-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6); /* Overlay oscuro */
            z-index: 0;
        }

        .about-us-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .about-us-content h2 {
            color: #4CAF50;
        }

        .about-us-content p {
            font-size: 1.15rem;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .testimonial-carousel {
            background-color: #2d3748;
            padding: 40px 20px;
            border-radius: 10px;
            margin-top: 50px;
        }

        .testimonial-item {
            padding: 20px;
            color: #fff;
            font-style: italic;
        }

        .testimonial-item img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #4CAF50;
        }

        .testimonial-item .client-name {
            font-weight: bold;
            color: #4CAF50;
            margin-top: 10px;
            font-size: 1.1rem;
        }

        .contact-section {
            background-color: #0D1F23;
            padding: 80px 0;
            color: #AFB3B7;
        }

        .contact-section h2 {
            color: #4CAF50;
        }

        .contact-info p {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .contact-info a {
            color: #AFB3B7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-info a:hover {
            color: #fff;
        }

        footer {
            background-color: #0F172A;
            color: #AFB3B7;
            text-align: center;
            padding: 20px 0;
            font-size: 0.9rem;
            border-top: 1px solid #1c2a38;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero-section h1 {
                font-size: 2.8rem;
            }
            .hero-section p {
                font-size: 1.1rem;
            }
            .content-section h2 {
                font-size: 2rem;
            }
            .feature-icon {
                font-size: 3rem;
            }
            .feature-item h3 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding-top: 100px; /* Más espacio en móviles */
            }
            .hero-section h1 {
                font-size: 2.2rem;
            }
            .hero-section p {
                font-size: 1rem;
            }
            .content-section, .about-us-section, .contact-section {
                padding: 60px 0;
            }
            .content-section h2 {
                font-size: 1.8rem;
                margin-bottom: 40px;
            }
            .feature-icon {
                font-size: 2.8rem;
            }
            .feature-item h3 {
                font-size: 1.3rem;
            }
        }

        /* Animación de explosión (si la tienes) */
        #explosionOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 1s ease-out;
            opacity: 1;
        }

        #explosionOverlay.fade-out {
            opacity: 0;
            pointer-events: none; /* Permite clics una vez que se desvanece */
        }

        .explosion-text {
            color: #ff4500; /* OrangeRed */
            font-size: 4rem;
            font-weight: bold;
            animation: text-flicker 1.5s infinite alternate;
        }

        @keyframes text-flicker {
            0%, 100% {
                opacity: 1;
                text-shadow: 0 0 10px #ff4500, 0 0 20px #ff4500, 0 0 30px #ff4500;
            }
            50% {
                opacity: 0.7;
                text-shadow: none;
            }
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#caracteristicas">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#nosotros">Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-custom-nav ms-lg-3" href="<?= base_url('/loginobtener'); ?>" style="background-color: #4CAF50; border-color: #4CAF50; border-radius: 50px;">
                            <i class="fas fa-sign-in-alt me-2"></i> Acceder
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section id="inicio" class="hero-section">
        <div class="hero-content">
            <h1>Seguridad del Gas Inteligente para tu Hogar</h1>
            <p>Monitorea y protege a tu familia con nuestros avanzados detectores de gas. Recibe alertas en tiempo real y gestiona tus dispositivos desde cualquier lugar.</p>
            <a href="<?= base_url('/register'); ?>" class="btn btn-custom"><i class="fas fa-user-plus me-2"></i> Registrarse Gratis</a>
        </div>
    </section>

    <section id="caracteristicas" class="content-section">
        <div class="container">
            <h2>Características Principales</h2>
            <div class="row mt-5">
                <div class="col-md-4 feature-item">
                    <i class="fas fa-bell feature-icon"></i>
                    <h3>Alertas en Tiempo Real</h3>
                    <p>Recibe notificaciones instantáneas en tu dispositivo móvil ante cualquier detección de gas, estés donde estés.</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3>Monitoreo Continuo</h3>
                    <p>Accede a gráficos y estadísticas del nivel de gas de tus detectores para una supervisión constante y proactiva.</p>
                </div>
                <div class="col-md-4 feature-item">
                    <i class="fas fa-mobile-alt feature-icon"></i>
                    <h3>Gestión Remota</h3>
                    <p>Controla y configura tus dispositivos desde nuestra aplicación intuitiva y fácil de usar, disponible 24/7.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="nosotros" class="about-us-section">
        <div class="about-us-content">
            <h2>Acerca de Nosotros</h2>
            <p>En **AgainSafeGas Solutions**, nos dedicamos a la protección de tu hogar y tus seres queridos. Desarrollamos tecnología de vanguardia para la detección de gases peligrosos, ofreciendo soluciones inteligentes y accesibles que brindan tranquilidad.</p>
            <p>Nuestra misión es innovar en seguridad, proporcionando sistemas de monitoreo fiables y eficientes que te mantengan informado y preparado ante cualquier eventualidad.</p>
            <div id="testimonialCarousel" class="carousel slide testimonial-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active text-center">
                        <img src="<?= base_url('/css/usuario.png'); ?>" class="mx-auto d-block" alt="Cliente 1">
                        <p>"Desde que instalé ASG, me siento mucho más seguro. Las alertas son instantáneas y la aplicación es muy fácil de usar."</p>
                        <p class="client-name">- Juan Pérez</p>
                    </div>
                    <div class="carousel-item text-center">
                        <img src="<?= base_url('/css/usuario.png'); ?>" class="mx-auto d-block" alt="Cliente 2">
                        <p>"Una inversión excelente para la tranquilidad de mi familia. La monitorización en tiempo real es clave."</p>
                        <p class="client-name">- María García</p>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    <section id="contacto" class="contact-section">
        <div class="container">
            <h2>Contacto</h2>
            <div class="row justify-content-center mt-4">
                <div class="col-md-6 contact-info">
                    <p><i class="fas fa-map-marker-alt me-2"></i> Dirección: Rio Tercero, Cordoba</p>
                    <p><i class="fas fa-phone me-2"></i> Teléfono: +54 9 3571-331234</p>
                    <p><i class="fas fa-envelope me-2"></i> Email: <a href="mailto:againsafegas.ascii@gmail.com" class="text-light">againsafegas.ascii@gmail.com</a></p>
                    <p><strong>Sitio Web:</strong> <a href="https://www.gassafe.com" target="_blank" class="text-light">www.AgainSafeGas.com</a></p>
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
                // Ajusta el desplazamiento para tener en cuenta la altura del navbar fijo
                const offset = $('.navbar').outerHeight() + 10; // Altura del navbar + un pequeño margen
                $('html, body').animate({ scrollTop: target.offset().top - offset }, 500);
            }
        });

        // Ocultar overlay de animación al cargar la página
        $(window).on('load', function() {
            $('#explosionOverlay').addClass('fade-out');
            // $('body').css('overflow', 'auto'); // Esto es si tenías el body con overflow: hidden inicialmente
        });
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
