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
      return;
    }

    // Donut
    var overviewCanvas = document.getElementById('applicationOverviewChart');
    if(overviewCanvas){
      var u = parseInt(overviewCanvas.dataset.undergrad||'0',10);
      var g = parseInt(overviewCanvas.dataset.grad||'0',10);
      var o = parseInt(overviewCanvas.dataset.open||'0',10);
      var t = parseInt(overviewCanvas.dataset.total||String(u+g+o),10);
      new Chart(overviewCanvas,{
        type:'doughnut',
        data:{
          labels:['Undergraduate','Graduate School','Open University'],
          datasets:[{ data:[u,g,o], backgroundColor:['#870000','#FFD54F','gray'] }]
        },
        options:{
          cutout:'70%',
          plugins:{
            legend:{display:true, position:'bottom'},
            title:{display:false},
            tooltip:{callbacks:{
              label:function(ctx){ var v = ctx.parsed; var sum = u+g+o; var pct = sum? ((v*100/sum).toFixed(1)+'%') : '0%'; return ctx.label+': '+v+' ('+pct+')'; }
            }},
            // Center text plugin (total)
            datalabels:false
          }
        },
        plugins:[{
          id:'centerText',
          afterDraw(chart,args,opts){
            var {ctx, chartArea:{width,height}} = chart;
            ctx.save();
            ctx.fillStyle = '#333';
            ctx.font = 'bold 14px Inter, Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('Total: '+t, chart.getDatasetMeta(0).data[0].x, chart.getDatasetMeta(0).data[0].y);
            ctx.restore();
          }
        }]
      });
    }

    // Applications by College bar
    var mainUndergradCanvas = document.getElementById('mainUndergradChart');
    if(mainUndergradCanvas){
      var labels = [];
      var values = [];
      try { labels = JSON.parse(mainUndergradCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { values = JSON.parse(mainUndergradCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      new Chart(mainUndergradCanvas,{
        type:'bar',
        data:{
          labels: labels,
          datasets:[{ label:'Applications', data: values, backgroundColor:'#870000' }]
        },
        options:{
          indexAxis:'x', // vertical bars as requested
          plugins:{
            legend:{display:false},
            title:{display:false},
            tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}}
          },
          scales:{
            x:{ ticks:{ autoSkip:false, maxRotation:45, minRotation:0 } },
            y:{ beginAtZero:true }
          }
        }
      });
    }

    // Applications by Campus bar (vertical)
    var branchesCanvas = document.getElementById('branchesChart');
    if(branchesCanvas){
      var labelsB = [];
      var valuesB = [];
      try { labelsB = JSON.parse(branchesCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { valuesB = JSON.parse(branchesCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      new Chart(branchesCanvas,{
        type:'bar',
        data:{ labels: labelsB, datasets:[{ label:'Applications', data: valuesB, backgroundColor:'#870000' }] },
        options:{
          indexAxis:'x', // force vertical orientation
          maintainAspectRatio:false,
          aspectRatio:1.4,
          plugins:{ legend:{display:false}, title:{display:false}, tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}} },
          scales:{
            x:{ ticks:{ autoSkip:false, maxRotation:15, minRotation:0 } },
            y:{ beginAtZero:true }
          }
        }
      });
    }

    // Work Classification distribution
    var wcCanvas = document.getElementById('workClassChart');
    if(wcCanvas){
      var labelsW = [];
      var valuesW = [];
      try { labelsW = JSON.parse(wcCanvas.getAttribute('data-labels')||'[]'); } catch(_) {}
      try { valuesW = JSON.parse(wcCanvas.getAttribute('data-values')||'[]'); } catch(_) {}
      new Chart(wcCanvas,{
        type:'bar',
        data:{ labels: labelsW, datasets:[{ label:'Count', data: valuesW, backgroundColor:'#870000' }] },
        options:{
          plugins:{ legend:{display:false}, title:{display:false}, tooltip:{callbacks:{ label:function(ctx){ return ctx.parsed.y; }}} },
          scales:{ x:{ ticks:{ autoSkip:false, maxRotation:45, minRotation:0 } }, y:{ beginAtZero:true } }
        }
      });
    }
  });
})();
