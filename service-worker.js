const CACHE_NAME = 'asg-pwa-cache-v3'; // <--- VERSIÓN DEL CACHÉ
const urlsToCache = [
  '/',
  '/inicio', // <--- Asegúrate de que esta ruta esté cacheada
  '/loginobtener',
  '/register',
  '/forgotpassword',
  '/perfil',
  '/perfil/configuracion',
  '/comprar',
  '/detalles',
  '/dispositivos',
  '/cambio_exitoso',
  '/verificar_email',
  '/reset_password',

  // Archivos CSS de tu proyecto
  '/css/login.css',
  '/css/forgot.css',
  '/css/register.css',
  '/css/reset.css',

  // Imágenes de tu proyecto (asegúrate de que estas rutas son correctas según tu estructura en Render)
  '/imagenes/Logo.png',
  '/imagenes/detector.jpg',
  '/imagenes/usuario.png',
  '/css/90514.jpg', // Revisa si estas imágenes están realmente en la carpeta /css/
  '/css/91639.jpg', // Revisa si estas imágenes están realmente en la carpeta /css/

  // Archivos CSS de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',

  // Archivos JavaScript de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js',
  'https://code.jquery.com/jquery-3.6.0.min.js',
  'https://www.paypal.com/sdk/js?client-id=Aaf4oThh4f97w4hkRqUL7QgtSSHKTpruCpklUqcwWhotqUyLbCMnGXQgwqNEvv-LZ9TnVHTdIH5FECk0&currency=USD'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Abriendo caché y añadiendo URLs');
        return cache.addAll(urlsToCache).catch(error => {
          console.error('Service Worker: Fallo al cachear algunas URLs:', error);
        });
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
