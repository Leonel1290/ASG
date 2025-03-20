<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Bienvenido</title>
    <style>
        body {
            background-color: #1E3D59; 
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #007bff;
        }

        .navbar-brand, .nav-link {
            color: #fff !important;
        }

        /* Estilos del Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            height: 100vh;
            background-color: #162B4E;
            padding-top: 20px;
            transition: left 0.3s ease-in-out;
            z-index: 1000;
        }

        .sidebar a {
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar i {
            margin-right: 10px;
        }

        /* Hover effect para mostrar el sidebar */
        .sidebar.show {
            left: 0;
        }

        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            font-size: 1.8rem;
            color: #007bff;
            cursor: pointer;
            z-index: 1001;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Estilos de la bienvenida */
        h1 {
            font-size: 2rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Botón para abrir el sidebar -->
    <span class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></span>

    <!-- Sidebar Menu -->
    <div class="sidebar">
        <a href="<?= base_url('dispositivos') ?>" class="d-flex align-items-center">
            <i class="fas fa-cogs"></i> Dispositivos
        </a>
        <a href="<?= base_url('logout') ?>" class="d-flex align-items-center"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Bienvenido <?= session()->get('nombre'); ?></h1>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
            document.querySelector('.main-content').classList.toggle('show');
        }
    </script>

</body>
</html>
