<?php
include 'header.php';
include 'connect_database.php';

$daysRu = [
    'Monday' => 'Понедельник',
    'Tuesday' => 'Вторник',
    'Wednesday' => 'Среда',
    'Thursday' => 'Четверг',
    'Friday' => 'Пятница',
    'Saturday' => 'Суббота',
    'Sunday' => 'Воскресенье'
];

$monthsRu = [
    'January' => 'января',
    'February' => 'февраля',
    'March' => 'марта',
    'April' => 'апреля',
    'May' => 'мая',
    'June' => 'июня',
    'July' => 'июля',
    'August' => 'августа',
    'September' => 'сентября',
    'October' => 'октября',
    'November' => 'ноября',
    'December' => 'декабря'
];

function getAverageRating($movie_id, $con) {
    $sql = "SELECT AVG(Rating) as avg_rating FROM 12222_movie_comment WHERE Movie_ID = " . (int)$movie_id;
    $result = $con->query($sql);
    
    if ($result && $row = $result->fetch_array(MYSQLI_ASSOC)) {
        return $row['avg_rating'] ? round($row['avg_rating'], 1) : 7.5; // Если нет оценок, возвращаем 5
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

if (isset($_GET['Movie_ID'])) {
    $movieID = $con->real_escape_string($_GET['Movie_ID']);
    
    // Получаем информацию о фильме
    $movieSql = "SELECT * FROM 12222_movie WHERE Movie_ID='$movieID'";
    $movieResult = $con->query($movieSql);
    $movieData = $movieResult->fetch_array(MYSQLI_ASSOC);
    
    // Получаем сеансы, сортируем по дате и времени
    $sql = "SELECT rm.*, h.Hall_ID 
            FROM 12222_running_movie rm
            JOIN 12222_hall h ON rm.Hall_ID = h.Hall_ID
            WHERE rm.Movie_ID='$movieID'
            ORDER BY rm.Showtime ASC";
    
    $result = $con->query($sql);

    if (!$result) {
        die("Ошибка запроса: " . $con->error);
    }
?>
<style>
    .movie-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: rgb(255, 255, 153);
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    
    .movie-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #007bff;
    }
    
    .movie-title {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }
    
    .movie-meta {
        color: #666;
        margin-bottom: 5px;
    }
    
    .sessions-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    
    .session-card {
        background: rgb(218, 236, 255);
        padding: 15px;
        border-radius: 8px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .session-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .session-time {
        font-size: 20px;
        font-weight: bold;
        color: #007bff;
        margin-bottom: 10px;
    }
    
    .session-hall {
        background: #007bff;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .session-price {
        font-size: 18px;
        color: #28a745;
        font-weight: bold;
    }
    
    .session-form {
        margin-top: 15px;
    }
    
    .select-btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    
    .select-btn:hover {
        background-color: #0056b3;
    }
    
    .day-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
    }
    
    .day-tab {
        padding: 10px 20px;
        cursor: pointer;
        background: #f8f9fa;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
    }
    
    .day-tab.active {
        background: #007bff;
        color: white;
    }
    
    .no-sessions {
        grid-column: 1 / -1;
        text-align: center;
        padding: 30px;
        color: #666;
    }
</style>

<div class="movie-container">
    <div class="movie-header">
        <div class="movie-info">
            <h1 class="movie-title"><?= htmlspecialchars($movieData['Movie_Name']) ?></h1>
            <p class="movie-meta"><strong>Рейтинг:</strong> <?= displayRatingStars(getAverageRating($movieData['Production_Year'], $con)) ?></p>
            <p class="movie-meta"><strong>Год:</strong> <?= htmlspecialchars($movieData['Production_Year']) ?></p>
            <p class="movie-meta"><strong>Режиссер:</strong> <?= htmlspecialchars($movieData['Director']) ?></p>
            <p class="movie-meta"><strong>Жанр:</strong> <?= htmlspecialchars($movieData['Movie_Type']) ?></p>
        </div>
    </div>
    
    <h2>Доступные сеансы</h2>
    
    <div class="sessions-container">
        <?php
        if ($result->num_rows > 0) {
            $currentDate = null;
            
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $showtime = new DateTime($row['Showtime']);
                $formattedDate = $showtime->format('d.m.Y');
                $formattedTime = $showtime->format('H:i');
                
                // Если дата изменилась, выводим заголовок
                if ($formattedDate != $currentDate) {
                    $dayEn = $showtime->format('l');
                    $monthEn = $showtime->format('F');
                    $dayNum = $showtime->format('d');
                    $year = $showtime->format('Y');
                    
                    echo '<div style="grid-column: 1 / -1; font-size: 20px; margin-top: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">';
                    echo $daysRu[$dayEn] . ', ' . $dayNum . ' ' . $monthsRu[$monthEn] . ' ' . $year;
                    echo '</div>';
                    $currentDate = $formattedDate;
                }
                ?>
                <div class="session-card">
                    <div class="session-time"><?= $formattedTime ?></div>
                    <div class="session-hall">Зал <?= $row['Hall_ID'] ?></div>
                    <div class="session-price"><?= $row['Price'] ?> ₽</div>
                    
                    <form action='choose_seat.php' method='post' class="session-form">
                        <input type='hidden' name='Running_Movie_ID' value='<?= $row['Running_Movie_ID'] ?>'>
                        <input type='hidden' name='Price' value='<?= $row['Price'] ?>'>
                        <button type='submit' class="select-btn">Выбрать места</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo '<div class="no-sessions">На данный момент нет доступных сеансов для этого фильма.</div>';
        }
        ?>
    </div>
</div>

<?php
}
include 'footer.php';
?>