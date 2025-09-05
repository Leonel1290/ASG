<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <img src="<?= base_url('/imagenes/Logo.png'); ?>" alt="Logo ASG" class="mb-3" style="height: 80px;">
            <h1 class="h3">Iniciar Sesión</h1>
          </div>
          
          <!-- Mostrar mensajes de error/success -->
          <?php if (session()->get('error')): ?>
            <div class="alert alert-danger" role="alert">
              <?= session()->get('error') ?>
            </div>
          <?php endif; ?>
          
          <?php if (session()->get('success')): ?>
            <div class="alert alert-success" role="alert">
              <?= session()->get('success') ?>
            </div>
          <?php endif; ?>

          <form id="login-form">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="password">Contraseña</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" required>
                <div class="input-group-append">
                  <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                    <i class="fas fa-eye"></i>
                  </span>
                </div>
              </div>
            </div>
            
            <div class="form-group form-check" id="biometric-option" style="display: none;">
              <input type="checkbox" class="form-check-input" id="use_biometric" name="use_biometric">
              <label class="form-check-label" for="use_biometric">
                Habilitar inicio de sesión con huella digital/reconocimiento facial
              </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-sign-in-alt"></i> Ingresar
            </button>
          </form>
          
          <div id="biometric-login-btn" class="mt-3" style="display: none;">
            <button class="btn btn-outline-primary btn-block" id="biometric-login-button">
              <i class="fas fa-fingerprint"></i> Iniciar con huella digital
            </button>
          </div>
          
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
    
    <!-- Script para autenticación biométrica -->
    <script>
      class BiometricAuth {
        constructor() {
          this.biometricKey = 'asg_biometric_token';
          this.isAvailable = this.checkBiometricSupport();
        }

        checkBiometricSupport() {
          return (typeof PublicKeyCredential !== 'undefined' && 
                 (typeof navigator.credentials !== 'undefined' && 
                  typeof navigator.credentials.create !== 'undefined')) || 
                 (typeof window.PasswordCredential !== 'undefined') ||
                 (typeof window.FederatedCredential !== 'undefined');
        }

        // Registrar credenciales biométricas
        async registerBiometric(email, token) {
          if (!this.isAvailable) {
            return false;
          }

          try {
            // Almacenar token localmente para autenticación futura
            localStorage.setItem(this.biometricKey, JSON.stringify({
              email: email,
              token: token,
              timestamp: new Date().getTime()
            }));

            if (typeof PublicKeyCredential !== 'undefined') {
              // Intentar usar WebAuthn para autenticación biométrica
              const publicKey = {
                challenge: new Uint8Array(32),
                rp: { name: "ASG App" },
                user: {
                  id: new Uint8Array(16),
                  name: email,
                  displayName: email
                },
                pubKeyCredParams: [{ type: "public-key", alg: -7 }]
              };

              const credential = await navigator.credentials.create({ publicKey });
              if (credential) {
                console.log("Credencial biométrica registrada");
                return true;
              }
            }

            return true;
          } catch (error) {
            console.error("Error registrando autenticación biométrica:", error);
            return false;
          }
        }

        // Autenticar con biométricos
        async authenticate() {
          if (!this.isAvailable) {
            return false;
          }

          const stored = localStorage.getItem(this.biometricKey);
          if (!stored) {
            return false;
          }

          const { email, token } = JSON.parse(stored);

          try {
            if (typeof PublicKeyCredential !== 'undefined') {
              // Intentar autenticación con WebAuthn
              const assertion = await navigator.credentials.get({
                publicKey: {
                  challenge: new Uint8Array(32),
                  allowCredentials: [{
                    type: 'public-key',
                    id: new Uint8Array(16),
                    transports: ['internal']
                  }],
                  userVerification: 'required'
                }
              });

              if (assertion) {
                // Autenticación biométrica exitosa, usar el token almacenado
                const response = await fetch('/biometric-login', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                  },
                  body: JSON.stringify({ biometric_token: token })
                });

                const result = await response.json();
                if (result.success) {
                  window.location.href = result.redirect;
                  return true;
                }
              }
            } else {
              // Fallback: usar el token directamente
              const response = await fetch('/biometric-login', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify({ biometric_token: token })
              });

              const result = await response.json();
              if (result.success) {
                window.location.href = result.redirect;
                return true;
              }
            }
          } catch (error) {
            console.error("Error en autenticación biométrica:", error);
            this.showError('Error en autenticación biométrica. Por favor, use su email y contraseña.');
            return false;
          }
        }

        // Verificar si hay credenciales biométricas almacenadas
        hasStoredCredentials() {
          return localStorage.getItem(this.biometricKey) !== null;
        }

        // Eliminar credenciales biométricas
        removeCredentials() {
          localStorage.removeItem(this.biometricKey);
          return true;
        }
        
        showError(message) {
          // Crear o mostrar elemento de error
          let errorDiv = document.getElementById('biometric-error');
          if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'biometric-error';
            errorDiv.className = 'alert alert-danger mt-3';
            document.querySelector('#biometric-login-btn').after(errorDiv);
          }
          errorDiv.textContent = message;
          errorDiv.style.display = 'block';
          
          // Ocultar después de 5 segundos
          setTimeout(() => {
            errorDiv.style.display = 'none';
          }, 5000);
        }
      }

      // Inicializar cuando el DOM esté listo
      document.addEventListener('DOMContentLoaded', function() {
        window.biometricAuth = new BiometricAuth();
        
        // Mostrar opción biométrica si está disponible
        if (window.biometricAuth.isAvailable) {
          const biometricOption = document.getElementById('biometric-option');
          if (biometricOption) {
            biometricOption.style.display = 'block';
          }
          
          // Si ya hay credenciales almacenadas, mostrar botón de login biométrico
          if (window.biometricAuth.hasStoredCredentials()) {
            const biometricLoginBtn = document.getElementById('biometric-login-btn');
            if (biometricLoginBtn) {
              biometricLoginBtn.style.display = 'block';
            }
          }
        }
        
        // Configurar toggle de contraseña
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          // Cambiar icono
          const icon = this.querySelector('i');
          icon.classList.toggle('fa-eye');
          icon.classList.toggle('fa-eye-slash');
        });
        
        // Configurar login biométrico
        const biometricLoginButton = document.getElementById('biometric-login-button');
        if (biometricLoginButton) {
          biometricLoginButton.addEventListener('click', function() {
            window.biometricAuth.authenticate();
          });
        }
        
        // Configurar envío del formulario
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
          loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const useBiometric = document.getElementById('use_biometric').checked;
            
            try {
              const response = await fetch('/login', {
                method: 'POST',
                body: formData
              });
              
              if (response.ok) {
                const result = await response.json();
                
                if (useBiometric && result.biometric_token) {
                  // Registrar autenticación biométrica
                  const email = document.getElementById('email').value;
                  await window.biometricAuth.registerBiometric(email, result.biometric_token);
                }
                
                window.location.href = result.redirect || '/perfil';
              } else {
                // Manejar error
                const error = await response.text();
                showFormError('Error: ' + error);
              }
            } catch (error) {
              console.error('Error:', error);
              showFormError('Error de conexión');
            }
          });
        }
        
        function showFormError(message) {
          // Crear o mostrar elemento de error
          let errorDiv = document.getElementById('form-error');
          if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'form-error';
            errorDiv.className = 'alert alert-danger mt-3';
            document.querySelector('form').appendChild(errorDiv);
          }
          errorDiv.textContent = message;
          errorDiv.style.display = 'block';
        }
      });
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