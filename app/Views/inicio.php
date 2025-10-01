<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASG - Seguridad Inteligente para tu Hogar</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0A192F;
            --primary-medium: #0D203B;
            --primary-light: #1A3E5C;
            --accent: #36678C;
            --accent-hover: #2A5173;
            --text-primary: #FFFFFF;
            --text-secondary: #AFB3B7;
            --text-hover: #8CA9B9;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-medium));
            font-family: 'Poppins', sans-serif;
            color: var(--text-secondary);
            margin: 0;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .navbar {
            backdrop-filter: blur(10px);
            background-color: rgba(10, 25, 47, 0.95);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.8rem 0;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            background-color: rgba(10, 25, 47, 0.98);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        .navbar-brand span {
            color: var(--accent);
        }

        .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link:focus {
            color: var(--text-hover);
            background: rgba(54, 103, 140, 0.1);
        }

        .btn-custom {
            background-color: var(--accent);
            border: none;
            color: var(--text-primary);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.7rem 1.8rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(54, 103, 140, 0.3);
        }

        .btn-custom:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 16px rgba(54, 103, 140, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.6rem 1.6rem;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background-color: var(--accent);
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        /* Hero */
        .hero {
            padding: 8rem 0 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero-img {
            animation: float 4s ease-in-out infinite;
            max-width: 90%;
            filter: drop-shadow(0 15px 20px rgba(0, 0, 0, 0.2));
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2 text-gradient"></i>ASG<span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <div class="d-flex flex-column flex-lg-row align-items-center btn-group-mobile">
                            <!-- Botón Comprar primero -->
                            <a href="<?= base_url('/comprar') ?>" class="btn btn-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-shopping-cart me-1"></i> Comprar Ahora
                            </a>
                            <a href="<?= base_url('/simulacion') ?>" class="btn btn-outline-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-desktop me-1"></i> Simulación
                            </a>
                            <a href="<?= base_url('/descarga') ?>" class="btn btn-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-download me-1"></i> Descargar App
                            </a>
                            <!-- Botón Login después -->
                            <a href="<?= base_url('/loginobtener') ?>" class="btn btn-outline-custom">
                                <i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero" id="inicio" data-aos="fade-in">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 data-aos="fade-up" data-aos-delay="200">
                        Protege lo que <span class="highlight">más importa</span>
                    </h1>
                    <p data-aos="fade-up" data-aos-delay="300">
                        Tu hogar seguro con ASG. Sistema de detección precisa de fugas de gas con monitoreo en tiempo real y control remoto.
                    </p>
                    <div class="d-flex gap-3 flex-wrap" data-aos="fade-up" data-aos-delay="400">
                        <a href="<?= base_url('/comprar') ?>" class="btn btn-custom">
                            <i class="fas fa-shopping-cart me-1"></i> Comprar Ahora
                        </a>
                        <a href="#company" class="btn btn-outline-custom">
                            <i class="fas fa-info-circle me-1"></i> Más Información
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center" data-aos="fade-left" data-aos-delay="500">
                    <img src="https://cdn3d.iconscout.com/3d/premium/thumb/fuga-de-gas-8440307-6706766.png?f=webp"
                         alt="Sistema de detección de fugas de gas ASG"
                         class="hero-img img-fluid"
                         loading="eager">
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="text-center py-3">
    <p>&copy; 2025 Again Safe Gas | Todos los derechos reservados.</p>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({ duration: 800, easing: 'ease-in-out', once: true });
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', function() {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    });
</script>

<!-- Chatbot -->
<script src="https://cdn.botpress.cloud/webchat/v3.3/inject.js" defer></script>
<script src="https://files.bpcontent.cloud/2025/08/21/16/20250821163950-FM8TYRF1.js" defer></script>

</body>
</html>
