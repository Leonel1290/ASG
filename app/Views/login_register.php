<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Login y Registro</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
            margin: 0;
        }
        .container-flip {
            perspective: 1000px;
        }
        .card-flip {
            width: 350px;
            height: 550px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s;
        }
        .card-flip.flipped {
            transform: rotateY(180deg);
        }
        /* Estilos modificados para hacer la tarjeta "invisible" */
        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            box-shadow: none; /* Quitamos la sombra */
            border-radius: 0;  /* Quitamos el radio de borde */
            background: none; /* Quitamos el color de fondo */
        }
        .card-back {
            transform: rotateY(180deg);
        }
        /* El resto de estilos se mantienen */
        h1, h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn-flip {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .btn-flip:hover {
            background-color: #0056b3;
        }
        .toggle-link {
            margin-top: 20px;
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
        }
        .ver {
            position: absolute;
            right: 35px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .ver img {
            width: 20px;
            height: auto;
        }
        /* Estilos de tus archivos CSS originales */
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
        .controls {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .botons {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container-flip">
    <div id="card-flip" class="card-flip">
        <div class="card-front">
            <h2>Iniciar Sesión</h2>
            <?php if (session()->get('error_login')): ?>
                <div class="alert alert-danger" role="alert">
                    <?= session()->get('error_login') ?>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('login') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" name="email" id="login-email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="login-password" class="form-control" required>
                        <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon-login" class="ver-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 20px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
            </form>
            <div class="text-center mt-3">
                <a href="<?= base_url('forgotpassword') ?>">¿Olvidaste tu contraseña?</a>
                <p class="toggle-link" onclick="flipCard()">Crear cuenta nueva</p>
            </div>
        </div>

        <div class="card-back">
            <h2>Registro</h2>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger" role="alert">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('register/store') ?>" method="post">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="register-nombre">Nombre</label>
                    <input type="text" name="nombre" id="register-nombre" class="form-control" value="<?= old('nombre') ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-apellido">Apellido</label>
                    <input type="text" name="apellido" id="register-apellido" class="form-control" value="<?= old('apellido') ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Correo Electrónico</label>
                    <input type="email" name="email" id="register-email" class="form-control" value="<?= old('email') ?>" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Contraseña</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="register-password" class="form-control" required>
                        <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon-register" class="ver-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 20px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </form>
            <div class="text-center mt-3">
                <p class="toggle-link" onclick="flipCard()">¿Ya tengo Cuenta? Iniciar Sesión</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

<script>
    function flipCard() {
        const card = document.getElementById('card-flip');
        card.classList.toggle('flipped');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Lógica para mostrar/ocultar contraseña del LOGIN
        const eyeiconLogin = document.getElementById("eyeicon-login");
        const passwordLogin = document.getElementById("login-password");
        if(eyeiconLogin && passwordLogin){
            eyeiconLogin.onclick = function(){
                if(passwordLogin.type == "password"){
                    passwordLogin.type = "text";
                    eyeiconLogin.src = "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
                }
                else {
                    passwordLogin.type = "password";
                    eyeiconLogin.src = "https://static.thenounproject.com/png/1035969-200.png";
                }
            };
        }

        // Lógica para mostrar/ocultar contraseña del REGISTRO
        const eyeiconRegister = document.getElementById("eyeicon-register");
        const passwordRegister = document.getElementById("register-password");
        if(eyeiconRegister && passwordRegister){
            eyeiconRegister.onclick = function(){
                if(passwordRegister.type == "password"){
                    passwordRegister.type = "text";
                    eyeiconRegister.src = "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
                }
                else {
                    passwordRegister.type = "password";
                    eyeiconRegister.src = "https://static.thenounproject.com/png/1035969-200.png";
                }
            };
        }
    });
</script>

</body>
</html>