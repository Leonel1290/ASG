<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Seguridad en tu Hogar</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #0D1F23;
            --secondary-bg: #132E35;
            --accent-color: #698180; /* Verde-gris azulado */
            --light-text: #AFB3B7;
            --white-text: #fff;
            --dark-blue: #2D4A53;
            --button-hover: #4A6C77; /* Un tono más oscuro del accent-color para el hover */
            --gradient-start: #0A1C20;
            --gradient-end: #0F252D;
        }

        body {
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            font-family: 'Poppins', sans-serif;
            color: var(--light-text);
            margin: 0;
            min-height: 100vh; /* Asegura que el gradiente cubra toda la altura */
            display: flex;
            flex-direction: column;
            overflow-x: hidden; /* Evita scroll horizontal */
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(13, 31, 35, 0.85); /* Un poco más opaco */
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(105, 129, 128, 0.1); /* Sutil borde inferior */
            transition: background-color 0.3s ease;
        }

        .navbar-brand, .nav-link {
            color: var(--light-text);
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--accent-color);
        }

        .navbar-brand img {
            height: 40px; /* Ajusta el tamaño del logo si se añade */
            margin-right: 8px;
        }

        .btn-custom {
            background-color: var(--accent-color);
            border: none;
            color: var(--white-text);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.7rem 1.8rem; /* Ligeramente más grande */
            transition: all 0.3s ease; /* Transición para todos los cambios */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-custom:hover {
            background-color: var(--button-hover); /* Tono de hover */
            transform: translateY(-2px); /* Pequeño levantamiento */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            color: var(--white-text); /* Asegura que el color del texto no cambie */
        }

        .btn-custom:active {
            transform: translateY(0);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 6rem; /* Más padding superior para compensar el navbar fijo */
            position: relative;
            text-align: left; /* Asegura que el texto esté a la izquierda por defecto */
        }

        .hero h1 {
            font-size: 3.5rem; /* Título más grande */
            font-weight: 700;
            position: relative;
            display: inline-block;
            color: var(--white-text);
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        /* --- ESTILOS PARA EL EFECTO DE HUMO VERDE A TRAVÉS DEL TEXTO --- */
        .hero h1::before {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            color: transparent;

            background-image: url('<?= base_url('/imagenes/verde.jpg'); ?>');
            background-size: 100% 250%;
            background-repeat: no-repeat;
            background-position: center bottom;
            -webkit-background-clip: text;
            background-clip: text;
            filter: blur(1.5px);
            opacity: 0;
            animation: greenSmokeTextEffect 5s infinite alternate ease-in-out;
        }

        @keyframes greenSmokeTextEffect {
            0% {
                background-position: center bottom;
                opacity: 0;
            }
            20% {
                opacity: 0.8;
            }
            60% {
                background-position: center top;
                opacity: 0.6;
            }
            100% {
                background-position: center top;
                opacity: 0;
            }
        }
        /* --- FIN ESTILOS PARA EL EFECTO DE HUMO VERDE A TRAVÉS DEL TEXTO --- */

        .hero-line {
            width: 100px; /* Línea más larga */
            height: 5px; /* Más gruesa */
            background-color: var(--accent-color);
            margin: 1.5rem 0 2rem; /* Más espacio */
            border-radius: 2px;
        }

        .hero p.lead {
            font-size: 1.25rem;
            color: var(--light-text);
            margin-bottom: 2rem;
        }

        .hero-img {
            max-width: 90%; /* Ligeramente más pequeña */
            height: auto;
            animation: float 6s ease-in-out infinite;
            display: block; /* Para centrar con margin auto */
            margin: 0 auto;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-18px); } /* Mayor flotación */
        }

        /* Features Section */
        .features {
            padding: 5rem 0; /* Más padding */
            background-color: rgba(19, 46, 53, 0.4); /* Fondo sutil para la sección */
            border-radius: 15px; /* Bordes redondeados para la sección */
            margin: 3rem 0;
        }

        .features .col-md-4 {
            padding: 2rem; /* Más padding dentro de cada columna */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            background-color: rgba(45, 74, 83, 0.2); /* Fondo sutil para las tarjetas de features */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .features .col-md-4:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            background-color: rgba(45, 74, 83, 0.4);
        }

        .features i {
            font-size: 4rem; /* Iconos más grandes */
            color: var(--accent-color);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px rgba(105, 129, 128, 0.5); /* Sutil sombra en el icono */
        }

        .features h3 {
            color: var(--white-text);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .features p {
            color: var(--light-text);
        }

        /* Company Info Section */
        .company-info {
            background-color: var(--dark-blue);
            border-radius: 15px; /* Más redondeado */
            padding: 3rem; /* Más padding */
            color: var(--white-text);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); /* Sombra más pronunciada */
            max-width: 700px; /* Ancho máximo para la tarjeta */
        }

        .company-info h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--white-text);
            margin-bottom: 2rem;
        }

        .company-info p {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }

        .company-info p strong {
            color: var(--accent-color);
        }

        .company-info a {
            color: var(--light-text) !important; /* Asegura que el color sea claro */
            transition: color 0.3s ease;
            text-decoration: underline;
            text-underline-offset: 4px;
            text-decoration-color: rgba(105, 129, 128, 0.5);
        }

        .company-info a:hover {
            color: var(--white-text) !important;
            text-decoration-color: var(--white-text);
        }

        footer {
            background-color: var(--primary-bg);
            text-align: center;
            padding: 1.5rem; /* Más padding en el footer */
            font-size: 0.95rem;
            margin-top: auto;
            color: var(--light-text);
            border-top: 1px solid rgba(105, 129, 128, 0.1);
        }

        a {
            text-decoration: none;
        }

        main {
            flex-grow: 1;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28175, 179, 183, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem rgba(105, 129, 128, .5);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero {
                padding-top: 6rem;
                text-align: center; /* Centrar texto y elementos en móviles */
            }
            .hero h1 {
                font-size: 2.5rem;
                margin-bottom: 1.5rem;
            }
            .hero-line {
                margin: 1rem auto 1.5rem; /* Centrar línea en móviles */
            }
            .hero p.lead {
                font-size: 1rem;
            }
            .hero-img {
                max-width: 80%; /* Ajustar tamaño de imagen */
            }
            .features .col-md-4 {
                margin-bottom: 2rem; /* Espacio entre tarjetas en móviles */
            }
            .company-info {
                padding: 2rem;
            }
            .company-info h2 {
                font-size: 2rem;
            }
            .navbar-nav {
                margin-top: 1rem; /* Espacio superior para el menú desplegado */
            }
            .navbar-nav .nav-item {
                margin-left: 0 !important;
                margin-bottom: 10px;
            }
            .navbar-nav .btn-custom {
                width: 100%; /* Botones de menú anchos */
            }
        }
    </style>
