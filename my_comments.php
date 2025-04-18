<?php

include 'header.php';
include 'connect_database.php';

function get_Movie_Name_By_Movie_ID($Movie_ID)
{
    global $con; // Доступ к соединению MySQLi
    $Movie_ID = $con->real_escape_string($Movie_ID); // Очистка входных данных

    $sql2 = "SELECT Movie_Name FROM 12222_Movie WHERE Movie_ID='$Movie_ID'";
    $result2 = $con->query($sql2); // Выполняем запрос с помощью MySQLi

    if (!$result2) 
    {
        die("Ошибка при запросе названия фильма: " . $con->error); // Используйте $con->error для MySQLi
    }

    if ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) 
    {
        return htmlspecialchars($row2['Movie_Name']); // Очистка вывода
    }
}

function formatDate($dateString)
{
    $date = new DateTime($dateString);
    return $date->format('d.m.Y');
}
?>

<a href="my_info.php">Моя информация</a>
<a href="my_orders.php">Мои заказы</a>
<a href="my_comments.php">Мои комментарии</a>

<div style="width:500px; height:300px; background-color:#ffff99">
    <h2>Мои комментарии</h2>
    <?php
        $sql = "SELECT 12222_Movie_Comment.Order_ID, 12222_Movie_Comment.Movie_ID, 
                       12222_Movie_Comment.Comment_Date, 12222_Movie_Comment.Comment 
                FROM 12222_Movie_Comment 
                JOIN 12222_Orders ON 12222_Movie_Comment.Order_ID = 12222_Orders.Order_ID 
                WHERE 12222_Orders.Customer_ID = " . $_SESSION['Customer_ID'];

        $result = $con->query($sql); // Выполняем запрос с помощью MySQLi

        if (!$result) 
        {
            die("Ошибка при запросе информации о заказах: " . $con->error); // Используйте $con->error для MySQLi
        }

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Номер заказа</th>";
        echo "<th>Фильм</th>";
        echo "<th>Дата отзыва</th>";
        echo "<th>Содержимое</th>";
        echo "</tr>";

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) 
        {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Order_ID']) . "</td>"; // Очистка вывода
            echo "<td>" . get_Movie_Name_By_Movie_ID($row['Movie_ID']) . "</td>";
            echo "<td>" . formatDate($row['Comment_Date']) . "</td>"; // Форматируем дату
            echo "<td>" . htmlspecialchars($row['Comment']) . "</td>"; // Очистка вывода
            echo "</tr>";
        }

        echo "</table>";
    ?>
</div>

<?php
include 'footer.php';
?>