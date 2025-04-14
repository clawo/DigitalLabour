<!-- Schriftart einbinden -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">

<style>
  .header {
    width: 100%;
    max-width: 1920px;
    height: 100px;
    background-color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 5%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    font-family: 'Bebas Neue', sans-serif;
    box-sizing: border-box;
  }

  .logo img {
    height: 150px;
    max-height: 200px;
    width: auto;
  }

  .nav {
    display: flex;
    gap: 100px;
    font-size: 1.8rem;
    flex-wrap: wrap;
    justify-content: center;
  }

  .nav a {
    text-decoration: none;
    color: black;
    transition: color 0.3s;
    white-space: nowrap;
  }

  .nav a:hover {
    color: #555;
  }

  .login-button {
    background-color: #1e2a38;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 1.2rem;
    white-space: nowrap;
  }

  .login-button:hover {
    background-color: #2e3e50;
  }

  @media (max-width: 768px) {
    .header {
      flex-direction: column;
      height: auto;
      padding: 20px;
      gap: 15px;
    }

    .nav {
      flex-direction: column;
      gap: 10px;
    }

    .logo img {
      height: 50px;
    }
  }
</style>

<div class="header">
  <div class="logo">
    <a href="/"><img src="../images/logo.png" alt="Examwise Logo"></a>
  </div>
  <div class="nav">
    <a href="#">Suchen</a>
    <a href="#">Für Dozenten</a>
    <a href="#">Für Studenten</a>
  </div>
  <a href="#" class="login-button">Login-Button</a>
</div>