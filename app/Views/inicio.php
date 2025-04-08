<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASG - Seguridad en tu Hogar</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style> /* Global Styles */
 body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: white;
    overflow-x: hidden;
    color: #333;
    min-height: 100vh;

}

h1, h3, p {
    margin: 0;
}

.navbar {
    position: absolute;
    top: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0px;
    backdrop-filter: blur(10px);
    z-index: 1000;
 
}

/* Línea degradada abajo */
.navbar::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    width: 100%;
    background: linear-gradient(to left, #ff416c, #ff4b2b); /* Degradado de derecha a izquierda */
}
.logo {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 42px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    background: linear-gradient(to left, #ff416c, #ff4b2b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    height: 100%;
}
.nav-links {
    list-style: none;
    display: flex;
    gap: 25px;
    position: relative;
    right: 40px;
}

.nav-links li {
    position: relative;
}
.social-links {
      display: flex;
      gap: 20px;
      margin-left: 40px;
    }

    .social-links a {
      font-size: 30px;
      color: white;
      transition: all 0.3s ease;
    }

    .social-links a.instagram:hover i {
      background: linear-gradient(45deg, #f9ce34, #ee2a7b, #6228d7);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      transform: scale(1.2);
    }

    .social-links a.youtube:hover {
      color: #ff0000;
      transform: scale(1.2);
    }

.nav-links a {
    text-decoration: none;
    color: white;
    font-size: 18px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    letter-spacing: 1px;
    padding: 10px 15px;
    transition: transform 0.3s ease;
    position: relative;
}

.nav-links a::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: -5px;
    width: 0;
    height: 3px;
    background: linear-gradient(to left, #ff416c, #ff4b2b);
    transition: all 0.3s ease;
    transform: translateX(-50%);
    border-radius: 2px;
}

.nav-links a:hover::after {
    width: 100%;
}

.menu-toggle {
    display: none;
    width: 30px;
    height: 23px;
    flex-direction: column;
    justify-content: space-between;
    cursor: pointer;
    position: relative;
    right: 30px;
}

.menu-toggle span {
    display: block;
    height: 4px;
    width: 100%;
    background: white;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
}
/* Hero Section */
.header {
    position: relative;
    height: 100vh;
    color: white;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #0a0a0a, #34495e8e);
     /* Modern gradient background */

}

.header-c {
    animation: zoomInImage 1.5s ease-out forwards;
}

.header img {
    width: 400px;
    height: 400px;
    border-radius: 20px;
    animation: zoomInImage 1.5s ease-out forwards;
    transition: transform 0.4s ease, box-shadow 0.4s ease;

  
}
.header img:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.69);
}
@keyframes fadeZoomIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
        filter: blur(4px);
    }
    100% {
        opacity: 1;
        transform: scale(1);
        filter: blur(0);
    }
}

.header h1 {
    font-size: 4em;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 5px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}


.header p {
    font-size: 1.5em;
    margin-bottom: 30px;
    font-weight: 300;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);
}

.boton{
    background-color: #e74c3c;
    color: white;
    padding: 15px 40px;
    border: none;
    font-size: 20px;
    cursor: pointer;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}
.boton:hover {
    background-color: #c0392b;
    transform: scale(1.1);
}

.features {
    display: flex;
    justify-content: space-around;
    margin: 50px;
    flex-wrap: wrap;
    text-align: center;
    opacity: 0;
    animation: fadeIn 1.2s 1.2s forwards;
}

.feature {
    flex: 1;
    padding: 40px;
    max-width: 300px;
    margin: 20px;
    transition: transform 0.3s ease;
    border-radius: 8px;
    background: transparent;
    margin-top: 100px;
}

.feature:hover {
    transform: translateY(-10px);
}

.feature i {
    font-size: 60px;
    color: #c0392b;
    margin-bottom: 20px;
    transition: color 0.3s ease;
}

.feature:hover i {
    color: #e74c3c;
}

.feature h3 {
    font-size: 1.5em;
    margin-bottom: 15px;
    color: #333;
}

