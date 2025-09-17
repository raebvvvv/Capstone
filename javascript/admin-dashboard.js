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
      new Chart(overviewCanvas,{
        type:'doughnut',
        data:{
          labels:['Undergraduate','Graduate School','Open University'],
          datasets:[{ data:[u,g,o], backgroundColor:['#870000','#FFD54F','gray'] }]
        },
        options:{ cutout:'70%', plugins:{ legend:{display:true, position:'bottom'}, title:{display:false} } }
      });
    }

    // Main Undergrad bar
    var mainUndergradCanvas = document.getElementById('mainUndergradChart');
    if(mainUndergradCanvas){
      new Chart(mainUndergradCanvas,{
        type:'bar',
        data:{
          labels:[
            'CSA',
            'CAF',
            'CSSD',
            'CPSPA',
            'ITECH',
            'CHK',
            'COC',
            'CBA',
            'CAL',
            'CAF',
            'CADBE',
            'COE',
            'CCIS'
          ],
          datasets:[{ label:'Applications', data:[20,10,15,5,10,359,8,7,6,5,4,3,2], backgroundColor:'#870000' }]
        },
        options:{
          // vertical bars (default indexAxis is 'x')
          plugins:{ legend:{display:false}, title:{display:false} },
          scales:{
            x:{ ticks:{ autoSkip:false, maxRotation:45, minRotation:45 } },
            y:{ beginAtZero:true }
          }
        }
      });
    }

    // Branches bar (Other Campus) - changed to vertical orientation
    var branchesCanvas = document.getElementById('branchesChart');
    if(branchesCanvas){
      new Chart(branchesCanvas,{
        type:'bar',
        data:{ labels:['Sta. Maria, Bulacan','Pulilan, Bulacan','Cabiao, Nueva Ecija'], datasets:[{ label:'Applications', data:[80,10,65], backgroundColor:'#870000' }] },
        options:{
          indexAxis:'x', // force vertical orientation
          maintainAspectRatio:false,
          aspectRatio:1.4,
          plugins:{ legend:{display:false}, title:{display:false} },
          scales:{
            x:{ ticks:{ autoSkip:false, maxRotation:15, minRotation:0 } },
            y:{ beginAtZero:true }
          }
        }
      });
    }
  });
})();
