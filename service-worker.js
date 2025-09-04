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
