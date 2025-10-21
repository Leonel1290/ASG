<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('css/register.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <title>Registro</title>

    <style>
        /* Estilos generales */
        body {
            background-color: #f4f6f8;
            font-family: 'Poppins', sans-serif;
        }

        .form-register {
            background: #ffffff;
            width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .form-register h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .controls {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .controls-container {
            position: relative;
        }

        #eyeicon {
            width: 25px;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .botons {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .botons:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error-messages ul {
            list-style: none;
            padding: 0;
            color: red;
            margin-bottom: 10px;
        }

        .g-recaptcha {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }

        /* --- Modal Términos y Condiciones --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 800px;
            color: #333;
            line-height: 1.6;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            text-align: justify;
        }
        .modal h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .close {
            color: #333;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #e74c3c;
        }
        .modal a {
            color: #0066cc;
        }
    </style>

    <!-- Script oficial de Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="body">
    <section class="form-register">
        <h1>Formulario de Registro</h1>

        <!-- Mensajes de sesión -->
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

        <!-- Formulario -->
        <form action="<?= base_url('register/store') ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese su Nombre" value="<?= old('nombre') ?>" required>
            <input class="controls" type="text" name="apellido" id="apellido" placeholder="Ingrese su Apellido" value="<?= old('apellido') ?>" required>
            <input class="controls" type="email" name="email" id="correo" placeholder="Ingrese su Correo" value="<?= old('email') ?>" required>

            <div class="controls-container">
                <input class="controls" type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>
                <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon" alt="Mostrar/Ocultar contraseña">
            </div>

            <p>Estoy de acuerdo con <a href="#" id="openTerms">Términos y Condiciones</a></p>

            <!-- CAPTCHA (site key insertada) -->
            <div class="g-recaptcha" data-sitekey="6LekJPIrAAAAABMwovEjr7lZj6lNoUlqBXr_iCzu"></div>

            <input class="botons" type="submit" value="Registrar">
        </form>

        <p><a href="<?= base_url('loginobtener') ?>">¿Ya tengo cuenta?</a></p>
    </section>

    <!-- Modal de Términos y Condiciones -->
    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeTerms">&times;</span>
            <h2>Términos y Condiciones de Uso</h2>
            <p><strong>Última actualización:</strong> 21 de octubre de 2025</p>

            <p>Bienvenido/a a <strong>ASG</strong> (“nosotros”, “nuestro”, “la empresa”), una plataforma desarrollada para la gestión y monitoreo de dispositivos de detección de gas. 
            Al registrarte y utilizar los servicios disponibles en 
            <a href="https://asg-rxnu.onrender.com/" target="_blank">https://asg-rxnu.onrender.com</a>, aceptás los siguientes Términos y Condiciones.</p>

            <h3>1. Aceptación de los Términos</h3>
            <p>Al registrarte, acceder o utilizar este sitio web, confirmás que has leído, entendido y aceptás estos Términos y Condiciones. 
            Si no estás de acuerdo, no deberás utilizar el sitio ni crear una cuenta.</p>

            <h3>2. Registro y Cuenta de Usuario</h3>
            <p>Para acceder a ciertas funciones, deberás crear una cuenta proporcionando información veraz, completa y actualizada. 
            Sos responsable de mantener la confidencialidad de tus credenciales.</p>

            <h3>3. Privacidad y Protección de Datos</h3>
            <p>Tus datos personales se manejan conforme a nuestra Política de Privacidad. 
            Al registrarte, aceptás el tratamiento de tus datos con fines de identificación, comunicación y mejora del servicio.</p>

            <h3>4. Uso del Servicio</h3>
            <p>El usuario se compromete a utilizar el sistema de manera responsable. 
            ASG no se responsabiliza por fallas eléctricas, de red, ni por daños derivados del mal uso del dispositivo o la plataforma.</p>

            <h3>5. Propiedad Intelectual</h3>
            <p>Todo el contenido del sitio (diseños, logotipos, textos, código, gráficos y marcas) es propiedad exclusiva de ASG o sus licenciantes. 
            Está prohibido copiar o modificar sin autorización previa.</p>

            <h3>6. Modificaciones</h3>
            <p>ASG podrá modificar estos Términos en cualquier momento. 
            El uso continuo del sitio tras los cambios implica su aceptación.</p>

            <h3>7. Limitación de Responsabilidad</h3>
            <p>El servicio se ofrece “tal cual está disponible”. 
            El usuario asume la responsabilidad del uso del sistema y de las decisiones tomadas en base a los datos proporcionados.</p>

            <h3>8. Cancelación de Cuenta</h3>
            <p>Podés solicitar la eliminación de tu cuenta en cualquier momento comunicándote a través de los canales de contacto disponibles en el sitio.</p>

            <h3>9. Legislación Aplicable</h3>
            <p>Estos Términos se rigen por las leyes de la República Argentina. 
            Cualquier controversia será resuelta ante los tribunales competentes del país.</p>

            <h3>10. Contacto</h3>
            <p>📧 <strong>soporte@asg.com.ar</strong></p>
        </div>
    </div>

    <script>
        // Mostrar / Ocultar contraseña
        let eyeicon = document.getElementById("eyeicon");
        let password = document.getElementById("password");
        eyeicon.onclick = function() {
            if (password.type === "password") {
                password.type = "text";
                eyeicon.src = "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
            } else {
                password.type = "password";
                eyeicon.src = "https://static.thenounproject.com/png/1035969-200.png";
            }
        }

        // Modal de Términos
        const modal = document.getElementById("termsModal");
        const openBtn = document.getElementById("openTerms");
        const closeBtn = document.getElementById("closeTerms");

        openBtn.onclick = function(e) {
            e.preventDefault();
            modal.style.display = "block";
        }
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
