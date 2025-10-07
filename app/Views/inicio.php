<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASG - Seguridad en tu Hogar</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --dark-blue: #0A1929;
            --navy-blue: #132F4C;
            --medium-blue: #1E4976;
            --light-blue: #2A5C8F;
            --accent-blue: #3B72AF;
            --highlight: #4F93D9;
            --text-light: #E6F1FF;
            --text-muted: #A8C6FF;
            --success: #4CAF50;
            --danger: #F44336;
            --warning: #FF9800;
            --card-bg: rgba(19, 47, 76, 0.7);
            --card-border: rgba(74, 144, 226, 0.2);
        }

        body {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--navy-blue) 100%);
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .navbar {
            background: rgba(10, 25, 41, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.8rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            border-bottom: 1px solid var(--card-border);
        }

        .navbar-brand {
            color: var(--text-light);
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }

        .navbar-brand span {
            color: var(--highlight);
        }

        .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--highlight);
            background: rgba(59, 114, 175, 0.1);
        }

        .btn-custom {
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--highlight) 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.8rem 2.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 147, 217, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(79, 147, 217, 0.4);
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-blue) 100%);
        }

        .btn-alert {
            background: linear-gradient(135deg, var(--danger) 0%, #E53935 100%);
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(244, 67, 54, 0.3);
        }

        .btn-alert:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(244, 67, 54, 0.4);
            background: linear-gradient(135deg, #E53935 0%, #D32F2F 100%);
        }

        .hero {
            padding: 5rem 0;
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero h1 {
            font-size: 3rem;
            color: var(--text-light);
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero p.lead {
            color: var(--text-muted);
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-line {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--highlight));
            margin: 1.5rem auto 2rem;
            border-radius: 2px;
        }

        .hero-img {
            max-width: 90%;
            height: auto;
            margin-top: 2rem;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .features {
            padding: 5rem 0;
            position: relative;
        }

        .features h2 {
            color: var(--text-light);
            font-weight: 700;
            margin-bottom: 3rem;
            position: relative;
            display: inline-block;
        }

        .features h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-blue), var(--highlight));
            border-radius: 2px;
        }

        .feature-card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2.5rem 2rem;
            margin-bottom: 1.5rem;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border: 1px solid var(--card-border);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-color: rgba(74, 144, 226, 0.4);
        }

        .features i {
            font-size: 3.5rem;
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--highlight) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover i {
            transform: scale(1.1);
        }

        .features h3 {
            color: var(--text-light);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .features p {
            color: var(--text-muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Estilos para el velocímetro */
        .gauge-container {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        .gauge {
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: var(--card-bg);
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--card-border);
        }

        .gauge-inner {
            position: absolute;
            top: 15px;
            left: 15px;
            width: calc(100% - 30px);
            height: calc(100% - 30px);
            border-radius: 50%;
            background: var(--navy-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .gauge-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-light);
        }

        .gauge-label {
            font-size: 1rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .gauge-needle {
            position: absolute;
            top: 15px;
            left: 50%;
            width: 2px;
            height: 95px;
            background: var(--danger);
            transform-origin: bottom center;
            transition: transform 0.5s ease-in-out;
            z-index: 10;
        }

        .gauge-markings {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .marking {
            position: absolute;
            top: 5px;
            left: calc(50% - 1px);
            width: 2px;
            height: 10px;
            background: var(--text-muted);
            transform-origin: bottom center;
        }

        .marking.major {
            height: 15px;
            width: 3px;
            background: var(--text-light);
        }

        .gauge-danger-zone {
            position: absolute;
            top: 15px;
            left: 15px;
            width: calc(100% - 30px);
            height: calc(100% - 30px);
            border-radius: 50%;
            clip-path: polygon(50% 50%, 50% 0, 0 0);
            background: rgba(244, 67, 54, 0.2);
        }

        .gauge-warning-zone {
            position: absolute;
            top: 15px;
            left: 15px;
            width: calc(100% - 30px);
            height: calc(100% - 30px);
            border-radius: 50%;
            clip-path: polygon(50% 50%, 50% 0, 0 0);
            background: rgba(255, 152, 0, 0.2);
            transform: rotate(90deg);
        }

        .gauge-safe-zone {
            position: absolute;
            top: 15px;
            left: 15px;
            width: calc(100% - 30px);
            height: calc(100% - 30px);
            border-radius: 50%;
            clip-path: polygon(50% 50%, 50% 0, 0 0);
            background: rgba(76, 175, 80, 0.2);
            transform: rotate(180deg);
        }

        .gauge-controls {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
            gap: 1rem;
        }

        .gauge-btn {
            background: var(--accent-blue);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gauge-btn:hover {
            background: var(--highlight);
            transform: scale(1.1);
        }

        .notification-permission {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 2.5rem;
            margin: 3rem auto;
            text-align: center;
            border: 1px solid var(--card-border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .notification-permission h3 {
            color: var(--text-light);
            margin-bottom: 1.2rem;
            font-weight: 600;
        }

        .notification-permission p {
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
        }

        .test-notification {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--card-border);
        }

        .test-notification p {
            margin-bottom: 1.2rem;
        }

        .company-info {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 3.5rem;
            color: var(--text-light);
            margin: 3rem auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            border: 1px solid var(--card-border);
        }

        .company-info h2 {
            color: var(--text-light);
            font-weight: 700;
            margin-bottom: 2.5rem;
            position: relative;
            display: inline-block;
        }

        .company-info h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-blue), var(--highlight));
            border-radius: 2px;
        }

        .company-info p {
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
            color: var(--text-muted);
        }

        .company-info strong {
            color: var(--highlight);
            font-weight: 600;
        }

        .company-info a {
            color: var(--accent-blue);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .company-info a:hover {
            color: var(--highlight);
            text-decoration: underline;
        }

        footer {
            background: rgba(10, 25, 41, 0.95);
            text-align: center;
            padding: 2.5rem;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: auto;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
            border-top: 1px solid var(--card-border);
        }

        @media (min-width: 768px) {
            .hero h1 {
                font-size: 3.5rem;
            }
            .hero-img {
                max-width: 100%;
            }
            .features, .company-info, .notification-permission {
                max-width: 85%;
                margin: 5rem auto;
            }
            .feature-card {
                padding: 3rem 2rem;
            }
        }

        @media (max-width: 767px) {
            .hero {
                padding: 3rem 0;
            }
            .hero h1 {
                font-size: 2.2rem;
            }
            .hero p.lead {
                font-size: 1rem;
            }
            .features, .company-info, .notification-permission {
                max-width: 95%;
                margin: 3rem auto;
            }
            .company-info {
                padding: 2rem;
            }
            .gauge {
                width: 180px;
                height: 180px;
            }
            .gauge-needle {
                height: 75px;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#"><span>ASG</span> Security</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#company">Contacto</a>
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
                        <a href="<?= base_url('loginobtener') ?>" class="btn btn-custom mt-4">Inicia Sesión</a>
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
</body>
</html>