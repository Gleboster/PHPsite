<?php

include 'header.php';
include 'connect_database.php';

function get_Movie_Name_By_Seat_ID($Seat_ID)
{
    global $con; // Доступ к соединению MySQLi
    $Seat_ID = $con->real_escape_string($Seat_ID); // Очистка входных данных

    $sql2 = "SELECT 12222_Movie.Movie_Name FROM 12222_Movie, 12222_Running_Movie, 12222_Seat_On_Sale
              WHERE 12222_Seat_On_Sale.Seat_ID='$Seat_ID'
              AND 12222_Running_Movie.Running_Movie_ID = 12222_Seat_On_Sale.Running_Movie_ID
              AND 12222_Movie.Movie_ID = 12222_Running_Movie.Movie_ID";

    $result2 = $con->query($sql2); // Выполняем запрос с помощью MySQLi

    if (!$result2) 
    {
        die("Ошибка при запросе названия фильма: " . $con->error); 
    }

    if ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) 
    {
        return $row2['Movie_Name'];
    }
}

function get_Movie_ID_By_Seat_ID($Seat_ID)
{
    global $con; // Доступ к соединению MySQLi
    $Seat_ID = $con->real_escape_string($Seat_ID); // Очистка входных данных

    $sql3 = "SELECT 12222_Movie.Movie_ID, 12222_Running_Movie.Running_Movie_ID FROM 12222_Movie, 12222_Running_Movie, 12222_Seat_On_Sale
              WHERE 12222_Seat_On_Sale.Seat_ID='$Seat_ID'
              AND 12222_Running_Movie.Running_Movie_ID = 12222_Seat_On_Sale.Running_Movie_ID
              AND 12222_Movie.Movie_ID = 12222_Running_Movie.Movie_ID";

    $result3 = $con->query($sql3); // Выполняем запрос с помощью MySQLi

    if (!$result3) 
    {
        die("Ошибка при запросе Movie_ID: " . $con->error); 
    }

    if ($row3 = $result3->fetch_array(MYSQLI_ASSOC)) 
    {
        return $row3['Movie_ID'];
    }
}

?>

<a href="my_info.php">Моя информация</a>
<a href="my_orders.php">Мои заказы</a>
<a href="my_comments.php">Мои комментарии</a>

<div style="width:500px; height:300px; background-color:#ffff99">
    <h2>Мои заказы</h2>

    <?php
        $sql = "SELECT * FROM 12222_Orders WHERE Customer_ID=" . $_SESSION['Customer_ID'];
        $result = $con->query($sql); // Выполняем запрос с помощью MySQLi

        if (!$result) 
        {
            die("Ошибка при запросе информации о заказах: " . $con->error); 
        }

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Номер заказа</th>";
        echo "<th>Фильм</th>";
        echo "<th>Дата</th>";
        echo "<th>Цена билета</th>";
        echo "<th>Оценка</th>";
        echo "<th>Статус</th>";
        echo "</tr>";

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) 
        {
            $movie_id = get_Movie_ID_By_Seat_ID($row['Seat_ID']);
            $rating_sql = "SELECT Rating FROM 12222_movie_comment WHERE Order_ID=".$row['Order_ID'];
            $rating_result = $con->query($rating_sql);
            $current_rating = $rating_result->num_rows > 0 ? $rating_result->fetch_array(MYSQLI_ASSOC)['Rating'] : null;

            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Order_ID']) . "</td>";
            echo "<td>" . get_Movie_Name_By_Seat_ID($row['Seat_ID']) . "</td>";
            echo "<td>" . (new DateTime($row['Order_Date']))->format('d.m.Y') . "</td>";
            echo "<td>" . htmlspecialchars($row['Total_Price']) . "</td>";
            // Отображение рейтинга
            echo "<td>";
            if ($current_rating !== null) {
                echo "<div class='rating-display'>";
                for ($i = 1; $i <= 10; $i++) {
                    echo $i <= $current_rating ? "★" : "☆";
                }
                echo " ($current_rating/10)";
                echo "</div>";
            } else {
                echo "Не оценено";
            }
            echo "<td>";
            if ($row['Is_Commented']) 
            {
                echo "Уже оценено";
            } 
            else 
            {
                echo "<input type='button' value='Оценить' onclick='comment(" . $row['Order_ID'] . ", " . get_Movie_ID_By_Seat_ID($row['Seat_ID']) . ")'>";
            }

            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    ?>
</div>

<script type="text/javascript">
function comment(Order_ID, Movie_ID)
{
    var content = prompt("Как вам этот фильм? Поделитесь мнением", "");
    var rating = prompt("Оцените от 1 до 10", "");
    location.href = "insert_comment.php?Order_ID=" + Order_ID + "&Movie_ID=" + Movie_ID + "&content=" + encodeURIComponent(content) + "&rating=" + encodeURIComponent(rating);
}
</script>

<?php
include 'footer.php';
?>
