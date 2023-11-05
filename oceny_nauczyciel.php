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
    $username = $_SESSION['user']['username'];
    if($username === "admin"){
        $sectionVisible = false;
    } else $sectionVisible = true;
}

$userSubject = null;

if (isset($_SESSION['user'])) {
    $logged_in_user = $_SESSION['user'];
    $username = $logged_in_user['username'];

    $query = "SELECT przedmiot FROM users WHERE username = ?";
    $get_subject = $conn->prepare($query);
    $get_subject->bind_param("s", $username);
    $get_subject->execute();
    $subject_result = $get_subject->get_result();

    if ($subject_result->num_rows > 0) {
        $row = $subject_result->fetch_assoc();
        $userSubject = $row['przedmiot'];
    }
}

if (isset($_SESSION['user'])) {
    $username = $_SESSION['user']['username'];
    $userClass = $logged_in_user['klasa'];
}

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true && isset($_SESSION['user'])) {
    if (isset($_SESSION['user']['nauczyciel']) && $_SESSION['user']['nauczyciel'] == 1) {
    } else {
        header('Location: logowanie.php');
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['wybierz-klase'])) {
    $selectedClass = $_POST['klasa'];
    $get_students = $conn->prepare("SELECT username FROM users WHERE klasa = ?");
    $get_students->bind_param("s", $selectedClass);
    $get_students->execute();
    $students_result = $get_students->get_result();
    $get_current_grades = $conn->prepare("SELECT $userSubject FROM oceny WHERE user = ?");
    $get_current_grades->bind_param("s", $selectedClass);
    $get_current_grades->execute();
    $grades_result = $get_current_grades->get_result();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit-grades'])) {
    $studentId = $_POST['student_id'];
    $grade = $_POST['grade'];
    $update_grade = $conn->prepare("UPDATE oceny SET $userSubject = ? WHERE user = ?");
    $update_grade->bind_param("ss", $grade, $studentId);
    $update_grade->execute();
}

if (isset($userSubject)) {
    $get_current_grades = $conn->prepare("SELECT $userSubject FROM oceny WHERE user = ?");
    $get_current_grades->bind_param("s", $username);
    $get_current_grades->execute();
    $grades_result = $get_current_grades->get_result();
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
                <li><a href="ustawienia.php"><i class="fa-solid fa-gear"></i>Ustawienia</a></li>
                <li><a href="logout.php"><i class="fa-solid fa-power-off"></i>Wyloguj</a></li>
            </ul>
        </div>
    </div>
    <div class="content">
        <h2>Przeglądaj oceny uczniów</h2>
        <form method="post" class="notifications-container1">
            <select id="klasa" name="klasa" required>
                <option value="">Wybierz klasę</option>
                <?php
                $query = "SELECT DISTINCT klasa FROM klasy";
                $get_classes = $conn->query($query);
                while ($row = $get_classes->fetch_assoc()) {
                    $class = $row['klasa'];
                    echo "<option value='$class'>$class</option>";
                }
                ?>
            </select>
            <input type="submit" name="wybierz-klase" value="Wybierz klasę">
        </form>

        <?php if (isset($students_result) && isset($grades_result)) : ?>
            <form method="post" class="notifications-container1">
            <h3>Aktualne oceny z przedmiotu <?php echo $userSubject; ?></h3>
                <table>
                    <tr>
                        <th>Uczeń</th>
                        <th>Ocena z przedmiotu</th>
                    </tr>
                    <?php while ($student = $students_result->fetch_assoc()) : ?>
                        <?php $grade = $grades_result->fetch_assoc(); ?>
                        <tr>
                            <td><?php echo $student['username']; ?></td>
                            <td>
                                <input type="number" name="grade" value="<?php echo $grade[$userSubject]; ?>">
                                <input type="hidden" name="subject" value="<?php echo $userSubject; ?>">
                                <input type="hidden" name="student_id" value="<?php echo $student['username']; ?>">
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
                <input type="submit" name="submit-grades" value="Zapisz oceny">
            </form>
        <?php endif; ?>
    </div>
    <script src="script.js"></script>
</body>
</html>