<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ASG - Seguridad en tu Hogar</title>

    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="manifest" href="<?= base_url('manifest.json'); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Colores Azul Marino */
            --dark-background: #0a162f;
            --dark-secondary: #0e1d40;
            --dark-card-background: #12254f;
            --primary-highlight: #1e90ff; /* azul brillante */
            --primary-highlight-darker: #166acb;
            --text-light: #d1d9e6;
            --text-lighter: #ffffff;
            --navbar-bg-opacity: rgba(10, 22, 47, 0.9);
        }

        body {
            background: linear-gradient(135deg, var(--dark-background), var(--dark-secondary));
            font-family: 'Poppins', sans-serif;
            color: var(--text-light);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .navbar {
            backdrop-filter: blur(10px);
            background-color: var(--navbar-bg-opacity);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand, .nav-link {
            color: var(--text-lighter);
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: var(--primary-highlight);
        }

        .btn-custom {
            background-color: var(--primary-highlight);
            border: none;
            color: var(--text-lighter);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.75rem 2rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(30, 144, 255, 0.2);
        }

        .btn-custom:hover {
            background-color: var(--primary-highlight-darker);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 144, 255, 0.3);
        }

        .hero {
            padding: 4rem 0;
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .hero h1 {
            font-size: 2.8rem;
            color: var(--text-lighter);
            font-weight: 700;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-line {
            width: 70px;
            height: 4px;
            background-color: var(--primary-highlight);
            margin: 1.25rem auto 1.75rem;
            border-radius: 2px;
        }

        .hero-img {
            max-width: 90%;
            height: auto;
            margin-top: 2rem;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .features {
            padding: 4rem 0;
            background-color: var(--dark-card-background);
            border-radius: 20px;
            margin: 3rem auto;
            max-width: 95%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .features h2 {
            color: var(--text-lighter);
            font-weight: 700;
            margin-bottom: 3rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .feature-card {
            background-color: var(--dark-background);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
        }

        .features i {
            font-size: 3rem;
            color: var(--primary-highlight);
            margin-bottom: 1.5rem;
            text-shadow: 0 0 10px rgba(30, 144, 255, 0.5);
        }

        .features h3 {
            color: var(--text-lighter);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .features p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .company-info {
            background-color: var(--dark-card-background);
            border-radius: 20px;
            padding: 3rem;
            color: var(--text-light);
            margin: 3rem auto;
            max-width: 95%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .company-info h2 {
            color: var(--text-lighter);
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .company-info p {
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
        }

        .company-info strong,
        .company-info a {
            color: var(--primary-highlight);
        }

        footer {
            background-color: var(--dark-background);
            text-align: center;
            padding: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-top: auto;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28d1, d9, e6, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 .25rem var(--primary-highlight);
            border-color: var(--primary-highlight);
        }

        @media (min-width: 768px) {
            .hero h1 { font-size: 3.5rem; }
            .hero-img { max-width: 100%; }
            .features, .company-info { max-width: 80%; margin: 5rem auto; }
            .feature-card { padding: 2.5rem; }
            .company-info { padding: 3.5rem; }
        }
    </style>
</head>
<body>
    <!-- El resto de tu contenido HTML permanece igual -->
</body>
</html>