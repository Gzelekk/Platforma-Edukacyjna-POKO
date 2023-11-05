<?php
session_start();
require_once "database.php";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

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

if ($sectionVisible) {
    $query = "SELECT user, angielski, polski, matematyka FROM oceny WHERE user = ?";
    $get_grades = $conn->prepare($query);
    $get_grades->bind_param("s", $username);
    $get_grades->execute();
    $grades_result = $get_grades->get_result();
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
    <div class="content">
        <h1>Oceny</h1>
        <table class="grades">
            <thead>
                <tr>
                    <th>Przedmiot</th>
                    <th>Ocena</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $grades_result->fetch_assoc()) : ?>
                    <tr>
                        <td>Angielski</td>
                        <td><?php echo $row['angielski']; ?></td>
                    </tr>
                    <tr>
                        <td>Polski</td>
                        <td><?php echo $row['polski']; ?></td>
                    </tr>
                    <tr>
                        <td>Matematyka</td>
                        <td><?php echo $row['matematyka']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
