let currentPage = 1;
const itemsPerPage = 20;

document.addEventListener('DOMContentLoaded', () => {
  loadUzytkownicy(currentPage);
  document.querySelector('.add-button')?.addEventListener('click', showAddUser);
});

async function loadUzytkownicy(page = 1) {
    currentPage = page;
    const offset = (page - 1) * itemsPerPage;
  
    const res = await fetch(`backend/mod_uzytkownicy.php?limit=${itemsPerPage}&offset=${offset}`);
  
    const tbody = document.querySelector('tbody');
    const table = tbody.closest('table');
    tbody.innerHTML = '';
    document.querySelectorAll('.faktura-card').forEach(card => card.remove());
  
    if (!res.ok) {
      tbody.innerHTML = `<tr><td colspan="6">Brak dostępu</td></tr>`;
      return;
    }
  
    const users = await res.json();
    const isMobile = window.innerWidth <= 768;
  
    if (!Array.isArray(users) || users.length === 0) {
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
      users.forEach(u => {
        const div = document.createElement('div');
        div.className = 'faktura-card';
        div.innerHTML = `
          <p><strong>Imię:</strong> ${u.imie}</p>
          <p><strong>Nazwisko:</strong> ${u.nazwisko}</p>
          <p><strong>Email:</strong> ${u.email}</p>
          <p><strong>Hasło:</strong> ••••••••</p>
          <p><strong>Rola:</strong> ${u.rola}</p>
        `;
  
        const actions = document.createElement('div');
        actions.className = 'card-actions';
  
        const editBtn = document.createElement('button');
        editBtn.className = 'edit';
        editBtn.textContent = 'Edytuj';
        editBtn.addEventListener('click', () => editUser(u));
  
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete';
        deleteBtn.textContent = 'Usuń';
        deleteBtn.addEventListener('click', () => deleteUser(u.id));
  
        actions.appendChild(editBtn);
        actions.appendChild(deleteBtn);
        div.appendChild(actions);
        table.insertAdjacentElement('beforebegin', div);
      });
    } else {
      table.style.display = '';
      users.forEach(u => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${u.imie}</td>
          <td>${u.nazwisko}</td>
          <td>${u.email}</td>
          <td>••••••••</td>
          <td>${u.rola}</td>
          <td></td>
        `;
  
        const td = tr.querySelector('td:last-child');
  
        const editBtn = document.createElement('button');
        editBtn.className = 'edit';
        editBtn.textContent = 'Edytuj';
        editBtn.addEventListener('click', () => editUser(u));
  
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete';
        deleteBtn.textContent = 'Usuń';
        deleteBtn.addEventListener('click', () => deleteUser(u.id));
  
        td.appendChild(editBtn);
        td.appendChild(deleteBtn);
  
        tbody.appendChild(tr);
      });
    }
  
    renderPagination(users.length === itemsPerPage);
}
  

function renderPagination(hasNextPage) {
  const container = document.querySelector('.pagination');
  if (!container) return;

  container.innerHTML = `
    <button ${currentPage === 1 ? 'disabled' : ''} onclick="loadUzytkownicy(${currentPage - 1})">Poprzednia</button>
    <span>Strona ${currentPage}</span>
    <button ${!hasNextPage ? 'disabled' : ''} onclick="loadUzytkownicy(${currentPage + 1})">Następna</button>
  `;
}

function showAddUser() {
  const wrapper = userForm();
  const form = wrapper.querySelector('form');

  form.onsubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    data.append('action', 'create');
    await fetch('backend/mod_uzytkownicy.php', { method: 'POST', body: data });
    wrapper.remove();
    loadUzytkownicy(currentPage);
  };
}

function editUser(u) {
  const wrapper = userForm(u);
  const form = wrapper.querySelector('form');

  form.onsubmit = async (e) => {
    e.preventDefault();
    const data = new FormData(form);
    data.append('action', 'update');
    data.append('id', u.id); 
    await fetch('backend/mod_uzytkownicy.php', { method: 'POST', body: data });
    wrapper.remove();
    loadUzytkownicy(currentPage);
  };
}

async function deleteUser(id) {
  showConfirmModal(async () => {
    const data = new FormData();
    data.append('action', 'delete');
    data.append('id', id);
    await fetch('backend/mod_uzytkownicy.php', { method: 'POST', body: data });
    loadUzytkownicy(currentPage);
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

function userForm(data = {}) {
  const wrapper = document.createElement('div');
  wrapper.classList.add('modal');

  const form = document.createElement('form');
  form.innerHTML = `
    <h3 style="margin-bottom: 10px;">${data.id ? 'Edytuj użytkownika' : 'Dodaj użytkownika'}</h3>
    <input name="imie" placeholder="Imię" value="${data.imie || ''}" required>
    <input name="nazwisko" placeholder="Nazwisko" value="${data.nazwisko || ''}" required>
    <input name="email" placeholder="E-mail" value="${data.email || ''}" required>
    ${data.id ? '' : '<input name="haslo" type="password" placeholder="Hasło" required>'}
    <input name="rola" placeholder="Rola" value="${data.rola || ''}" required>
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
