<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ASG - Prueba de Alarma</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">

    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #0A192F, #0D203B);
            font-family: 'Poppins', sans-serif;
            color: #AFB3B7;
            padding: 2rem;
        }

        .btn-circle {
            width: 60px;
            height: 60px;
            padding: 0;
            border-radius: 50%;
            background-color: #28a745;
            color: #fff;
            border: none;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .btn-circle:hover {
            background-color: #218838;
        }

        .image-container {
            position: relative;
            width: 700px;
            height: 500px;
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
<body class="text-center">

    <h1 class="mb-4">Panel de Pruebas</h1>

    <!-- Botones -->
    <button class="btn btn-outline-light mt-3 ms-2" onclick="probarAlerta()">🔊 Probar Alarma</button>
    <button class="btn btn-outline-light mt-3 ms-2" data-bs-toggle="modal" data-bs-target="#simulacionModal">⚡ Simular Actuación</button>

    <!-- Audio para la alarma -->
    <audio id="alarmaAudio" src="<?= base_url('/audio/alarma.mp3') ?>" preload="auto"></audio>

    <!-- Modal de simulación -->
    <div class="modal fade" id="simulacionModal" tabindex="-1" aria-labelledby="simulacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background-color:#000; color:#fff;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" id="simulacionModalLabel">Simulación de Fuga de Gas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                </div>
                <div class="modal-body text-center d-flex flex-column justify-content-between align-items-center">
                    <div class="image-container">
                        <img id="frame-1" class="image active" src="<?= base_url('/imagenes/frame_1.jpg'); ?>" alt="Garrafa sin fuga">
                        <img id="frame-2" class="image" src="<?= base_url('/imagenes/frame_2.jpg'); ?>" alt="Fuga de gas">
                        <img id="frame-3" class="image" src="<?= base_url('/imagenes/frame_3.jpg'); ?>" alt="Detector activo">
                    </div>
                    <button id="animationButton" class="btn btn-circle mt-3">
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS y jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Probar sonido de alerta
        function probarAlerta() {
            const audio = document.getElementById('alarmaAudio');
            audio.play();
        }

        // Animación del modal
        const simulacionModal = document.getElementById('simulacionModal');
        const animationButton = document.getElementById('animationButton');
        const frame1 = document.getElementById('frame-1');
        const frame2 = document.getElementById('frame-2');
        const frame3 = document.getElementById('frame-3');
        const alarmaAudio = new Audio('<?= base_url('/audio/alarma.mp3') ?>');

        let isAnimating = false;
        let animationTimeout;

        simulacionModal.addEventListener('hidden.bs.modal', resetAnimation);

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

            frame1.classList.remove('active');
            frame2.classList.add('active');

            alarmaAudio.loop = true;
            alarmaAudio.play();

            animationTimeout = setTimeout(() => {
                frame2.classList.remove('active');
                frame3.classList.add('active');
                alarmaAudio.pause();
                alarmaAudio.currentTime = 0;
            }, 2000);
        });

        animationButton.addEventListener('mouseup', resetAnimation);
    </script>

</body>
</html>
