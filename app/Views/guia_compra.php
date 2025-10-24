<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guía de Compra - ASG</title>
    <link rel="shortcut icon" href="<?= base_url('/imagenes/Logo.png'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #0A192F;
            --primary-medium: #0D203B;
            --primary-light: #1A3E5C;
            --accent: #36678C;
            --accent-hover: #2A5173;
            --text-primary: #FFFFFF;
            --text-secondary: #AFB3B7;
            --text-hover: #8CA9B9;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-medium));
            font-family: 'Poppins', sans-serif;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .guide-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .guide-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 2rem;
        }

        .guide-header h1 {
            color: var(--text-primary);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .guide-header .subtitle {
            font-size: 1.2rem;
            color: var(--text-hover);
            max-width: 600px;
            margin: 0 auto;
        }

        .step-card {
            background: rgba(26, 62, 92, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(54, 103, 140, 0.2);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: rgba(54, 103, 140, 0.4);
        }

        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            color: white;
            border-radius: 50%;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .step-title {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .step-content {
            margin-bottom: 1.5rem;
        }

        .step-actions {
            background: rgba(10, 25, 47, 0.5);
            padding: 1rem;
            border-radius: 10px;
            border-left: 4px solid var(--accent);
        }

        .step-actions h6 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .icon-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .icon-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: flex-start;
        }

        .icon-list i {
            color: var(--accent);
            margin-right: 0.75rem;
            margin-top: 0.2rem;
            flex-shrink: 0;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .status-important {
            background: var(--warning);
            color: #000;
        }

        .status-success {
            background: var(--success);
            color: white;
        }

        .status-info {
            background: var(--info);
            color: white;
        }

        .flow-diagram {
            background: rgba(26, 62, 92, 0.5);
            border-radius: 15px;
            padding: 2rem;
            margin: 3rem 0;
            text-align: center;
            border: 1px solid rgba(54, 103, 140, 0.3);
        }

        .flow-step {
            display: inline-block;
            background: var(--primary-light);
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin: 0 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
            position: relative;
        }

        .flow-arrow {
            display: inline-block;
            color: var(--accent);
            font-size: 1.5rem;
            margin: 0 0.5rem;
        }

        .checklist {
            background: rgba(26, 62, 92, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 3rem;
            border: 1px solid rgba(54, 103, 140, 0.2);
        }

        .checklist h3 {
            color: var(--text-primary);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(54, 103, 140, 0.2);
        }

        .checklist-item:last-child {
            border-bottom: none;
        }

        .checklist-item input[type="checkbox"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }

        .checklist-item label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .checklist-important {
            background: rgba(255, 193, 7, 0.1);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin: 0.5rem 0;
            border-left: 4px solid var(--warning);
        }

        .btn-guide {
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            border: none;
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }

        .btn-guide:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(54, 103, 140, 0.4);
            color: white;
        }

        .btn-outline-guide {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            font-weight: 600;
            border-radius: 30px;
            padding: 0.8rem 2rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }

        .btn-outline-guide:hover {
            background: var(--accent);
            color: white;
        }

        @media (max-width: 768px) {
            .guide-container {
                padding: 1rem;
            }
            
            .flow-step {
                display: block;
                margin: 0.5rem 0;
            }
            
            .flow-arrow {
                display: block;
                transform: rotate(90deg);
                margin: 0.5rem 0;
            }
            
            .step-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="guide-container">
    <!-- Header -->
    <div class="guide-header">
        <h1>🛒 Guía Completa de Compra ASG</h1>
        <p class="subtitle">Sigue estos pasos simples para adquirir tu ASG Sentinel y proteger tu hogar</p>
    </div>

    <!-- Paso 1 -->
    <div class="step-card">
        <div class="step-number">1</div>
        <h3 class="step-title">Acceder a la Tienda</h3>
        <div class="step-content">
            <p>Navega hasta la sección <strong>"Comprar"</strong> en nuestra aplicación o sitio web ASG</p>
            <ul class="icon-list">
                <li><i class="fas fa-store"></i> Encuentra el producto <strong>"ASG Sentinel"</strong></li>
                <li><i class="fas fa-info-circle"></i> Revisa las características y especificaciones</li>
                <li><i class="fas fa-tag"></i> Precio: <strong>$100 USD</strong></li>
            </ul>
        </div>
    </div>

    <!-- Paso 2 -->
    <div class="step-card">
        <div class="step-number">2</div>
        <h3 class="step-title">Verificación de Cuenta</h3>
        <div class="step-content">
            <p>El sistema verificará automáticamente tu estado de sesión:</p>
            
            <div class="step-actions">
                <h6>✅ Si YA estás logueado:</h6>
                <p>Continúa directamente al proceso de pago</p>
            </div>
            
            <div class="step-actions mt-3">
                <h6>🔐 Si NO estás logueado:</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-redo"></i> Serás redirigido automáticamente al login</li>
                    <li><i class="fas fa-save"></i> Tu progreso de compra se guarda automáticamente</li>
                    <li><i class="fas fa-sign-in-alt"></i> Inicia sesión con tu email y contraseña</li>
                    <li><i class="fas fa-arrow-right"></i> Volverás automáticamente a la página de compra</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Paso 3 -->
    <div class="step-card">
        <div class="step-number">3</div>
        <h3 class="step-title">Realizar el Pago</h3>
        <div class="step-content">
            <ul class="icon-list">
                <li><i class="fab fa-paypal"></i> Haz clic en el botón <strong>"Pago con PayPal"</strong></li>
                <li><i class="fas fa-shield-alt"></i> Serás dirigido a la plataforma segura de PayPal</li>
                <li><i class="fas fa-credit-card"></i> Completa el proceso de pago en PayPal</li>
                <li><i class="fas fa-check-circle"></i> Una vez confirmado, volverás automáticamente a ASG</li>
            </ul>
        </div>
    </div>

    <!-- Paso 4 -->
    <div class="step-card">
        <div class="step-number">4</div>
        <h3 class="step-title">Confirmación de Compra <span class="status-badge status-important">CRÍTICO</span></h3>
        <div class="step-content">
            <p>Aparecerá un <strong>modal de éxito</strong> con toda la información importante:</p>
            
            <div class="step-actions">
                <h6>📋 Elementos del Modal:</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-check-circle text-success"></i> <strong>Icono de verificación</strong> verde</li>
                    <li><i class="fas fa-bullhorn"></i> <strong>Mensaje:</strong> "¡Compra Completada con Éxito!"</li>
                    <li><i class="fas fa-key"></i> <strong>Payment ID:</strong> Tu código único de identificación</li>
                    <li><i class="fas fa-copy"></i> <strong>Botón "Copiar":</strong> Para copiar fácilmente tu Payment ID</li>
                </ul>
            </div>
            
            <div class="step-actions mt-3">
                <h6>⚠️ ACCIÓN IMPORTANTE:</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-copy"></i> <strong>COPIA tu Payment ID</strong> inmediatamente</li>
                    <li><i class="fas fa-save"></i> <strong>GUARDA este ID</strong> en un lugar seguro</li>
                    <li><i class="fas fa-exclamation-triangle"></i> <strong>Este ID es NECESARIO</strong> para completar el registro de envío</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Paso 5 -->
    <div class="step-card">
        <div class="step-number">5</div>
        <h3 class="step-title">Registrar Dirección de Envío</h3>
        <div class="step-content">
            <p>Tienes <strong>DOS opciones</strong> para registrar tu dirección:</p>
            
            <div class="step-actions">
                <h6>🎯 Opción A - Desde el Modal de Éxito:</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-mouse-pointer"></i> Haz clic en <strong>"Ir a Mis Compras"</strong> en el modal</li>
                    <li><i class="fas fa-arrow-right"></i> Serás dirigido directamente a la gestión de compras</li>
                </ul>
            </div>
            
            <div class="step-actions mt-3">
                <h6>📱 Opción B - Desde el Menú Principal:</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-user"></i> Ve a tu <strong>Perfil</strong> (icono de usuario)</li>
                    <li><i class="fas fa-receipt"></i> Selecciona <strong>"Mis Compras"</strong> en el menú</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Paso 6 -->
    <div class="step-card">
        <div class="step-number">6</div>
        <h3 class="step-title">Completar Información de Envío</h3>
        <div class="step-content">
            <p>En la página <strong>"Mis Compras"</strong> completa los siguientes pasos:</p>
            
            <div class="step-actions">
                <h6>📝 Formulario de Dirección (Sección Izquierda):</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-key"></i> Ingresa tu <strong>Payment ID</strong> (el que copiaste)</li>
                    <li><i class="fas fa-user"></i> <strong>Nombre y Apellido</strong> (* obligatorio)</li>
                    <li><i class="fas fa-phone"></i> <strong>Teléfono</strong> (* obligatorio)</li>
                    <li><i class="fas fa-map-marker-alt"></i> <strong>Dirección completa</strong> (* obligatorio)</li>
                    <li><i class="fas fa-city"></i> <strong>Ciudad y Provincia</strong> (* obligatorio)</li>
                    <li><i class="fas fa-envelope"></i> <strong>Código Postal</strong> (* obligatorio)</li>
                    <li><i class="fas fa-info-circle"></i> <strong>Referencias</strong> (opcional pero recomendado)</li>
                </ul>
            </div>
            
            <div class="step-actions mt-3">
                <h6>👀 Verificación (Sección Derecha):</h6>
                <ul class="icon-list">
                    <li><i class="fas fa-check-circle"></i> Verifica tu <strong>orden de compra</strong> confirmada</li>
                    <li><i class="fas fa-status"></i> Estado: <strong>"COMPLETED"</strong> o <strong>"APROBADA"</strong></li>
                    <li><i class="fas fa-save"></i> Haz clic en <strong>"Guardar Dirección de Envío"</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Paso 7 -->
    <div class="step-card">
        <div class="step-number">7</div>
        <h3 class="step-title">Confirmación Final</h3>
        <div class="step-content">
            <ul class="icon-list">
                <li><i class="fas fa-check-double text-success"></i> Aparecerá un mensaje de <strong>éxito</strong> confirmando que tu dirección fue guardada</li>
                <li><i class="fas fa-list"></i> Tu orden ahora aparece en la lista de <strong>"Mis Compras"</strong> con la dirección completa</li>
                <li><i class="fas fa-envelope"></i> Recibirás un <strong>email de confirmación</strong> con todos los detalles de tu compra</li>
                <li><i class="fas fa-shipping-fast"></i> Tu pedido está en camino a procesamiento</li>
            </ul>
        </div>
    </div>

    <!-- Diagrama de Flujo -->
    <div class="flow-diagram">
        <h4 class="text-primary mb-4">🔄 Resumen Visual del Flujo</h4>
        <div class="flow-step">Inicio</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Comprar</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">[¿Sesión? NO → Login]</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Pago PayPal</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Modal Éxito</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Copiar Payment ID</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Mis Compras</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Ingresar Payment ID</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Completar Dirección</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step">Guardar</div>
        <div class="flow-arrow">→</div>
        <div class="flow-step text-success">✅ Compra Registrada</div>
    </div>

    <!-- Tiempos y Soporte -->
    <div class="row">
        <div class="col-md-6">
            <div class="step-card">
                <h4 class="step-title">⏰ Tiempos Estimados</h4>
                <ul class="icon-list">
                    <li><i class="fas fa-clock"></i> <strong>Proceso de pago:</strong> 2-5 minutos</li>
                    <li><i class="fas fa-address-card"></i> <strong>Registro de dirección:</strong> 3-5 minutos</li>
                    <li><i class="fas fa-shipping-fast"></i> <strong>Procesamiento de envío:</strong> 2-5 días hábiles</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="step-card">
                <h4 class="step-title">📞 Soporte</h4>
                <p>Si encuentras algún problema durante el proceso:</p>
                <ul class="icon-list">
                    <li><i class="fas fa-key"></i> <strong>Guarda tu Payment ID</strong></li>
                    <li><i class="fas fa-headset"></i> <strong>Contacta a soporte</strong> en: againsafegas.ascii@gmail.com</li>
                    <li><i class="fas fa-bullhorn"></i> <strong>Proporciona</strong> tu Payment ID para atención rápida</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Checklist -->
    <div class="checklist">
        <h3>✅ Checklist del Usuario</h3>
        
        <div class="checklist-important">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>IMPORTANTE:</strong> No olvides copiar y guardar tu Payment ID
        </div>
        
        <div class="checklist-item">
            <input type="checkbox" id="check1">
            <label for="check1">Tengo mi cuenta de ASG verificada</label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check2">
            <label for="check2">Inicié sesión correctamente</label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check3">
            <label for="check3">Completé el pago en PayPal</label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check4">
            <label for="check4"><strong>COPIASTE Y GUARDASTE tu Payment ID</strong></label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check5">
            <label for="check5">Registré mi dirección de envío en "Mis Compras"</label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check6">
            <label for="check6">Recibí el email de confirmación</label>
        </div>
        <div class="checklist-item">
            <input type="checkbox" id="check7">
            <label for="check7">Mi orden aparece en "Mis Compras" con dirección completa</label>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="text-center mt-5">
        <a href="<?= base_url('/comprar') ?>" class="btn btn-guide">
            <i class="fas fa-shopping-cart me-2"></i> Comenzar Compra
        </a>
        <a href="<?= base_url('/mis_compras') ?>" class="btn btn-outline-guide">
            <i class="fas fa-receipt me-2"></i> Ir a Mis Compras
        </a>
        <a href="<?= base_url('/loginobtener') ?>" class="btn btn-outline-guide">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </a>
    </div>

    <!-- Consejo Final -->
    <div class="text-center mt-4">
        <p class="text-muted">
            <i class="fas fa-lightbulb text-warning me-1"></i>
            <strong>Consejo Importante:</strong> Siempre guarda tu Payment ID, es tu referencia principal para cualquier consulta sobre tu compra y envío.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>