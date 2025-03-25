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

        <!-- Formulario para agregar dispositivo -->
        <form id="deviceForm">
            <div class="mb-3">
                <label for="deviceName" class="form-label">Nombre del Dispositivo</label>
                <input type="text" class="form-control" id="deviceName" required>
            </div>
            <div class="mb-3">
                <label for="macAddress" class="form-label">Dirección MAC</label>
                <input type="text" class="form-control" id="macAddress" required placeholder="AA:BB:CC:DD:EE:FF">
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>

        <div id="responseMessage" class="mt-3"></div>

        <!-- Tabla para mostrar dispositivos -->
        <h3 class="mt-5">Dispositivos Registrados</h3>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección MAC</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="deviceTableBody">
                <!-- Aquí se mostrarán los dispositivos -->
            </tbody>
        </table>
    </div>

    <script>
        async function fetchDevices() {
            const response = await fetch("/api/listar_dispositivos");
            const devices = await response.json();

            const tableBody = document.getElementById("deviceTableBody");
            tableBody.innerHTML = ""; // Limpiar tabla antes de actualizar

            devices.forEach(device => {
                const row = `<tr>
                    <td>${device.name}</td>
                    <td>${device.mac}</td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteDevice('${device.mac}')">Eliminar</button>
                    </td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        }

        document.getElementById("deviceForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            const deviceName = document.getElementById("deviceName").value;
            const macAddress = document.getElementById("macAddress").value;

            const response = await fetch("/api/agregar_dispositivo", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ name: deviceName, mac: macAddress })
            });

            const result = await response.json();
            document.getElementById("responseMessage").innerText = result.message;

            if (response.ok) fetchDevices(); // Actualizar lista
        });

        async function deleteDevice(mac) {
            await fetch(`/api/eliminar_dispositivo/${mac}`, { method: "DELETE" });
            fetchDevices(); // Actualizar lista tras eliminar
        }

        fetchDevices(); // Cargar dispositivos al inicio
    </script>
</body>
</html>