const CACHE_NAME = 'valve-control-v1';
const urlsToCache = [
    '/',
    '/index.php', // CodeIgniter usa index.php por defecto
    '/css/detalle_dispositivo.css',
    '/js/detalle_dispositivo.js',
    '/manifest.json',
    // Asegúrate de añadir tus iconos aquí si los estás utilizando
    '/imagenes/Logo.png' // Asegúrate de que esta ruta sea correcta
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Cacheando archivos estáticos');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', event => {
    // Para todas las peticiones, intentar servir desde la caché primero
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request);
            })
            .catch(error => {
                console.error('Error al recuperar:', error);
                // Aquí podrías servir una página offline genérica si lo deseas,
                // si la petición de la API falla por estar offline, por ejemplo.
                // return caches.match('/offline.html');
            })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Eliminando caché antigua:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});
