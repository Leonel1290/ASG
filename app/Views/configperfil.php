<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Configuración de Perfil</h2>
    <form>
        <div class="mb-3 text-center">
            <img src="https://via.placeholder.com/120" class="rounded-circle mb-2" alt="Foto de perfil" width="120" height="120">
            <div>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2">Cambiar foto</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" placeholder="Tu nombre">
        </div>
        <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" placeholder="Tu apellido">
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-outline-primary w-100 mb-2">Modificar correo electrónico</button>
            <button type="button" class="btn btn-outline-primary w-100">Modificar contraseña</button>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-success">Guardar cambios</button>
        </div>
    </form>
</div>
</body>
</html>