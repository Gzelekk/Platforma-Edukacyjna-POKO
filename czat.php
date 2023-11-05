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
    $userClass = $logged_in_user['klasa'];
    if($username === "admin"){
        $sectionVisible = false;
    } else $sectionVisible = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $message = $_POST['message'];

    $insert_message = $conn->prepare("INSERT INTO chat_messages (sender, message, class) VALUES (?, ?, ?)");
    $insert_message->bind_param("sss", $username, $message, $userClass);

    if ($insert_message->execute()) {
        header("Location: czat.php");
    } else {
      
    }
}

$get_messages = $conn->prepare("SELECT * FROM chat_messages WHERE class = ?");
$get_messages->bind_param("s", $userClass);
$get_messages->execute();
$messages_result = $get_messages->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<script>
        function scrollToBottom() {
            var chatBox = document.getElementById('chat-box');
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        window.onload = function() {
            scrollToBottom();
        }

    </script>
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
        <?php
        echo "<h2> Czat Klasowy Klasy: $userClass </h2>"
        ?>
                <div id="chat-box" style="height: 300px; overflow-y: scroll;">
            <?php while ($row = $messages_result->fetch_assoc()) : ?>
                <div class="message" style=" display: flex; align-items: center;margin-bottom: 10px;">
                    <img src="images/avatrar.png" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;">
                    <p><strong><?php echo $row['sender']; ?>:</strong> <?php echo $row['message']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
        <form method="post">
            <input type="text" name="message" placeholder="Napisz wiadomość" required>
            <input type="submit" name="send_message" value="Wyślij">
        </form>
    </div>

    <script src="script.js"></script>
</body>
</html>
