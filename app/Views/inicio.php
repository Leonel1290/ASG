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
            0% { background-position: center bottom; opacity: 0; }
            20% { opacity: 0.8; }
            60% { background-position: center top; opacity: 0.6; }
            100% { background-position: center top; opacity: 0; }
        }

        .hero-line {
            width: 80px;
            height: 4px;
            background-color: #36678C;
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

        /* --- Estilos del Modal --- */
        .modal-body {
            position: relative;
        }
        .modal-content-simulacion {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-button:hover, .close-button:focus {
            color: black;
            text-decoration: none;
        }

        /* --- Estilos de la Animación --- */
        .image-container {
            position: relative;
            width: 500px; /* Ajusta según el tamaño de tus imágenes */
            height: 500px; /* Ajusta según el tamaño de tus imágenes */
            margin: 20px auto;
        }

        .image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .image.active {
            opacity: 1;
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
                        <a class="nav-link" href="#company">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <div class="d-flex flex-column flex-lg-row align-items-center hstack gap-3">
                            <a class="btn btn-custom" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
                            <a class="btn btn-custom" href="#" data-bs-toggle="modal" data-bs-target="#appModal" data-url="https://pwa-1s1m.onrender.com/instalar-pwa">Descargar App</a>
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
                    <button class="btn btn-outline-light mt-3 ms-2" onclick="probarAlerta()">🔊 Probar Alarma</button>
                    <button class="btn btn-outline-light mt-3 ms-2" data-bs-toggle="modal" data-bs-target="#simulacionModal">⚡ Simular Actuación</button>
                    <audio id="alarmaAudio" src="<?= base_url('/audio/alarma.mp3') ?>" preload="auto"></audio>
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

<div class="modal fade" id="appModal" tabindex="-1" aria-labelledby="appModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="appModalLabel">Descargar App PWA</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="appIframe" src="" style="width: 100%; height: 80vh; border: none;"></iframe>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="simulacionModal" tabindex="-1" aria-labelledby="simulacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simulacionModalLabel">Simulación de Fuga de Gas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="image-container">
                    <img id="frame-1" class="image active" src="<?= base_url('/imagenes/frame_1.jpg'); ?>" alt="Garrafa sin fuga">
                    <img id="frame-2" class="image" src="<?= base_url('/imagenes/frame_2.jpg'); ?>" alt="Fuga de gas">
                    <img id="frame-3" class="image" src="<?= base_url('/imagenes/frame_3.jpg'); ?>" alt="Detector activo">
                </div>
                <button id="animationButton" class="btn btn-custom mt-3">Mantén Pulsado para Simular Fuga</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Smooth scroll
    $(document).ready(function(){
        $('a[href^="#"]').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if(target.length) {
                const offset = $('.navbar').outerHeight() + 10;
                $('html, body').animate({ scrollTop: target.offset().top - offset }, 500);
            }
        });
    });

    // Probar sonido de alerta
    function probarAlerta() {
        const audio = document.getElementById('alarmaAudio');
        audio.play();
    }

    // Contador animado de hogares protegidos
    let contador = 0;
    const objetivo = 3274;
    const contadorElemento = document.getElementById("contador");

    function actualizarContador() {
        if (contador < objetivo) {
            contador += Math.ceil((objetivo - contador) / 15);
            contadorElemento.textContent = contador;
            setTimeout(actualizarContador, 30);
        } else {
            contadorElemento.textContent = objetivo;
        }
    }

    document.addEventListener("DOMContentLoaded", actualizarContador);
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

    document.addEventListener('DOMContentLoaded', function() {
        const appModal = document.getElementById('appModal');
        const appIframe = document.getElementById('appIframe');

        if (appModal && appIframe) {
            appModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const url = button.getAttribute('data-url');
                appIframe.src = url;
            });

            appModal.addEventListener('hidden.bs.modal', function () {
                appIframe.src = '';
            });
        }
    });

    // Lógica de la animación de la garrafa dentro del modal
    const simulacionModal = document.getElementById('simulacionModal');
    const animationButton = document.getElementById('animationButton');
    const frame1 = document.getElementById('frame-1');
    const frame2 = document.getElementById('frame-2');
    const frame3 = document.getElementById('frame-3');
    const alarmaAudio = new Audio('<?= base_url('/audio/alarma.mp3') ?>');

    let isAnimating = false;
    let animationTimeout;

    // Resetear la animación cuando el modal se oculta
    simulacionModal.addEventListener('hidden.bs.modal', function () {
        resetAnimation();
    });

    function resetAnimation() {
        isAnimating = false;
        clearTimeout(animationTimeout);
        frame1.classList.add('active');
        frame2.classList.remove('active');
        frame3.classList.remove('active');
        alarmaAudio.pause();
        alarmaAudio.currentTime = 0;
    }

    animationButton.addEventListener('mousedown', () => {
        if (isAnimating) return;
        isAnimating = true;

        // Transición de frame_1 a frame_2
        frame1.classList.remove('active');
        frame2.classList.add('active');
        
        // Iniciar la alarma al pasar a frame_2
        alarmaAudio.loop = true; // Para que se repita
        alarmaAudio.play();

        // Temporizador para pasar a frame_3 después de 2 segundos
        animationTimeout = setTimeout(() => {
            frame2.classList.remove('active');
            frame3.classList.add('active');
            alarmaAudio.pause(); // Detener la alarma
            alarmaAudio.currentTime = 0; // Reiniciar el audio
        }, 2000);
    });

    animationButton.addEventListener('mouseup', () => {
        if (isAnimating) {
            resetAnimation();
        }
    });
</script>

</body>
</html>