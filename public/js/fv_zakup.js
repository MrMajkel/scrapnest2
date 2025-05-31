let currentPage = 1;
const itemsPerPage = 20;

document.addEventListener('DOMContentLoaded', () => {
  loadFaktury(currentPage);
  document.querySelector('.add-button')?.addEventListener('click', showAddFaktura);
});

async function loadFaktury(page = 1) {
  currentPage = page;
  const offset = (page - 1) * itemsPerPage;

  const res = await fetch(`backend/mod_fv_zakup.php?limit=${itemsPerPage}&offset=${offset}`);
  const tbody = document.querySelector('tbody');
  const table = tbody.closest('table');
  tbody.innerHTML = '';
  document.querySelectorAll('.faktura-card').forEach(card => card.remove());

  if (!res.ok) {
    tbody.innerHTML = `<tr><td colspan="6">Błąd pobierania danych</td></tr>`;
    return;
  }

  const data = await res.json();
  const isMobile = window.innerWidth <= 768;

  if (!Array.isArray(data) || data.length === 0) {
    if (isMobile) {
      const div = document.createElement('div');
      div.className = 'faktura-card';
      div.textContent = 'Brak danych';
      table.insertAdjacentElement('beforebegin', div);
    } else {
      tbody.innerHTML = `<tr><td colspan="6">Brak danych</td></tr>`;
    }
    renderPagination(false);
    return;
  }

  if (isMobile) {
    table.style.display = 'none';
    data.forEach(f => {
      const div = document.createElement('div');
      div.className = 'faktura-card';
      div.innerHTML = `
        <p><strong>Nr faktury:</strong> ${f.numer}</p>
        <p><strong>Data:</strong> ${f.data}</p>
        <p><strong>Firma:</strong> ${f.firma}</p>
        <p><strong>Metal:</strong><br>${(f.metal || '').replace(/\n/g, '<br>')}</p>
        <p><strong>Waga:</strong><br>${(f.waga || '').split('\n').map(w => `${w} kg`).join('<br>')}</p>
      `;

      const actions = document.createElement('div');
      actions.className = 'card-actions';

      const editBtn = document.createElement('button');
      editBtn.className = 'edit';
      editBtn.textContent = 'Edytuj';
      editBtn.addEventListener('click', () => editFaktura(f));

      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'delete';
      deleteBtn.textContent = 'Usuń';
      deleteBtn.addEventListener('click', () => deleteFaktura(f.id));

      actions.appendChild(editBtn);
      actions.appendChild(deleteBtn);
      div.appendChild(actions);
      table.insertAdjacentElement('beforebegin', div);
    });
  } else {
    table.style.display = '';
    data.forEach(f => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${f.numer}</td>
        <td>${f.data}</td>
        <td>${f.firma}</td>
        <td>${(f.metal || '').replace(/\n/g, '<br>')}</td>
        <td>${(f.waga || '').split('\n').map(w => `${w} kg`).join('<br>')}</td>
        <td></td>
      `;

      const td = tr.querySelector('td:last-child');

      const editBtn = document.createElement('button');
      editBtn.className = 'edit';
      editBtn.textContent = 'Edytuj';
      editBtn.addEventListener('click', () => editFaktura(f));

      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'delete';
      deleteBtn.textContent = 'Usuń';
      deleteBtn.addEventListener('click', () => deleteFaktura(f.id));

      td.appendChild(editBtn);
      td.appendChild(deleteBtn);

      tbody.appendChild(tr);
    });
  }

  renderPagination(data.length === itemsPerPage);
}


function renderPagination(hasNextPage) {
  const container = document.querySelector('.pagination');
  if (!container) return;

  container.innerHTML = `
    <button ${currentPage === 1 ? 'disabled' : ''} onclick="loadFaktury(${currentPage - 1})">Poprzednia</button>
    <span>Strona ${currentPage}</span>
    <button ${!hasNextPage ? 'disabled' : ''} onclick="loadFaktury(${currentPage + 1})">Następna</button>
  `;
}

function showAddFaktura() {
  fakturaForm();
}

function editFaktura(f) {
  fakturaForm(f);
}

async function deleteFaktura(id) {
  showConfirmModal(async () => {
    const data = new FormData();
    data.append('action', 'delete');
    data.append('id', id);

    try {
      const res = await fetch('backend/mod_fv_zakup.php', { method: 'POST', body: data });
      const result = await res.json();
      if (!result.success) throw new Error("Błąd usuwania");
      loadFaktury();
    } catch (err) {
      alert("Błąd podczas usuwania: " + err.message);
    }
  });
}

function showConfirmModal(onConfirm) {
  const modal = document.getElementById("confirmModal");
  const yesBtn = document.getElementById("confirmYes");
  const noBtn = document.getElementById("confirmNo");

  modal.classList.remove("hidden");

  const newYes = yesBtn.cloneNode(true);
  const newNo = noBtn.cloneNode(true);
  yesBtn.parentNode.replaceChild(newYes, yesBtn);
  noBtn.parentNode.replaceChild(newNo, noBtn);

  newYes.addEventListener("click", () => {
    modal.classList.add("hidden");
    onConfirm();
  });

  newNo.addEventListener("click", () => {
    modal.classList.add("hidden");
  });
}

function fakturaForm(data = {}) {
  const wrapper = document.createElement('div');
  wrapper.classList.add('modal');

  const form = document.createElement('form');
  form.method = 'POST';
  form.enctype = 'multipart/form-data';

  const metale = ['miedź', 'aluminium', 'mosiądz', 'żelazo i stal', 'puszki'];
  const metalList = data.metal ? data.metal.split('\n') : [];
  const wagaList = data.waga ? data.waga.split('\n') : [];

  form.innerHTML = `
    <h3 style="margin-bottom: 20px;">${data.id ? 'Edytuj fakturę' : 'Dodaj fakturę'}</h3>
    <div class="scrollable-content">
      <input name="numer" placeholder="Nr faktury" value="${data.numer || ''}" required><br>
      <input type="date" name="data" value="${data.data || ''}" required><br>
      <input name="firma" placeholder="Firma" value="${data.firma || ''}" required><br>
      <div id="pozycje-container"></div>
      <button type="button" id="add-pozycja">+ Dodaj pozycję</button>
    </div>
    <div class="form-actions">
      <button type="submit">Zapisz</button>
      <button type="button" class="cancel">Anuluj</button>
    </div>
  `;

  const pozycjeContainer = form.querySelector('#pozycje-container');
  const addBtn = form.querySelector('#add-pozycja');

  function createPozycjaRow(metal = '', waga = '') {
    const index = pozycjeContainer.children.length;
    if (index >= 5) return;

    const row = document.createElement('div');
    row.className = 'pozycja-row';
    row.innerHTML = `
      <select class="metal-select" required>
        <option value="">-- wybierz metal --</option>
        ${metale.map(m => `<option value="${m}" ${m === metal ? 'selected' : ''}>${m}</option>`).join('')}
      </select>
      <input type="number" class="waga-input" step="0.01" min="0" placeholder="Waga (kg)" value="${waga}" required>
      <button type="button" class="remove-pozycja" title="Usuń">🗑</button>
    `;

    row.querySelector('.remove-pozycja').addEventListener('click', () => {
      row.remove();
      if (pozycjeContainer.children.length < 5) {
        addBtn.disabled = false;
        addBtn.style.opacity = 1;
        addBtn.style.cursor = 'pointer';
      }
    });

    pozycjeContainer.appendChild(row);
    row.scrollIntoView({ behavior: 'smooth', block: 'end' });

    if (pozycjeContainer.children.length >= 5) {
      addBtn.disabled = true;
      addBtn.style.opacity = 0.5;
      addBtn.style.cursor = 'not-allowed';
    }
  }

  const maxRows = Math.max(metalList.length, wagaList.length);
  for (let i = 0; i < maxRows; i++) {
    createPozycjaRow(metalList[i] || '', wagaList[i] || '');
  }
  if (pozycjeContainer.children.length === 0) createPozycjaRow();

  addBtn.addEventListener('click', () => {
    if (pozycjeContainer.children.length < 5) {
      createPozycjaRow();
    }
  });

  form.onsubmit = async (e) => {
    e.preventDefault();
    const metals = [], weights = [];
    pozycjeContainer.querySelectorAll('.pozycja-row').forEach(row => {
      const metal = row.querySelector('.metal-select')?.value?.trim();
      const waga = row.querySelector('.waga-input')?.value?.trim();
      if (metal && waga && !isNaN(waga)) {
        metals.push(metal);
        weights.push(waga);
      }
    });

    if (metals.length === 0) {
      alert("Dodaj przynajmniej jedną poprawną pozycję.");
      return;
    }

    const formData = new FormData(form);
    formData.set('metal', metals.join('\n'));
    formData.set('waga', weights.join('\n'));
    formData.append('action', data.id ? 'update' : 'create');
    if (data.id) formData.append('id', data.id);

    try {
      const res = await fetch('backend/mod_fv_zakup.php', { method: 'POST', body: formData });
      const text = await res.text();
      const result = JSON.parse(text);

      if (!result.success) throw new Error(result.error || "Nieznany błąd");
      wrapper.remove();
      loadFaktury(currentPage);
    } catch (err) {
      alert("Błąd faktury: " + err.message);
    }
  };

  form.classList.add('modal-form');
  wrapper.appendChild(form);
  document.body.appendChild(wrapper);

  form.querySelector('.cancel').addEventListener('click', () => wrapper.remove());
  return form;
}
