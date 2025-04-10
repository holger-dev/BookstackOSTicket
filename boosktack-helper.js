document.addEventListener('DOMContentLoaded', () => {
    const debug = false; 
  
    function waitForSummaryField(retries = 120) {
      const input = document.querySelector('#dynamic-form input');
      if (input) {
        if (debug) console.log('[BookStack Helper] Summary-Feld gefunden:', input);
        setupHelper(input);
      } else {
        if (debug) console.log('[BookStack Helper] Suche nach Summary-Feld... Versuch', 30 - retries + 1);
        if (retries > 0) {
          setTimeout(() => waitForSummaryField(retries - 1), 300);
        } else {
          if (debug) console.warn('[BookStack Helper] Summary-Feld nicht gefunden – Abbruch.');
        }
      }
    }
  
  function setupHelper(input) {
    const box = document.createElement('div');
    box.style.marginTop = '1em';
    box.style.border = '1px solid #ccc';
    box.style.background = '#f9f9f9';
    box.style.padding = '1em';
    box.style.borderRadius = '8px';
    box.style.boxShadow = '0 2px 6px rgba(0,0,0,0.1)';
    input.parentNode.appendChild(box);
  
    let timeout;
    input.addEventListener('input', () => {
      const query = input.value.trim();
      if (query.length < 3) {
        box.innerHTML = '';
        return;
      }
  
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        fetch('/bookstack-search.php?q=' + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            box.innerHTML = '';
            if (!data || !data.length) return;
  
            const heading = document.createElement('h4');
            heading.textContent = '📘 Passende Anleitungen';
            heading.style.marginBottom = '0.5em';
            box.appendChild(heading);
  
            data.forEach(item => {
              const container = document.createElement('div');
              container.style.marginBottom = '1em';
              container.style.paddingBottom = '0.5em';
              container.style.borderBottom = '1px solid #ddd';
  
              const title = document.createElement('a');
              title.href = item.url;
              title.target = '_blank';
              title.textContent = item.title;
              title.style.fontWeight = 'bold';
              title.style.fontSize = '1em';
              title.style.display = 'block';
              title.style.color = '#007bff';
              container.appendChild(title);
  
              const preview = document.createElement('div');
              preview.textContent = item.excerpt?.slice(0, 150) + '...';
              preview.style.color = '#444';
              preview.style.fontSize = '0.9em';
              preview.style.marginTop = '0.3em';
              container.appendChild(preview);
  
              box.appendChild(container);
            });
  
          });
      }, 300);
    });
  }
  
    waitForSummaryField(); // Start!
  });
  
  