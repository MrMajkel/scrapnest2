<?php require 'backend/auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest Panel</title>
  <link rel="stylesheet" href="css/common.css" />
  <link rel="stylesheet" href="css/panel.css" />
  <script src="js/logout.js" defer></script>
  <script src="js/panel.js" defer></script>
  <style> body { display: none; } </style>
</head>
<body>
  <header class="top-bar">
    <div class="logo">
      <img src="images/logo2.png" alt="Logo" />
      <span>SCRAPNEST</span>
    </div>
    <div class="hamburger" onclick="toggleNav()">
      <div></div>
      <div></div>
      <div></div>
    </div>
    <nav class="nav">
      <a href="panel.php"><button class="active">Panel</button></a>
      <a href="formularze.php"><button>Formularze</button></a>
      <a href="fv_zakup.php"><button>Faktury zakup</button></a>
      <a href="fv_sprzedaz.php"><button>Faktury sprzedaż</button></a>
      <a href="kontrahenci.php"><button>Kontrahenci</button></a>
      <a href="raporty.php"><button>Raporty</button></a>
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
  <main class="dashboard">
    <section class="stats">
      <div class="card">
        <img src="images/recycle.png" alt="Liczba metali" />
        <p>Łączna liczba metali</p>
        <h2 id="laczna_ilosc_metali"></h2>
      </div>   
      <div class="card">
        <img src="images/cube.png" alt="Całkowita masa" />
        <p>Całkowita masa</p>
        <h2 id="calkowita_masa"></h2>
      </div>
      <div class="card">
        <img src="images/person.png" alt="Ilość odbiorców" />
        <p>Ilość odbiorców</p>
        <h2 id="liczba_odbiorcow"></h2>
      </div>
      <div class="card">
        <img src="images/transaction.png" alt="Ilość formularzy" />
        <p>Ilość formularzy</p>
        <h2 id="liczba_formularzy"></h2>
      </div>
    </section>
    <section class="tables">
      <div class="table-box">
        <h3>Magazyn</h3>
        <table>
          <thead><tr><th>Metal</th><th>Waga</th></tr></thead>
          <tbody id="magazynBody"></tbody>
        </table>
      </div>
      <div class="table-box">
        <h3>Ostatnie sprzedaże</h3>
        <table>
          <thead><tr><th>Data</th><th>Odbiorca</th><th>Metal</th><th>Waga</th></tr></thead>
          <tbody id="sprzedazeBody"></tbody>
        </table>
      </div>
    </section>
  </main>
  <script>
    fetch("backend/check_session.php")
      .then(res => res.text())
      .then(status => {
        if (status === "1") {
          document.body.style.display = "block";
          updateStats();
          updateMagazyn();
          updateSprzedaze();
        } else {
          window.location.href = "index.php";
        }
      });
  </script>
</body>
</html>
