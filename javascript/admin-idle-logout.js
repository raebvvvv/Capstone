(function(){
  'use strict';
  // Only run on admin pages (path contains /admin/)
  var path = (location.pathname || '').toLowerCase();
  if (path.indexOf('/admin/') === -1) { return; }

  // Config (can be overridden via data-attrs on body)
  var IDLE_MINUTES = 5; // auto logout after 5 minutes of inactivity
  var WARN_SECONDS = 60; // show warning 60s before logout

  try {
    var attrMins = parseInt(document.body.getAttribute('data-idle-minutes')||'', 10);
    if (!isNaN(attrMins) && attrMins > 0) IDLE_MINUTES = attrMins;
    var attrWarn = parseInt(document.body.getAttribute('data-idle-warn-seconds')||'', 10);
    if (!isNaN(attrWarn) && attrWarn >= 10) WARN_SECONDS = attrWarn;
  } catch(_){}

  var IDLE_MS = IDLE_MINUTES * 60 * 1000;
  var WARN_MS = WARN_SECONDS * 1000;
  var lastActivity = Date.now();
  var warnTimer = null;
  var logoutTimer = null;
  var bannerEl = null;
  var countdownEl = null;

  function getCsrf(){
    var m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : '';
  }
  function postLogout(){
    var csrf = getCsrf();
    if (!csrf) {
      // Fallback redirect if token missing
      window.location.href = '../logout.php';
      return;
    }
    // Build and submit a hidden POST form (avoids fetch/CORS/cookie issues)
    var f = document.createElement('form');
    f.method = 'POST';
    f.action = '../logout.php';
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'csrf_token'; inp.value = csrf; f.appendChild(inp);
    document.body.appendChild(f);
    try { f.submit(); } catch(_){ window.location.href = '../logout.php'; }
  }

  function removeBanner(){ if (bannerEl && bannerEl.parentNode) { bannerEl.parentNode.removeChild(bannerEl); } bannerEl = null; countdownEl = null; }

  function showWarning(seconds){
    removeBanner();
    var wrap = document.createElement('div');
    wrap.className = 'idle-warning-banner';
    wrap.style.cssText = 'position:fixed;left:0;right:0;bottom:0;z-index:1080;background:#fff3cd;border-top:1px solid #ffe69c;color:#664d03;padding:10px 16px;display:flex;align-items:center;justify-content:center;gap:12px;box-shadow:0 -2px 8px rgba(0,0,0,.08)';
    var msg = document.createElement('div');
    msg.innerHTML = '<strong>Session timeout:</strong> You\'ll be logged out in <span id="idleCountdown">'+seconds+'</span>s due to inactivity.';
    var stay = document.createElement('button');
    stay.type = 'button';
    stay.className = 'btn btn-sm btn-primary';
    stay.textContent = 'Stay logged in';
    stay.addEventListener('click', function(){ resetTimers(true); });
    wrap.appendChild(msg); wrap.appendChild(stay);
    document.body.appendChild(wrap);
    bannerEl = wrap;
    countdownEl = document.getElementById('idleCountdown');
  }

  function updateCountdown(msRemaining){
    if (!countdownEl) return;
    var s = Math.max(0, Math.ceil(msRemaining/1000));
    countdownEl.textContent = String(s);
  }

  function scheduleTimers(){
    var now = Date.now();
    var elapsed = now - lastActivity;
    var remaining = IDLE_MS - elapsed;
    var warnIn = Math.max(0, remaining - WARN_MS);

    // Clear any existing timers
    if (warnTimer) { clearTimeout(warnTimer); warnTimer = null; }
    if (logoutTimer) { clearTimeout(logoutTimer); logoutTimer = null; }

    // Schedule warning
    warnTimer = setTimeout(function(){
      showWarning(Math.ceil(WARN_MS/1000));
      var endAt = Date.now() + WARN_MS;
      // Start a 1s countdown
      var iv = setInterval(function(){
        var msLeft = endAt - Date.now();
        if (msLeft <= 0) { clearInterval(iv); }
        updateCountdown(msLeft);
      }, 1000);
    }, warnIn);

    // Schedule logout
    logoutTimer = setTimeout(function(){ postLogout(); }, remaining);
  }

  function resetTimers(fromUser){
    lastActivity = Date.now();
    if (fromUser) { removeBanner(); }
    scheduleTimers();
  }

  // Track activity
  ['mousemove','mousedown','keydown','scroll','touchstart','click'].forEach(function(evt){
    window.addEventListener(evt, function(){ resetTimers(true); }, { passive:true });
  });

  // Start timers on load
  document.addEventListener('DOMContentLoaded', function(){ scheduleTimers(); });
})();
