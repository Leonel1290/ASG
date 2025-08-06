<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Seguridad en tu Hogar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-bg: #0A192F;
            --secondary-bg: #1A3E5C;
            --primary-color: #AFB3B7;
            --secondary-color: #fff;
            --accent-color: #36678C;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Poppins', sans-serif;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background-color: rgba(10, 25, 47, 0.8);
            backdrop-filter: blur(10px);
        }

        .navbar-brand, .nav-link {
            color: var(--primary-color);
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--accent-color);
        }

        .btn-custom {
            background-color: var(--accent-color);
            border: none;
            color: var(--secondary-color);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.6rem 1.6rem;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #2A5173;
            transform: translateY(-2px);
        }

        .hero {
            padding-top: 100px;
            padding-bottom: 80px;
            position: relative;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--secondary-color);
            line-height: 1.2;
        }

        .hero-line {
            width: 80px;
            height: 4px;
            background-color: var(--accent-color);
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
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .features i {
            font-size: 3rem;
            color: var(--accent-color);
        }

        .features h3 {
            color: var(--secondary-color);
        }

        .company-info {
            background-color: var(--secondary-bg);
            border-radius: 10px;
            padding: 2rem;
            color: var(--secondary-color);
        }

        footer {
            background-color: var(--primary-bg);
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            margin-top: auto;
        }

        a {
            text-decoration: none;
            color: var(--accent-color);
        }

        main {
            flex-grow: 1;
        }
    </style>
</head>
<body>

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
                        <a class="nav-link" href="#features">Producto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item d-flex gap-2">
                        <a class="btn btn-custom" href="<?= base_url('/comprar') ?>">Comprar ahora</a>
                        <a class="btn btn-outline-light" href="<?= base_url('/loginobtener') ?>">Inicia Sesión</a>
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
                <div class="col-lg-6 order-lg-2 text-center text-lg-end">
                    <img src="URL_IMAGEN_DEL_PRODUCTO_REAL" alt="ASG - Dispositivo de seguridad para el hogar" class="img-fluid hero-img" loading="lazy">
                </div>
                <div class="col-lg-6 order-lg-1 text-center text-lg-start">
                    <h1>Seguridad inteligente para tu hogar</h1>
                    <div class="hero-line mx-auto mx-lg-0"></div>
                    <p class="lead text-white-50">
                        Detecta fugas de gas al instante y protege a tu familia con nuestro sistema de cierre automático de válvulas.
                    </p>
                    <div class="d-flex flex-column flex-md-row gap-3 mt-4 justify-content-center justify-content-lg-start">
                        <a href="<?= base_url('/comprar') ?>" class="btn btn-custom btn-lg">Comprar ahora</a>
                        <a href="#features" class="btn btn-outline-light btn-lg">Saber más</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features py-5 text-center" id="features">
        <div class="container">
            <h2 class="mb-5 text-white">¿Por qué elegir ASG?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <i class="fas fa-shield-alt mb-3"></i>
                    <h3>Protección proactiva</h3>
                    <p>Nuestro sistema cierra automáticamente el paso del gas ante cualquier fuga detectada, previniendo accidentes antes de que ocurran.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-mobile-alt mb-3"></i>
                    <h3>Control total desde tu celular</h3>
                    <p>Monitorea el estado de tu hogar en tiempo real y recibe alertas inmediatas a través de nuestra aplicación fácil de usar.</p>
                </div>
                <div class="col-md-4">
                    <i class="fas fa-bolt mb-3"></i>
                    <h3>Instalación sencilla</h3>
                    <p>Instala el dispositivo sin complicaciones. No requiere herramientas especializadas ni técnicos. ¡Tú mismo puedes hacerlo!</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="testimonials">
        <div class="container">
            <h2 class="text-center text-white mb-5">Lo que dicen nuestros clientes</h2>
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <blockquote class="blockquote bg-light p-4 rounded text-dark">
                        <p class="mb-0 fst-italic">"Me siento mucho más tranquila sabiendo que mi hogar está protegido. La instalación fue muy fácil y la app es muy intuitiva."</p>
                        <footer class="blockquote-footer mt-2">Maria G., <cite title="Source Title">Buenos Aires</cite></footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="company">
        <div class="container">
            <div class="company-info mx-auto text-center">
                <h2 class="mb-3">Contacto</h2>
                <p>Estamos para ayudarte a proteger lo que más te importa. Contáctanos para más información.</p>
                <address>
                    <p><strong>Empresa:</strong> AgainSafeGas</p>
                    <p><strong>Dirección:</strong> Río Tercero</p>
                    <p><strong>Teléfono:</strong> <a href="tel:+543571623889" class="text-light">3571-623889</a></p>
                    <p><strong>Email:</strong> <a href="mailto:againsafegas.ascii@gmail.com" class="text-light">againsafegas.ascii@gmail.com</a></p>
                </address>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 AgainSafeGas Solutions | Todos los derechos reservados.</p>
</footer>
</body>
</html>