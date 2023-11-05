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

$klasa = ""; 

if (isset($_SESSION['user']) && isset($_SESSION['user']['klasa'])) {
    $klasa = $_SESSION['user']['klasa'];
}

if (isset($_SESSION['user'])) {
    $username = $_SESSION['user']['username'];
    if ($username === "admin") {
        $sectionVisible = false;
    } else {
        $sectionVisible = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Twoja Strona</title>
    <script src="https://kit.fontawesome.com/82451c73a2.js" crossorigin="anonymous"></script>
</head>
<body class="planlekcji">
    <div class="header">
        <div class="profile">
            <img src="images/avatrar.png" alt="Avatar">
            <h2><?php echo $username ?></h2>
            <p>Klasa: <?php echo $klasa; ?></p>
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
    <div class="content">
        <?php
        $sql = "SELECT * FROM plan_lekcji WHERE klasa = '$klasa'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<table class="timetable">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Godzina</th>';
            echo '<th>Poniedziałek</th>';
            echo '<th>Wtorek</th>';
            echo '<th>Środa</th>';
            echo '<th>Czwartek</th>';
            echo '<th>Piątek</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['godzina'] . '</td>';
                echo '<td>' . $row['poniedzialek'] . '</td>';
                echo '<td>' . $row['wtorek'] . '</td>';
                echo '<td>' . $row['sroda'] . '</td>';
                echo '<td>' . $row['czwartek'] . '</td>';
                echo '<td>' . $row['piatek'] . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo "Brak danych do wyświetlenia.";
        }
        ?>
    </div>
</body>
</html>
