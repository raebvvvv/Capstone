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

    // Keep refs to charts for dynamic updates
    var charts = { overview:null, college:null, campus:null, workClass:null };
    function setAsOf(iso){
      try{
        var lbl = document.getElementById('dashAsOf');
        if(!lbl) return;
        if(!iso){ lbl.textContent = 'As of —'; return; }
        var d = new Date(iso);
        if(String(d) === 'Invalid Date'){ lbl.textContent = 'As of —'; return; }
        lbl.textContent = 'As of ' + d.toLocaleString();
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

    // Reload button logic
    var reloadBtn = document.getElementById('dashReloadBtn');
    if(reloadBtn){
      reloadBtn.addEventListener('click', function(){
        (async function(){
          try{
            reloadBtn.disabled = true;
            var asOfInput = document.getElementById('dashAsOfInput');
            var payload = {};
            if(asOfInput && asOfInput.value){
              var asOf = new Date(asOfInput.value);
              if(String(asOf) !== 'Invalid Date'){
                var y = asOf.getFullYear();
                var m = String(asOf.getMonth()+1).padStart(2,'0');
                var d = String(asOf.getDate()).padStart(2,'0');
                var hh = String(asOf.getHours()).padStart(2,'0');
                var mm = String(asOf.getMinutes()).padStart(2,'0');
                payload.as_of = y+'-'+m+'-'+d+' '+hh+':'+mm+':00';
              }
            }
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            var token = csrfMeta ? csrfMeta.getAttribute('content') : '';
            var res = await fetch('dashboard_metrics.php', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token }, body: JSON.stringify(payload) });
            var data = null; try { data = await res.json(); } catch(e) {}
            if(!data || !data.success){ var txt=''; try{ txt = await res.text(); }catch(e){} alert('Failed to reload metrics' + (data && data.error? (': '+data.error):'') + (txt?'\n'+txt:'')); return; }
            // Update charts and counters
            if(charts.overview){ charts.overview.data.datasets[0].data = [data.overview.undergrad, data.overview.grad, data.overview.open]; charts.overview.update(); }
            var elU = document.getElementById('countUndergrad'); if(elU){ elU.textContent = String(data.overview.undergrad); }
            var elG = document.getElementById('countGrad'); if(elG){ elG.textContent = String(data.overview.grad); }
            var elO = document.getElementById('countOpen'); if(elO){ elO.textContent = String(data.overview.open); }
            var elT = document.getElementById('countTotalApplications'); if(elT){ elT.textContent = String(data.overview.total); }
            if(charts.college){ charts.college.data.labels = data.byCollege.labels; charts.college.data.datasets[0].data = data.byCollege.values; charts.college.update(); }
            if(charts.campus){ charts.campus.data.labels = data.byCampus.labels; charts.campus.data.datasets[0].data = data.byCampus.values; charts.campus.update(); }
            if(charts.workClass){ charts.workClass.data.labels = data.workClass.labels; charts.workClass.data.datasets[0].data = data.workClass.values; charts.workClass.update(); }
            setAsOf(data.as_of);
          } catch(err){ console.error('Dashboard reload error', err); alert('Network error while reloading metrics'); }
          finally{ reloadBtn.disabled = false; }
        })();
      });
    }
  });
})();
