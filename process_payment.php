<?php
include 'connect_database.php';
include 'header.php';

// Проверка, переданы ли выбранные места
if (!isset($_POST['selectedSeats']) || empty($_POST['selectedSeats'])) {
    die("Ошибка: Не выбрано ни одного места.");
}

// Декодирование JSON
$selectedSeats = json_decode($_POST['selectedSeats'], true);

// Проверка, вошел ли пользователь в систему
if (!isset($_SESSION['username'])) {
    die("Ошибка: Вы не вошли в систему. Пожалуйста, <a href='login.php'>войдите</a>.");
}

$Customer_ID = $_SESSION['Customer_ID'];
$Running_Movie_ID = $_SESSION['Running_Movie_ID'];
$Price = $_SESSION['Price'];

// Инициализация массива для хранения результатов
$results = [];

foreach ($selectedSeats as $seat) {
    $Row_Num = $con->real_escape_string($seat['row']);
    $Column_Num = $con->real_escape_string($seat['column']);

    // Проверка, занято ли место
    $sql = "SELECT * FROM 12222_seat_on_sale WHERE Running_Movie_ID='$Running_Movie_ID' AND Row_Num='$Row_Num' AND Column_Num='$Column_Num'";
    $result = $con->query($sql);

    if (!$result) {
        $results[] = "Ошибка при проверке места (Ряд $Row_Num, Место $Column_Num): " . $con->error;
        continue;
    }

    $row = $result->fetch_assoc();
    if ($row['Is_Reserved'] == 1) {
        $results[] = "Ошибка: Место (Ряд $Row_Num, Место $Column_Num) уже занято.";
        continue;
    }

    // Обновление статуса бронирования места
    $sql = "UPDATE 12222_seat_on_sale SET Is_Reserved=1 WHERE Running_Movie_ID='$Running_Movie_ID' AND Row_Num='$Row_Num' AND Column_Num='$Column_Num'";
    $result = $con->query($sql);

    if (!$result) {
        $results[] = "Ошибка при бронировании места (Ряд $Row_Num, Место $Column_Num): " . $con->error;
        continue;
    }

    // Генерация информации о заказе
    date_default_timezone_set("Etc/GMT-8");
    $Order_Date = date("Y-m-d H:i:s");
    $Seat_ID = $row['Seat_ID'];

    $sql = "INSERT INTO 12222_Orders (Customer_ID, Seat_ID, Order_Date, Total_Price) VALUES ('$Customer_ID', '$Seat_ID', '$Order_Date', '$Price')";
    $result = $con->query($sql);

    if (!$result) {
        $results[] = "Ошибка при генерации информации о заказе (Ряд $Row_Num, Место $Column_Num): " . $con->error;
        continue;
    }

    $results[] = "Успешно: Место (Ряд $Row_Num, Место $Column_Num) забронировано.";
}

// Вывод результатов
foreach ($results as $message) {
    echo "<p>$message</p>";
}
?>

<?php
include 'footer.php';
?>
