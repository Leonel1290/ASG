const CACHE_NAME = 'asg-pwa-cache-v4'; // <--- ¡AUMENTA LA VERSIÓN DEL CACHE! (ej. v4)
const urlsToCache = [
  '/', // La raíz de tu aplicación
  '/inicio',
  '/login',
  '/loginobtener',
  '/perfil',
  '/perfil/configuracion',
  '/instalar-pwa', // <-- Asegúrate de que esta URL esté aquí
  '/manifest.json', // <-- Asegúrate de que esta URL esté aquí
  
  // Archivos CSS de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
  'https://cdn.tailwindcss.com',

  // Imágenes en public/css/
  '/css/90514.jpg',
  '/css/91639.jpg',
  '/css/usuario.png',

  // Archivos JavaScript de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',

  // Imágenes en public/imagenes/
  '/imagenes/Logo.png',
  '/imagenes/detector.jpg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Cache abierta.');
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Service Worker: Fallo al cachear URLs:', error);
      })
  );
});

self.addEventListener('fetch', event => {
  if (event.request.url.startsWith(self.location.origin)) {
    event.respondWith(
      caches.match(event.request)
        .then(response => {
          if (response) {
            return response;
          }
          return fetch(event.request).catch(() => {
            console.log('Service Worker: Fallo al cargar desde caché y red para', event.request.url);
          });
        })
    );
  }
});

self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            console.log('Service Worker: Borrando caché antigua:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
