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
            /* Colores Base */
            --bg-color: #0B0C10; /* Casi negro, fondo principal */
            --panel-color: #1F2833; /* Gris oscuro azulado, para elementos como navbar, cards */
            --text-color: #C5C6C7; /* Gris claro, para texto general */
            --white-text: #fff; /* Blanco puro para texto de alto contraste */

            /* Colores Neón/Acento */
            --accent-color: #66FCF1; /* Turquesa neón vibrante */
            --highlight-color: #45A29E; /* Verde azulado más suave, para hover de acento */

            /* Sombras Neón */
            --shadow-color-light: rgba(102, 252, 241, 0.2); /* Sombra turquesa neón suave */
            --shadow-color-medium: rgba(102, 252, 241, 0.4); /* Sombra turquesa neón media */
            --shadow-color-strong: rgba(102, 252, 241, 0.6); /* Sombra turquesa neón fuerte */

            /* Transiciones */
            --transition-speed: 0.3s;
            --transition-ease: ease-in-out;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            background-image: radial-gradient(circle at top left, rgba(69, 162, 158, 0.1) 0%, transparent 30%),
                              radial-gradient(circle at bottom right, rgba(102, 252, 241, 0.08) 0%, transparent 40%);
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(8px); /* Ligeramente menos blur */
            background-color: rgba(31, 40, 51, 0.95); /* Más opaco para un fondo sólido */
            border-bottom: 1px solid var(--accent-color);
            box-shadow: 0 4px 15px var(--shadow-color-light); /* Sombra más pronunciada y neón */
            transition: background-color var(--transition-speed) var(--transition-ease), box-shadow var(--transition-speed) var(--transition-ease);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand, .nav-link {
            color: var(--text-color);
            font-weight: 600;
            transition: color var(--transition-speed) var(--transition-ease);
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--accent-color);
            text-shadow: 0 0 8px var(--shadow-color-medium); /* Sombra al pasar el ratón */
        }

        /* Botones Globales */
        .btn-custom {
            background-color: var(--accent-color);
            border: 2px solid var(--accent-color); /* Borde que coincide con el color */
            color: var(--bg-color); /* Texto oscuro sobre el color de acento */
            font-weight: 600;
            border-radius: 5px; /* Bordes ligeramente menos redondeados */
            padding: 0.75rem 1.8rem;
            transition: all var(--transition-speed) var(--transition-ease);
            box-shadow: 0 4px 12px var(--shadow-color-light); /* Sombra suave por defecto */
            position: relative;
            overflow: hidden; /* Para efectos internos del botón si se añaden */
            z-index: 1;
        }

        .btn-custom:hover {
            background-color: var(--highlight-color); /* Tono de hover */
            border-color: var(--highlight-color);
            transform: translateY(-3px); /* Mayor levantamiento */
            box-shadow: 0 6px 20px var(--shadow-color-medium); /* Sombra más fuerte al hover */
            color: var(--bg-color); /* Asegura que el color del texto no cambie */
        }

        .btn-custom:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px var(--shadow-color-light);
        }

        /* Hero Section */
        .hero {
            padding: 8rem 0 6rem;
            position: relative;
            text-align: left;
        }

        .hero h1 {
            font-size: 3.8rem; /* Título aún más grande */
            font-weight: 700;
            position: relative;
            display: inline-block;
            color: var(--white-text); /* Color base del texto (letras blancas fijas) */
            line-height: 1.2;
            margin-bottom: 1rem;
            /* Animación de brillo sutil para el texto base */
            animation: textPulse 3s infinite alternate ease-in-out;
        }

        @keyframes textPulse {
            0% { text-shadow: 0 0 5px rgba(255,255,255,0.1); }
            100% { text-shadow: 0 0 15px rgba(255,255,255,0.3); }
        }

        /* --- ESTILOS PARA EL EFECTO DE HUMO VERDE A TRAVÉS DEL TEXTO --- */
        .hero h1::before {
            content: attr(data-text); /* Toma el texto del atributo data-text */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            color: transparent; /* Hace el texto del pseudo-elemento transparente */

            background-image: url('<?= base_url('/imagenes/verde.jpg'); ?>'); /* ¡Tu nueva imagen de humo verde! */
            background-size: 100% 250%; /* Ajusta el tamaño de la imagen. El 250% permite que el humo suba desde bien abajo. */
            background-repeat: no-repeat;
            background-position: center bottom; /* Inicia la imagen desde abajo */
            -webkit-background-clip: text; /* Recorta el fondo a la forma del texto */
            background-clip: text;
            filter: blur(1.5px); /* Un desenfoque para hacer el humo más etéreo. Ajusta a tu gusto. */
            opacity: 0; /* Empieza invisible */
            animation: greenSmokeTextEffect 5s infinite alternate ease-in-out; /* Animación de subida y desvanecimiento más suave */
        }

        @keyframes greenSmokeTextEffect {
            0% {
                background-position: center bottom; /* El humo empieza desde abajo del texto */
                opacity: 0; /* Empieza invisible */
                filter: blur(1.5px);
            }
            20% {
                opacity: 0.8; /* Se vuelve visible rápidamente, pero no completamente opaco para un efecto de humo */
                filter: blur(1.5px);
            }
            60% {
                background-position: center top; /* El humo sube y se va por arriba del texto */
                opacity: 0.6; /* Permanece visible pero empieza a desvanecerse */
                filter: blur(2.5px); /* Se desenfoca un poco más al subir */
            }
            100% {
                background-position: center top; /* Asegura que termina en la parte superior */
                opacity: 0; /* Desaparece completamente */
                filter: blur(3px); /* Se desenfoca aún más al desaparecer */
            }
        }
        /* --- FIN ESTILOS PARA EL EFECTO DE HUMO VERDE A TRAVÉS DEL TEXTO --- */

        .hero-line {
            width: 120px; /* Línea más larga */
            height: 4px; /* Más gruesa */
            background-color: var(--accent-color);
            margin: 1.5rem 0 2rem;
            border-radius: 2px;
            box-shadow: 0 0 10px var(--shadow-color-medium); /* Sombra neón en la línea */
        }

        .hero p.lead {
            font-size: 1.35rem; /* Más grande */
            color: var(--text-color);
            margin-bottom: 2.5rem;
        }

        .hero-img {
            max-width: 95%; /* Ligeramente más grande */
            height: auto;
            animation: float 6s ease-in-out infinite;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 0 15px var(--shadow-color-medium)); /* Sombra neón en la imagen */
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); } /* Mayor flotación */
        }

        /* Features Section */
        .features {
            padding: 5rem 0;
            background-color: var(--panel-color); /* Fondo sólido para la sección */
            border-radius: 15px;
            margin: 4rem 0;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5); /* Sombra más fuerte para la sección */
        }

        .features .col-md-4 {
            padding: 2.5rem; /* Más padding */
            transition: transform var(--transition-speed) var(--transition-ease), box-shadow var(--transition-speed) var(--transition-ease), background-color var(--transition-speed) var(--transition-ease);
            border-radius: 10px;
            background-color: rgba(45, 74, 83, 0.3); /* Fondo sutil para las tarjetas de features */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(102, 252, 241, 0.1); /* Borde sutil */
        }

        .features .col-md-4:hover {
            transform: translateY(-8px); /* Mayor levantamiento */
            box-shadow: 0 12px 30px var(--shadow-color-medium); /* Sombra más fuerte al hover */
            background-color: rgba(45, 74, 83, 0.6); /* Más opaco al hover */
        }

        .features i {
            font-size: 4.5rem; /* Iconos más grandes */
            color: var(--accent-color);
            margin-bottom: 1.8rem;
            text-shadow: 0 0 15px var(--shadow-color-medium); /* Sombra neón en el icono */
            animation: iconPulse 2s infinite alternate ease-in-out; /* Animación de brillo para iconos */
        }

        @keyframes iconPulse {
            0% { text-shadow: 0 0 5px var(--shadow-color-light); }
            100% { text-shadow: 0 0 20px var(--shadow-color-strong); }
        }

        .features h3 {
            color: var(--white-text);
            font-weight: 600;
            margin-bottom: 1.2rem;
            text-shadow: 0 0 5px rgba(255,255,255,0.1);
        }

        .features p {
            color: var(--text-color);
        }

        /* Company Info Section */
        .company-info {
            background-color: var(--panel-color);
            border-radius: 15px;
            padding: 3.5rem; /* Más padding */
            color: var(--white-text);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6); /* Sombra más pronunciada y oscura */
            max-width: 800px;
            margin-bottom: 4rem; /* Espacio antes del footer */
            border: 1px solid rgba(102, 252, 241, 0.2); /* Borde sutil */
        }

        .company-info h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 2.2rem;
            text-shadow: 0 0 10px var(--shadow-color-medium);
        }

        .company-info p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .company-info p strong {
            color: var(--accent-color);
        }

        .company-info address a {
            color: var(--text-color) !important;
            transition: color var(--transition-speed) var(--transition-ease), text-shadow var(--transition-speed) var(--transition-ease);
            text-decoration: underline;
            text-underline-offset: 6px;
            text-decoration-color: rgba(102, 252, 241, 0.3);
        }

        .company-info address a:hover {
            color: var(--accent-color) !important;
            text-shadow: 0 0 8px var(--shadow-color-medium);
            text-decoration-color: var(--accent-color);
        }

        /* Footer */
        footer {
            background-color: var(--bg-color);
            border-top: 1px solid var(--accent-color);
            padding: 2rem;
            font-size: 1rem;
            margin-top: auto;
            color: var(--text-color);
            box-shadow: 0 -4px 15px var(--shadow-color-light);
        }

        a {
            text-decoration: none;
        }

        main {
            flex-grow: 1;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28197, 198, 199, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem var(--shadow-color-medium);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 3rem;
            }
            .hero p.lead {
                font-size: 1.15rem;
            }
            .features .col-md-4 {
                margin-bottom: 2rem;
                padding: 2rem;
            }
            .features i {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero {
                padding-top: 6rem;
                text-align: center;
            }
            .hero h1 {
                font-size: 2.5rem;
                margin-bottom: 1.5rem;
            }
            .hero-line {
                margin: 1rem auto 1.5rem;
            }
            .hero p.lead {
                font-size: 1rem;
            }
            .hero-img {
                max-width: 80%;
            }
            .company-info {
                padding: 2rem;
            }
            .company-info h2 {
                font-size: 2rem;
            }
            .navbar-nav {
                margin-top: 1rem;
            }
            .navbar-nav .nav-item {
                margin-left: 0 !important;
                margin-bottom: 10px;
            }
            .navbar-nav .btn-custom {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero p.lead {
                font-size: 0.9rem;
            }
            .features i {
                font-size: 3rem;
            }
            .company-info h2 {
                font-size: 1.8rem;
            }
            .company-info p {
                font-size: 0.95rem;
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
            <div class="row align-items-center">
                <div class="col-md-6 text-start">
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
        // Asegúrate de que el overlay exista en tu HTML y tenga las clases CSS para funcionar.
        // No proporcionaste las clases `explosion-animation-overlay` y `explosion-image-container`
        // ni la clase `fade-out` en el CSS, así que esto solo funcionará si las defines.
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
