<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dispositivos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Personalización de los márgenes y espaciado para mejor visualización en móviles */
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 100%;
            margin-top: 20px;
        }

        .form-label {
            font-size: 1rem;
        }

        .form-control {
            font-size: 1rem;
        }

        .table th, .table td {
            text-align: center;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .alert {
            font-size: 1rem;
            text-align: center;
        }

        /* Mejorar visualización en pantallas pequeñas */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
            }

            .table th, .table td {
                font-size: 0.9rem;
            }

            .btn {
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Gestión de Dispositivos</h2>

        <!-- Formulario para agregar dispositivo -->
        <form id="deviceForm">
            <input type="hidden" id="usuarioId" value="1"> <!-- Cambiar por el ID real del usuario -->
            <div class="mb-3">
                <label for="deviceName" class="form-label">Nombre del Dispositivo</label>
                <input type="text" class="form-control" id="deviceName" required>
            </div>
            <div class="mb-3">
                <label for="numeroSerie" class="form-label">Número de Serie</label>
                <input type="text" class="form-control" id="numeroSerie" required>
            </div>
            <div class="mb-3">
                <label for="macAddress" class="form-label">Dirección MAC</label>
                <input type="text" class="form-control" id="macAddress" required 
                       placeholder="AA:BB:CC:DD:EE:FF" pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$">
                <div class="form-text">Formato: AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF</div>
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>

        <div id="responseMessage" class="mt-3 alert" style="display: none;"></div>

        <!-- Tabla para mostrar dispositivos -->
        <h3 class="mt-5">Dispositivos Registrados</h3>
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Número de Serie</th>
                        <th>MAC</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="deviceTableBody">
                    <!-- Aquí se mostrarán los dispositivos -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Función para validar formato MAC
        function validateMAC(mac) {
            return /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(mac);
        }

        // Función para formatear MAC
        function formatMAC(mac) {
            return mac.replace(/-/g, ':').toUpperCase();
        }

        async function fetchDevices() {
            try {
                const usuarioId = document.getElementById('usuarioId').value;
                const response = await fetch(`/api/dispositivos?usuario_id=${usuarioId}`);
                
                if (!response.ok) throw new Error('Error al obtener dispositivos');
                
                const devices = await response.json();

                const tableBody = document.getElementById("deviceTableBody");
                tableBody.innerHTML = "";

                devices.forEach(device => {
                    const row = `<tr>
                        <td>${device.nombre}</td>
                        <td>${device.numero_serie}</td>
                        <td>${device.mac_address}</td>
                        <td>${device.estado}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="deleteDevice('${device.mac_address}')">Eliminar</button>
                        </td>
                    </tr>`;
                    tableBody.innerHTML += row;
                });
            } catch (error) {
                showMessage(error.message, 'danger');
            }
        }

        document.getElementById("deviceForm").addEventListener("submit", async function(event) {
            event.preventDefault();

            const usuarioId = document.getElementById('usuarioId').value;
            const deviceName = document.getElementById("deviceName").value;
            const numeroSerie = document.getElementById("numeroSerie").value;
            let macAddress = document.getElementById("macAddress").value.trim();

            // Validar formato MAC
            if (!validateMAC(macAddress)) {
                showMessage('Formato de MAC inválido. Use AA:BB:CC:DD:EE:FF o AA-BB-CC-DD-EE-FF', 'danger');
                return;
            }

            // Estandarizar formato MAC
            macAddress = formatMAC(macAddress);

            try {
                const response = await fetch("/api/dispositivos", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ 
                        usuario_id: usuarioId,
                        nombre: deviceName,
                        numero_serie: numeroSerie,
                        mac_address: macAddress
                    })
                });

                const result = await response.json();
                
                if (response.ok) {
                    // Redirigir a la otra vista solo si fue exitoso
                    window.location.href = "/http://localhost/SanchezLeonel2024/login2/public/login"; // Cambiar por tu URL real
                } else {
                    showMessage(result.message || 'Error al agregar dispositivo', 'danger');
                }
            } catch (error) {
                showMessage('Error de conexión con el servidor', 'danger');
                console.error("Error:", error);
            }
        });

        async function deleteDevice(mac) {
            if (!confirm(`¿Está seguro de eliminar el dispositivo con MAC ${mac}?`)) return;
            
            try {
                const response = await fetch(`/api/dispositivos/${mac}`, { 
                    method: "DELETE" 
                });
                
                if (!response.ok) throw new Error('Error al eliminar dispositivo');
                
                showMessage('Dispositivo eliminado correctamente', 'success');
                fetchDevices();
            } catch (error) {
                showMessage(error.message, 'danger');
            }
        }

        function showMessage(message, type) {
            const msgElement = document.getElementById("responseMessage");
            msgElement.innerText = message;
            msgElement.className = `mt-3 alert alert-${type}`;
            msgElement.style.display = 'block';
            
            setTimeout(() => {
                msgElement.style.display = 'none';
            }, 5000);
        }

        // Cargar dispositivos al inicio
        document.addEventListener('DOMContentLoaded', fetchDevices);
    </script>
</body>
</html>
