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

$wybranaKlasa = isset($_POST['klasa']) ? $_POST['klasa'] : '';
$planLekcji = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($wybranaKlasa)) {
    $sql = "SELECT godzina_id, godzina, poniedzialek, wtorek, sroda, czwartek, piatek FROM plan_lekcji WHERE klasa = '$wybranaKlasa'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $planLekcji[] = $row;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['zaktualizuj_plan'])) {
    $godzina_id = isset($_POST['godzina_id']) ? $_POST['godzina_id'] : [];
    $poniedzialek = isset($_POST['poniedzialek']) ? $_POST['poniedzialek'] : [];
    $wtorek = isset($_POST['wtorek']) ? $_POST['wtorek'] : [];
    $sroda = isset($_POST['sroda']) ? $_POST['sroda'] : [];
    $czwartek = isset($_POST['czwartek']) ? $_POST['czwartek'] : [];
    $piatek = isset($_POST['piatek']) ? $_POST['piatek'] : [];

    foreach ($godzina_id as $key => $id) {
        $poniedzialek_val = $poniedzialek[$key];
        $wtorek_val = $wtorek[$key];
        $sroda_val = $sroda[$key];
        $czwartek_val = $czwartek[$key];
        $piatek_val = $piatek[$key];

        $sql = "UPDATE plan_lekcji SET poniedzialek = '$poniedzialek_val', wtorek = '$wtorek_val', sroda = '$sroda_val', czwartek = '$czwartek_val', piatek = '$piatek_val' WHERE godzina_id = $id";
        $conn->query($sql);
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <?php
    if ($username === 'admin') {
        echo '<form method="post" class="timetable">';
        echo '<label for="klasa">Wybierz klasę:</label>';
        echo '<select name="klasa" id="klasa">';
        $sql = "SELECT DISTINCT klasa FROM plan_lekcji";
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $class = $row['klasa'];
                echo "<option value=\"$class\" " . ($class === $wybranaKlasa ? 'selected' : '') . ">$class</option>";
            }
        }
        echo '</select>';
        echo '<input type="submit" value="Wybierz klasę">';
        echo '</form>';

        if (!empty($wybranaKlasa) && !empty($planLekcji)) {
            echo '<form method="post" class="timetable" id="timetableon">';
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

            foreach ($planLekcji as $lekcja) {
                echo '<tr>';
                echo '<td>' . $lekcja['godzina'] . '</td>';
                echo '<td><input type="text" name="poniedzialek[]" value="' . $lekcja['poniedzialek'] . '"><input type="hidden" name="godzina_id[]" value="' . $lekcja['godzina_id'] . '"></td>';
                echo '<td><input type="text" name="wtorek[]" value="' . $lekcja['wtorek'] . '"></td>';
                echo '<td><input type="text" name="sroda[]" value="' . $lekcja['sroda'] . '"></td>';
                echo '<td><input type="text" name="czwartek[]" value="' . $lekcja['czwartek'] . '"></td>';
                echo '<td><input type="text" name="piatek[]" value="' . $lekcja['piatek'] . '"></td>';
                echo '</tr>';
            }

            echo '<input type="submit" value="Zaktualizuj plan lekcji" name="zaktualizuj_plan" id="zaktualizuj-plan">';
            echo '</form>';
        }
    }
    ?>
    <div id="update-message"></div>
</div>
<script>
$(document).ready(function() {
    $("#zaktualizuj-plan").click(function() {
        var klasa = $("#klasa").val();

        var godzina_id = [];
        var poniedzialek = [];
        var wtorek = [];
        var sroda = [];
        var czwartek = [];
        var piatek = [];

        $("input[name='godzina_id[]']").each(function() {
            godzina_id.push($(this).val());
        });
        $("input[name='poniedzialek[]']").each(function() {
            poniedzialek.push($(this).val());
        });
        $("input[name='wtorek[]']").each(function() {
            wtorek.push($(this).val());
        });
        $("input[name='sroda[]']").each(function() {
            sroda.push($(this).val());
        });
        $("input[name='czwartek[]']").each(function() {
            czwartek.push($(this).val());
        });
        $("input[name='piatek[]']").each(function() {
            piatek.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "admin_plan.php",
            data: {
                klasa: klasa,
                godzina_id: godzina_id,
                poniedzialek: poniedzialek,
                wtorek: wtorek,
                sroda: sroda,
                czwartek: czwartek,
                piatek: piatek,
                zaktualizuj_plan: true
            },
            success: function(response) {
                $("#update-message").html(response);
            },
            error: function() {
                $("#update-message").html("Błąd podczas przetwarzania zapytania.");
            }
        });
    });
});

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
