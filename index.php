<?php
	include 'header.php';
?>
<?php
// Массив с данными для изображений
$imageData = [
    ['src' => 'images/1.jpg', 'title' => '1'],
    ['src' => 'images/2.jpg', 'title' => '2'],
    ['src' => 'images/3.jpg', 'title' => '3'],
    ['src' => 'images/4.jpg', 'title' => '4'],
    ['src' => 'images/5.jpg', 'title' => '5']
];

// Переменная для текущего изображения
$currentImage = isset($_GET['img']) ? intval($_GET['img']) : 0;
$totalImages = count($imageData);
$nextImage = ($currentImage + 1) % $totalImages; // Вычисляем следующее изображение

// Обработка переключения изображений
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['next'])) {
        $currentImage = ($currentImage + 1) % $totalImages;
    } elseif (isset($_POST['prev'])) {
        $currentImage = ($currentImage - 1 + $totalImages) % $totalImages;
    }
}

?>

<div class="box" id="box">
    <div class="box1">
        <div class="picbox" style="display:block;">
            <img src="<?php echo $imageData[$currentImage]['src']; ?>" />
            <div class="shadow"></div>
            <p class="title"><?php echo $imageData[$currentImage]['title']; ?></p>
        </div>
    </div>

    <!-- <form method="post" action="">
        <div class="picbtn" id="btn">
            <button type="submit" name="prev">Предыдущее</button>
            <button type="submit" name="next">Следующее</button>
        </div>
    </form> -->
</div>

<?php
// Запуск автоматической перемотки
// Используйте функцию header для автоматического перенаправления
header("Refresh: 5; url=?img=$nextImage"); // Автоматическое переключение через 5 секунд
?>


<p style= "width:50%">Предметная область – Кинотеатр.
Данный сервис будет предоставлять возможность пользователям
просматривать актуальную информацию о кинотеатре, будущих сеансах, ценах на них, а также бронирования мест на сеанс.
Главная цель информационной системы – упростить взаимодействие пользователя с сайтом и базой данных кинотеатра. Всю доступную информацию так же можно будет уточнить на сайте кинотеатра.</p>	


<?php
    include 'footer.php';
?>