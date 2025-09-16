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
          datasets:[{ data:[u,g,o], backgroundColor:['#3b82f6','#a855f7','#ef4444'] }]
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
            'College of Tourism, Hospitality and Transportation Management',
            'Science College',
            'College of Social Sciences and Development',
            'College of Political Science and Public Administration',
            'College of Law',
            'College of Human Kinetics',
            'College of Communication',
            'College of Business Administration',
            'College of Arts and Letters',
            'College of Accountancy and Finance',
            'College of Education',
            'College of Engineering',
            'College of Computer and Information Sciences'
          ],
          datasets:[{ label:'Applications', data:[20,10,15,5,10,359,8,7,6,5,4,3,2], backgroundColor:'#3b82f6' }]
        },
        options:{ indexAxis:'y', plugins:{ legend:{display:false}, title:{display:false} }, scales:{ x:{beginAtZero:true} } }
      });
    }

    // Branches bar
    var branchesCanvas = document.getElementById('branchesChart');
    if(branchesCanvas){
      new Chart(branchesCanvas,{
        type:'bar',
        data:{ labels:['San Juan City','Quezon City','Taguig City'], datasets:[{ label:'Applications', data:[80,10,65], backgroundColor:'#a855f7' }] },
        options:{ indexAxis:'y', plugins:{ legend:{display:false}, title:{display:false} }, scales:{ x:{beginAtZero:true} } }
      });
    }
  });
})();
