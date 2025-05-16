<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <title>Registro</title>
    <style>
        /* Estilos para mensajes de éxito y error (se mantienen aquí) */
        .success-message {
            color: green; /* O el color de tu tema para éxito */
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center; /* Centrar mensajes */
        }
        .error-messages ul {
            list-style: none;
            padding: 0;
            margin: 0 0 15px 0;
            color: red; /* O el color de tu tema para error */
            text-align: left; /* Alinear lista de errores a la izquierda si hay varios */
        }
        .error-messages li {
            margin-bottom: 5px;
            text-align: center; /* Centrar cada mensaje de error */
        }
        /* Nota: Otros estilos como .form-register, .controls, .botons, etc.,
                 se espera que provengan de tu archivo register.css */
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