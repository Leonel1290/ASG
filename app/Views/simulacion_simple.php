<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulación de Fuga</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #000;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .simulation-button {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 2em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .simulation-button:hover {
            background-color: #218838;
            transform: scale(1.1);
        }

        /* --- Estilos del Modal --- */
        .modal-content-transparent {
            background-color: transparent;
            border: none;
            box-shadow: none;
        }

        .modal-dialog-transparent {
            max-width: none;
            width: auto;
            margin: auto;
        }

        .image-container {
            position: relative;
            width: 800px;
            height: 600px;
            margin: auto;
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

    <button class="simulation-button" data-bs-toggle="modal" data-bs-target="#simulacionModal">
        <i class="fas fa-play"></i>
    </button>
    <p class="mt-3">Mantén pulsado para iniciar la simulación</p>

    <div class="modal fade" id="simulacionModal" tabindex="-1" aria-labelledby="simulacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-transparent">
            <div class="modal-content modal-content-transparent">
                <div class="modal-body text-center">
                    <div class="image-container">
                        <img id="frame-1" class="image active" src="<?= base_url('/imagenes/frame_1.jpg'); ?>" alt="Garrafa sin fuga">
                        <img id="frame-2" class="image" src="<?= base_url('/imagenes/frame_2.jpg'); ?>" alt="Fuga de gas">
                        <img id="frame-3" class="image" src="<?= base_url('/imagenes/frame_3.jpg'); ?>" alt="Detector activo">
                    </div>
                    <button id="animationButton" class="btn btn-primary mt-3 d-none">
                        Mantén Pulsado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const simulacionModal = document.getElementById('simulacionModal');
        const animationButton = document.querySelector('.simulation-button');
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
            alarmaAudio.loop = true;
            alarmaAudio.play();

            // Temporizador para pasar a frame_3 después de 2 segundos
            animationTimeout = setTimeout(() => {
                frame2.classList.remove('active');
                frame3.classList.add('active');
                alarmaAudio.pause();
                alarmaAudio.currentTime = 0;
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