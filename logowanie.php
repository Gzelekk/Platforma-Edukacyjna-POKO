<?php
session_start();
require_once"database.php";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_user_sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($check_user_sql);

    if ($result->num_rows > 0) {
        echo "Użytkownik o tej nazwie już istnieje. Proszę wybrać inną nazwę.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_user_sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashed_password')";
        
        if ($conn->query($insert_user_sql) === TRUE) {
            echo "Rejestracja zakończona sukcesem. Możesz się teraz zalogować.";
        } else {
            echo "Błąd podczas rejestracji: " . $conn->error;
        }
    }
}

if (isset($_POST['login'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $nauczyciel = $_POST['nauczyciel'];

        $get_user_sql = "SELECT * FROM users WHERE username='$username'";
        $result = $conn->query($get_user_sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['zalogowany'] = true;
                $logged_in_user = array(
                    'username' => $username,
                    'klasa' => $row['klasa'],
                    'nauczyciel' => $row['nauczyciel'],
                );
                $_SESSION['user'] = $logged_in_user;
                header('Location: platforma.php');
            } else {
                $logerror = "Niepoprawna nazwa użytkownika lub hasło.";
            }
        } else {
            $logerror = "Niepoprawna nazwa użytkownika lub hasło.";
        }
    }
}

if (isset($_POST['register'])) {
    if (isset($_POST['new-username']) && isset($_POST['new-password'])){
    $newUsername = $_POST['new-username'];
    $newPassword = $_POST['new-password'];

 
    $check_username_sql = "SELECT * FROM users WHERE username='$newUsername'";
    $result = $conn->query($check_username_sql);

    if ($result->num_rows > 0) {
        echo "Błąd rejestracji. Użytkownik o takiej nazwie już istnieje.";
    } else {

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $insert_user_sql = "INSERT INTO users (username, password) VALUES ('$newUsername', '$hashedPassword')";
        
        if ($conn->query($insert_user_sql) === TRUE) {
            echo "Rejestracja pomyślna. Możesz teraz zalogować się jako $newUsername.";
        } else {
            echo "Błąd rejestracji: " . $conn->error;
        }
    }
}}

?>

<!DOCTYPE html>
<html class="html1" lang="pl" dir="ltr">
   <head>
      <meta charset="utf-8">
      <title>Platforma Poko</title>
      <link rel="stylesheet" href="style.css">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body class="log">
    <div class="back">
    <img src="images/login-img.jpg" class="img-login">
      <div class="wrapper" id="login">
         <div class="title">Logowanie</div>
         <form method="post">
            <div class="field">
               <input type="text" id ="username" name="username" required>
               <label for="username" >Nazwa użytkownika</label>
            </div>
            <div class="field">
               <input type="password" name="password" id="password" required>
               <label for="password">Hasło</label>
            </div>
            <div class="field">
               <input type="submit" name="login">   
               <?php
                    if (!empty($logerror)) {
                        echo '<br>';
                        echo '<div style="text-align: center; color: red;">';
                        echo $logerror;
                        echo '</div>';
                      }
              ?>          
            </div>
            <div class="signup-link" id="switch-button">  Nie posiadasz konta? <a href="#">Zarejestruj się</a> </div>
         </form>
      </div>
      

      <div class="wrapper" id="register" style="display: none";>
         <div class="title">Rejestracja</div>
         <form method="post">
            <div class="field">
               <input type="text" id ="username" name="username" required>
               <label for="username" >Nazwa użytkownika</label>
            </div>
            <div class="field">
               <input type="password" name="password" id="password" required>
               <label for="password">Hasło</label>
            </div>
            <div class="field">
               <input type="submit" name="register">
            </div>
            <div class="signup-link" id="switch-button">  Posiadasz Konto? <a href="#">Zaloguj się</button> </a>
         </form>
      </div>
      </div>
      <script>
        const Button = document.getElementById('switch-button');
        const login = document.getElementById('login');
        const register= document.getElementById('register');

        Button.addEventListener('click', function() {
            if (login.style.display === 'block') {
                login.style.display = 'none';
                register.style.display = 'block';
            } else {
                register.style.display = 'none';
                login.style.display = 'block';
            }
        });
        </script>
   </body>
</html>

