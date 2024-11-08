<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nivel de Gas en Tiempo Real</title>
    <!-- Incluye Bootstrap para el estilo -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Nivel de Gas</h1>
        <p class="text-center">Estado actual de la lectura de gas del sensor.</p>

        <!-- Muestra el nivel de gas -->
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Nivel de Gas Actual.</h5>
                <p id="nivel_gas" class="display-4 text-primary">--</p>
            </div>
        </div>
    </div>

    <!-- JavaScript para actualizar el nivel de gas cada pocos segundos -->
    <script>
        // Función para obtener el nivel de gas desde la API
        async function actualizarNivelGas() {
            try {
                const response = await fetch('/api/get-lectura'); // Ruta de la API
                const data = await response.json();
                
                // Actualiza el texto en la página con el valor obtenido
                document.getElementById('nivel_gas').innerText = data.nivel_gas ?? '--';
            } catch (error) {
                console.error('Error al obtener el nivel de gas:', error);
            }
        }

        // Llama a la función cada 5 segundos
        setInterval(actualizarNivelGas, 5000);
        
        // Llama a la función al cargar la página
        actualizarNivelGas();
    </script>
</body>
</html>
