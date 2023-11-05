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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dodaj_klase'])) {
    $klasa = $_POST['klasa'];
    $wychowawca = $_POST['wychowawca'];

    $insert_class_sql = $conn->prepare("INSERT INTO klasy (klasa, wychowawca) VALUES (?, ?)");
    $insert_class_sql->bind_param("ss", $klasa, $wychowawca);

    $query = "INSERT INTO plan_lekcji (godzina, poniedzialek, wtorek, sroda, czwartek, piatek, klasa) VALUES
    ('07:10 - 07:55', 'Matematyka', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('08:00 - 08:45', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('08:55 - 09:40', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('09:50 - 10:35', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('10:50 - 11:35', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('11:45 - 12:30', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('12:40 - 13:25', 'Matematyka', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('13:30 - 14:15', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('14:20 - 15:05', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa'),
    ('15:10 - 15:55', 'bbb', 'Fizyka', 'Chemia', 'Polski', 'Polski', '$klasa')";
    

    if ($conn->query($query) === TRUE) {
        echo "Pomyślnie dodano dane do tabeli.";
    } else {
        echo "Błąd podczas dodawania danych: " . $conn->error;
    }

    if ($insert_class_sql->execute()) {
        header('Location: admin_klasa.php');
    } else {
        echo "Błąd podczas dodawania klasy: " . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dodaj_uzytkownika_do_klasy'])) {
    $user_id = $_POST['wybierz-uzytkownika'];
    $klasa = $_POST['klasa'];

    $assign_to_class_sql = $conn->prepare("UPDATE users SET klasa = ? WHERE id = ?");
    $assign_to_class_sql->bind_param("si", $klasa, $user_id);

    if ($assign_to_class_sql->execute()) {
        header('Location: admin_klasa.php');
    } else {
        echo "Błąd podczas przypisywania użytkownika do klasy: " . $conn->error;
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
    </div>>
    <div class="content">
        <form method="post" class="notifications-container1">
            <input type="text" id="klasa" name="klasa" placeholder="Nazwa klasy" required>
            <select id="wychowawca" name="wychowawca" required>
            <option value="wybierz">Wybierz Wychowawce</option>
            <?php 
                    $query = "SELECT id, username FROM users WHERE nauczyciel = 1";
                    $result = mysqli_query($conn, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $userId = $row['id'];
                        $userName = $row['username'];
                        echo "<option value='$userId'>$userName</option>";
                    }
                ?>
            <input type="submit" name="dodaj_klase" value="Dodaj klasę">
        </form>

    <select id="klasa" name="klasa" required>
        <option value="wybierz">Przypisz klasę</option>
        <?php
        $query = "SELECT DISTINCT nazwa_klasy FROM klasy";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $class = $row['nazwa_klasy'];
            echo "<option value='$class'>$class</option>";
        }
        ?>
    </select>


            <input type="submit" name="dodaj_uzytkownika_do_klasy" value="Przypisz użytkownika do klasy">
        </form>
    </div> 
    <script>
     </script>
</body>
</html>