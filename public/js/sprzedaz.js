document.addEventListener('DOMContentLoaded', () => {
    loadSprzedaz();
    document.querySelector('.add-button')?.addEventListener('click', showAddSprzedaz);
  });
  
  async function loadSprzedaz() {
    const res = await fetch('backend/sprzedaz.php');
    const faktury = await res.json();
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = '';
    faktury.forEach(f => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${f.numer}</td>
        <td>${f.data}</td>
        <td>${f.firma}</td>
        <td>${f.metal.replace(/\\n/g, '<br>')}</td>
        <td>${f.waga.replace(/\\n/g, '<br>')}</td>
        <td>
          <button onclick='editSprzedaz(${JSON.stringify(f)})'>Edytuj</button>
          <button onclick='deleteSprzedaz(${f.id})'>Usuń</button>
        </td>`;
      tbody.appendChild(tr);
    });
  }
  
  function showAddSprzedaz() {
    const form = sprzedazForm();
    form.onsubmit = async (e) => {
      e.preventDefault();
      const data = new FormData(form);
      data.append('action', 'create');
      await fetch('backend/sprzedaz.php', { method: 'POST', body: data });
      form.remove();
      loadSprzedaz();
    };
  }
  
  function editSprzedaz(f) {
    const form = sprzedazForm(f);
    form.onsubmit = async (e) => {
      e.preventDefault();
      const data = new FormData(form);
      data.append('action', 'update');
      data.append('id', f.id);
      await fetch('backend/sprzedaz.php', { method: 'POST', body: data });
      form.remove();
      loadSprzedaz();
    };
  }
  
  async function deleteSprzedaz(id) {
    if (!confirm('Na pewno usunąć fakturę sprzedaży?')) return;
    const data = new FormData();
    data.append('action', 'delete');
    data.append('id', id);
    await fetch('backend/sprzedaz.php', { method: 'POST', body: data });
    loadSprzedaz();
  }
  
  function sprzedazForm(data = {}) {
    const form = document.createElement('form');
    form.innerHTML = `
      <input name="numer" placeholder="Nr faktury" value="${data.numer || ''}" required><br>
      <input type="date" name="data" value="${data.data || ''}" required><br>
      <input name="firma" placeholder="Firma" value="${data.firma || ''}" required><br>
      <textarea name="metal" placeholder="Metal">${data.metal || ''}</textarea><br>
      <textarea name="waga" placeholder="Waga">${data.waga || ''}</textarea><br>
      <button type="submit">Zapisz</button>
      <button type="button" onclick="this.closest('form').remove()">Anuluj</button>
    `;
    Object.assign(form.style, {
      position: 'fixed', top: '20%', left: '50%',
      transform: 'translate(-50%, 0)', background: '#fff',
      padding: '20px', zIndex: 1000, boxShadow: '0 0 10px rgba(0,0,0,0.3)'
    });
    document.body.appendChild(form);
    return form;
  }
  