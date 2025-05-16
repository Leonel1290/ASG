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
            overflow: hidden;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(13, 31, 35, 0.8);
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
        @keyframes pulse-fade {
            0% { transform: scale(0.8); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .explosion-animation-overlay.fade-out {
            opacity: 0;
            pointer-events: none;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                    <h1>Protege lo que más importa</h1>
                    <div class="hero-line"></div>
                    <p class="lead">Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
                    <a href="<?= base_url('/loginobtener') ?>" class="btn btn-custom mt-3">Inicia Sesión</a>
                </div>
                <div class="col-md-6 text-center mt-4 mt-md-0">
                    <img src="https://cdn3d.iconscout.com/3d/premium/thumb/fuga-de-gas-8440307-6706766.png?f=webp"
                         alt="Ilustración de gas seguro"
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
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if(target.length) {
                $('html, body').animate({ scrollTop: target.offset().top - 70 }, 500);
            }
        });

        $(window).on('load', function() {
            $('#explosionOverlay').addClass('fade-out');
            $('body').css('overflow', 'auto');
        });
    });
</script>
</body>
</html>
