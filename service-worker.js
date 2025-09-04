self.addEventListener("push", function(event){
    const data = event.data.json();
    const opciones = {
        body: data.body,
        icon: "/imagenes/Logo.png",
        badge: "/imagenes/Logo.png",
        vibrate: [200,100,200],
        data: { url: data.url || "/" }
    };
    event.waitUntil(self.registration.showNotification(data.title, opciones));
});

self.addEventListener("notificationclick", function(event){
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
self.addEventListener('push', event => {
  const data = event.data.json();

  const options = {
    body: data.body,
    icon: '/icono.png',      // icono de la notificación
    badge: '/badge.png',     // icono pequeño para móviles
    vibrate: [100, 50, 100], // vibración
    data: { url: '/' }       // info que quieras pasar al click
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});
