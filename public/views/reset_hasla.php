<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ScrapNest logowanie</title>
  <link rel="stylesheet" href="css/logowanie.css" />
  <link rel="icon" href="images/favicon.png" type="image/png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="logo-box">
      <img src="images/logo.png" alt="ScrapNest Logo" class="logo" />
    </div>
    <div class="login-box">
      <form id="resetForm">
        <h2 style="text-align: center;">Resetuj hasło</h2>
        <input type="email" name="email" id="email" placeholder="Twój adres e-mail" required />
        <button type="submit">Wyświetl nowe hasło</button>
        <div class="options">
          <a href="logowanie.php" class="option1">Wróć do logowania</a>
        </div>
        <div id="response"></div>
      </form>
    </div>
  </div>

  <script>
    const form = document.getElementById('resetForm');
    const responseDiv = document.getElementById('response');

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(form);

      fetch('backend/reset_hasla.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        responseDiv.innerHTML = data;
      })
      .catch(err => {
        responseDiv.innerHTML = '<p style="color:red;">Wystąpił błąd.</p>';
      });
    });
  </script>
</body>
</html>
