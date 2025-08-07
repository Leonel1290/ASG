<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Login y Registro</title>
    <style>
        /* Estilos base de tu register.css */
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
        /* Estilos de la tarjeta para el efecto de volteo */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f2f5;
        }
        .container-flip {
            perspective: 1000px;
        }
        .card-flip {
            width: 350px;
            height: 550px; /* Altura ajustada para los formularios */
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.8s;
        }
        .card-flip.flipped {
            transform: rotateY(180deg);
        }
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background: #fff;
        }
        .card-back {
            transform: rotateY(180deg);
        }
        /* Estilos del formulario de registro */
        .form-register {
            width: 100%;
            height: auto;
            padding: 0;
            background: none;
            box-shadow: none;
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
        .toggle-link {
            cursor: pointer;
            text-decoration: underline;
            color: #007bff;
            margin-top: 10px;
        }
        /* Estilos del formulario de login */
        .container {
            width: 100%;
            padding: 0;
        }
        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .ver {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .ver-icon {
            width: 20px;
        }
    </style>
</head>
<body class="body">
    <div class="container-flip">
        <div id="card-flip" class="card-flip">
            <div class="card-front">
                <div class="col-sm-12 col-md-12">
                    <div class="text-center mb-4">
                        <h1 class="h3">Login</h1>
                    </div>
                    <?php if (session()->get('error')): ?>
                        <div>
                            <p style="color: red;"><?= session()->get('error') ?></p>
                        </div>
                    <?php endif; ?>
                    <form action="<?= base_url('login') ?>" method="POST">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <input type="email" name="email" id="login-email" class="form-control" required>
                        </div>
                        <div class="form-group" style="position: relative;">
                            <label for="login-password">Password</label>
                            <input type="password" name="password" id="login-password" class="form-control" required>
                            <div class="ver">
                                <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon-login" class="ver-icon" alt="Mostrar/Ocultar contraseña">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                    </form>
                    <div class="text-center mt-3">
                        <br><a href="<?= base_url('/forgotpassword') ?>">¿Olvidaste tu contraseña?</a></br>
                        <a href="#" class="toggle-link" onclick="flipCard()">Crear cuenta nueva</a>
                    </div>
                </div>
            </div>

            <div class="card-back">
                <section class="form-register">
                    <h1>Formulario Registro</h1>
                    <?php if (session()->getFlashdata('success')): ?>
                        <p class="success-message"><?= session()->getFlashdata('success') ?></p>
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
                        <div class="controls-container" style="position: relative;">
                            <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su password" required>
                            <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon-register" class="ver-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; width: 20px;" alt="Mostrar/Ocultar contraseña">
                        </div>
                        <p>Estoy de acuerdo con <a href="#">Términos y Condiciones</a></p>
                        <input class="botons" type="submit" value="Registrar">
                    </form>
                    <p><a href="#" class="toggle-link" onclick="flipCard()">¿Ya tengo Cuenta?</a></p>
                </section>
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

        // Lógica para el login
        const eyeiconLogin = document.getElementById("eyeicon-login");
        const passwordLogin = document.getElementById("login-password");
        if(eyeiconLogin && passwordLogin) {
            eyeiconLogin.onclick = function(){
                if(passwordLogin.type == "password"){
                    passwordLogin.type = "text";
                    eyeiconLogin.src= "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
                }
                else{
                    passwordLogin.type = "password";
                    eyeiconLogin.src= "https://static.thenounproject.com/png/1035969-200.png";
                }
            };
        }

        // Lógica para el registro
        const eyeiconRegister = document.getElementById("eyeicon-register");
        const passwordRegister = document.getElementById("password");
        if(eyeiconRegister && passwordRegister) {
            eyeiconRegister.onclick = function(){
                if(passwordRegister.type == "password"){
                    passwordRegister.type = "text";
                    eyeiconRegister.src= "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
                }
                else{
                    passwordRegister.type = "password";
                    eyeiconRegister.src= "https://static.thenounproject.com/png/1035969-200.png";
                }
            };
        }
    </script>
</body>
</html>