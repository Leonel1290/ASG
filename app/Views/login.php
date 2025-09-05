<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" href="<?= base_url('css/login.css') ?>">
<link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
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

      <?php if (session()->get('error')): ?>
        <div class="alert alert-danger"><?= session()->get('error') ?></div>
      <?php endif; ?>
      <?php if (session()->get('success')): ?>
        <div class="alert alert-success"><?= session()->get('success') ?></div>
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
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fas fa-sign-in-alt"></i> Ingresar
        </button>
      </form>

      <div class="text-center mt-3">
        <br><a href="<?= base_url('/forgotpassword') ?>">¿Olvidaste tu contraseña?</a></br>
        <a href="<?= base_url('/register') ?>">Crear cuenta nueva</a>
      </div>
    </div>
  </div>
</div>

<script>
class BiometricAuth {
  constructor() {
    this.biometricKey = 'asg_biometric_token';
    this.isAvailable = (typeof PublicKeyCredential !== 'undefined');
  }

  hasStoredCredentials() {
    return localStorage.getItem(this.biometricKey) !== null;
  }

  async authenticate() {
    if (!this.isAvailable || !this.hasStoredCredentials()) return false;
    const { token } = JSON.parse(localStorage.getItem(this.biometricKey));

    try {
      const assertion = await navigator.credentials.get({
        publicKey: {
          challenge: new Uint8Array(32),
          allowCredentials: [{ type: 'public-key', id: new Uint8Array(16), transports: ['internal'] }],
          userVerification: 'required'
        }
      });

      if (assertion) {
        const res = await fetch('/biometric-login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ biometric_token: token })
        });
        const result = await res.json();
        if (result.success) window.location.href = result.redirect;
        return result.success;
      }
    } catch(e) {
      console.error("Autenticación biométrica fallida:", e);
    }
    return false;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const biometricAuth = new BiometricAuth();

  // Intentar login biométrico automáticamente
  if (biometricAuth.isAvailable && biometricAuth.hasStoredCredentials()) {
    biometricAuth.authenticate();
  }

  // Toggle password
  document.getElementById('toggle-password').addEventListener('click', function() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
    this.querySelector('i').classList.toggle('fa-eye-slash');
  });

  // Login tradicional
  document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    try {
      const res = await fetch('/login', { method: 'POST', body: formData });
      const result = await res.json();
      if (result.success) window.location.href = result.redirect || '/perfil';
      else showError(result.message || 'Credenciales incorrectas');
    } catch (err) {
      console.error(err); showError('Error de conexión');
    }
  });

  function showError(msg) {
    let errorDiv = document.getElementById('form-error');
    if (!errorDiv) {
      errorDiv = document.createElement('div');
      errorDiv.id = 'form-error';
      errorDiv.className = 'alert alert-danger mt-3';
      document.querySelector('form').appendChild(errorDiv);
    }
    errorDiv.textContent = msg;
    errorDiv.style.display = 'block';
  }
});
</script>

<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
      .then(reg => console.log('ServiceWorker registrado:', reg.scope))
      .catch(err => console.log('Fallo ServiceWorker:', err));
  });
}
</script>

</body>
</html>
