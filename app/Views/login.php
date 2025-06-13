<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Login</title>

    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ASG">
    <link rel="apple-touch-icon" href="<?= base_url('imagenes/Logo.png') ?>">

  </head>
  <body>
    <div class="container">
      <div class="row justify-content-center mt-5">
        <div class="col-sm-6 col-md-4">
          <div class="text-center mb-4">
            <h1 class="h3">Login</h1>
          </div>
          <!-- Mostrar mensaje de error si existe -->
          <?php if (session()->get('error')): ?>
        <div>
            <p style="color: red;"><?= session()->get('error') ?></p>
        </div>
    <?php endif; ?>

          <form action="<?php echo base_url('/login') ?>" method="POST">
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="ver">
              <img src="https://static.thenounproject.com/png/1035969-200.png" id="eyeicon">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
          </form>
          <div class="text-center mt-3">
            <br><a href="<?= base_url('/forgotpassword') ?>">¿Olvidaste tu contraseña?</a></br>
            <a href="<?= base_url('/register') ?>">Crear cuenta nueva</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Optional JavaScript; choose one of the two! -->
    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    
    <script>let eyeicon = document.getElementById("eyeicon");
    let password= document.getElementById("password");
     eyeicon.onclick = function(){
        if(password.type == "password"){
            password.type = "text";
            eyeicon.src= "https://icons.veryicon.com/png/o/miscellaneous/myfont/eye-open-4.png";
        }
        else{
            password.type = "password";
            eyeicon.src= "https://static.thenounproject.com/png/1035969-200.png";
        }
     }
     </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('ServiceWorker registrado con éxito:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Fallo el registro de ServiceWorker:', error);
                    });
            });
        }
    </script>

</body>
</html>