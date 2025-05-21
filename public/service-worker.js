const CACHE_NAME = 'asg-pwa-cache-v3'; // <--- INCREMENTA LA VERSIÓN DEL CACHE (¡Importante para que los cambios se apliquen!)
const urlsToCache = [
  '/', // La raíz de tu aplicación
  '/inicio', // <--- AÑADIDA LA PÁGINA DE INICIO
  '/login',
  '/loginobtener', // Página de login
  '/perfil', // Ruta común para el perfil, si es relevante
  '/perfil/configuracion', // Página de configuración
  '/instalar-pwa', // <-- NUEVO: Añadir la página de instalación de PWA
  '/manifest.json', // <-- NUEVO: Añadir el propio manifest.json
  // Otros endpoints importantes que quieras que funcionen offline
  // Por ejemplo, si tienes una ruta para ver dispositivos: '/perfil/dispositivos'
  // Si tienes una página de error o offline específica: '/offline.html'

  // Archivos CSS de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
  'https://cdn.tailwindcss.com', // <-- Asegúrate de que Tailwind CDN esté en la caché

  // Imágenes en public/css/
  '/css/90514.jpg',
  '/css/91639.jpg',
  '/css/usuario.png',

  // Archivos JavaScript de CDN
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', // Corregido: 5.3.0 para que coincida con CSS

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
  // Aseguramos que solo interceptamos solicitudes HTTP/HTTPS y no extensiones u otros esquemas
  if (event.request.url.startsWith(self.location.origin)) {
    event.respondWith(
      caches.match(event.request)
        .then(response => {
          // Si el recurso está en caché, lo devolvemos
          if (response) {
            return response;
          }
          // Si no está en caché, intentamos obtenerlo de la red
          return fetch(event.request).catch(() => {
            // Si la red también falla y el recurso no está en caché, puedes devolver una página offline.
            // Para eso, necesitarías tener una página offline pre-cacheada (ej: 'offline.html')
            // if (event.request.mode === 'navigate') { // Para solicitudes de navegación
            //   return caches.match('/offline.html'); // Asume que tienes un offline.html cacheado
            // }
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
