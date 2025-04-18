<?php

//выровнял новости по центру и рандомизировать из из БД (3 из 10, которые в БД)

include 'header.php';

// Подключение к базе данных
include 'connect_database.php';

// Запрос на получение 3 случайных новостей
$sql = "SELECT * FROM news ORDER BY RAND() LIMIT 3";  // выбираем 3 случайные новости

$result = $con->query($sql);

if (!$result) {
    die("Ошибка запроса: " . $con->error); // Проверка на ошибки
}

// Отображение новостей
if ($result->num_rows > 0) {
    echo "<div style='display: flex; justify-content: center; flex-wrap: wrap; gap: 20px;'>"; // Центрируем элементы

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo "<div style='width: 300px; position: relative;'>";

        // Отображение изображения новости
        echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['title']) . "' style='width: 100%; height: 200px; object-fit: cover;'>";

        // Накладываем текст на изображение
        echo "<div style='position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0, 0, 0, 0.7); color: white; padding: 10px;'>";
        echo "<h3><a href='new.php?news_id=" . intval($row['id']) . "' style='color: white; text-decoration: none;'>" . htmlspecialchars($row['title']) . "</a></h3>";
        echo "</div>";
        echo "</div>";
    }

    echo "</div>"; // Закрытие контейнера
} else {
    echo "<p>Нет новостей для отображения.</p>";
}

// Освобождение результата и закрытие соединения
$result->free();
$con->close();

include 'footer.php';
?>
