<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest logowanie</title>
  <link rel="stylesheet" href="/css/logowanie.css" />
  <link rel="icon" href="/images/favicon.png" type="image/png" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="container">
    <div class="logo-box">
      <img src="/images/logo.png" alt="ScrapNest Logo" class="logo" />
    </div>
    <div class="login-box">
      <form action="/backend/login.php" method="POST">
        <input type="email" name="email" placeholder="Adres e-mail" required />
        <input type="password" name="haslo" placeholder="Hasło" required />
        <button type="submit">Zaloguj się</button>
        <div class="options">
          <a href="/reset_hasla" class="option1">Nie pamiętasz hasła?</a><br />
          <a href="/rejestracja" class="option2">Załóż nowe konto</a>
        </div>      

        <?php if (isset($_GET['zarejestrowano']) && $_GET['zarejestrowano'] == 1): ?>
          <p class="success-message">Rejestracja zakończona sukcesem</p>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
          <p class="error-message">Nieprawidłowy e-mail lub hasło</p>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
          <p class="success-message">Zostałeś wylogowany</p>
        <?php endif; ?>
      </form>
    </div>
  </div>
</body>
</html>
