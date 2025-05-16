<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <title>Registro</title>
    <style>
        /* Añade o ajusta tus estilos CSS aquí */
        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .error-messages ul {
            list-style: none;
            padding: 0;
            margin: 0 0 15px 0;
            color: red;
        }
        .error-messages li {
            margin-bottom: 5px;
        }
         .form-register {
            width: 400px; /* Ajusta el ancho si es necesario */
            background: #24303c;
            padding: 30px;
            margin: auto;
            margin-top: 100px;
            border-radius: 4px;
            font-family: 'calibri';
            color: white;
            box-shadow: 7px 13px 37px #000;
        }

        .form-register h1 {
            font-size: 22px;
            margin-bottom: 20px;
            text-align: center;
        }

        .controls {
            width: 100%;
            background: #24303c;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 16px;
            border: 1px solid #1f53c5;
            font-family: 'calibri';
            font-size: 18px;
            color: white;
        }

        .form-register p {
            height: 40px;
            text-align: center;
            font-size: 18px;
            line-height: 40px;
        }

        .form-register a {
            color: white;
            text-decoration: none;
        }

        .form-register a:hover {
            color: white;
            text-decoration: underline;
        }

        .botons {
            width: 100%;
            background: #1f53c5;
            border: none;
            padding: 12px;
            color: white;
            margin: 16px 0;
            font-size: 16px;
            cursor: pointer;
        }

        .botons:hover {
            background: #143c8a;
        }
    </style>
</head>
<body class="body">
    <section class="form-register">
        <h1>Formulario Registro</h1>

        <?php if (session()->getFlashdata('success')): ?>
            <p class="success-message"><?= session()->getFlashdata('success') ?></p>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="error-messages">
                <ul>
                    <li><?= esc(session()->getFlashdata('error')) ?></li>
                </ul>
            </div>
        <?php endif; ?>


        <?php if (session()->getFlashdata('errors')): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register/store') ?>" method="post">
            <?= csrf_field() ?>
            <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre" value="<?= old('nombre') ?>" required>

            <input class="controls" type="text" name="apellido" id="apellido" placeholder="Ingrese su Apellido" value="<?= old('apellido') ?>" required>

            <input class="controls" type="email" name="email" id="correo" placeholder="Ingrese su Correo" value="<?= old('email') ?>" required>

            <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su password" required>

            <p>Estoy de acuerdo con <a href="#">Términos y Condiciones</a></p>

            <input class="botons" type="submit" value="Registrar">
        </form>
        <p><a href="<?= base_url('loginobtener') ?>">¿Ya tengo Cuenta?</a></p>
    </section>
</body>
</html>
