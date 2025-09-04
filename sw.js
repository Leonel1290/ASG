// sw.js
self.addEventListener('push', function(event) {
  const options = {
    body: event.data ? event.data.text() : '¡Alerta de fuga de gas detectada!',
    icon: '/icon-192.png',
    badge: '/icon-72.png',
    vibrate: [200, 100, 200],
    tag: 'gas-leak-alert'
  };

  event.waitUntil(
    self.registration.showNotification('Alerta de Gas', options)
  );
});

self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  event.waitUntil(
    clients.openWindow('/') // Redirige a tu app
  );
});