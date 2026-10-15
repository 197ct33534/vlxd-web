/**
 * Zalo Mini App entry (skeleton).
 * Production: host this folder as Mini App start URL, or point Mini App
 * webview directly to /m (same Laravel portal + session auth by phone).
 */
(function () {
  var portalPath = '/m';
  var params = new URLSearchParams(window.location.search);
  var debug = document.getElementById('debug');
  var openBtn = document.getElementById('open-portal');
  var nativeBtn = document.getElementById('open-native');

  // Preserve UTM / zalo ids if present
  ['zalo_id', 'oa_id', 'utm_source'].forEach(function (k) {
    if (params.get(k)) {
      // keep for analytics later
    }
  });

  var portalUrl = portalPath + (params.toString() ? '?' + params.toString() : '');
  if (openBtn) openBtn.setAttribute('href', portalUrl);

  function log(msg) {
    if (debug) debug.textContent = msg;
  }

  log('Portal target: ' + portalUrl);

  // Auto-redirect after short splash (Mini App UX)
  var auto = params.get('noredirect') !== '1';
  if (auto) {
    setTimeout(function () {
      window.location.replace(portalUrl);
    }, 600);
  }

  if (nativeBtn) {
    nativeBtn.addEventListener('click', function () {
      // Zalo JS Bridge (when running inside Mini App runtime)
      try {
        if (window.zaloMiniApp && typeof window.zaloMiniApp.openWebview === 'function') {
          window.zaloMiniApp.openWebview({ url: window.location.origin + portalUrl });
          return;
        }
        if (window.ZaloSocial && window.ZaloSocial.OpenGraph) {
          window.location.href = portalUrl;
          return;
        }
      } catch (e) {
        log('Bridge error: ' + e.message);
      }
      window.location.href = portalUrl;
    });
  }
})();
