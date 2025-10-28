<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <title>Registro</title>

    <style>
        /* Mensajes */
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

        /* Estilo del recaptcha */
        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }

        /* Ajustes generales */
        .form-register {
            width: 90%;
            max-width: 400px;
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .form-register h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        .controls {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .controls-container {
            display: flex;
            align-items: center;
            position: relative;
        }

        .controls-container img {
            width: 25px;
            position: absolute;
            right: 10px;
            cursor: pointer;
        }

        .botons {
            width: 100%;
            background-color: #0069d9;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .botons:hover {
            background-color: #004da1;
        }

        p {
            text-align: center;
        }

        a {
            color: #0069d9;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="body">
    <section class="form-register">
        <h1>Formulario de Registro</h1>

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
                <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
                <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon" alt="Mostrar/Ocultar contraseña">
            </div>

            <input class="controls" type="password" name="confirm_password" id="confirm_password" placeholder="Confirme su contraseña" required>

            <div class="g-recaptcha" data-sitekey="6LekJPIrAAAAABMwovEjr7lZj6lNoUlqBXr_iCzu"></div>

            <p>Estoy de acuerdo con los <a href="<?= base_url('terminos') ?>" target="_blank">Términos y Condiciones</a></p>

            <input class="botons" type="submit" value="Registrar">
        </form>

        <p><a href="<?= base_url('loginobtener') ?>">¿Ya tengo Cuenta?</a></p>
    </section>

    <script>
        // Mostrar / ocultar contraseña
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("password");
        // Asegúrate de que el script solo controle el primer campo de contraseña si usas el mismo ícono/lógica
        // Si quieres que el ícono funcione para ambos campos, necesitarías dos íconos y lógica separada
        // Por ahora, mantendremos la lógica original solo para el campo 'password'
        eyeicon.onclick = function() {
            if (password.type == "password") {
                password.type = "text";
                eyeicon.src = "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
            } else {
                password.type = "password";
                eyeicon.src = "https://static.thenounproject.com/png/1035969-200.png";
            }
        }
    </script>
</body>
</html>