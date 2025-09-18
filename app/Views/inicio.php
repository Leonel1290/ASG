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
            background: linear-gradient(135deg, #0A192F, #0D203B);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            margin: 0;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(10, 25, 47, 0.8);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand, .nav-link {
            color: #AFB3B7;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #8CA9B9;
        }

        .btn-custom {
            background-color: #36678C;
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.6rem 1.6rem;
        }

        .btn-custom:hover {
            background-color: #2A5173;
        }

        .hero {
            padding: 6rem 0;
            position: relative;
            padding-top: 80px;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
            color: #fff;
            line-height: 1.2;
        }

        /* Animación para la imagen de fuga de gas */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .hero-img {
            animation: float 3s ease-in-out infinite;
        }

        .features {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .features i {
            font-size: 3rem;
            color: #36678C;
        }

        .features h3 {
            color: #fff;
        }

        .company-info {
            background-color: #1A3E5C;
            border-radius: 10px;
            padding: 2rem;
            color: #fff;
        }

        footer {
            background-color: #0A192F;
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            margin-top: auto;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28175, 179, 183, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem rgba(54, 103, 140, .5);
        }

        /* Estilo para ocultar el botón de instalación por defecto */
        #installPWAButton {
            display: none;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <div class="d-flex flex-column flex-lg-row align-items-center hstack gap-3">
                            <a class="btn btn-custom" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
                            <a href="<?= base_url('/simulacion') ?>" class="btn btn-custom">Simulación</a>
                            <button id="installPWAButton" class="btn btn-custom">Descargar App</button>
                            <a href="<?= base_url('/descarga') ?>" class="btn btn-custom">Descargar App</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero" id="inicio">
        <div class="container">
            <div class="row align-items-centers">
                <div class="col-md-6 text-start">
                    <h1 data-text="Protege lo que más importa">Protege lo que más importa</h1>
                    <div class="hero-line"></div>
                    <p class="lead">Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
                    <a href="<?= base_url('/loginobtener') ?>" class="btn btn-custom mt-3">Inicia Sesión</a>
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
                </address>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>&copy; 2025 AgainSafeGas Solutions | Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.botpress.cloud/webchat/v3.2/inject.js"></script>
<script src="https://files.bpcontent.cloud/2025/08/21/16/20250821163950-FM8TYRF1.js" defer></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let deferredPrompt;
    const installButton = document.getElementById('installPWAButton');
    const fallbackButton = document.getElementById('fallbackDownloadButton');

    // Escuchar el evento 'beforeinstallprompt'
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        installButton.style.display = 'block';
        fallbackButton.style.display = 'none';
    });

    // Manejar el clic en el botón de instalación
    installButton.addEventListener('click', async () => {
        if (deferredPrompt) {
            installButton.style.display = 'none';
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`El usuario eligió: ${outcome}`);
            deferredPrompt = null;
        }
    });

    // Si el evento 'beforeinstallprompt' no se dispara, mostrar el botón de respaldo
    window.addEventListener('load', () => {
        if (!deferredPrompt) {
            fallbackButton.classList.remove('d-none');
        }
    });
</script>

</body>
</html>