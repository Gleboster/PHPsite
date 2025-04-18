<?php

include 'header.php';
include 'connect_database.php';

// Функция для получения среднего рейтинга фильма
function getAverageRating($movie_id, $con) {
    $sql = "SELECT AVG(Rating) as avg_rating FROM 12222_movie_comment WHERE Movie_ID = " . (int)$movie_id;
    $result = $con->query($sql);
    
    if ($result && $row = $result->fetch_array(MYSQLI_ASSOC)) {
        return $row['avg_rating'] ? round($row['avg_rating'], 1) : 5; // Если нет оценок, возвращаем 5
    }
    return 5; // По умолчанию, если что-то пошло не так
}

// Функция для отображения звезд рейтинга
function displayRatingStars($rating) {
    $fullStars = floor($rating); // Целая часть (так как у нас шкала до 10)
    $halfStar = ($rating - $fullStars) >= 1 ? 1 : 0;
    $emptyStars = 10 - $fullStars - $halfStar;
    
    $stars = '';
    // Полные звезды
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '★';
    }
    // Половина звезды
    if ($halfStar) {
        $stars .= '½';
    }
    // Пустые звезды
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '☆';
    }
    
    return $stars . ' (' . $rating . '/10)';
}

// Создание запроса для получения всех фильмов для фильтрации
$sql = "SELECT Movie_ID, Movie_Name FROM 12222_movie;";
$result = $con->query($sql); // Выполняем запрос для получения списка фильмов

if (!$result) {
    die("Ошибка запроса: " . $con->error); // Используем $con->error для MySQLi
}

// Создание массива опций для выпадающего списка
$filmsOptions = [];
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $filmsOptions[] = $row['Movie_Name'];
}

// Проверка, выбран ли фильм для фильтрации
$filmFilter = isset($_GET['film']) ? $_GET['film'] : '';

// Запрос для фильтрации фильмов по выбранному фильму
$filterSql = "SELECT * FROM 12222_movie";
if ($filmFilter) {
    $filterSql .= " WHERE Movie_Name = '" . $con->real_escape_string($filmFilter) . "'";
}

// Выполнение запроса фильтрации
$filteredResult = $con->query($filterSql); // Выполняем фильтрацию

if (!$filteredResult) {
    die("Ошибка запроса: " . $con->error); // Используем $con->error для MySQLi
}

// Форма фильтрации
echo "<form method='GET'>";
echo "<label>Выберите фильм:</label>";
echo "<select name='film'>";
echo "<option value=''>Все фильмы</option>";

foreach ($filmsOptions as $option) {
    $selected = ($option == $filmFilter) ? 'selected' : '';
    echo "<option value='$option' $selected>$option</option>";
}

echo "</select>";
echo "<input type='submit' value='Фильтровать'>";
echo "</form>";

// Отображение фильтрованных фильмов
while ($row = $filteredResult->fetch_array(MYSQLI_ASSOC)) {
    $averageRating = getAverageRating($row['Movie_ID'], $con);

    echo "<div style='width:500px; height:250px; background-color:#ffff99'>";
    echo "<div style='width:150px; height:250px; float:left'>";
    echo "<img src='images/movies/" . $row['Movie_ID'] . ".jpg' style='position:relative; left=30; top=20'/>";
    echo "</div>";
    echo "<div style='width:350px; height:250px; float:left; text-align:left'>";
    echo "<h3>";
    echo "<a href='running_movie.php?Movie_ID=" . $row['Movie_ID'] . "'>";
    echo $row['Movie_Name'];
    echo "</a>";
    echo "</h3>";

    echo "<div style='margin-bottom:10px; font-size:18px; color:#ff9900;'>";
    echo displayRatingStars($averageRating);
    echo "</div>";

    echo "Год выпуска: " . $row['Production_Year'];
    echo "<br />Жанр: " . $row['Movie_Type'];
    echo "<br />Режиссер: " . $row['Director'];
    echo "<br />Актеры: " . $row['Actors'];
    echo "<br />Описание: " . $row['Movie_Desc'];
    echo "</div>";
    echo "</div>";
}

// Освобождение результата и закрытие соединения
$result->free();
$filteredResult->free();
$con->close();

include 'footer.php';

?>