</head>
<body>

<div id="explosionOverlay" class="explosion-animation-overlay">
    <div class="explosion-image-container"></div>
</div>

<header>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-custom" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-custom" href="https://pwa-1s1m.onrender.com/instalar-pwa" target="_blank">Descargar App</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-custom" href="<?= base_url('/loginobtener') ?>">Inicia Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero" id="inicio">
        <div class="container">
            <div class="row align-items-center"> <div class="col-md-6 text-start">
                    <h1 data-text="Protege lo que más importa">Protege lo que más importa</h1>
                    <div class="hero-line"></div>
                    <p class="lead">Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
                </div>
                <div class="col-md-6 text-center mt-4 mt-md-0">
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
            <div class="row g-4">
                <div class="col-md-4">
                    <i class="fas fa-shield-alt mb-3"></i>
                    <h3>Seguridad Total</h3>
                    <p>Sistema de cierre automático de válvulas para una protección eficaz.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-mobile-alt mb-3"></i>
                    <h3>Monitoreo Remoto</h3>
                    <p>Control desde tu celular a través de nuestra app segura.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-bell mb-3"></i>
                    <h3>Alertas en Tiempo Real</h3>
                    <p>Notificaciones inmediatas ante cualquier fuga detectada.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="company">
        <div class="container">
            <div class="company-info mx-auto text-center">
                <h2 class="mb-3">Sobre Nosotros</h2>
                <address>
                    <p><strong>Empresa:</strong> AgainSafeGas</p>
                    <p><strong>Dirección:</strong> Río Tercero</p>
                    <p><strong>Teléfono:</strong> <a href="tel:+543571623889" class="text-light">3571-623889</a></p>
                    <p><strong>Email:</strong> <a href="mailto:againsafegas.ascii@gmail.com" class="text-light">againsafegas.ascii@gmail.com</a></p>
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
                const offset = $('.navbar').outerHeight() + 10;
                $('html, body').animate({ scrollTop: target.offset().top - offset }, 500);
            }
        });

        // Ocultar overlay de animación al cargar la página
        $(window).on('load', function() {
            $('#explosionOverlay').addClass('fade-out');
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