.feature p {
    font-size: 1em;
    color: #7f8c8d;
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

.video-demo {
    text-align: center;
    padding: 60px 20px 20px;
    background: linear-gradient(to left, #34495e8e, #0a0a0a);
    margin: 0;
}

.video-demo h2 {
    font-size: 2.5em;
    margin-bottom: 30px;
    color:rgb(255, 255, 255);
}

.video-demo iframe {
    width: 80%;
    height: 500px;
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.testimonials {
    color: white;
    padding: 60px;
    text-align: center;
}

.testimonials h2 {
    font-size: 2.5em;
    margin-bottom: 30px;
    color: #2c3e50;
}

.testimonial {
    display: inline-block;
    width: 30%;
    padding: 20px;
    margin: 20px;
    background: linear-gradient(135deg, #0a0a0aa4, #34495e); /* Modern gradient background */
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.testimonial p {
    font-size: 1.1em;
    font-style: italic;
    color: #ffffff;
}

.testimonial h3 {
    margin-top: 15px;
    font-size: 1.5em;
    color: #ffffff;

}

.footer {
    background-color: #34495e;
    color: white;
    text-align: center;
    padding: 20px;
    font-size: 14px;
}
.company-info {
  background: linear-gradient(to left, #34495e8e, #0a0a0a);
        color: white;
        padding: 40px;
        margin-top: 2;
        text-align: center;
      }

      .company-info a {
        color: white;
        text-decoration: underline;
      }

      .company-info a:hover {
        color: #ccc;
      }
      .section-divider {
  height: 4px;
  background: linear-gradient(to left, #ff416c, #ff4b2b)
  margin: 10px auto;
  width: 80%;
  border-radius: 2px;
}
  
@media (max-width: 768px) {
    .header h1 {
        font-size: 1.9em;
    }

    .header p {
        font-size: 1.2em;
    }

    .features {
        flex-direction: column;
        margin: 20px;
    }

    .cta button {
        padding: 12px 30px;
    }

    .feature {
        max-width: 100%;
        margin: 10px;
    }

    .video-demo iframe {
        width: 100%;
        height: 300px;
    }

    .testimonial {
        width: 80%;
    }

    .testimonials h2 {
        font-size: 1.7em;
        margin-bottom: 30px;
        color: #2c3e50;
    }

    .nav-links {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 76%;
        left: 0;
        width: 100%;
        background: linear-gradient(135deg, #0a0a0a, #34495e); /* Modern gradient background */
        text-align: center;
        padding: 15px;
        border-radius: 16px;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
        visibility: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);

    }

    .logo {
        left: 25px;
    }

    .video-demo h2{
        font-size: 1.7rem;
    }

    .nav-links.active {
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
    }

    .nav-links li {
        margin: 10px 0;
    }

    .menu-toggle {
        display: flex;
    }

    .menu-toggle.active span:nth-child(1) {
        transform: translateY(10px) rotate(45deg);
    }

    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .menu-toggle.active span:nth-child(3) {
        transform: translateY(-10px) rotate(-45deg);
    }
    
}</style>
</head>
<body>
    <nav class="navbar">
    <div class="social-links">
    <a href="https://www.instagram.com/manu_urruttia" target="_blank" class="instagram"><i class="fab fa-instagram"></i></a>
    <a href="https://www.youtube.com/@eVILcARBONCETE" target="_blank" class="youtube"><i class="fab fa-youtube"></i></a>
  </div>
        <div class="logo">ASG</div>
        <ul class="nav-links">
            <li class="nav-item"><a class="nav-link" href="#companyInfo">¿Cómo contactarnos?</a></li>
            <li class="nav-item"><a href="<?= base_url('/loginobtener') ?>" class="btn btn-light ml-3">Inicia Sesión</a></li>
        </ul>
        
    </nav>

    <!-- Hero Section -->
    <div class="header"> 
  <div class="header-c">
  <a href="<?= base_url('/comprar') ?>">
    <img src="<?= base_url('imagenes/detector.png') ?> "href="<?= base_url('/comprar') ?>" alt="Logo ASG" style="max-width: 1000px; height: auto;">
</a>
    <h1>Protege lo que más importa</h1>
    <p>Tu hogar seguro con ASG. Detección precisa de fugas de gas en tiempo real.</p>
    <a class="boton" href="<?= base_url('/comprar') ?>">Comprar Dispositivo</a>
  </div>
</div>
    <!-- Features Section -->
    <div class="features">
        <div class="feature">
            <i class="fas fa-shield-alt"></i>
            <h3>Seguridad Total</h3>
            <p>Sistema avanzado de cierre automático de válvulas para garantizar tu seguridad.</p>
        </div>
        <div class="feature">
            <i class="fas fa-line-chart"></i>
            <h3>Monitoreo Remoto</h3>
            <p>Controla y monitorea las fugas desde tu dispositivo móvil con nuestra app.</p>
        </div>
        <div class="feature">
            <i class="fas fa-bell"></i>
            <h3>Alertas en Tiempo Real</h3>
            <p>Recibe notificaciones instantáneas cuando detectemos cualquier fuga de gas.</p>
        </div>
    </div>

<!-- Contacto -->
    <div class="container mt-5" id="companyInfo">
      <div class="company-info mt-5">
        <h2>Contacto</h2>
        <p><strong>Empresa:</strong> ASG</p>
        <p><strong>Dirección:</strong> Río Tercero</p>
        <p><strong>Teléfono:</strong> 3571-623889</p>
        <p><strong>Email:</strong> againsafegas.ascii@gmail.com</p>
        <p><strong>Sitio Web:</strong> <a href="https://www.gassafe.com">www.Againsafegas.com</a></p>
      </div>
      <div class="section-divider"></div>
    </div>


    <!-- Video Demo Section -->
    <div class="video-demo">
    <h2>¡NOSOTROS!</h2>
    <iframe width="1920" height="" 
        src="https://www.youtube.com/embed/Uw6_4XlYMF4" 
        title="Video de demostración" 
        frameborder="0" 
        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; web-share" 
        allowfullscreen>
    </iframe>
</div>

    
    

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 AgainSafeGas Solutions | Todos los derechos reservados.</p>
    </div>

    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    </script>
</body>
</html>