let currentPage = 1;
const itemsPerPage = 20;

document.addEventListener('DOMContentLoaded', () => {
  loadKontrahenci(currentPage);
  document.querySelector('.add-button')?.addEventListener('click', showAddForm);
});

async function loadKontrahenci(page = 1) {
    currentPage = page;
    const offset = (page - 1) * itemsPerPage;
  
    const res = await fetch(`backend/mod_kontrahenci.php?limit=${itemsPerPage}&offset=${offset}`);
  
    const tbody = document.querySelector('tbody');
    const table = tbody.closest('table');
    tbody.innerHTML = '';
    document.querySelectorAll('.faktura-card').forEach(card => card.remove());
  
    if (!res.ok) {
      tbody.innerHTML = `<tr><td colspan="7">Błąd pobierania danych</td></tr>`;
      return;
    }
  
    const contractors = await res.json();
    const isMobile = window.innerWidth <= 768;
  
    if (!Array.isArray(contractors) || contractors.length === 0) {
      if (isMobile) {
        const div = document.createElement('div');
        div.className = 'faktura-card';
        div.textContent = 'Brak danych';
        table.insertAdjacentElement('beforebegin', div);
      } else {
        tbody.innerHTML = `<tr><td colspan="7">Brak danych</td></tr>`;
      }
      renderPagination(false);
      return;
    }
  
    if (isMobile) {
      table.style.display = 'none';
      contractors.forEach(c => {
        const div = document.createElement('div');
        div.className = 'faktura-card';
        div.innerHTML = `
          <p><strong>Nazwa firmy:</strong> ${c.nazwa_firmy}</p>
          <p><strong>BDO:</strong> ${c.bdo}</p>
          <p><strong>NIP:</strong> ${c.nip}</p>
          <p><strong>Adres:</strong> ${c.adres}</p>
          <p><strong>Telefon:</strong> ${c.telefon}</p>
          <p><strong>Email:</strong> ${c.mail}</p>
        `;
  
        const actions = document.createElement('div');
        actions.className = 'card-actions';
  
        const editBtn = document.createElement('button');
        editBtn.className = 'edit';
        editBtn.textContent = 'Edytuj';
        editBtn.addEventListener('click', () => editKontrahent(c));
  
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete';
        deleteBtn.textContent = 'Usuń';
        deleteBtn.addEventListener('click', () => deleteKontrahent(c.id));
  
        actions.appendChild(editBtn);
        actions.appendChild(deleteBtn);
        div.appendChild(actions);
        table.insertAdjacentElement('beforebegin', div);
      });
    } else {
      table.style.display = '';
      contractors.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${c.nazwa_firmy}</td>
          <td>${c.bdo}</td>
          <td>${c.nip}</td>
          <td>${c.adres}</td>
          <td>${c.telefon}</td>
          <td>${c.mail}</td>
          <td></td>
        `;
  
        const td = tr.querySelector('td:last-child');
  
        const editBtn = document.createElement('button');
        editBtn.className = 'edit';
        editBtn.textContent = 'Edytuj';
        editBtn.addEventListener('click', () => editKontrahent(c));
  
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete';
        deleteBtn.textContent = 'Usuń';
        deleteBtn.addEventListener('click', () => deleteKontrahent(c.id));
  
        td.appendChild(editBtn);
        td.appendChild(deleteBtn);
  
        tbody.appendChild(tr);
      });
    }
  
    renderPagination(contractors.length === itemsPerPage);
  }
  

function renderPagination(hasNextPage) {
  const container = document.querySelector('.pagination');
  if (!container) return;

  container.innerHTML = `
    <button ${currentPage === 1 ? 'disabled' : ''} onclick="loadKontrahenci(${currentPage - 1})">Poprzednia</button>
    <span>Strona ${currentPage}</span>
    <button ${!hasNextPage ? 'disabled' : ''} onclick="loadKontrahenci(${currentPage + 1})">Następna</button>
  `;
}

function showAddForm() {
  const wrapper = promptForm();
  const form = wrapper.querySelector('form');

  form.onsubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    data.append('action', 'create'); 
    await fetch('backend/mod_kontrahenci.php', { method: 'POST', body: data });
    wrapper.remove();
    loadKontrahenci(currentPage);
  };
}

function editKontrahent(k) {
  const wrapper = promptForm(k);
  const form = wrapper.querySelector('form');

  form.onsubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    data.append('action', 'update');
    data.append('id', k.id);
    await fetch('backend/mod_kontrahenci.php', { method: 'POST', body: data });
    wrapper.remove();
    loadKontrahenci(currentPage);
  };
}

async function deleteKontrahent(id) {
  showConfirmModal(async () => {
    const data = new FormData();
    data.append('action', 'delete');
    data.append('id', id);
    await fetch('backend/mod_kontrahenci.php', { method: 'POST', body: data });
    loadKontrahenci(currentPage);
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

function promptForm(data = {}) {
  const wrapper = document.createElement('div');
  wrapper.classList.add('modal');

  const form = document.createElement('form');
  form.innerHTML = `
    <h3 style="margin-bottom: 10px;">${data.id ? 'Edytuj kontrahenta' : 'Dodaj kontrahenta'}</h3>
    <input name="nazwa_firmy" placeholder="Nazwa firmy" value="${data.nazwa_firmy || ''}" required>
    <input name="bdo" placeholder="BDO" value="${data.bdo ||  ''}" required>
    <input name="nip" placeholder="NIP - 10 cyfr" value="${data.nip ||  ''}" required>
    <textarea name="adres" placeholder="Adres">${data.adres || ''}</textarea>
    <input name="telefon" placeholder="Telefon" value="${data.telefon || ''}" required>
    <input name="mail" placeholder="Email" value="${data.mail || ''}" required>
    <div class="form-actions">
      <button type="submit">Zapisz</button>
      <button type="button" class="cancel">Anuluj</button>
    </div>
  `;

  form.classList.add('modal-form');
  wrapper.appendChild(form);
  document.body.appendChild(wrapper);

  form.querySelector('.cancel').addEventListener('click', () => {
    wrapper.remove();
  });

  wrapper.addEventListener('click', (e) => {
    if (e.target === wrapper) {
      wrapper.remove();
    }
  });

  return wrapper;
}
