<?php
session_start();
require_once"database.php";
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
    if($username === "admin"){
        $sectionVisible = false;
    } else {
        header('Location: logowanie.php');
        $sectionVisible = true;
    } 
}
    if (isset($_POST['nowe_powiadomienie'])) {
        $nowepowiadomienie = $_POST['nowe_powiadomienie'];
        $date = date('Y-m-d H:i:s');
            $insert_user_sql = "INSERT INTO powiadomienia (powiadomienie, data) VALUES ('$nowepowiadomienie', '$date')";

            if ($conn->query($insert_user_sql) === TRUE) {
            } else {
            }
        }
        $query = "SELECT powiadomienie, data, id FROM powiadomienia ORDER BY data DESC";
        $result = mysqli_query($conn, $query);
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $delete_sql = "DELETE FROM powiadomienia WHERE id = $id";
            
            if ($conn->query($delete_sql) === TRUE) {
                echo "Powiadomienie zostało usunięte.";
            } else {
                echo "Błąd podczas usuwania powiadomienia: " . $conn->error;
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
        <?php if ($sectionVisible) : ?>
        <li><a href="planlekcji.php"><i class="fa-solid fa-pen-to-square"></i>Plan lekcji</a></li>
        <li><a href="oceny.php"><i class="fa-regular fa-clipboard"></i>Oceny</a></li>
        <?php endif; ?>
        <li><a href="#"><i class="fa-solid fa-gear"></i>Ustawienia</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-power-off"></i>Wyloguj</a></li>
    </ul>
</div>
    </div>
    <div class="content">
    <div class="notifications-container">
            <div class="notifications">
            <?php
              while ($row = mysqli_fetch_assoc($result)) {
                $id = $row['id'];
                $tekst = $row['powiadomienie'];
                $data = $row['data'];
                echo '<div class="notification">';
                echo '<p class="notification-text">' . $tekst . '</p>';
                echo '<span class="notification-date">' . $data . '</span>';
                echo '<form method="post">';
                echo '<input type="hidden" name="id" value="' . $id . '">';
                echo '<button style="color: red;"><span class="fas fa-trash-alt"></span></button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
             </div>
    </div>
    <div class="add-notification">
        <h2>Dodaj nowe powiadomienie</h2>
        <form method="post">
            <textarea name="nowe_powiadomienie" rows="4" cols="50" placeholder="Wprowadź nowe powiadomienie" required></textarea>
            <br>
            <input type="submit" value="Dodaj">
        </form>
    </div>
    </div>
    <script src="script.js"></script>
</body>
</html>

