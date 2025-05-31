<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest - rejestracja</title>
  <link rel="stylesheet" href="css/rejestracja.css" />
  <link rel="icon" href="images/favicon.png" type="image/png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="logo-box">
      <img src="images/logo.png" alt="ScrapNest Logo" class="logo" />
    </div>
    <div class="register-box">
        <form action="backend/zapis_rejestracja.php" method="POST">
            <input type="text" name="imie" placeholder="Imię" required />
            <input type="text" name="nazwisko" placeholder="Nazwisko" required />
            <input type="email" name="email" placeholder="Adres e-mail" required />
            <input type="password" name="haslo" placeholder="Hasło" required />
            <input type="password" name="powtorzhaslo" placeholder="Powtórz hasło" required />
            <select name="rola" required class="select-rola">
              <option value="" disabled selected hidden>Wybierz rolę</option>
              <option value="magazynier">Magazynier</option>
              <option value="księgowa">Księgowa</option>
            </select>
            <button type="submit">Zarejestruj się</button>
            <?php if (isset($_GET['error'])): ?>
              <p class="error-message">
                <?php
                  switch ($_GET['error']) {
                    case 'puste':
                      echo "Wszystkie pola są wymagane";
                      break;
                    case 'hasla':
                      echo "Hasła nie są identyczne";
                      break;
                    case 'email':
                      echo "Podany adresie e-mail już istnieje";
                      break;
                  }
                ?>
              </p>
            <?php endif; ?>
        </form>
    </div>
  </div>
</body>
</html>
