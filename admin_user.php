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
        header('Location: logowanie.php');
        $sectionVisible = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dodaj_uzytkownika'])) {
    $password = $_POST['passwd'];
    $imieNazwisko = $_POST['imie_nazwisko'];
    $nick = $_POST['nick'];
    $pesel = $_POST['pesel'];
    $email = $_POST['email'];
    $klasa = $_POST['klasa'];
    $nauczyciel = isset($_POST['nauczyciel']) ? 1 : 0;
    $przedmiot = $_POST['przedmiot'];

    if ($_POST['wybierz-uzytkownika'] === 'nowy') {
        $insert_user_sql = $conn->prepare("INSERT INTO users (imie_nazwisko, pesel, email, klasa, username, password, nauczyciel, przedmiot) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_user_sql->bind_param("ssssssss", $imieNazwisko, $pesel, $email, $klasa, $nick, $password, $nauczyciel, $przedmiot);
        if ($insert_user_sql->execute()) {
            header('Location: admin_user.php');
        } else {
            echo "Błąd podczas dodawania użytkownika: " . $conn->error;
        }
    } else {
        $selectedUserId = $_POST['wybierz-uzytkownika'];
        $update_user_sql = $conn->prepare("UPDATE users SET imie_nazwisko = ?, pesel = ?, email = ?, klasa = ?, username = ?, password = ?, nauczyciel = ?, przedmiot = ? WHERE id = ?");
        $update_user_sql->bind_param("ssssssssi", $imieNazwisko, $pesel, $email, $klasa, $nick, $password, $nauczyciel, $przedmiot, $selectedUserId);

        if ($update_user_sql->execute()) {
            header('Location: admin_user.php');
        } else {
            echo "Błąd podczas aktualizacji użytkownika: " . $conn->error;
        }
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
    <script>
        function togglePrzedmiotField() {
            const checkbox = document.getElementById("nauczyciel");
            const przedmiotDiv = document.getElementById("przedmiot-div");

            if (checkbox.checked) {
                przedmiotDiv.style.display = "block";
            } else {
                przedmiotDiv.style.display = "none";
            }
        }
    </script>
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
        <h2>Dodaj nowego użytkownika</h2>
        <form method="post" class="notifications-container1">
            <select id="wybierz-uzytkownika" name="wybierz-uzytkownika">
                <option value="nowy">Nowy użytkownik</option>
                <?php
                $query = "SELECT id, username FROM users";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $userId = $row['id'];
                    $userName = $row['username'];
                    echo "<option value='$userId'>$userName</option>";
                }
                ?>
            </select>

            <input type="text" id="imie_nazwisko" name="imie_nazwisko" placeholder="Imię i Nazwisko" required>
            <input type="text" id="pesel" name="pesel" placeholder="PESEL" required>
            <input type="email" id="email" name="email" placeholder="Adres e-mail" required>
            <input type="text" id="nick" name="nick" placeholder="Nazwa użytkownika" required>
            <input type="text" id="passwd" name="passwd" placeholder="Hasło Użytkownika" required>
            <select id="klasa" name="klasa" required>
                <option value="wybierz">Przypisz Klasę</option>
                <?php
                $query = "SELECT DISTINCT klasa FROM plan_lekcji";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $class = $row['klasa'];
                    echo "<option value='$class'>$class</option>";
                }
                ?>
            </select>
            
            <label for="nauczyciel">Nauczyciel:</label>
            <input type="checkbox" id="nauczyciel" name="nauczyciel" value="1" onclick="togglePrzedmiotField();">

            <div id="przedmiot-div" style="display: none;">
                <label for="przedmiot">Przedmiot:</label>
                <select id="przedmiot" name="przedmiot">
                    <option value="angielski">Angielski</option>
                    <option value="polski">Polski</option>
                    <option value="matematyka">Matematyka</option>
                </select>
            </div>

            <input type="submit" name="dodaj_uzytkownika" value="Dodaj lub Aktualizuj użytkownika">
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>