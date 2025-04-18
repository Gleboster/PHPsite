<?php
include 'header.php';
include 'connect_database.php';

// Проверка наличия идентификатора новости
if (isset($_GET['news_id'])) {
    $news_id = $con->real_escape_string($_GET['news_id']);
    
    // Запрос для получения выбранной новости
    $sql = "SELECT * FROM news WHERE id='$news_id'";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        
        // Стили для улучшенного отображения
        echo "<style>
            .news-container {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
            }
            .news-title {
                font-size: 2.2rem;
                margin-bottom: 1rem;
                color: #2c3e50;
                font-weight: 700;
            }
            .news-image {
                width: 100%;
                max-height: 500px;
                object-fit: cover;
                border-radius: 8px;
                margin: 20px 0;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            .news-content {
                font-size: 1.1rem;
                margin-bottom: 2rem;
                text-align: justify;
            }
            .news-meta {
                color: #7f8c8d;
                font-size: 0.9rem;
                margin-bottom: 2rem;
                border-bottom: 1px solid #ecf0f1;
                padding-bottom: 1rem;
            }
            .news-date {
                margin-right: 15px;
            }
            .news-author {
                font-style: italic;
            }
            .back-link {
                display: inline-block;
                margin-top: 20px;
                color: #3498db;
                text-decoration: none;
                font-weight: 500;
            }
            .back-link:hover {
                text-decoration: underline;
            }
        </style>";
        
        // Отображение новости
        echo "<div class='news-container'>";
        echo "<h1 class='news-title'>" . htmlspecialchars($row['title']) . "</h1>";
        
        // Блок мета-информации (дата, автор и т.д.)
        echo "<div class='news-meta'>";
        if (!empty($row['author'])) {
            echo "<span class='news-author'>Автор: " . htmlspecialchars($row['author']) . "</span>";
        }
        echo "</div>";
        
        // Изображение новости
        if (!empty($row['image_url'])) {
            echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['title']) . "' class='news-image'>";
        }
        
        // Контент новости с обработкой переносов строк
        $content = nl2br(htmlspecialchars($row['content']));
        echo "<div class='news-content'>" . $content . "</div>";
        
        // Ссылка "Назад"
        echo "<a href='news.php' class='back-link'>← Вернуться к списку новостей</a>";
        echo "</div>";
    } else {
        echo "<div class='news-container'><p>Новость не найдена.</p></div>";
    }
} else {
    echo "<div class='news-container'><p>Идентификатор новости не указан.</p></div>";
}

// Освобождение результата и закрытие соединения
if (isset($result)) {
    $result->free();
}
$con->close();

include 'footer.php';
?>