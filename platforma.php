<?php
session_start();
require_once"database.php";
if (!isset($_SESSION['zalogowany'])) {
    header('Location: logowanie.php');
    exit();
}
if (isset($_SESSION['user'])) {
    $logged_in_user = $_SESSION['user'];
    $username = $logged_in_user['username'];
}

if (isset($_SESSION['zalogowany']) && isset($_SESSION['user'])) {
    $logged_in_user = $_SESSION['user'];
    $username = $logged_in_user['username'];

    if ($username === "admin") {
        $sectionVisible = false;
    } else {
        $sectionVisible = true;
    }

    $isTeacher = $logged_in_user['nauczyciel'];

    if ($isTeacher === "1") {
        $sectionVisibleTeacher = true; 
    } else {
        $sectionVisibleTeacher = false;
    }
}
?>

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
<body style="background-image: url('images/1.png'); background-size: cover; background-repeat: no-repeat; background-attachment: fixed;">
    <div class="header">
        <div class="profile">
            <img src="images/avatrar.png" alt="Avatar">
            <h2><?php echo $username?>
            </h2>
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
                <li><a href="admin_user.php">Zarządzaj Użytkownikami</a></li>
                <li><a href="admin_plan.php">Zarządzaj Planem Lekcji</a></li>
                <li><a href="admin_klasa.php">Zarządzaj Klasami</a></li>
            </ul>
        </li>
        <?php endif; ?>
        <?php if ($sectionVisibleTeacher) : ?>
        <li><a href="oceny_nauczyciel.php"><i class="fa-solid fa-pen-to-square"></i>Oceny nauczyciela</a></li>
             <?php endif; ?>
        <?php if ($sectionVisible) : ?>
        <li><a href="planlekcji.php"><i class="fa-solid fa-pen-to-square"></i>Plan lekcji</a></li>
        <li><a href="oceny.php"><i class="fa-regular fa-clipboard"></i>Oceny</a></li>
        <li><a href="czat.php"><i class="fa-regular fa-comment"></i>Czat</a></li>
        <?php endif; ?>
        <li><a href="ustawienia.php"><i class="fa-solid fa-gear"></i>Ustawienia</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-power-off"></i>Wyloguj</a></li>
    </ul>
</div>
    </div>
    <div class="content">
    </div>
    <script src="script.js">
        var adminPanel = document.querySelector('.admin-panel');
    var adminOptions = document.querySelector('.admin-options');
    adminPanel.addEventListener('mouseenter', function() {
        adminOptions.style.display = 'block';
    });
    adminPanel.addEventListener('mouseleave', function() {
        adminOptions.style.display = 'none';
    });
     </script>
</body>
</html>

