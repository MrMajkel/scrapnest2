async function fetchStat(typ) {
    const response = await fetch(`backend/mod_panel.php?typ=${typ}`);
    return await response.json();
  }
  
  async function updateStats() {
    try {
      const masaData = await fetchStat("masa");
      document.getElementById("calkowita_masa").innerText = 
        masaData.suma_wag !== undefined ? `${masaData.suma_wag} kg` : "Brak danych";
  
      const metaleData = await fetchStat("iloscmetali");
      document.getElementById("laczna_ilosc_metali").innerText = 
        metaleData.liczba_metali !== undefined ? metaleData.liczba_metali : "Brak danych";
  
      const odbiorcyData = await fetchStat("iloscodbiorcow");
      document.getElementById("liczba_odbiorcow").innerText = 
        odbiorcyData.liczba !== undefined ? odbiorcyData.liczba : "Brak danych";
  
      const formularzeData = await fetchStat("iloscformularzy");
      document.getElementById("liczba_formularzy").innerText = 
        formularzeData.liczba !== undefined ? formularzeData.liczba : "Brak danych";
  
    } catch (error) {
      console.error("Błąd podczas pobierania danych:", error);
      document.getElementById("calkowita_masa").innerText = "Błąd";
      document.getElementById("laczna_ilosc_metali").innerText = "Błąd";
      document.getElementById("liczba_odbiorcow").innerText = "Błąd";
      document.getElementById("liczba_formularzy").innerText = "Błąd";
    }
  }
  
  async function updateMagazyn() {
    try {
      const response = await fetch("backend/mod_panel.php?typ=magazyn");
      const data = await response.json();
  
      const magazynBody = document.getElementById("magazynBody");
      magazynBody.innerHTML = "";
  
      if (data.length > 0) {
        const date = new Date(data[0].data).toLocaleDateString("pl-PL");
        document.querySelectorAll(".table-box h3")[0].innerText = `Magazyn (${date})`;
      }
  
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td>${row.metal}</td><td>${parseFloat(row.stan_magazynowy).toFixed(2)} kg</td>`;
        magazynBody.appendChild(tr);
      });
    } catch (err) {
      console.error("Błąd podczas pobierania danych magazynu:", err);
    }
  }
  
  async function updateSprzedaze() {
    try {
      const response = await fetch("backend/mod_panel.php?typ=ostatnie_sprzedaze");
      const data = await response.json();
  
      const tbody = document.getElementById("sprzedazeBody");
      tbody.innerHTML = "";
  
      if (!Array.isArray(data) || data.length === 0) {
        tbody.innerHTML = "<tr><td colspan='4'>Brak danych</td></tr>";
        return;
      }
  
      data.forEach(f => {
        const metale = f.metal?.split('\n') || [];
        const wagi = f.waga?.split('\n') || [];
  
        const rowspan = Math.max(metale.length, wagi.length);
  
        metale.forEach((metal, i) => {
          const tr = document.createElement("tr");
          if (i === 0) {
            tr.innerHTML = `
              <td rowspan="${rowspan}">${f.data}</td>
              <td rowspan="${rowspan}">${f.firma}</td>
              <td>${metal}</td>
              <td>${parseFloat(wagi[i] || 0).toFixed(2)} kg</td>
            `;
          } else {
            tr.innerHTML = `
              <td>${metal}</td>
              <td>${parseFloat(wagi[i] || 0).toFixed(2)} kg</td>
            `;
          }
          tbody.appendChild(tr);
        });
      });
    } catch (err) {
      console.error("Błąd podczas pobierania ostatnich sprzedaży:", err);
      const tbody = document.getElementById("sprzedazeBody");
      tbody.innerHTML = "<tr><td colspan='4'>Błąd wczytywania danych</td></tr>";
    }
  }
  
  function toggleNav() {
    const nav = document.querySelector('.nav');
    const dropdown = document.getElementById('dropdownMenu');
    nav.classList.toggle('active');
    if (dropdown) dropdown.style.display = 'none'; 
  }
  