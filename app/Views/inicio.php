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

        /* Navbar mejorada */
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
            box-shadow: 0 4px 10px rgba(54, 103, 140, 0.3);
        }

        .btn-custom:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(54, 103, 140, 0.4);
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

        /* Hero section mejorada */
        .hero {
            padding: 8rem 0 5rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(54, 103, 140, 0.1) 0%, transparent 70%);
            z-index: -1;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }
        
        .hero h1 .highlight {
            position: relative;
            display: inline-block;
        }
        
        .hero h1 .highlight::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30%;
            background: rgba(54, 103, 140, 0.3);
            z-index: -1;
            border-radius: 3px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 90%;
        }

        /* Animación mejorada para la imagen */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .hero-img {
            animation: float 4s ease-in-out infinite;
            max-width: 90%;
            filter: drop-shadow(0 15px 20px rgba(0, 0, 0, 0.2));
        }

        /* Features section mejorada */
        .features {
            padding: 5rem 0;
            background: rgba(13, 32, 59, 0.5);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title h2 {
            color: var(--text-primary);
            font-weight: 700;
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }

        .feature-card {
            background: rgba(26, 62, 92, 0.3);
            border-radius: 15px;
            padding: 2.5rem 2rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(54, 103, 140, 0.2);
            backdrop-filter: blur(5px);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: rgba(54, 103, 140, 0.4);
        }

        .feature-icon {
            font-size: 3.5rem;
            color: var(--accent);
            margin-bottom: 1.5rem;
            display: inline-block;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-card h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        /* Company info mejorada */
        .company-info {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-medium));
            border-radius: 15px;
            padding: 3rem;
            color: var(--text-primary);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(54, 103, 140, 0.2);
            text-align: center;
        }
        
        .company-info h2 {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            font-weight: 700;
        }
        
        .company-info h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--accent);
            border-radius: 2px;
        }
        
        .company-info address p {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .company-info strong {
            min-width: 100px;
            display: inline-block;
            color: var(--accent);
        }
        
        .company-info a {
            color: var(--text-hover);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .company-info a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        /* Footer mejorado */
        footer {
            background-color: var(--primary-dark);
            text-align: center;
            padding: 2rem 1rem;
            font-size: 0.9rem;
            margin-top: auto;
        }
        
        .social-links {
            margin-bottom: 1rem;
        }
        
        .social-links a {
            color: var(--text-secondary);
            font-size: 1.2rem;
            margin: 0 0.7rem;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .social-links a:hover {
            color: var(--accent);
            transform: translateY(-3px);
        }

        /* Utilidades */
        .text-gradient {
            background: linear-gradient(135deg, var(--accent), var(--text-hover));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Responsive improvements */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .hero p {
                max-width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 7rem 0 4rem;
                text-align: center;
            }
            
            .hero h1 {
                font-size: 2.3rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .feature-card {
                margin-bottom: 1.5rem;
            }
            
            .company-info {
                padding: 2rem;
            }
            
            .company-info address p {
                flex-direction: column;
                text-align: center;
            }
            
            .company-info strong {
                min-width: auto;
                margin-bottom: 0.3rem;
            }
            
            .btn-group-mobile {
                display: flex;
                flex-direction: column;
                gap: 0.8rem;
            }
            
            .btn-group-mobile .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <div class="d-flex flex-column flex-lg-row align-items-center btn-group-mobile">
                            <a href="<?= base_url('/comprar') ?>" class="btn btn-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-shopping-cart me-1"></i>Comprar
                            </a>
                            <a href="<?= base_url('/simulacion') ?>" class="btn btn-outline-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-desktop me-1"></i>Simulación
                            </a>
                            <a href="<?= base_url('/descarga') ?>" class="btn btn-custom mb-2 mb-lg-0 me-lg-2">
                                <i class="fas fa-download me-1"></i>Descargar App
                            </a>
                            <a href="<?= base_url('/loginobtener') ?>" class="btn btn-outline-custom">
                                <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
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
                        <a href="<?= base_url('/comprar') ?>">
                            <i class="btn btn-custom"></i>Comprar Ahora 
                        </a>
                        <a href="#company" class="btn btn-outline-custom">
                            <i class="fas fa-info-circle me-1"></i>Más Información
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

    <section class="features" id="features">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Nuestras Soluciones</h2>
                <p>Tecnología avanzada para la seguridad de tu familia</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h3>Seguridad Total</h3>
                        <p>Sistema de cierre automático de válvulas que se activa instantáneamente ante cualquier fuga detectada.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-mobile-alt feature-icon"></i>
                        <h3>Monitoreo Remoto</h3>
                        <p>Controla y supervisa tu sistema desde cualquier lugar a través de nuestra aplicación móvil segura.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <i class="fas fa-bell feature-icon"></i>
                        <h3>Alertas Inteligentes</h3>
                        <p>Notificaciones inmediatas en tu dispositivo ante cualquier incidente o nivel peligroso de gas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5" id="company">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Contáctanos</h2>
                <p>Estamos aquí para proteger lo que más te importa</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="company-info">
                        <h2>AgainSafeGas</h2>
                        <address>
                            <p><strong>Empresa:</strong> AgainSafeGas Solutions</p>
                            <p><strong>Dirección:</strong> Río Tercero, Córdoba, Argentina</p>
                            <p><strong>Teléfono:</strong> <a href="tel:+543571623889">+54 3571-623889</a></p>
                            <p><strong>Email:</strong> <a href="mailto:againsafegas.ascii@gmail.com">againsafegas.ascii@gmail.com</a></p>
                        </address>
                        
                        <div class="social-links mt-4">
        <a href="https://www.facebook.com/" target="_blank" aria-label="Facebook">
            <i class="fab fa-facebook-f"></i>
            </a>
                 <a href="https://www.instagram.com/" target="_blank" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                    </a>
                        <a href="https://x.com/" target="_blank" aria-label="x">
                            <i class="fab fa-x"></i>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
        <p>&copy; 2025 Again Safe Gas | Todos los derechos reservados.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    // Inicializar AOS (Animate On Scroll)
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
        
        // Navbar scroll effect
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    });
</script>

</body>
</html>
