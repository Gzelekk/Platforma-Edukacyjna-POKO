<?php
session_start();
require_once "database.php";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

if (!isset($_SESSION['zalogowany'])) {
    header('Location: logowanie.php');
    exit();
}

if (isset($_SESSION['user'])) {
    $logged_in_user = $_SESSION['user'];
    $username = $logged_in_user['username'];
}

if (isset($_SESSION['user'])) {
    $username = $_SESSION['user']['username'];
    if ($username === "admin") {
        $sectionVisible = false;
    } else {
        $sectionVisible = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['zmien_haslo'])) {
    $nowe_haslo = $_POST['nowe_haslo'];
    $potwierdz_haslo = $_POST['potwierdz_haslo'];

    if ($nowe_haslo === $potwierdz_haslo) {
        $hashed_password = password_hash($nowe_haslo, PASSWORD_DEFAULT);
        $update_password_sql = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $update_password_sql->bind_param("ss", $hashed_password, $username);

        if ($update_password_sql->execute()) {
            $password_change_success = true;
        } else {
            $password_change_error = "Błąd podczas zmiany hasła: " . $conn->error;
        }
    } else {
        $password_change_error = "Nowe hasło i jego potwierdzenie nie pasują do siebie.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Ustawienia</title>
    <script src="https://kit.fontawesome.com/82451c73a2.js" crossorigin="anonymous"></script>
</head>
<body class="planlekcji">
    <div class="header">
    <div class="profile">
            <img src="images/avatrar.png" alt="Avatar">
            <h2><?php echo $username ?></h2>
            <p>Klasa: <?php echo $logged_in_user['klasa']; ?></p>
        </div>
        <div class="menu">
    <ul>
            <li><a href="powiadomienia.php"><i class="fa-regular fa-bell"></i>Powiadomienia</a></li>
            <?php if ($username === 'admin') : ?>
            <li class="admin-panel">
                <a href="#"><i class="fa-solid fa-briefcase"></i>Admin Panel</a>
                <ul class="admin-options">
                    <li><a href="admin_pow.php">Zarządzaj Powiadomieniami</a></li>
                    <li><a href="zarzadzaj_rolami.php">Zarządzaj rolami</a></li>
                </ul>
            </li>
            <?php endif; ?>
            <?php if ($sectionVisible) : ?>
            <li><a href="planlekcji.php"><i class="fa-solid fa-pen-to-square"></i>Plan lekcji</a></li>
            <li><a href="oceny.php"><i class="fa-regular fa-clipboard"></i>Oceny</a></li>
            <li><a href="czat.php"><i class="fa-regular fa-comment"></i>Czat</a></li>
            <?php endif; ?>
            <li><a href="#"><i class="fa-solid fa-gear"></i>Ustawienia</a></li>
            <li><a href="logout.php"><i class="fa-solid fa-power-off"></i>Wyloguj</a></li>
    </ul>
        </div>
    </div>
    </div>

    <div class="notifications-container1">
        <h2>Ustawienia użytkownika: <?php echo $username; ?></h2>
        <form method="post">
            <h3>Zmiana hasła</h3>
            <?php if (isset($password_change_error)) { ?>
                <p style="color: red;"><?php echo $password_change_error; ?></p>
            <?php } ?>
            <?php if (isset($password_change_success) && $password_change_success) { ?>
                <p style="color: green;">Hasło zostało zmienione pomyślnie.</p>
            <?php } ?>
            <input type="password" name="nowe_haslo" placeholder="Nowe hasło" required>
            <input type="password" name="potwierdz_haslo" placeholder="Potwierdź nowe hasło" required>
            <input type="submit" name="zmien_haslo" value="Zmień hasło">
        </form>

        <a href="logout.php">Wyloguj</a>
    </div>

    <script src="script.js"></script>
</body>
</html>
