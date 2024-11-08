<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <title>Registro</title>
</head>
<body class="body">
    <section class="form-register">
    <h1>Formulario Registro</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <p><?= session()->getFlashdata('success') ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <ul>
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="<?= base_url('registerController/store') ?>" method="post">
       
        <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre" value="<?= old('nombre') ?>">

        <input class="controls" type="text" name="apellido" id="apellido" placeholder="Ingrese su Apellido" value="<?= old('apellido') ?>">

        <input class="controls" type="email" name="email" id="correo" placeholder="Ingrese su Correo" value="<?= old('email') ?>">

        <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su password">

        <p>Estoy de acuerdo con <a href="#">Terminos y Condiciones</a></p>

        <input class="botons" type="submit" value="Registrar">
    </form>
    <p><a href="<?= base_url('loginobtener/') ?>">¿Ya tengo Cuenta?</a></p>
    </section>
</body>
</html>