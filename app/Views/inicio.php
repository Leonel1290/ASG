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
        /* Tu CSS ya existente */
        :root {
            --dark-background: #0B1A1E;
            --dark-secondary: #10262B;
            --dark-card-background: #1A2F34;
            --primary-highlight: #00A3A3;
            --primary-highlight-darker: #008D8D;
            --text-light: #C9D6DF;
            --text-lighter: #F0F4F8;
            --navbar-bg-opacity: rgba(11, 26, 30, 0.9);
        }
        body { background: linear-gradient(135deg, var(--dark-background), var(--dark-secondary)); font-family: 'Poppins', sans-serif; color: var(--text-light); margin:0; min-height:100vh; display:flex; flex-direction:column; overflow-x:hidden; }
        .navbar { backdrop-filter: blur(10px); background-color: var(--navbar-bg-opacity); position: sticky; top:0; width:100%; z-index:1000; padding:1rem 0; box-shadow:0 2px 10px rgba(0,0,0,0.3); }
        .navbar-brand, .nav-link { color: var(--text-lighter); font-weight:600; transition:color 0.3s ease; }
        .navbar-brand:hover, .nav-link:hover { color: var(--primary-highlight); }
        .btn-custom { background-color: var(--primary-highlight); border:none; color: var(--text-lighter); font-weight:600; border-radius:30px; padding:0.75rem 2rem; transition: background-color 0.3s ease, transform 0.2s ease; box-shadow:0 4px 15px rgba(0,163,163,0.2); }
        .btn-custom:hover { background-color: var(--primary-highlight-darker); transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,163,163,0.3); }
        .hero { padding:4rem 0; text-align:center; flex-grow:1; display:flex; align-items:center; }
        .hero h1 { font-size:2.8rem; color: var(--text-lighter); font-weight:700; line-height:1.2; text-shadow:0 2px 4px rgba(0,0,0,0.3); }
        .hero-line { width:70px; height:4px; background-color: var(--primary-highlight); margin:1.25rem auto 1.75rem; border-radius:2px; }
        .hero-img { max-width:90%; height:auto; margin-top:2rem; animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0);}50%{transform:translateY(-15px);} }
        .features { padding:4rem 0; background-color: var(--dark-card-background); border-radius:20px; margin:3rem auto; max-width:95%; box-shadow:0 8px 25px rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.05); }
        .features h2 { color: var(--text-lighter); font-weight:700; margin-bottom:3rem; text-shadow:0 1px 3px rgba(0,0,0,0.2); }
        .feature-card { background-color: var(--dark-background); border-radius:15px; padding:2rem; margin-bottom:1.5rem; transition:transform 0.3s ease, box-shadow 0.3s ease; box-shadow:0 4px 15px rgba(0,0,0,0.3); height:100%; display:flex; flex-direction:column; justify-content:space-between; border:1px solid rgba(255,255,255,0.08); }
        .feature-card:hover { transform:translateY(-8px); box-shadow:0 12px 30px rgba(0,0,0,0.5); }
        .features i { font-size:3rem; color: var(--primary-highlight); margin-bottom:1.5rem; text-shadow:0 0 10px rgba(0,163,163,0.5); }
        .features h3 { color: var(--text-lighter); font-size:1.5rem; font-weight:600; margin-bottom:0.75rem; }
        .features p { font-size:1rem; line-height:1.6; }
        .company-info { background-color: var(--dark-card-background); border-radius:20px; padding:3rem; color: var(--text-light); margin:3rem auto; max-width:95%; box-shadow:0 8px 25px rgba(0,0,0,0.4); text-align:center; border:1px solid rgba(255,255,255,0.05); }
        .company-info h2 { color: var(--text-lighter); font-weight:700; margin-bottom:2rem; text-shadow:0 1px 3px rgba(0,0,0,0.2); }
        footer { background-color: var(--dark-background); text-align:center; padding:2rem; font-size:0.9rem; color: var(--text-light); margin-top:auto; box-shadow:0 -2px 10px rgba(0,0,0,0.2); border-top:1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">ASG</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#company">Contacto</a></li>
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
                         alt="Ilustración de fuga de gas" class="hero-img img-fluid" loading="lazy">
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
    // Smooth scroll
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if(target.length){
            const offset = $('.navbar').outerHeight() + 10;
            $('html, body').animate({scrollTop: target.offset().top - offset}, 500);
        }
    });

    // Service Worker
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
        .then(reg => console.log('SW registrado ✅', reg.scope))
        .catch(err => console.log('Fallo registro SW ❌', err));
    }

    // Solicitar permiso para notificaciones
    async function solicitarPermisoNotificaciones() {
        const permiso = await Notification.requestPermission();
        if (permiso === "granted") {
            console.log("Permiso concedido ✅");
            suscribirUsuario();
        }
    }

    async function suscribirUsuario() {
        const registro = await navigator.serviceWorker.ready;
        const suscripcion = await registro.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: "TU_PUBLIC_VAPID_KEY_BASE64"
        });

        await fetch("<?= base_url('/api/suscripciones') ?>", {
            method: "POST",
            body: JSON.stringify(suscripcion),
            headers: { "Content-Type": "application/json" }
        });
        console.log("Usuario suscrito a notificaciones:", suscripcion);
    }

    document.addEventListener("DOMContentLoaded", solicitarPermisoNotificaciones);
</script>

</body>
</html>
