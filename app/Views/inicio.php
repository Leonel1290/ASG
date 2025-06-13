
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
        :root {
            /* Colores Base */
            --dark-background: #0B1A1E;
            --dark-secondary: #10262B;
            --dark-card-background: #1A2F34; /* Un poco más claro para las tarjetas */
            --primary-highlight: #00A3A3; /* Un teal vibrante */
            --primary-highlight-darker: #008D8D; /* Un tono más oscuro para hover */
            --text-light: #C9D6DF; /* Gris claro para el texto principal */
            --text-lighter: #F0F4F8; /* Blanco casi puro para títulos */
            --navbar-bg-opacity: rgba(11, 26, 30, 0.9); /* Fondo del navbar ligeramente transparente */
        }

        body {
            background: linear-gradient(135deg, var(--dark-background), var(--dark-secondary));
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden; /* Evita el scroll horizontal */
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: var(--navbar-bg-opacity);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0; /* Padding vertical para un navbar más robusto */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3); /* Sombra más pronunciada para profundidad */
        }

        .navbar-brand, .nav-link {
            color: var(--text-lighter); /* Blanco para el texto del navbar */
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--primary-highlight);
        }

        .btn-custom {
            background-color: var(--primary-highlight);
            border: none;
            color: var(--text-lighter);
            font-weight: 600;
            border-radius: 30px; /* Más redondeado */
            padding: 0.75rem 2rem; /* Más padding para botones prominentes */
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(0, 163, 163, 0.2); /* Sombra sutil del color del botón */
        }

        .btn-custom:hover {
            background-color: var(--primary-highlight-darker);
            transform: translateY(-2px); /* Efecto sutil al pasar el mouse */
            box-shadow: 0 6px 20px rgba(0, 163, 163, 0.3);
        }

        .hero {
            padding: 4rem 0; /* Ajuste de padding */
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-size: 2.8rem; /* Tamaño de fuente para el título */
            color: var(--text-lighter);
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3); /* Pequeña sombra para el texto */
        }

        .hero-line {
            width: 70px; /* Más compacto */
            height: 4px;
            background-color: var(--primary-highlight);
            margin: 1.25rem auto 1.75rem;
            border-radius: 2px;
        }

        .hero-img {
            max-width: 90%; /* Ajuste para móviles */
            height: auto;
            margin-top: 2rem;
            animation: float 6s ease-in-out infinite; /* Mantener la animación si te gusta el efecto */
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .features {
            padding: 4rem 0;
            background-color: var(--dark-card-background); /* Fondo para la sección */
            border-radius: 20px; /* Bordes más redondeados */
            margin: 3rem auto; /* Espacio entre secciones */
            max-width: 95%; /* Ocupa más ancho */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); /* Sombra más profunda */
            border: 1px solid rgba(255, 255, 255, 0.05); /* Borde sutil */
        }

        .features h2 {
            color: var(--text-lighter);
            font-weight: 700;
            margin-bottom: 3rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .feature-card {
            background-color: var(--dark-background); /* Fondo aún más oscuro para las tarjetas internas */
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.08); /* Borde sutil */
        }

        .feature-card:hover {
            transform: translateY(-8px); /* Mayor elevación */
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5); /* Sombra más pronunciada */
        }

        .features i {
            font-size: 3rem; /* Iconos más grandes */
            color: var(--primary-highlight);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px rgba(0, 163, 163, 0.5); /* Sombra luminosa para iconos */
        }

        .features h3 {
            color: var(--text-lighter);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .features p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .company-info {
            background-color: var(--dark-card-background);
            border-radius: 20px;
            padding: 3rem;
            color: var(--text-light);
            margin: 3rem auto;
            max-width: 95%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .company-info h2 {
            color: var(--text-lighter);
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .company-info p {
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
        }

        .company-info strong {
            color: var(--primary-highlight); /* Resaltar las etiquetas */
        }

        .company-info a {
            color: var(--primary-highlight);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .company-info a:hover {
            color: var(--text-lighter);
        }

        footer {
            background-color: var(--dark-background);
            text-align: center;
            padding: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* --- Estilos para las líneas del menú  */
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28201, 214, 223, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem var(--primary-highlight); /* Resaltado al hacer foco */
            border-color: var(--primary-highlight);
        }

        /* Responsive adjustments */
        @media (min-width: 768px) {
            .hero h1 {
                font-size: 3.5rem;
            }
            .hero-img {
                max-width: 100%;
            }
            .features, .company-info {
                max-width: 80%; /* Menos ancho en pantallas grandes para centrar mejor */
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
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-custom w-100" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
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
