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
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }
        .error-messages ul {
            list-style: none;
            padding: 0;
            margin: 0 0 15px 0;
            color: red;
            text-align: left;
        }
        .error-messages li {
            margin-bottom: 5px;
            text-align: center;
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

            <div class="controls-container">
                <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su password" required>
                <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon" alt="Mostrar/Ocultar contraseña">
            </div>

            <p>Estoy de acuerdo con <a href="#">Términos y Condiciones</a></p>

            <input class="botons" type="submit" value="Registrar">
        </form>
        <p><a href="<?= base_url('loginobtener') ?>">¿Ya tengo Cuenta?</a></p>
    </section>

    <script>
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("password");
        eyeicon.onclick = function(){
            if(password.type == "password"){
                password.type = "text";
                eyeicon.src = "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
            }
            else{
                password.type = "password";
                eyeicon.src = "https://static.thenounproject.com/png/1035969-200.png";
            }
        }
    </script>
</body>
</html>