// app.js
class Notificaciones {
  static async inicializar() {
    if ('serviceWorker' in navigator && 'PushManager' in window) {
      try {
        // Registrar Service Worker
        const registro = await navigator.serviceWorker.register('/sw.js');
        console.log('Service Worker registrado');
        
        // Solicitar permisos
        const permiso = await Notification.requestPermission();
        if (permiso === 'granted') {
          console.log('Permisos de notificación concedidos');
          return true;
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }
    return false;
  }

  // Simular notificación de prueba
  static async enviarNotificacionPrueba() {
    if ('serviceWorker' in navigator) {
      const registro = await navigator.serviceWorker.ready;
      registro.active.postMessage({
        type: 'PUSH',
        title: 'Prueba de Alerta',
        body: '¡Esta es una prueba de fuga de gas!'
      });
    }
  }
}

// Inicializar cuando cargue la app
window.addEventListener('load', () => {
  Notificaciones.inicializar();
});