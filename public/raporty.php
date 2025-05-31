<?php require 'backend/auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest - Faktury Sprzedaż</title>
  <link rel="stylesheet" href="css/common.css" />
  <link rel="stylesheet" href="css/common2.css" />
  <link rel="stylesheet" href="css/modal_add.css" />
  <link rel="stylesheet" href="css/modal.css" />
  <script src="js/logout.js"></script>
  <script src="js/raporty.js"></script>
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
      <a href="panel.php"><button>Panel</button></a>
      <a href="formularze.php"><button>Formularze</button></a>
      <a href="fv_zakup.php"><button>Faktury zakup</button></a>
      <a href="fv_sprzedaz.php"><button>Faktury sprzedaż</button></a>
      <a href="kontrahenci.php"><button>Kontrahenci</button></a>
      <a href="raporty.php"><button class="active">Raporty</button></a>
      <?php if (isset($_SESSION['user']) && $_SESSION['user']['rola'] === 'admin'): ?>
        <a href="uzytkownicy.php"><button>Użytkownicy</button></a>
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
        <h2>Raporty</h2>
      </div>
      <table>
        <thead>
          <tr>
            <th>Nazwa raportu</th>
            <th>Opis</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Raport dzienny</td>
            <td>Podsumowanie obrotów z wybranego dnia</td>
            <td>
              <button class="export" onclick="generujRaport('html')">Drukuj</button>
              <button class="export" onclick="generujRaport('csv')">Excel (CSV)</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="raporty-cards">
        <div class="raport-card">
          <p><strong>Nazwa raportu:</strong> Raport dzienny</p>
          <p><strong>Opis:</strong> Podsumowanie obrotów z wybranego dnia</p>
          <div class="card-actions">
            <button class="export" onclick="generujRaport('html')">Drukuj</button>
            <button class="export" onclick="generujRaport('csv')">Excel (CSV)</button>
          </div>
        </div>
      </div>
    </section>
  </main>
  <div id="dateModal" class="modal-form" style="display: none;">
    <h3 style="margin-bottom: 20px;">Podaj datę (RRRR-MM-DD):</h3>
    <input type="date" id="dateInput" />
    <div class="form-actions">
      <button type="button" class="cancel" onclick="closeModal()">Anuluj</button>
      <button type="submit" onclick="submitDate()">OK</button>
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
