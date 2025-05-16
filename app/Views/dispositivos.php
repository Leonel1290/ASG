<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dispositivos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                const response = await fetch(`/api/dispositivos?mac_address=${macAddress}`);

                if (response.ok) {
                    window.location.href = `/dispositivo/${macAddress}`;
                } else {
                    showMessage("MAC no encontrada. Asegúrate de que el ESP32 haya enviado su dirección.", "danger");
                }
            } catch (error) {
                showMessage("Error de conexión con el servidor", "danger");
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
</body>
</html>
