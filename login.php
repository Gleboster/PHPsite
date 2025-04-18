<?php
include 'header.php';
include 'connect_database.php';

// Инициализация сессии для капчи
if (!isset($_SESSION['captcha_id'])) {
    $_SESSION['captcha_id'] = rand(1, 10); // Выбираем случайную капчу
}

// Сохраняем введенные данные из POST в переменные
$username_value = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '';
$password_value = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '';

// Проверка, отправлены ли данные формы
if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['captcha'])) 
{
    // Проверка капчи
    $captcha_id = $_SESSION['captcha_id'];
    $sql_captcha = "SELECT solution FROM captchas WHERE id = $captcha_id";
    $result_captcha = $con->query($sql_captcha);
    $row_captcha = $result_captcha->fetch_assoc();
    
    if (strtolower($_POST['captcha']) !== strtolower($row_captcha['solution'])) {
        // Капча неверная - генерируем новую и выводим сообщение
        $_SESSION['captcha_id'] = rand(1, 10);
        echo "<h2>Неверно введена капча! Попробуйте снова.</h2>";
    } else {
        // Капча верная - проверяем логин/пароль
        
        // Очистка введенного имени пользователя
        $username = $con->real_escape_string($_POST['username']); 

        // Запрос для поиска пользователя в базе данных
        $sql = "SELECT Customer_ID, Pwd FROM 12222_customer WHERE Nickname = '$username'";
        $result = $con->query($sql);

        if (!$result) {
            die("Ошибка запроса: " . $con->error);
        }

        echo "Пароль успешно найден";

        // Получение данных из результата запроса
        $row = $result->fetch_array(MYSQLI_ASSOC); 

        echo "<br />Введенный пароль: " . md5($_POST['password']);
        echo "<br />Пароль в базе данных: " . $row['Pwd'];

        // Проверка совпадения паролей
        if (md5($_POST['password']) == $row['Pwd']) {
            $_SESSION['username'] = $username;
            $_SESSION['Customer_ID'] = $row['Customer_ID'];
            header("Location: login.php");
            exit();
        } else {
            echo "<h2>Неверный пароль</h2>";
            $_SESSION['captcha_id'] = rand(1, 10);
        }
    }
}

// Проверка, авторизован ли пользователь
if (isset($_SESSION['username'])) {
    echo "<h2>Авторизация успешна</h2>";
    exit();
}

// Получаем текущую капчу
$current_captcha_id = $_SESSION['captcha_id'];
$sql_current_captcha = "SELECT image_path FROM captchas WHERE id = $current_captcha_id";
$result_current_captcha = $con->query($sql_current_captcha);
$row_current_captcha = $result_current_captcha->fetch_assoc();
$captcha_image = $row_current_captcha['image_path'];
?>

<!-- Форма для входа -->
<div style="width:500px; height:400px; background-color:#ffff99">
    <h2>Вход</h2>
    <form action="" method="post">
        <p>Имя пользователя: <input type="text" name="username" required value="<?php echo $username_value; ?>"></p>
        <p>Пароль: <input type="password" name="password" required value="<?php echo $password_value; ?>"></p>
        
        <!-- Поле для капчи -->
        <p>Капча: 
            <img src="<?php echo $captcha_image; ?>" alt="CAPTCHA" style="vertical-align: middle;">
            <input type="text" name="captcha" required>
        </p>
        
        <input type="submit" value="Войти">
    </form>
</div>

<?php
include 'footer.php';
?>