<?php

include 'header.php';
include 'connect_database.php';

// Проверка, является ли пользователь администратором
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') 
{
    echo "<h2>Вы не администратор и не можете управлять расписанием</h2><br>";
    echo "<h2>Пожалуйста, войдите как администратор</h2>";
    exit();
}

// Обработка отправки формы для добавления фильма в расписание
if (isset($_POST['movie'])) 
{
    // Отладочный вывод данных формы
    print_r($_POST);

    // Очистка входных данных
    $movie = $con->real_escape_string($_POST['movie']); // ID фильма
    $hall = $con->real_escape_string($_POST['hall']); // ID зала
    $hour = (int)$_POST['hour']; // Час показа
    $minute = (int)$_POST['minute']; // Минута показа
    $price = (float)$_POST['price']; // Цена билета

    // Вставка данных о сеансе в базу данных
    $sql = "INSERT INTO 12222_running_movie (Movie_ID, Hall_ID, Showtime, Price) VALUES "
         . "('$movie', '$hall', '2014-01-21 $hour:$minute:00', '$price')";

    $result = $con->query($sql); // Выполнение запроса

    if (!$result) 
    {
        die("<h2>Ошибка при добавлении сеанса</h2>" . $con->error); // Вывод ошибки MySQLi
    } 
    else 
    {
        echo "<h2>Сеанс успешно добавлен</h2>";
    }

    // Организация мест для добавленного сеанса
    $Running_Movie_ID = $con->insert_id; // Получение ID последней вставки
    for ($i = 1; $i <= 12; $i++) 
    { 
        for ($j = 1; $j <= 12; $j++) 
        { 
            $sql = "INSERT INTO 12222_Seat_On_Sale (Running_Movie_ID, Row_Num, Column_Num) "
                . "VALUES ('$Running_Movie_ID', '$i', '$j')";

            $result = $con->query($sql); // Выполнение запроса

            if (!$result) 
            {
                die("Ошибка при добавлении мест" . $con->error); // Вывод ошибки MySQLi
            }
        }
    }

    exit(); 
}

?>

<!-- Форма для добавления сеанса -->
<p>
<form action="" method="post">
    Фильм
    <select name="movie">
        <?php
            // Запрос для получения списка фильмов
            $sql = "SELECT Movie_ID, Movie_Name FROM 12222_movie;";
            $result = $con->query($sql); // Выполнение запроса

            if (!$result) 
            {
                echo "Ошибка при получении списка фильмов";    
            }

            // Вывод списка фильмов в выпадающем меню
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) 
            {
                echo "<option value='" . $row['Movie_ID'] . "'>" . $row['Movie_Name'] . "</option>";
            }
        ?>
    </select>

    &nbsp;&nbsp;Зал
    <select name="hall">
        <option value="1">Зал 1</option>
        <option value="2">Зал 2</option>
        <option value="3">Зал 3</option>
        <option value="4">Зал 4</option>
        <option value="5">Зал 5</option>
    </select>

    &nbsp;&nbsp;Время показа
    <select name="hour">
        <?php for ($h = 9; $h <= 23; $h++): ?>
            <option value="<?= $h ?>"><?= $h ?></option>
        <?php endfor; ?>
    </select>:

    <select name="minute">
        <?php for ($m = 0; $m <= 50; $m += 10): ?>
            <option value="<?= $m ?>"><?= str_pad($m, 2, '0', STR_PAD_LEFT) ?></option>
        <?php endfor; ?>
    </select>

    &nbsp;&nbsp;Цена
    <input type="text" name="price" style="width:50px">руб.
    <br /><br />
    
    <input type="submit" value="Добавить сеанс">
</form>
</p>

<?php

include 'footer.php';

?>