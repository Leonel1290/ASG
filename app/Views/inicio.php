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
            width: 100%; /* Asegura que ocupe todo el ancho */
            z-index: 1000; /* Asegura que esté por encima del contenido */
        }

        .navbar-brand, .nav-link {
            color: #AFB3B7;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #698180;
        }

        .btn-custom {
            background-color: #698180;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.6rem 1.6rem;
        }

        .btn-custom:hover {
            background-color: #2D4A53;
        }

        .hero {
            padding: 6rem 0;
            position: relative;
            /* --- CORRECCIÓN: Añadir padding-top para compensar la altura del navbar fijo --- */
            /* Ajusta este valor (ej. 70px) para que sea ligeramente mayor que la altura de tu navbar */
            /* Puedes necesitar ajustar el valor si la altura del navbar cambia en diferentes tamaños de pantalla */
            padding-top: 80px; /* Añadimos un padding superior para que el contenido no quede detrás del navbar */
            /* --- FIN CORRECCIÓN --- */
        }

        .hero h1 {
            font-size: 3rem;
            color: #fff;
            font-weight: 700;
        }

        .hero-line {
            width: 80px;
            height: 4px;
            background-color: #698180;
            margin: 1rem 0 1.5rem;
        }

        .hero-img {
            max-width: 100%;
            height: auto;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .features {
             padding-top: 3rem; /* Asegurar padding si esta sección sigue a hero */
             padding-bottom: 3rem;
        }

        .features i {
            font-size: 3rem;
            color: #698180;
        }

        .features h3 {
            color: #fff;
        }

        .company-info {
            background-color: #2D4A53;
            border-radius: 10px;
            padding: 2rem;
            color: #fff;
        }

        footer {
            background-color: #0D1F23;
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            margin-top: auto; /* Empuja el footer hacia abajo si el contenido es corto */
        }

        a {
            text-decoration: none;
        }

        /* Estilos para la animación de explosión/humo */
        .explosion-animation-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #0D1F23; /* Fondo oscuro */
            z-index: 1001; /* Asegura que esté por encima de todo */
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
            transition: opacity 1s ease-out 1.5s; /* Desaparece después de 1.5s */
        }
        /* @keyframes pulse-fade {
            0% { transform: scale(0.8); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        } */ /* Comentado porque no se usa */

        .explosion-animation-overlay.fade-out {
            opacity: 0;
            pointer-events: none; /* Permite interactuar con los elementos debajo una vez que desaparece */
        }

        /* Asegurar que el main content tenga espacio por encima del footer si no hay suficiente contenido */
         main {
             flex-grow: 1; /* Permite que el main crezca para llenar el espacio */
         }

    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url(); ?>">
                <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG">
                ASG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#hero">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Características</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/loginobtener'); ?>">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <section id="hero" class="hero-section">
            <div class="hero-overlay">
                <h1 class="hero-title">AgainSafeGas: Tu Hogar, Más Seguro que Nunca</h1>
                <p class="hero-subtitle">Monitorea y protege a tu familia de fugas de gas con nuestra tecnología avanzada.</p>
                <a href="<?= base_url('/register'); ?>" class="btn btn-primary">Registrarse Ahora</a>
            </div>
        </section>

        <section id="features" class="section-padding bg-light-dark">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="cta-title">Características Clave</h2>
                    <p class="hero-subtitle">Descubre cómo AgainSafeGas protege tu hogar.</p>
                </div>
                <div class="row text-center">
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <i class="fas fa-bell feature-icon"></i>
                            <h5 class="card-title">Alertas en Tiempo Real</h5>
                            <p class="card-text">Recibe notificaciones instantáneas en tu dispositivo móvil ante cualquier detección de gas anómala.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <i class="fas fa-chart-line feature-icon"></i>
                            <h5 class="card-title">Monitoreo Constante</h5>
                            <p class="card-text">Visualiza el nivel de gas en tu hogar las 24 horas del día, los 7 días de la semana, desde cualquier lugar.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <i class="fas fa-mobile-alt feature-icon"></i>
                            <h5 class="card-title">Acceso Remoto y Sencillo</h5>
                            <p class="card-text">Gestiona tus dispositivos y configura alertas fácilmente desde nuestra intuitiva aplicación.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cta" class="cta-section">
            <div class="container">
                <h2 class="cta-title">¿Listo para un Hogar Más Seguro?</h2>
                <p class="hero-subtitle">Únete a la comunidad de AgainSafeGas y protege lo que más importa.</p>
                <a href="<?= base_url('/register'); ?>" class="btn btn-primary">Comenzar Ahora</a>
            </div>
        </section>

        <section id="about" class="section-padding bg-light-dark">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="cta-title">Sobre Nosotros</h2>
                    <p class="hero-subtitle">Nuestra misión es tu tranquilidad.</p>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <p>En AgainSafeGas, estamos comprometidos con la seguridad de tu hogar. Desarrollamos soluciones de monitoreo de gas innovadoras y accesibles, utilizando tecnología de punta para brindarte tranquilidad.</p>
                        <p>Nuestra visión es un mundo donde las familias estén protegidas de los peligros invisibles del gas, a través de sistemas de detección confiables y alertas rápidas.</p>
                    </div>
                    <div class="col-md-6 mb-4">
                        <p>Creemos en la importancia de la prevención y la información. Por eso, nuestra plataforma no solo te alerta, sino que también te permite visualizar datos y tomar el control de la seguridad de tu entorno.</p>
                        <p>Forma parte de nuestra comunidad y experimenta la diferencia que hace la seguridad inteligente en tu vida diaria.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact-section">
            <div class="container">
                <h2>Contáctanos</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <p>Si tienes alguna pregunta, sugerencia o necesitas soporte, no dudes en contactarnos. Estamos aquí para ayudarte.</p>
                        <p>Puedes enviarnos un mensaje directamente, o contactarnos a través de nuestras redes sociales (si las tienes).</p>
                    </div>
                    <div class="col-md-6 mb-4">
                        <address>
                            <p><i class="fas fa-map-marker-alt me-2"></i> Dirección: [Tu Dirección Física si aplica]</p>
                            <p><i class="fas fa-phone me-2"></i> Teléfono: [Tu Número de Teléfono si aplica]</p>
                            <p><i class="fas fa-envelope me-2"></i> Email: <a href="mailto:againsafegas.ascii@gmail.com" class="text-light">againsafegas.ascii@gmail.com</a></p>
                            <p><i class="fas fa-globe me-2"></i> Sitio Web: <a href="https://www.gassafe.com" target="_blank" class="text-light">www.AgainSafeGas.com</a></p>
                        </address>
                    </div>
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
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                // Asegúrate de que base_url('service-worker.js') apunte a la ruta correcta en Render
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
