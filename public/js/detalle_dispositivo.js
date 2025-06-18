// Estas variables globales se definen en el archivo PHP de la vista (detalle_dispositivo.php)
// const API_BASE_URL = '<?= base_url(); ?>';
// const MAC_ADDRESS = '<?= esc($mac); ?>';
// const GAS_ALARM_THRESHOLD_FRONTEND = 100; // Define este valor en el PHP si es dinámico

// Elementos del DOM
const gasLevelDisplay = document.getElementById('gasLevelDisplay');
const valveStateDisplay = document.getElementById('valveStateDisplay');
const openValveBtn = document.getElementById('openValveBtn');
const closeValveBtn = document.getElementById('closeValveBtn');
const valveMessageDiv = document.getElementById('valveMessage');
const openThresholdDisplay = document.getElementById('openThresholdDisplay');

// La vista PHP ya establece el texto inicial del umbral de alarma en openThresholdDisplay

// Función para enviar comandos a la válvula
function sendValveCommand(mac, action) { // Cambiado 'command' a 'action' para coincidir con PHP
    valveMessageDiv.classList.add('d-none'); // Ocultar mensaje anterior
    valveMessageDiv.classList.remove('alert-success', 'alert-danger', 'alert-info'); // Limpiar clases de estilo

    const apiUrl = `${API_BASE_URL}/api/controlValve`; // Ruta correcta para el controlador

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json', // Se envía JSON
            'X-Requested-With': 'XMLHttpRequest' // Para identificar la solicitud como AJAX
        },
        body: JSON.stringify({ mac: mac, action: action }) // ¡Ahora envía 'action'!
    })
    .then(response => {
        if (!response.ok) {
            // Si la respuesta no es 2xx, intentamos parsear el JSON de error
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            valveMessageDiv.textContent = data.message;
            valveMessageDiv.classList.remove('d-none');
            valveMessageDiv.classList.add('alert-success');
        } else {
            valveMessageDiv.textContent = data.message || 'Error al enviar el comando.';
            valveMessageDiv.classList.remove('d-none');
            valveMessageDiv.classList.add('alert-danger');
        }
        // Después de enviar un comando, actualizamos el estado para reflejar los cambios
        updateDeviceStatus(); 
    })
    .catch(error => {
        console.error('Error en sendValveCommand:', error);
        valveMessageDiv.textContent = 'Ocurrió un error de conexión o en el servidor.';
        valveMessageDiv.classList.remove('d-none');
        valveMessageDiv.classList.add('alert-danger');
        if (error.message) {
            valveMessageDiv.textContent = error.message;
        } else if (error.error) {
            valveMessageDiv.textContent = error.error;
        }
    });
}

// Función para actualizar el estado del dispositivo (nivel de gas y estado de válvula)
async function updateDeviceStatus() {
    const apiUrl = `${API_BASE_URL}/api/getValveState/${MAC_ADDRESS}`;
    
    try {
        const response = await fetch(apiUrl);
        if (!response.ok) {
            throw new Error('Network response was not ok.');
        }
        const data = await response.json();

        if (data.status === 'success') {
            const nivelGas = data.ultimo_nivel_gas; // ¡Ahora esperamos esto del PHP!
            const estadoValvula = data.estado_valvula;

            gasLevelDisplay.textContent = `${nivelGas} PPM`;

            // Actualizar estado visual de la válvula
            valveStateDisplay.textContent = estadoValvula === 1 ? 'ABIERTA' : 'CERRADA';
            valveStateDisplay.classList.toggle('valve-state-open', estadoValvula === 1);
            valveStateDisplay.classList.toggle('valve-state-closed', estadoValvula === 0);

            // Los botones SIEMPRE están habilitados para control manual
            openValveBtn.disabled = false;
            closeValveBtn.disabled = false;

        } else {
            console.error('Error al obtener estado del dispositivo:', data.message);
            gasLevelDisplay.textContent = 'Error';
            valveStateDisplay.textContent = 'Error';
            openValveBtn.disabled = false; 
            closeValveBtn.disabled = false; 
        }
    } catch (error) {
        console.error('Error al obtener estado del dispositivo:', error);
        gasLevelDisplay.textContent = 'Sin conexión';
        valveStateDisplay.textContent = 'Sin conexión';
        openValveBtn.disabled = false; 
        closeValveBtn.disabled = false; 
    }
}

// Actualizar estado inicial y luego periódicamente
document.addEventListener('DOMContentLoaded', () => {
    updateDeviceStatus(); // Llamada inicial al cargar la página
    setInterval(updateDeviceStatus, 5000); // Actualizar cada 5 segundos
});

