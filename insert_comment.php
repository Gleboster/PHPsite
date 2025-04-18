<?php

//TODO добавить Rating ((

include 'connect_database.php';

if (!isset($_GET['Order_ID']) || !isset($_GET['Movie_ID']) || !isset($_GET['content'])) 
{
    header("Location: index.php"); 
    exit(); // Всегда выходим после перенаправления
}

// Принимаем параметры через метод GET и вставляем данные комментария в базу данных
date_default_timezone_set("Etc/GMT-8");

$movieID = $con->real_escape_string($_GET['Movie_ID']); // Очистка входных данных
$orderID = $con->real_escape_string($_GET['Order_ID']); // Очистка входных данных
$content = $con->real_escape_string($_GET['content']); // Очистка входных данных
$rating = $con->real_escape_string($_GET['rating']); // Очистка входных данных

$sql = "INSERT INTO 12222_Movie_Comment (Movie_ID, Order_ID, Comment_Date, Comment, Rating) 
        VALUES ('$movieID', '$orderID', '" . date("Y-m-d H:i:s") . "', '$content', '$rating')";

$result = $con->query($sql); // Выполняем запрос с помощью MySQLi

if (!$result) 
{
    die("Ошибка вставки в базу данных: " . $con->error); // Используйте $con->error для MySQLi
}

// Обновляем таблицу заказов, чтобы установить статус отзыва
$sql = "UPDATE 12222_Orders SET Is_Commented=1 WHERE Order_ID='$orderID'";
$result = $con->query($sql); // Выполняем запрос с помощью MySQLi

if (!$result) 
{
    die("Ошибка изменения статуса отзыва в таблице заказов: " . $con->error); // Используйте $con->error для MySQLi
}

header("Location: my_orders.php");
exit(); // Убеждаемся, что выход после перенаправления

?>
