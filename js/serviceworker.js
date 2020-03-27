var CACHE_NAME ='pwa';

/*
あなたが、別ファイルで、js、html、css、imageファイルを用意している場合は
下記のurlsToCache内に、同じように追加をする必要があります。
*/

var urlsToCache = [
  '/',
  '/index.html',
  '/style.css',
  '/js/sample.js',
  '/images/icons/icon.png',
  '/favicon.ico'
];

//下記はPWAを作成するうえで、必要な処理です。

self.addEventListener('install', function(event) {
  // インストール処理
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Opened cache');
        return cache.addAll(urlsToCache.map(url => new Request(url, {credentials: 'same-origin'})));
      })
  );
});
 
self.addEventListener('activate',  function(event) {
  event.waitUntil(self.clients.claim());
});
 
self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request, {ignoreSearch:true}).then(function(response) {
      return response || fetch(event.request);
    })
  );
});