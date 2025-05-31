<?php require_once __DIR__ . '/../backend/auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest - Faktury Zakup</title>
  <link rel="stylesheet" href="css/common.css" />
  <link rel="stylesheet" href="css/common2.css" />
  <link rel="stylesheet" href="css/modal.css" />
  <link rel="stylesheet" href="css/modal_add.css" />
  <script src="js/logout.js"></script>
</head>
<body>
  <header class="top-bar">
    <div class="logo">
      <img src="images/logo2.png" alt="Logo SCRAPNEST" />
      <span>SCRAPNEST</span>
    </div>
    <div class="hamburger" onclick="toggleNav()">
      <div></div>
      <div></div>
      <div></div>
    </div>
    <nav class="nav">
      <a href="/panel"><button>Panel</button></a>
      <a href="/formularze"><button>Formularze</button></a>
      <a href="/fv_zakup"><button class="active">Faktury zakup</button></a>
      <a href="/fv_sprzedaz"><button>Faktury sprzedaż</button></a>
      <a href="/kontrahenci"><button>Kontrahenci</button></a>
      <a href="/raporty"><button>Raporty</button></a>
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['rola'] === 'admin'): ?>
        <a href="/uzytkownicy"><button>Użytkownicy</button></a>
      <?php endif; ?>
      <div class="avatar-container">
        <div class="avatar" id="avatarBtn">
          <img src="images/avatar.png" alt="avatar">
        </div>
        <div class="dropdown" id="dropdownMenu">
          <button onclick="logout()">Wyloguj się</button>
        </div>
      </div>
    </nav>
  </header>
  <main class="content">
    <section class="section">
      <div class="section-header">
        <h2>Faktury zakupowe</h2>
        <button class="add-button">+ Dodaj fakturę</button>
      </div>
      <table>
        <thead>
          <tr><th>Nr. faktury</th><th>Data</th><th>Firma</th><th>Metal</th><th>Waga</th><th></th></tr>
        </thead>
        <tbody><tr><td colspan="6">Ładowanie danych...</td></tr></tbody>
      </table>
      <div class="pagination"></div>
    </section>
  </main>
  <script src="js/fv_zakup.js"></script>
  <div id="confirmModal" class="modal hidden">
    <div class="modal-content">
      <p>Na pewno usunąć fakturę?</p>
      <div class="modal-actions">
        <button id="confirmYes">Tak</button>
        <button id="confirmNo">Anuluj</button>
      </div>
    </div>
  </div>
  <script>
    function toggleNav() {
        const nav = document.querySelector('.nav');
        if (nav) {
        nav.classList.toggle('active');
     }
    }
  </script>
</body>
</html>