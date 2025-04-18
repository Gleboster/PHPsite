<?php
include 'header.php';
include 'connect_database.php';
include 'mail_sender.php'; // Подключаем файл с функцией отправки почты

// Инициализация сессии для капчи
if (!isset($_SESSION['captcha_id'])) {
    $_SESSION['captcha_id'] = rand(1, 10); // Выбираем случайную капчу
}

// Сохраняем введенные данные из POST
$form_values = [
    'username' => isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '',
    'tel' => isset($_POST['tel']) ? htmlspecialchars($_POST['tel']) : '',
    'email' => isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '',
    'sex' => isset($_POST['sex']) ? htmlspecialchars($_POST['sex']) : '1',
    'password' => isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''
];

// Проверка, отправлены ли данные формы
if (isset($_POST["username"])) 
{
    // Проверка капчи
    if (isset($_POST['captcha'])) {
        
        $captcha_id = $_SESSION['captcha_id'];
        $sql_captcha = "SELECT solution FROM captchas WHERE id = $captcha_id";
        $result_captcha = $con->query($sql_captcha);
        
        if ($result_captcha && $row_captcha = $result_captcha->fetch_assoc()) {
            if (strtolower($_POST['captcha']) !== strtolower($row_captcha['solution'])) {
                // Капча неверная - генерируем новую
                $_SESSION['captcha_id'] = rand(1, 10);
                echo "<h2>Неверно введена капча! Попробуйте снова.</h2>";
                $show_form = true;
            } else {
                // Капча верная - продолжаем регистрацию
                
                // Очистка и подготовка данных
                $password = md5($_POST['password']);
                $username = $con->real_escape_string($_POST['username']);
                $tel = $con->real_escape_string($_POST['tel']);
                $email = $con->real_escape_string($_POST['email']);
                $sex = $con->real_escape_string($_POST['sex']);

                // Запрос на вставку данных в таблицу
                $sql = "INSERT INTO 12222_customer (Pwd, Nickname, Tel, Email, Sex) VALUES ('$password', '$username', '$tel', '$email', '$sex')";
                
                $result = $con->query($sql);

                if (!$result) {
                    die("Ошибка при вставке данных: " . $con->error);
                }

                // Сохранение данных в сессии
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['Customer_ID'] = $con->insert_id;
                
                // Формируем и отправляем письмо с подтверждением
                $message = '
                <html>
                <head>
                    <title>Благодарим за регистрацию!</title>
                </head>
                <body>
                    <h2 style="color: #0066cc;">Уважаемый(ая) '.$username.',</h2>
                    <p>Благодарим вас за регистрацию в кинотеатре «Высокого стиля»!</p>
                    <p>Ваши регистрационные данные:</p>
                    <ul>
                        <li><strong>Логин:</strong> '.$username.'</li>
                        <li><strong>Телефон:</strong> '.$tel.'</li>
                        <li><strong>Email:</strong> '.$email.'</li>
                    </ul>
                    <p>Теперь вы можете войти в систему, используя указанные данные.</p>
                    <p>Желаем вам приятного просмотра!</p>
                    <p>С уважением,<br>Команда кинотеатра «Высокого стиля»</p>
                </body>
                </html>
                ';
                
                // Вызываем функцию отправки письма
                $sendResult = sendEmail($message, $email);
                
                if (!$sendResult['success']) {
                    // Если письмо не отправилось, пишем в лог, но не прерываем регистрацию
                    error_log("Не удалось отправить письмо для $email: " . $sendResult['error']);
                }
                
                header("Location: register.php");
                exit();
            }
        }
    } else {
        echo "<h2>Пожалуйста, введите капчу!</h2>";
        $show_form = true;
    }
}

// Проверка, авторизован ли пользователь
if (isset($_SESSION['username'])) {
    echo "<h2>Поздравляем, регистрация прошла успешно!</h2>";
    echo "<p>На вашу электронную почту было отправлено письмо с подтверждением.</p>";
    exit();
}

// Получаем текущую капчу
$current_captcha_id = $_SESSION['captcha_id'];
$sql_current_captcha = "SELECT image_path FROM captchas WHERE id = $current_captcha_id";
$result_current_captcha = $con->query($sql_current_captcha);
$row_current_captcha = $result_current_captcha->fetch_assoc();
$captcha_image = $row_current_captcha['image_path'];
?>

<!-- Остальная часть формы регистрации без изменений -->
<div style="width:500px; height:400px; background-color:#ffff99">
    <h2>Регистрация</h2> 
    <form action="" onsubmit="return validate_form(this)" method="post">
        <p>Имя пользователя: <input type="text" name="username" value="<?php echo $form_values['username']; ?>"></p>
        <p>Пароль: <input type="password" name="password" value="<?php echo $form_values['password']; ?>"></p>
        <p>Телефон: <input type="text" name="tel" value="<?php echo $form_values['tel']; ?>"></p>
        <p>Электронная почта: <input type="text" name="email" value="<?php echo $form_values['email']; ?>"></p>
        <p>Пол: 
            <input type="radio" name="sex" value="1" <?php echo ($form_values['sex'] == '1') ? 'checked="checked"' : ''; ?> />Мужчина
            <input type="radio" name="sex" value="0" <?php echo ($form_values['sex'] == '0') ? 'checked="checked"' : ''; ?> />Женщина
        </p>
        
        <!-- Поле для капчи -->
        <p>Капча: 
            <img src="<?php echo $captcha_image; ?>" alt="CAPTCHA" style="vertical-align: middle;">
            <input type="text" name="captcha" required>
        </p>
        
        <input type="submit" value="Зарегистрироваться">
    </form>
</div>

<script type="text/javascript">
function validate_form(thisform) {
    if (thisform.username.value == null || thisform.username.value=="") {
        alert("Имя пользователя не может быть пустым!");
        return false;
    }
    else if (thisform.tel.value == null || thisform.tel.value=="") {
        alert("Номер телефона не может быть пустым!");
        return false;
    }
    else if (thisform.email.value == null || thisform.email.value=="") {
        alert("Электронная почта не может быть пустой!");
        return false;
    }
    else if (thisform.captcha.value == null || thisform.captcha.value=="") {
        alert("Пожалуйста, введите капчу!");
        return false;
    }
    return true;
}
</script>

<?php
include 'footer.php';
?>