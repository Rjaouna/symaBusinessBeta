const CACHE_NAME = "symboost-cache-v1";
const urlsToCache = [
  "/",
  "/index.php", // Si vous utilisez Symfony avec le contrôleur par défaut
  "/manifest.json",
  "/icons/icon-192x192.png",
  "/icons/icon-512x512.png",
  "/assets/css/bootstrap.css",
  "/assets/js/bootstrap.bundle.min.js",
  // Ajoutez ici d'autres ressources essentielles à mettre en cache
];

// Installation du service worker et mise en cache des ressources
self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log("Cache ouvert");
      return cache.addAll(urlsToCache);
    })
  );
});

// Activation du service worker et nettoyage des caches anciens
self.addEventListener("activate", (event) => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (!cacheWhitelist.includes(cacheName)) {
            console.log("Suppression du cache:", cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interception des requêtes pour servir les ressources depuis le cache
self.addEventListener("fetch", (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      // Ressource trouvée dans le cache
      if (response) {
        return response;
      }
      // Ressource non trouvée dans le cache, la télécharger
      return fetch(event.request);
    })
  );
});
