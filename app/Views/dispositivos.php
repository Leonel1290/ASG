<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dispositivos</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1a202c; /* Dark mode background */
            color: #cbd5e0; /* Light text */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
            padding-top: 50px; /* Adjust for potential fixed headers */
        }
        h2 {
            color: #4CAF50; /* Green for headings */
            margin-bottom: 25px;
            text-align: center;
        }
        .form-label {
            color: #a0aec0; /* Lighter text for labels */
        }
        .form-control {
            background-color: #2d3748; /* Darker input fields */
            color: #fff;
            border: 1px solid #4a5568;
        }
        .form-control:focus {
            background-color: #2d3748;
            color: #fff;
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        }
        .btn-primary {
            background-color: #4CAF50; /* Green button */
            border-color: #4CAF50;
        }
        .btn-primary:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .alert-danger {
            background-color: #fed7d7;
            color: #c53030;
            border-color: #fbcbcb;
        }
    </style>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Dispositivos</h2>

        <form id="deviceForm">
            <div class="mb-3">
                <label for="macAddress" class="form-label">Dirección MAC</label>
                <input type="text" class="form-control" id="macAddress" required
                       placeholder="AA:BB:CC:DD:EE:FF" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
                <div class="form-text">Formato: AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF</div>
            </div>
            <button type="submit" class="btn btn-primary">Buscar</button>
        </form>

        <div id="responseMessage" class="mt-3 alert" style="display: none;"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("deviceForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            let macAddress = document.getElementById("macAddress").value.trim().toUpperCase();

            // Validar formato MAC
            if (!/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(macAddress)) {
                showMessage("Formato de MAC inválido", "danger");
                return;
            }

            // Verificar si la MAC existe en la base de datos
            try {
                // Modifica esta URL si tu API de dispositivos no está directamente bajo la raíz
                const response = await fetch(`<?= base_url('/api/dispositivos') ?>?mac_address=${macAddress}`);

                if (response.ok) {
                    const data = await response.json();
                    if (data.exists) { // Asume que tu API devuelve { exists: true } si encuentra la MAC
                        window.location.href = `<?= base_url('/dispositivo/') ?>${macAddress}`;
                    } else {
                        showMessage("MAC no encontrada. Asegúrate de que el ESP32 haya enviado su dirección.", "danger");
                    }
                } else {
                    showMessage("Error al verificar la MAC con el servidor.", "danger");
                }
            } catch (error) {
                showMessage("Error de conexión con el servidor", "danger");
                console.error("Fetch error:", error);
            }
        });

        function showMessage(message, type) {
            const msgElement = document.getElementById("responseMessage");
            msgElement.innerText = message;
            msgElement.className = `mt-3 alert alert-${type}`;
            msgElement.style.display = "block";

            setTimeout(() => {
                msgElement.style.display = "none";
            }, 5000);
        }
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
    </script>
</body>
</html>
