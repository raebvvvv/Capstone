// Admin dashboard charts (externalized to comply with CSP)
(function(){
  function ready(fn){ if(document.readyState!=='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
  ready(function(){
    var hasChart = typeof window.Chart !== 'undefined';
    function fallback(id, msg){ var c = document.getElementById(id); if(!c) return; var n = document.createElement('div'); n.className='text-muted'; n.style.padding='8px 0'; n.textContent=msg; c.replaceWith(n); }
    if(!hasChart){
      fallback('applicationOverviewChart','Charts unavailable (Chart.js failed to load).');
      fallback('mainUndergradChart','Charts unavailable (Chart.js failed to load).');
      fallback('branchesChart','Charts unavailable (Chart.js failed to load).');
      fallback('workClassChart','Charts unavailable (Chart.js failed to load).');
      return;
    }

    // Helpers: abbreviate college labels and collapse into codes
    function abbreviateCollegeLabel(raw){
      if(!raw) return 'Other';
      var s = String(raw).trim();
      var low = s.toLowerCase();
      if (low.indexOf('institute of technology') !== -1) return 'ITech';
      var dash = s.indexOf(' - ');
      if (dash > -1) {
        var code = s.slice(0, dash).trim();
        if (code) return code.toUpperCase();
      }
      var m = s.match(/\(([^)]+)\)\s*$/);
      if (m && m[1]) {
        return String(m[1]).trim().toUpperCase();
      }
      if (s.length <= 7 && s === s.toUpperCase()) return s;
      var stop = { of:1, and:1, the:1, in:1, for:1, college:1, school:1, institute:1, faculty:1 };
      var abbr = '';
      s.split(/\s+/).forEach(function(w){
        var ww = (w||'').trim(); if(!ww) return;
        var lw = ww.toLowerCase(); if (stop[lw]) return;
        abbr += ww[0] ? ww[0].toUpperCase() : '';
      });
      return abbr || 'Other';
    }
    function collapseCollegeSeriesToCodes(labels, values){
      var agg = Object.create(null);
      var order = [];
      for (var i=0;i<labels.length;i++){
        var code = abbreviateCollegeLabel(labels[i]);
        var val = parseInt(values[i]||0,10) || 0;
        if (!(code in agg)) { agg[code] = 0; order.push(code); }
        agg[code] += val;
      }
      var outL = [], outV = [];
      order.forEach(function(k){ outL.push(k); outV.push(agg[k]); });
      return { labels: outL, values: outV };
    }

    // Keep refs to charts for dynamic updates
    var charts = { overview:null, college:null, campus:null, workClass:null };
    function setAsOf(iso){
      try{
        var lbl = document.getElementById('dashAsOf');
        if(!lbl) return;
        if(!iso){ lbl.textContent = 'As of —'; lbl.classList.add('d-none'); return; }
        var d = new Date(iso);
        if(String(d) === 'Invalid Date'){ lbl.textContent = 'As of —'; lbl.classList.add('d-none'); return; }
        lbl.textContent = 'As of ' + d.toLocaleString('en-PH', { timeZone: 'Asia/Manila' });
        lbl.classList.remove('d-none');
      }catch(_){ }
    }

    // Donut (Application Overview)
    var overviewCanvas = document.getElementById('applicationOverviewChart');
    if(overviewCanvas){
      var u = parseInt(overviewCanvas.dataset.undergrad||'0',10);
      var g = parseInt(overviewCanvas.dataset.grad||'0',10);
      var o = parseInt(overviewCanvas.dataset.open||'0',10);
      charts.overview = new Chart(overviewCanvas,{
        type:'doughnut',
        data:{ labels:['Undergraduate','Graduate School','Open University'], datasets:[{ data:[u,g,o], backgroundColor:['#870000','#FFD54F','gray'] }] },
        options:{
          cutout:'70%',
          plugins:{
            legend:{display:true, position:'bottom'},
            title:{display:false},
            tooltip:{ callbacks:{
              label:function(ctx){
                var v = ctx.parsed;
                var ds = (ctx && ctx.chart && ctx.chart.data && ctx.chart.data.datasets && ctx.chart.data.datasets[0] && ctx.chart.data.datasets[0].data) ? ctx.chart.data.datasets[0].data : [];
                var sum = 0; for(var i=0;i<ds.length;i++){ sum += (parseFloat(ds[i])||0); }
                var pct = sum? ((v*100/sum).toFixed(1)+'%') : '0%';
                return ctx.label+': '+v+' ('+pct+')';
              }
            } }
          }
        },
        plugins:[{
          id:'centerText',
          afterDraw:function(chart){
            var ctx = chart.ctx;
            var ds = (chart.data && chart.data.datasets && chart.data.datasets[0] && chart.data.datasets[0].data) ? chart.data.datasets[0].data : [];
            var totalNow = 0; for (var i=0;i<ds.length;i++){ totalNow += (parseFloat(ds[i])||0); }
            var meta = chart.getDatasetMeta(0);
            if (!meta || !meta.data || !meta.data.length) { return; }
            var first = meta.data[0];
            var cx = first.x; var cy = first.y;
            if (typeof cx !== 'number' || typeof cy !== 'number') { return; }
            ctx.save();
            ctx.fillStyle = '#333';
            ctx.font = 'bold 14px Inter, Arial, sans-serif';
            ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
            ctx.fillText('Total: '+ totalNow, cx, cy);
            ctx.restore();
          }
        }]
      });
    }

    // Applications by College bar
    var mainUndergradCanvas = document.getElementById('mainUndergradChart');
    if(mainUndergradCanvas){
      var labels = []; var values = [];
      try { labels = JSON.parse(mainUndergradCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { values = JSON.parse(mainUndergradCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      // Normalize to abbreviations/codes on initial render as well
      try {
        var collapsed = collapseCollegeSeriesToCodes(labels, values);
        labels = collapsed.labels; values = collapsed.values;
      } catch(_) {}
      charts.college = new Chart(mainUndergradCanvas,{
        type:'bar',
        data:{ labels: labels, datasets:[{ label:'Applications', data: values, backgroundColor:'#870000' }] },
        options:{ indexAxis:'x', plugins:{ legend:{display:false}, title:{display:false}, tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}} }, scales:{ x:{ ticks:{ autoSkip:false, maxRotation:45, minRotation:0 } }, y:{ beginAtZero:true } } }
      });
    }

    // Applications by Campus bar (vertical)
    var branchesCanvas = document.getElementById('branchesChart');
    if(branchesCanvas){
      var labelsB = []; var valuesB = [];
      try { labelsB = JSON.parse(branchesCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { valuesB = JSON.parse(branchesCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      charts.campus = new Chart(branchesCanvas,{
        type:'bar',
        data:{ labels: labelsB, datasets:[{ label:'Applications', data: valuesB, backgroundColor:'#870000' }] },
        options:{ indexAxis:'x', maintainAspectRatio:false, aspectRatio:1.4, plugins:{ legend:{display:false}, title:{display:false}, tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}} }, scales:{ x:{ ticks:{ autoSkip:false, maxRotation:15, minRotation:0 } }, y:{ beginAtZero:true } } }
      });
    }

    // Work Classification distribution
    var wcCanvas = document.getElementById('workClassChart');
    if(wcCanvas){
      var labelsW = []; var valuesW = [];
      try { labelsW = JSON.parse(wcCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { valuesW = JSON.parse(wcCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      charts.workClass = new Chart(wcCanvas,{
        type:'bar',
        data:{ labels: labelsW, datasets:[{ label:'Count', data: valuesW, backgroundColor:'#870000' }] },
        options:{ plugins:{ legend:{display:false}, title:{display:false}, tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}} }, scales:{ x:{ ticks:{ autoSkip:false, maxRotation:45, minRotation:0 } }, y:{ beginAtZero:true } } }
      });
    }

    // Set initial As-of from server-rendered timestamp
    (function(){
      var lbl = document.getElementById('dashAsOf');
      if(lbl){
        var initIso = lbl.getAttribute('data-initial-iso');
        var init = initIso || lbl.getAttribute('data-initial');
        setAsOf(init || '');
      }
    })();

    // Reload button logic (cached summary only)
    var reloadBtn = document.getElementById('dashReloadBtn');
    if(reloadBtn){
      reloadBtn.addEventListener('click', function(){
        (async function(){
          try{
            reloadBtn.disabled = true;
            var spin = document.getElementById('dashReloadSpin'); if(spin){ spin.classList.remove('d-none'); }
            var payload = { reload: true };
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var token = csrfMeta ? csrfMeta.getAttribute('content') : '';
            var res = await fetch('dashboard_refresh.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token }, body: JSON.stringify(payload) });
            var data = null; try { data = await res.json(); } catch(e) {}
            if(!data || !data.success){ var txt=''; try{ txt = await res.text(); }catch(e){} alert('Failed to reload summary' + (data && data.error? (': '+data.error):'') + (txt?'\n'+txt:'')); return; }
            // Update charts and counters
            if(charts.overview){ charts.overview.data.datasets[0].data = [data.overview.undergrad, data.overview.grad, data.overview.open]; charts.overview.update(); }
            var elU = document.getElementById('countUndergrad'); if(elU){ elU.textContent = String(data.overview.undergrad); }
            var elG = document.getElementById('countGrad'); if(elG){ elG.textContent = String(data.overview.grad); }
            var elO = document.getElementById('countOpen'); if(elO){ elO.textContent = String(data.overview.open); }
            var elT = document.getElementById('countTotalApplications'); if(elT){ elT.textContent = String(data.overview.total); }
            // Update top summary cards if present
            if (data.totals) {
              var tUsers = document.getElementById('totalUsers'); if(tUsers){ tUsers.textContent = String(data.totals.users || 0); }
              var tApps = document.getElementById('totalApplications'); if(tApps){ tApps.textContent = String(data.totals.applications || 0); }
              var tPend = document.getElementById('pendingApplications'); if(tPend){ tPend.textContent = String(data.totals.pending || 0); }
              var tAppr = document.getElementById('approvedApplications'); if(tAppr){ tAppr.textContent = String(data.totals.approved || 0); }
              var tComp = document.getElementById('completedApplications'); if(tComp){ tComp.textContent = String(data.totals.completed || 0); }
            }
            if(charts.college){
              var cLabels = (data.byCollege && Array.isArray(data.byCollege.labels)) ? data.byCollege.labels : [];
              var cValues = (data.byCollege && Array.isArray(data.byCollege.values)) ? data.byCollege.values : [];
              try{
                var collapsed2 = collapseCollegeSeriesToCodes(cLabels, cValues);
                cLabels = collapsed2.labels; cValues = collapsed2.values;
              }catch(_){}
              charts.college.data.labels = cLabels;
              charts.college.data.datasets[0].data = cValues;
              charts.college.update();
            }
            if(charts.campus){ charts.campus.data.labels = data.byCampus.labels; charts.campus.data.datasets[0].data = data.byCampus.values; charts.campus.update(); }
            if(charts.workClass){ charts.workClass.data.labels = data.workClass.labels; charts.workClass.data.datasets[0].data = data.workClass.values; charts.workClass.update(); }
            setAsOf(data.last_updated_iso || data.last_updated || data.as_of);
            // After a successful refresh, also reload the page so the whole dashboard reflects latest data/state
            // (requested behavior). This ensures server-rendered counts and any other UI pieces are in sync.
            window.location.reload();
          } catch(err){ console.error('Dashboard reload error', err); alert('Network error while reloading metrics'); }
          finally{ reloadBtn.disabled = false; var spin2 = document.getElementById('dashReloadSpin'); if(spin2){ spin2.classList.add('d-none'); } }
        })();
      });
    }
  });
})();
